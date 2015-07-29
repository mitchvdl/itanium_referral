<?php
/**
 * Referral Customer grid container
 *
 * @category    Itanium
 * @package     Itanium_Referral

 */
class Itanium_Referral_Block_Adminhtml_Referral_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'itanium_referral';
        $this->_controller = 'adminhtml_referral_customer';
        $this->_headerText = Mage::helper('itanium_referral')->__('Manage Referal Customers');
        parent::__construct();
        $this->_updateButton('add', 'label', Mage::helper('itanium_referral')->__('Add Referal Customer'));
    }
}
