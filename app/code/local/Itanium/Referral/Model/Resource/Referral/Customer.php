<?php

/**
 * Referral Customer resource model
 *
 * @category    Itanium
 * @package     Itanium_Referral

 */
class Itanium_Referral_Model_Resource_Referral_Customer extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        $this->_init('itanium_referral/referral_customer', 'entity_id');
    }

    /**
     * Fetch Referral Customers,  by customer and website and set data to  object
     *
     * @param Itanium_Referral_Model_Referral_Customer $referral_Customer
     * @param integer $customerId
     * @param integer $websiteId
     * @return Itanium_Referral_Model_Referral_Customer
     */
    public function loadByCustomerId(Itanium_Referral_Model_Referral_Customer $referral_Customer, $customerId, $websiteId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('customer_id = :customer_id')
            ->where('website_id = :website_id');
        $bind = array(
            ':customer_id' => $customerId,
            ':website_id'  => $websiteId
        );
        if ($data = $this->_getReadAdapter()->fetchRow($select, $bind)) {
            $referral_Customer->addData($data);
        }
        $this->_afterLoad($referral_Customer);
        return $this;
    }

    /**
     * @param \Itanium_Referral_Model_Referral_Customer $referral_Customer
     * @param $uuid
     * @param $websiteId
     *
     * @return $this
     */
    public function loadByCustomerUUID(Itanium_Referral_Model_Referral_Customer $referral_Customer, $uuid, $websiteId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('customer_id = :customer_id')
            ->where('website_id = :website_id');
        $bind = array(
            ':customer_uuid' => $uuid,
            ':website_id'  => $websiteId
        );
        if ($data = $this->_getReadAdapter()->fetchRow($select, $bind)) {
            $referral_Customer->addData($data);
        }
        $this->_afterLoad($referral_Customer);
        return $this;
    }
}
