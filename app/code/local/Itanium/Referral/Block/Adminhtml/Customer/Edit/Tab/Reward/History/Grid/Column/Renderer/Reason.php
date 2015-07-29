<?php
/**
 * Column renderer for messages in referral history grid
 *
 */
class Itanium_Referral_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid_Column_Renderer_Reason
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render "Expired / not expired" reward "Reason" field
     *
     * @param   Varien_Object $row
     * @return  string
     */
    protected function _getValue(Varien_Object $row)
    {
        $expired = '';
        if ($row->getData('is_duplicate_of') !== null) {
             $expired = '<em>' . Mage::helper('itanium_referral')->__('Expired Referral.') . '</em> ';
        }
        return $expired . (parent::_getValue($row));
    }
}
