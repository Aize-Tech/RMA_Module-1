<?php

namespace Aize\Rma\ViewModel;


use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\InventoryApi\Api\StockRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class RmaModel implements ArgumentInterface
{
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private SortOrderBuilder $sortOrderBuilder;
    private OrderRepositoryInterface $orderRepository;
    private GetProductSalableQtyInterface $getProductSalableQty;
    private StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver;
    private StoreRepositoryInterface $storeRepository;
    private Session $session;


    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        GetProductSalableQtyInterface $getProductSalableQty,
        StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver,
        StoreRepositoryInterface $storeRepository,
        Session $session
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository = $orderRepository;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->stockByWebsiteIdResolver = $stockByWebsiteIdResolver;
        $this->storeRepository = $storeRepository;
        $this->session = $session;
    }


    public function getOrders()
    {
        $sortOrder = $this->sortOrderBuilder
            ->setField('created_at')
            ->setDescendingDirection()
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('customer_id', $this->session->getCustomerId())
            ->setPageSize(5)
            ->addSortOrder($sortOrder)
            ->create();

        $orders = $this->orderRepository->getList($searchCriteria);

        return $orders;
    }

    public function getStockId(OrderInterface $order) :int
    {
        $storeId = $order->getStoreId();
        $store = $this->storeRepository->getById($storeId);


        $stock = $this->stockByWebsiteIdResolver->execute($store->getWebsiteId());

        return $stock->getStockId();
    }

    public function getSalableQty($sku, $stockId) :float
    {
        return $this->getProductSalableQty->execute($sku, $stockId);
    }
}
