<?php

namespace AJH\D2R\Controller\Tpms;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\SessionFactory;
use AJH\D2R\Model\Help as D2RHelp;
use AJH\D2R\Helper\Email as D2REmail;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Post extends \Magento\Framework\App\Action\Action {

    protected $_customerSession, $_d2rHelp, $_d2rEmail, $_scopeConfig;

    public function __construct(Context $context, SessionFactory $customerSession, D2RHelp $d2rHelp, D2REmail $d2rEmail, ScopeConfigInterface $scopeConfig) {
        parent::__construct($context);

        $this->_customerSession = $customerSession->create();
        $this->_d2rHelp = $d2rHelp;
        $this->_d2rEmail = $d2rEmail;
        $this->_scopeConfig = $scopeConfig;
    }

    public function execute() {
        $trackingNumber = date('z-B-') . rand(10, 99);
        $data = $this->getRequest()->getParams();

        unset($data['form_key']);
        unset($data['_']);

        $customer = $this->_customerSession->getCustomer();

        try {
            $model = $this->_d2rHelp
                    ->prepareForSave($data)
                    ->save();

            $emailHelper = $this->_d2rEmail;

            $sender = [
                'name' => $this->_scopeConfig->getValue('d2r_tpms/email/sender/from_name'),
                'email' => $this->_scopeConfig->getValue('d2r_tpms/email/sender/from_email')
            ];

            $vars = $data;
            $vars['trackingNumber'] = $trackingNumber;
            $vars['customer'] = $customer->getData();
//            $vars['customer']['id'] = $customer->getID();
//            $vars['customer']['name'] = $customer->getName();
//            $vars['customer']['email'] = $customer->getEmail();
            $vars['tpmsId'] = $model->getId();
            
//            var_dump($customer->getEmail());
//            var_dump($this->_scopeConfig->getValue('d2r_tpms/email/admin_notification/admin_notify_email'));
////            
//            echo $this->_scopeConfig->getValue('d2r_tpms/email/customer_notification/customer_notify_template') . "<br/>";
////            die($this->_scopeConfig->getValue('d2r_tpms/email/customer_notification/customer_notify_template'));

            $emailHelper->send(
                    'd2r_tpms_email_admin_notification_admin_notify_template',
                    $sender, 
                    $this->_scopeConfig->getValue('d2r_tpms/email/admin_notification/admin_notify_email'), 
                    'TPMS Challenge', 
                    $vars, 
                    $customer->getEmail() // reply to
            );
//
            $emailHelper->send(
                    'd2r_tpms_email_customer_notification_customer_notify_template',
                    $sender,
                    $customer->getEmail(),
                    $customer->getName(),
                    $vars,
                    $this->_scopeConfig->getValue('d2r_tpms/email/admin_notification/admin_notify_email') // reply to
            );

            $emailHelper->send(
                    'd2r_tpms_email_admin_notification_admin_notify_template',
                    $sender,
                    'rey@ajhcreate.com',
                    'TPMS Challenge',
                    $vars,
                    $customer->getEmail() // reply to
            );
//
            $emailHelper->send(
                    'd2r_tpms_email_customer_notification_customer_notify_template',
                    $sender,
                    'rey@ajhcreate.com',
                    'TPMS Challenge CUSTOMER',
                    $vars,
                    $this->_scopeConfig->getValue('d2r_tpms/email/admin_notification/admin_notify_email') // reply to
            );

            echo $this->_view->getLayout()->createBlock('Magento\Framework\View\Element\Template')
                    ->setTemplate('AJH_D2R::tpms/success.phtml')
                    ->setTrackingNumber($trackingNumber)
                    ->setEmail($customer->getEmail())
                    ->toHtml();
            die;
        } catch (\Exception $e) {
            echo $e->getMessage();
            die;
        }
        die;
    }

}
