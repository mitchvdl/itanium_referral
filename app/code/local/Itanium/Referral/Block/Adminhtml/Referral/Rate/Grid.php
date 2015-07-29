<?php
/**
 * Reward rate grid
 *
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Block_Adminhtml_Referral_Rate_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('referralRatesGrid');
    }

    /**
     * Prepare grid collection object
     *
     * @return Itanium_Referral_Block_Adminhtml_Referral_Rate_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Itanium_Referral_Model_Resource_Reward_Rate_Collection */
        $collection = Mage::getModel('itanium_referral/reward_rate')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Itanium_Referral_Block_Adminhtml_Referral_Rate_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('rate_id', array(
            'header' => Mage::helper('itanium_referral')->__('ID'),
            'align'  => 'left',
            'index'  => 'rate_id',
            'width'  => 1,
        ));

        $this->addColumn('website_id', array(
            'header'  => Mage::helper('itanium_referral')->__('Website'),
            'index'   => 'website_id',
            'type'    => 'options',
            'options' => Mage::getModel('itanium_referral/source_website')->toOptionArray()
        ));

        $this->addColumn('customer_group_id', array(
            'header'  => Mage::helper('itanium_referral')->__('Customer Group'),
            'index'   => 'customer_group_id',
            'type'    => 'options',
            'options' => Mage::getModel('itanium_referral/source_customer_groups')->toOptionArray()
        ));

        $this->addColumn('rate', array(
            'getter'   => array($this, 'getRateText'),
            'header'   => Mage::helper('itanium_referral')->__('Rate'),
            'filter'   => false,
            'sortable' => false,
            'html_decorators' => 'nobr',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('rate_id' => $row->getId()));
    }

    /**
     * Rate text getter
     *
     * @param Varien_Object $row
     * @return string|null
     */
    public function getRateText($row)
    {
        $websiteId = $row->getWebsiteId();
        return Itanium_Referral_Model_Reward_Rate::getRateText($row->getDirection(), $row->getPoints(),
            $row->getCurrencyAmount(),
            0 == $websiteId ? null : Mage::app()->getWebsite($websiteId)->getBaseCurrencyCode()
        );
    }
}
