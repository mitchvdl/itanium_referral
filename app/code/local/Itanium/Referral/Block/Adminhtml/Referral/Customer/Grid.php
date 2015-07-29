<?php
/**
 * Reward rate grid
 *
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Block_Adminhtml_Referral_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
     * @return Itanium_Referral_Block_Adminhtml_Referral_Customer_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Itanium_Referral_Model_Resource_Referral_Customer_Collection */
        $collection = Mage::getModel('itanium_referral/referral_customer')->getCollection();
        $collection->addCustomerInfo();
        $collection->addReferralCustomerInfo();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @param Itanium_Referral_Model_Resource_Reward_History_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Text $column
     *
     * @return $this
     */
    protected function _addCustomerFilterData($collection, $column)
    {
        $filterValue = $column->getFilter()->getValue();

        // allow the useof REGEX searches when the user does not spicy a regex in particular
        $filterValue = preg_replace("/^\*/", '', $filterValue);
        $filterValue = !preg_match("/\*$/", $filterValue) ? $filterValue . '*' : $filterValue;

        switch ( $column->getIndex() ) {
            case 'customer_email' :
                //id = :id
                $collection->getSelect()->where('ce.email RLIKE ?', [$filterValue]);
                break;
            case 'customer_firstname':
                $collection->getSelect()->where('cft.value RLIKE ?', [$filterValue]);
//                $collection->addFieldToFilter('cft.value', array('rlike' => $column->getFilter()->getValue()));
                break;
            case 'customer_lastname' :
                $collection->getSelect()->where('clt.value RLIKE ?', [$filterValue]);
//                $collection->addFieldToFilter('clt.value', array('rlike' => $column->getFilter()->getValue()));
                break;
            default:
                break;
        }
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return Itanium_Referral_Block_Adminhtml_Referral_Customer_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('itanium_referral')->__('ID'),
            'align'  => 'left',
            'index'  => 'entity_id',
            'width'  => 1,
        ));

        $this->addColumn('website_id', array(
            'header'  => Mage::helper('itanium_referral')->__('Website'),
            'index'   => 'website_id',
            'type'    => 'options',
            'options' => Mage::getModel('itanium_referral/source_website')->toOptionArray()
        ));

        $this->addColumn('customer_id', array(
            'header'  => Mage::helper('itanium_referral')->__('Customer Id'),
            'index'   => 'customer_id',
            'type'    => 'text',
        ));

        $this->addColumn('customer_uuid', array(
            'header'  => Mage::helper('itanium_referral')->__('Customer UUID'),
            'index'   => 'customer_uuid',
            'type'    => 'text',
        ));

        foreach ( [
                      'customer_email' => 'Email',
                      'customer_firstname' => 'Firstname',
                      'customer_lastname' => 'Lastname',
                  ] as $index => $label ) {
            $this->addColumn($index, array(
                'header' => Mage::helper('itanium_referral')->__($label),
                'align' => 'right',
                'index' => $index,
                'html_decorators' => 'nobr',
                'filter_condition_callback' => array($this, '_addCustomerFilterData'),
//                'width'  => 1,
            ));
        }

        $this->addColumn('referral_customer_id', array(
            'header'  => Mage::helper('itanium_referral')->__('Referral Customer Id'),
            'index'   => 'referral_customer_id',
            'type'    => 'text',
        ));

        $this->addColumn('referral_customer_uuid', array(
            'header'  => Mage::helper('itanium_referral')->__('Referral Customer UUID'),
            'index'   => 'referral_customer_uuid',
            'type'    => 'text',
        ));
        foreach ( [
                      'referral_customer_email' => 'Referral Email',
                      'referral_customer_firstname' => 'Referral Firstname',
                      'referral_customer_lastname' => 'Referral Lastname',
                  ] as $index => $label ) {
            $this->addColumn($index, array(
                'header' => Mage::helper('itanium_referral')->__($label),
                'align' => 'right',
                'index' => $index,
                'html_decorators' => 'nobr',
                'filter_condition_callback' => array($this, '_addCustomerFilterData'),
//                'width'  => 1,
            ));
        }


        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('entity_id' => $row->getId()));
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
