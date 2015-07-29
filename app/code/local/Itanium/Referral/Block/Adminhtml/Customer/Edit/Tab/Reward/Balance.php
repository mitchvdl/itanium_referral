<?php
/**
 * Referral points balance container
 *
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('itanium/referral/customer/edit/management/balance.phtml');
    }

    /**
     * Prepare layout.
     * Create balance grid block
     *
     * @return Itanium_Referral_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance
     */
    protected function _prepareLayout()
    {
        if (!Mage::getSingleton('admin/session')
            ->isAllowed(Itanium_Referral_Helper_Data::XML_PATH_PERMISSION_BALANCE)
        ) {
            // unset template to get empty output
            $this->setTemplate(null);
        } else {
            $grid = $this->getLayout()
                ->createBlock('itanium_referral/adminhtml_customer_edit_tab_reward_management_balance_grid');
            $this->setChild('grid', $grid);
        }
        return parent::_prepareLayout();
    }
}
