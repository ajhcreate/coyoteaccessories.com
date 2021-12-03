<?php

namespace AJH\LabelImages\Model\Product\Attribute\Frontend;

class Image extends \Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend {

    public function getUrl($object, $size = null) {
        $url = false;
        $image = $object->getData($this->getAttribute()->getAttributeCode());

        if (!is_null($size) && file_exists($this->getUrl('pub/media') . 'catalog/product/' . $size . '/' . $image)) {
            # resized image is cached
            $url = $this->getUrl('pub/media') . 'catalog/product/' . $size . '/' . $image;
        } elseif (!is_null($size)) {
            # resized image is not cached
            $url = $this->getUrl('pub/media') . 'catalog/product/image/size/' . $size . '/' . $image;
        } elseif ($image) {
            # using original image
            $url = $this->getUrl('pub/media') . 'catalog/product/' . $image;
        }
        return $url;
    }

}
