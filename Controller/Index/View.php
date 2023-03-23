<?php

namespace Aize\Rma\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Url;

class View implements HttpGetActionInterface
{
    private ResultFactory $resultFactory;
    private Session $customerSession;
    private Url $url;

    public function __construct(
        ResultFactory $resultFactory,
        Session $customerSession,
        Url $url
    ) {
        $this->resultFactory = $resultFactory;
        $this->customerSession = $customerSession;
        $this->url = $url;
    }

    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {

            $this->customerSession->setBeforeAuthUrl($this->url->getCurrentUrl());

            /** @var Redirect $redirectResult */
            $redirectResult = $this->resultFactory->create($this->resultFactory::TYPE_REDIRECT);
            $redirectResult->setPath('customer/account/login');

            return $redirectResult;
        }

        $pageResult = $this->resultFactory->create(
            $this->resultFactory::TYPE_PAGE
        );
        return $pageResult;
    }
}
