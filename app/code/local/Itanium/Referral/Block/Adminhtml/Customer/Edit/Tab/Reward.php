<?php

/**
 * Referral tab block
 *
 * @category    Itanium
 * @package     Itanium_Referral
 * 
 */
class Itanium_Referral_Block_Adminhtml_Customer_Edit_Tab_Reward
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Return tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('itanium_referral')->__('Customer Referral Points');
    }

    /**
     * Return tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('itanium_referral')->__('Customer Referral Points');
    }

    /**
     * Check if can show tab
     *
     * @return boolean
     */
    public function canShowTab()
    {
        $customer = Mage::registry('current_customer');
        return $customer->getId()
            && Mage::helper('itanium_referral')->isEnabled()
            && Mage::getSingleton('admin/session')
                ->isAllowed(Itanium_Referral_Helper_Data::XML_PATH_PERMISSION_BALANCE);
    }

    /**
     * Check if tab hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare layout.
     * Add accordion items
     *
     * @return Itanium_Referral_Block_Adminhtml_Customer_Edit_Tab_Reward
     */
    protected function _prepareLayout()
    {
        $accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion');
        $accordion->addItem('referral_points_history', array(
            'title'       => Mage::helper('itanium_referral')->__('Referral Points History'),
            'open'        => false,
            'class'       => '',
            'ajax'        => true,
            'content_url' => $this->getUrl('*/customer_referral/history', array('_current' => true))
        ));
        $this->setChild('referral_history_accordion', $accordion);

        return parent::_prepareLayout();
    }

    /**
     * Precessor tab ID getter
     *
     * @return string
     */
    public function getAfter()
    {
        return 'tags';
    }
}
