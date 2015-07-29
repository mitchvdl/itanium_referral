<?php

/**
 * Referral Customer Groups source model
 *
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Model_Source_Customer_Groups
{
    /**
     * Retrieve option array of customer groups
     *
     * @return array
     */
    public function toOptionArray()
    {
        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();
        $groups = array(0 => Mage::helper('itanium_referral')->__('All Customer Groups'))
                + $groups;
        return $groups;
    }
}
