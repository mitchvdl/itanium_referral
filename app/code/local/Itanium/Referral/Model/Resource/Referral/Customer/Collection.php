<?php
/**
 * Referral Customer collection
 *
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Model_Resource_Referral_Customer_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        $this->_init('itanium_referral/referral_customer');
    }


    /**
     * Join reward table to filter history by customer id
     *
     * @param string $customerId
     * @return Itanium_Referral_Model_Resource_Referral_Customer_Collection
     */
    public function addCustomerFilter($customerId)
    {
        if ($customerId) {
            $this->getSelect()->where('main_table.customer_id = ?', $customerId);
        }
        return $this;
    }

    /**
     * Add filter by website id
     *
     * @param integer|array $websiteId
     * @return Itanium_Referral_Model_Resource_Referral_Customer_Collection
     */
    public function addWebsiteFilter($websiteId)
    {
        $this->getSelect()->where(
            is_array($websiteId) ? 'main_table.website_id IN (?)' : 'main_table.website_id = ?', $websiteId
        );
        return $this;
    }

    /**
     * Join additional customer information, such as email, name etc.
     *
     * @return Itanium_Referral_Model_Resource_Referral_Customer_Collection
     */
    public function addCustomerInfo()
    {
        if ($this->getFlag('customer_added')) {
            return $this;
        }

        $customer = Mage::getModel('customer/customer');
        /* @var $customer Mage_Customer_Model_Customer */
        $firstname  = $customer->getAttribute('firstname');
        $lastname   = $customer->getAttribute('lastname');
        $warningNotification = $customer->getAttribute('itanium_referral_update_not');

        $connection = $this->getConnection();
        /* @var $connection Zend_Db_Adapter_Abstract */

        $this->getSelect()
            ->joinInner(
                array('ce' => $customer->getAttribute('email')->getBackend()->getTable()),
                'ce.entity_id=main_table.customer_id',
                array('customer_email' => 'email')
            )
            ->joinInner(
                array('cg' => $customer->getAttribute('group_id')->getBackend()->getTable()),
                'cg.entity_id=main_table.customer_id',
                array('customer_group_id' => 'group_id')
            )
            ->joinLeft(
                array('clt' => $lastname->getBackend()->getTable()),
                $connection->quoteInto('clt.entity_id=main_table.customer_id AND clt.attribute_id = ?',
                    $lastname->getAttributeId()),
                array('customer_lastname' => 'value')
            )
            ->joinLeft(
                array('cft' => $firstname->getBackend()->getTable()),
                $connection->quoteInto(
                    'cft.entity_id=main_table.customer_id AND cft.attribute_id = ?',
                    $firstname->getAttributeId()
                ),
                array('customer_firstname' => 'value')
            )
            ->joinLeft(
                array('warning_notification' => $warningNotification->getBackend()->getTable()),
                $connection->quoteInto(
                    'warning_notification.entity_id=main_table.customer_id AND warning_notification.attribute_id = ?',
                    $warningNotification->getAttributeId()
                ),
                array('referral_warning_notification' => 'value')
            )
        ;

        $this->setFlag('customer_added', true);
        return $this;
    }

    /**
     * Join additional customer information, such as email, name etc.
     *
     * @return Itanium_Referral_Model_Resource_Referral_Customer_Collection
     */
    public function addReferralCustomerInfo()
    {
        if ($this->getFlag('referral_customer_added')) {
            return $this;
        }

        $customer = Mage::getModel('customer/customer');
        /* @var $customer Mage_Customer_Model_Customer */
        $firstname  = $customer->getAttribute('firstname');
        $lastname   = $customer->getAttribute('lastname');
        $warningNotification = $customer->getAttribute('itanium_referral_update_not');

        $connection = $this->getConnection();
        /* @var $connection Zend_Db_Adapter_Abstract */

        $this->getSelect()
            ->joinInner(
                array('rce' => $customer->getAttribute('email')->getBackend()->getTable()),
                'rce.entity_id=main_table.referral_customer_id',
                array('referral_customer_email' => 'email')
            )
            ->joinInner(
                array('rcg' => $customer->getAttribute('group_id')->getBackend()->getTable()),
                'rcg.entity_id=main_table.referral_customer_id',
                array('referral_customer_group_id' => 'group_id')
            )
            ->joinLeft(
                array('rclt' => $lastname->getBackend()->getTable()),
                $connection->quoteInto('rclt.entity_id=main_table.referral_customer_id AND rclt.attribute_id = ?',
                    $lastname->getAttributeId()),
                array('referral_customer_lastname' => 'value')
            )
            ->joinLeft(
                array('rcft' => $firstname->getBackend()->getTable()),
                $connection->quoteInto(
                    'rcft.entity_id=main_table.referral_customer_id AND rcft.attribute_id = ?',
                    $firstname->getAttributeId()
                ),
                array('referral_customer_firstname' => 'value')
            )
            ->joinLeft(
                array('rwarning_notification' => $warningNotification->getBackend()->getTable()),
                $connection->quoteInto(
                    'rwarning_notification.entity_id=main_table.referral_customer_id AND rwarning_notification.attribute_id = ?',
                    $warningNotification->getAttributeId()
                ),
                array('referral_referral_warning_notification' => 'value')
            )
        ;

        $this->setFlag('referral_customer_added', true);
        return $this;
    }



    /**
     * Order by primary key desc
     *
     * @return Itanium_Referral_Model_Resource_Referral_Customer_Collection
     */
    public function setDefaultOrder()
    {
        $this->getSelect()->reset(Zend_Db_Select::ORDER);

        return $this
            ->addOrder('created_at', 'DESC')
            ->addOrder('entity_id', 'DESC');
    }
}
