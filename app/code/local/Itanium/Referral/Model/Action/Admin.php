<?php
/**
 * Referal action for updating balance by administrator
 *
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Model_Action_Admin extends Itanium_Referral_Model_Action_Abstract
{
    /**
     * Check whether rewards can be added for action
     *
     * @return bool
     */
    public function canAddRewardPoints()
    {
        return true;
    }

    /**
     * Return action message for history log
     *
     * @param array $args Additional history data
     * @return string
     */
    public function getHistoryMessage($args = array())
    {
        return Mage::helper('itanium_referral')->__('Updated by moderator.');
    }
}
