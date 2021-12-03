<?php

namespace AJH\Catalog\Block\Product\View;

class Attributes extends \Magento\Catalog\Block\Product\View\Attributes {

    private $attributes = [
        "web_pack_qty_0" => ["label" => "web_pack_qty_0", "value" => "web_level_0_qty"],
        "web_level_1_qty" => ["label" => "web_pack_qty_1", "value" => "web_level_1_qty"],
        "web_level_2_qty" => ["label" => "web_pack_qty_2", "value" => "web_level_2_qty"],
        "web_level_3_qty" => ["label" => "web_pack_qty_3", "value" => "web_level_3_qty"],
        "web_level_4_qty" => ["label" => "web_pack_qty_4", "value" => "web_level_4_qty"],
        "web_level_5_qty" => ["label" => "web_pack_qty_5", "value" => "web_level_5_qty"]
    ];

    /**
     * $excludeAttr is optional array of attribute codes to exclude them from additional data array
     *
     * @param array $excludeAttr
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAdditionalData(array $excludeAttr = []) {
        $data = [];
        $product = $this->getProduct();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            if ($this->isVisibleOnFrontend($attribute, $excludeAttr)) {
                $code = $attribute->getAttributeCode();
                $label = $attribute->getStoreLabel();
                $value = $attribute->getFrontend()->getValue($product);

                if ($value instanceof Phrase) {
                    $value = (string) $value;
                } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = $this->priceCurrency->convertAndFormat($value);
                }

                list($_label, $_value) = $this->overrideLabelValue($code, $label, $value);

                if (is_string($value) && strlen(trim($value))) {
                    $data[$attribute->getAttributeCode()] = [
                        'label' => $_label,
                        'value' => $_value,
                        'code' => $code,
                    ];
                }
            }
        }
        return $data;
    }

    /**
     * @param string $code
     * @param string $label
     * @param string $value     
     * @return array     
     */
    public function overrideLabelValue($code, $label, $value) {
        $attrs = $this->attributes;
        $_label = $label;
        $_value = $value;
        foreach ($attrs as $key => $attr) {
            if ($code == $key) {
                $_label = $this->getProduct()->getAttributeText($attr["label"]);
                $_value = $this->getProduct()->getData($attr["value"]);
            }
        }

        return [$_label, $_value];
    }

}
