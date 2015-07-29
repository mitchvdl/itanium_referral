<?php
/**
 * File: Overview.ph.php
 *
 * User: Mitch Vanderlinden
 * email: mitchvdl@gmail.com
 * Date: 22.07.15 11:26
 * Package:
 */

class Itanium_Referral_Block_Adminhtml_Referral_Overview extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'itanium_referral';
        $this->_controller = 'adminhtml_referral_overview';
        $this->_headerText = Mage::helper('itanium_referral')->__('Manage Overview History');
        parent::__construct();
//        $this->_updateButton('add', 'label', Mage::helper('itanium_referral')->__('Add New History entry'));
        $this->removeButton('add');
    }
}