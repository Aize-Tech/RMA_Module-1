<?php
namespace Aize\Rma\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Aize\Rma\Model\ResourceModel\RmaRequest\CollectionFactory;
use Magento\Sales\Model\Order\ItemFactory;

class RmaHistory extends Template
{
    protected $customerSession;
    protected $rmaCollectionFactory;
    protected $orderItemFactory;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CollectionFactory $rmaCollectionFactory,
        ItemFactory $orderItemFactory,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->rmaCollectionFactory = $rmaCollectionFactory;
        $this->orderItemFactory = $orderItemFactory;
        parent::__construct($context, $data);
    }

    public function getCustomerRmaRequests()
    {
        $collection = $this->rmaCollectionFactory->create();
        $collection->setOrder('entity_id', 'DESC');

        return $collection;
    }

    public function getOrderItemName($orderItemId)
    {
        $orderItem = $this->orderItemFactory->create()->load($orderItemId);
        return $orderItem->getName();
    }

}
