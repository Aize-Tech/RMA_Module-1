<?php

namespace Aize\Rma\Block;

use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\OrderFactory;

class RmaForm extends Template
{
    protected $_template = 'Aize_Rma::rma_form.phtml';

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * RmaForm constructor.
     *
     * @param Template\Context $context
     * @param OrderFactory $orderFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        OrderFactory $orderFactory,
        array $data = []
    ) {
        $this->orderFactory = $orderFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get order items
     *
     * @return array
     */
    public function getOrderItems()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->orderFactory->create()->load($orderId);
        return $order->getAllVisibleItems();
    }

    public function getRmaRequestData()
    {
        return $this->getViewModel('Aize\Rma\ViewModel\RmaRequestData');
    }

}
