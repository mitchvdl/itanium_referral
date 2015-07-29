<?php
/**
 * Source model for websites, including "All" option
 *
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Model_Source_Store
{
    /**
     * Prepare and return array of stores ids and their names
     *
     * @param bool $withAll Whether to prepend "All Stores" option on not
     * @return array
     */
    public function toOptionArray($withAll = true)
    {
        /** @var Mage_Adminhtml_Model_System_Store $stores */
        $stores = Mage::getSingleton('adminhtml/system_store')->getStoreOptionHash();
        if ($withAll) {
            $stores = array(0 => Mage::helper('itanium_referral')->__('All Stores'))
                      + $stores;
        }
        return $stores;
    }
}
