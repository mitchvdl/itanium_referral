<?php
/**
 * Referral history container
 *
 * @category    Itanium
 * @package     Itanium_Referral

 */
class Itanium_Referral_Block_Adminhtml_Customer_Edit_Tab_Reward_History
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('itanium/referral/customer/edit/history.phtml');
    }

    /**
     * Prepare layout.
     * Create history grid block
     *
     * @return Itanium_Referral_Block_Adminhtml_Customer_Edit_Tab_Reward_History
     */
    protected function _prepareLayout()
    {
        $grid = $this->getLayout()
            ->createBlock('itanium_referral/adminhtml_customer_edit_tab_reward_history_grid')
            ->setCustomerId($this->getCustomerId());
        $this->setChild('referral.grid', $grid);
        return parent::_prepareLayout();
    }
}
