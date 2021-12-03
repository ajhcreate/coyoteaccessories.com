<?php

namespace AJH\Fitment\Block\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\App\Request\Http as HttpRequest;

class Cache extends Field {
    /*
     * Set template
     */

    /**
     * @var string
     */
    public $buttonLabel = "Flush Cache Storage";
    protected $_template = "AJH_Fitment::system/config/form/field/button.phtml";

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    public function __construct(Context $context, HttpRequest $request, array $data = []) {
        parent::__construct($context, $data);
        $this->_request = $request;
    }

    /**
     * Remove scope label
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for collect button
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('fitment/system_config/cache');
    }

    /**
     * Generate collect button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'clear_cache_button',
                'label' => __($this->buttonLabel),
            ]
        );

        return $button->toHtml();
    }

}
