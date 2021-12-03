<?php

namespace AJH\D2R\Model\Source\Retailer;

class Status extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource {

    const NONE = 0; // no business retationship ever established
    const CANDIDATE = 1; // application sent
    const APPROVED = 2; // application approved
    const DECLINED = 3; // application declined
    const TERMINATED = 4; // cooperation terminated
    const REVOKED = 5; // application revoked
    const ORAPPROVED = 6; // Active OROTek retailer (Partnership approved)

    public function getOptionArray() {
        return array(
            self::NONE => __('Not ever tried'),
            self::CANDIDATE => __('Application sent'),
            self::APPROVED => __('Active retailer (Partnership approved)'),
            self::ORAPPROVED => __('Active OROTek retailer (Partnership approved)'),
            self::DECLINED => __('Application declined'),
            self::TERMINATED => __('Terminated'),
            self::REVOKED => __('Revoked'),
        );
    }

    /**
     * to option array
     *
     * @return array
     */
    public function getAllOptions() {
        $_options = $this->getOptionArray();
        $options = array();
        $options[] = [
            'value' => '',
            'label' => __('-- Please Select --')
        ];
        foreach ($_options as $key => $_option) {
            $options[] = [
                'value' => $key,
                'label' => __($_option)
            ];
        }
        return $options;
    }

    public function toOptionArray() {

        return $this->getOptionArray();
    }

}
