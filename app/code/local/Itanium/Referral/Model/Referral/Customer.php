<?php

/**
 * Referral model
 * 
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Model_Referral_Customer extends Mage_Core_Model_Abstract
{
   /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('itanium_referral/referral_customer');
    }

}
