<?php

namespace AJH\D2R\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use AJH\D2R\Model\Distributor;
use Magento\Store\Model\StoreManagerInterface;
use AJH\D2R\Model\Source\Retailer\Status;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Customer\Model\Address\Config as AddressConfig;
use Magento\Customer\Model\ResourceModel\Address\CollectionFactory as AddressFactory;
use Magento\Framework\DataObject;

class Email extends AbstractHelper {

    const CUSTOMER_NOTIFICATION_TYPE_RETAILER_APP_SENT = 'retailer_app_sent';
    const CUSTOMER_NOTIFICATION_TYPE_RETAILER_APP_APPROVED = 'retailer_app_approved';
    const CUSTOMER_NOTIFICATION_TYPE_RETAILER_APP_DECLINED = 'retailer_app_declined';
    const XML_PATH_EMAIL_TEMPLATE_FIELD = 'section/group/your_email_template_field_id';

    protected $_emailData = null;
    protected $_modelDistributor;
    protected $_storeManager;
    protected $_retaierStatus;
    protected $_transportBuilder;
    protected $temp_id, $_scopeConfig, $_context;
    protected $_logger;
    protected $_inlineTranslation;
    protected $_addressConfig, $_addressFactory;

    public function __construct(Context $context, Distributor $modelDistributor,
            StoreManagerInterface $storeManager, Status $retaierStatus,
            TransportBuilder $transportBuilder,
            ScopeConfigInterface $scopeConfig, LoggerInterface $logger,
            StateInterface $inlineTranslation, AddressConfig $addressConfig,
            AddressFactory $addressFactory) {
        $this->_modelDistributor = $modelDistributor;
        $this->_storeManager = $storeManager;

        $this->_retaierStatus = $retaierStatus;
        $this->_transportBuilder = $transportBuilder;

        $this->_scopeConfig = $scopeConfig;

        $this->_inlineTranslation = $inlineTranslation;

        $this->_logger = $logger;

        $this->_addressConfig = $addressConfig;
        $this->_addressFactory = $addressFactory;

        parent::__construct($context);
    }

    protected function _prepareEmailData($customer, $address = null) {
        if (null === $this->_emailData) {
            if (!$address) {
                $address = $customer->getDefaultBillingAddress();
            }
            $application = new \Magento\Framework\DataObject($customer->getData(\AJH\D2R\Helper\Retailer::ATTR_APPLICATION_DATA));
            $authKey = $application['auth_key'];
            $distributor = $this->_modelDistributor->load($application['distributor_id']);
            if (!$distributor->getId()) {
                throw new \Exception('Invalid distributor ID');
            }

            $store = $this->_storeManager->getStore();

            $addressCollection = $this->_addressFactory->create()
                    ->addAttributeToFilter("entity_id", $address->getId())
                    ->getFirstItem();


            $renderer = $this->_addressConfig->getFormatByCode('html')->getRenderer();
            $address_html = $renderer->renderArray($addressCollection->getData());

//            var_dump($customer->getEmail());
//            die;

            $this->_emailData = [
                'customer' => $customer,
                '_customer' => [
                    'name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
                    'email' => $customer->getEmail()
                ],
                'store_id' => $store->getId(),
                'app' => $application,
                'address' => $address,
                'address_html' => $address_html,
                'view_profile_url' => $store->getUrl('customer/account', ['_secure' => true]),
                'distributor' => $distributor,
            ];
            $this->_emailData['approve_url'] = $store->getUrl('*/*/status', [
                '_secure' => true,
                'action' => $this->_retaierStatus::APPROVED,
                'id' => $customer->getId(),
                'key' => $authKey
            ]);
            $this->_emailData['decline_url'] = $store->getUrl('*/*/status', [
                '_secure' => true,
                'action' => $this->_retaierStatus::DECLINED,
                'id' => $customer->getId(),
                'key' => $authKey
            ]);
        }


        return $this->_emailData;
    }

    public function send($template, $from, $toEmail, $toName, $data, $replyTo = null) {

        try {
            $this->_inlineTranslation->suspend();


            $storeId = isset($data['store_id']) ? $data['store_id'] : $this->_storeManager->getStore()->getId();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;           
//
//            $transport = $this->_transportBuilder
//                    ->setTemplateIdentifier($template, $storeScope)
//                    ->setTemplateOptions(array(
//                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
//                        'store' => $storeId
//                    ))
//                    ->setTemplateVars($data)
//                    ->setFrom($from)
//                    ->addTo($toEmail)
//                    ->addBcc(['rey@ajhcreate.com'])
//                    ->getTransport();
//
//            if (!is_null($replyTo)) {
//                $transport->setReplyTo($replyTo);
//            }
            
            
            
            
            
            
            $transport = $this->_transportBuilder
                    ->setTemplateIdentifier($template) // this code we have mentioned in the email_templates.xml
                    ->setTemplateOptions(
                            [
                                'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                                'store' => $storeId,
                            ]
                    )
                    ->setTemplateVars($data)
                    ->setFrom($from)
                    ->addTo($toEmail)
                    ->addBcc(['rey@ajhcreate.com'])
                    ->getTransport();

            $transport->sendMessage();
            $this->_inlineTranslation->resume();
            
            
            


//        $emailTemplate->sendTransactional(
//                $template, $from, $toEmail, $toName, $data, $storeId
//        );
//
//        $res = $emailTemplate->getSentSuccess();


//            $transport->getTransport();
//            $transport->sendMessage();
//
//            $this->_inlineTranslation->resume();

            return true;
        } catch (\Exception $e) {

            $this->_logger->alert($e->getMessage());

//            $this->_logger->log(sprintf('Error sending email to %s, %s', $toEmail, print_r([
//                'template' => $template,
//                'from' => print_r($from, true),
//                'name' => $toName,
//                'data' => print_r($data, true),
//                                    ], true)
//            ));

            throw new \Exception($e->getMessage());
        }

        return false;
    }

    public function notifySales($customer, $address = null) {
        return $this->send(
                        $this->_scopeConfig->getValue('d2r_retailer/reg_emails/notif_sales_department/sales_notify_template', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                        [
                            'name' => $this->_scopeConfig->getValue('d2r_retailer/reg_emails/notif_emails_sender/send_from_name'),
                            'email' => $this->_scopeConfig->getValue('d2r_retailer/reg_emails/notif_emails_sender/send_from_email'),
                        ],
                        $this->_scopeConfig->getValue('d2r_retailer/reg_emails/notif_sales_department/sales_email'),
                        'Sales Dept',
                        $this->_prepareEmailData($customer, $address), $customer->getEmail()
        );
    }

    public function notifyDistributor($customer, $address) {
        $emailData = $this->_prepareEmailData($customer, $address);

        return $this->send(
                        $this->_scopeConfig->getValue('d2r_retailer/reg_emails/notif_to_distributor/distributor_notify_template'), ['name' => $this->_scopeConfig->getValue('d2r_retailer/reg_emails/notif_emails_sender/send_from_name'),
                    'email' => $this->_scopeConfig->getValue('d2r_retailer/reg_emails/notif_emails_sender/send_from_email'),
                        ], $emailData['distributor']['Email'], 'Sales Dept', $emailData, $customer->getEmail()
        );
    }

    public function notifyCustomer($customer, $address = null,
            $subject = self::CUSTOMER_NOTIFICATION_TYPE_RETAILER_APP_SENT) {
        switch ($subject) {
            case self::CUSTOMER_NOTIFICATION_TYPE_RETAILER_APP_SENT:
                $template = $this->_scopeConfig->getValue('d2r_retailer/reg_emails/retailer_notif_templates/retailer_appsent_template');
                break;

            case self::CUSTOMER_NOTIFICATION_TYPE_RETAILER_APP_APPROVED:
                $template = $this->_scopeConfig->getValue('d2r_retailer/reg_emails/retailer_notif_templates/retailer_approved_template');
                break;

            case self::CUSTOMER_NOTIFICATION_TYPE_RETAILER_APP_DECLINED:
                $template = $this->_scopeConfig->getValue('d2r_retailer/reg_emails/retailer_notif_templates/retailer_declined_template');
                break;
        }

        return $this->send(
                        $template, ['name' => $this->_scopeConfig->getValue('d2r_retailer/reg_emails/notif_emails_sender/send_from_name'),
                    'email' => $this->_scopeConfig->getValue('d2r_retailer/reg_emails/notif_emails_sender/send_from_email'),
                        ], $customer->getEmail(), $customer->getName(), $this->_prepareEmailData($customer, $address), $customer->getEmail()
        );
    }

    /**
     * Return template id according to store
     *
     * @return mixed
     */
    public function getTemplateId($xmlPath) {
        return $this->getConfigValue($xmlPath, $this->getStore()->getStoreId());
    }

    /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId) {
        return $this->_scopeConfig->getValue(
                        $path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId
        );
    }

}
