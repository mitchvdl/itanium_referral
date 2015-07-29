<?php
/**
 * Source model for list of Expiry Calculation algorythms
 */
class Itanium_Referral_Model_Source_Points_ExpiryCalculation
{
    const CALC_STATIC = 'static';
    const CALC_DYNAMIC = 'dynamic';

    public function toOptionArray()
    {
        return [
            ['value' => self::CALC_STATIC, 'label' => Mage::helper('itanium_referral')->__('Static')],
            ['value' => self::CALC_DYNAMIC, 'label' => Mage::helper('itanium_referral')->__('Dynamic')],
        ];
    }
}
