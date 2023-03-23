<?php

namespace Aize\Rma\Controller\Index;

use Magento\Framework\App\ActionInterface as HttpGetActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
// use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class Index implements HttpGetActionInterface
{
    protected $resultPageFactory;
    protected $customerSession;
    protected $orderRepository;
    protected $searchCriteriaBuilder;
    protected $orderItemRepository;
    protected $productRepository;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Session $customerSession,
        //OrderRepositoryInterface $orderRepository,
        OrderRepository $orderRepository,
        OrderItemRepositoryInterface $orderItemRepository,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder

    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;

    }

    public function execute()
    {

        /**
         *         $loginUrl = $this->customerUrl->getLoginUrl();

        if (!$this->customerSession->authenticate($loginUrl)) {
        $subject->getActionFlag()->set('', $subject::FLAG_NO_DISPATCH, true);
        }
         */

        if (!$this->customerSession->isLoggedIn()) {
            $this->customerSession->setAfterAuthUrl($this->_url->getCurrentUrl());
            $this->_redirect('customer/account/login');
            return;
        }

        $customerId = $this->customerSession->getCustomerId();



        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('customer_id', $customerId)
            ->addSortOrder()
            //->addSortOrder('created_at', 'DESC')
            ->setPageSize(1)
            ->setCurrentPage(1)
            ->create();

        $orders = $this->orderRepository->getList($searchCriteria);


        if (!$orders->getTotalCount()) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('No Orders Found'));
            return $resultPage;
        }

        $lastOrder = $orders->getFirstItem();
        $orderItems = $this->orderItemRepository->getList($lastOrder->getEntityId());

        $products = [];
        foreach ($orderItems->getItems() as $item) {
            $product = $this->productRepository->get($item->getSku());
            $products[] = [
                'name' => $product->getName(),
                'price' => $item->getPrice(),
            ];
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Last Ordered Products'));

        $block = $resultPage->getLayout()->getBlock('last.ordered.products');
        if ($block) {
            $block->setProducts($products);
        }

        return $resultPage;
    }
}
