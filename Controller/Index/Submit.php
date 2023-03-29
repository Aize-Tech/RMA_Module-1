<?php
namespace Aize\Rma\Controller\Index;

use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Aize\Rma\Model\RmaRequestFactory;
use Aize\Rma\Model\ResourceModel\RmaRequest as RmaRequestResource;
use Magento\Framework\Data\Form\FormKey\Validator;

class Submit extends Action
{
    protected $logger;
    protected $orderFactory;
    protected $rmaRequestFactory;
    protected $rmaRequestResource;
    protected $formKeyValidator;
    protected $orderRepository;

    public function __construct(
        Context $context,
        RmaRequestFactory $rmaRequestFactory,
        RmaRequestResource $rmaRequestResource,
        Validator $formKeyValidator,
        OrderFactory $orderFactory,
        LoggerInterface $logger,
        OrderRepositoryInterface $orderRepository

    ) {
        $this->rmaRequestFactory = $rmaRequestFactory;
        $this->rmaRequestResource = $rmaRequestResource;
        $this->formKeyValidator = $formKeyValidator;
        $this->orderFactory = $orderFactory;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;

        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $orderId = $this->getRequest()->getParam('order_id');
            $orderItemId = $this->getRequest()->getParam('order_item_id');
            $quantity = $this->getRequest()->getParam('quantity');
            $reason = $this->getRequest()->getParam('reason');

            if (!$orderId || !$orderItemId || !$quantity || !$reason) {
                throw new \Exception("Missing required fields.");
            }

            $order = $this->orderRepository->get($orderId);
            if (!$order->getId()) {
                throw new \Exception("Invalid order ID.");
            }

            $rmaRequest = $this->rmaRequestFactory->create();
            $rmaRequest->setOrderId($orderId);
            $rmaRequest->setOrderItemId($orderItemId);
            $rmaRequest->setQuantity($quantity);
            $rmaRequest->setReason($reason);
            $this->rmaRequestResource->save($rmaRequest);

            $this->messageManager->addSuccessMessage(__('Your RMA request has been submitted successfully.'));
            $resultRedirect->setPath('rma/index/view');
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->messageManager->addErrorMessage(__('An error occurred while submitting your RMA request.'));
            return $resultRedirect->setPath('*/*/index');
        }
    }
}
