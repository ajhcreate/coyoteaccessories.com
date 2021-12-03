<?php

namespace Magento\Contact\Controller\Index;

use AJH\ExtendMageContact\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Post extends \Magento\Contact\Controller\Index {

    protected $_pageFactory, $_helper, $_urlInterface, $_transportBuilder;
    
    protected $_scopeConfig;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $pageFactory, Data $data, \Magento\Framework\UrlInterface $urlInterface, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, ScopeConfigInterface $scopeConfig) {
        $this->_pageFactory = $pageFactory;
        $this->_helper = $data;

        $this->_urlInterface = $urlInterface;
        $this->_transportBuilder = $transportBuilder;
        
        $this->_scopeConfig = $scopeConfig;

        return parent::__construct($context);
    }

    public function execute() {

        /** @var AJH_ExtendMageContact_Helper_Data $helper */
        $helper = $this->_helper;

        $contactCmsUrlKey = $helper->getContactCmsUrlKey();
        if (!$helper->isEnabled() && empty($contactCmsUrlKey)) {
            return parent::postAction();
        }
        $newRedirectUrl = $this->_urlInterface->getUrl($contactCmsUrlKey);
        $post = $this->getRequest()->getPost();
        if ($post) {
            /* @var $translate Mage_Core_Model_Translate */
            $this->inlineTranslation->suspend();
            try {
                $postObject = new \Magento\Framework\DataObject();
                $postObject->setData($post);

                $error = false;

                if (!\Zend_Validate::is(trim($post['name']), 'NotEmpty')) {
                    $error = true;
                }

                if (!\Zend_Validate::is(trim($post['comment']), 'NotEmpty')) {
                    $error = true;
                }

                if (!\Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if (\Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }

                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $transport = $this->_transportBuilder
                        ->setTemplateIdentifier($this->_scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope))
                        ->setTemplateOptions(
                                [
                                    'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                                ]
                        )
                        ->setTemplateVars(['data' => $postObject])
                        ->setFrom($this->_scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope))
                        ->addTo($this->_scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope))
                        ->setReplyTo($post['email'])
                        ->getTransport();

                $transport->sendMessage();
                
                $transport_cc = $this->_transportBuilder
                        ->setTemplateIdentifier($this->_scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope))
                        ->setTemplateOptions(
                                [
                                    'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                                ]
                        )
                        ->setTemplateVars(['data' => $postObject])
                        ->setFrom($this->_scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope))
                        ->addTo($helper->getCCEmail())
                        ->setReplyTo($post['email'])
                        ->getTransport();

                $transport_cc->sendMessage();

                $this->inlineTranslation->resume();
                $this->messageManager->addSuccess(__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));

                $this->_redirect($newRedirectUrl);
                return;
            } catch (Exception $e) {
                $this->inlineTranslation->resume();

                $this->messageManager->addError(__('Unable to submit your request. Please, try again later'));

                $this->_redirect($newRedirectUrl);
                return;
            }
        } else {
            $this->_redirect($newRedirectUrl);
        }

        $this->_redirect('*/*/');
        return;
    }

}
