<?php
/**
 * Customer account referral points balance block
 *
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Block_Customer_Reward_Info extends Mage_Core_Block_Template
{
    /**
     * Reward pts model instance
     *
     * @var Itanium_Referral_Model_Reward
     */
    protected $_rewardInstance = null;

    /**
     * Render if all there is a customer and a balance
     *
     * @return string
     */
    protected function _toHtml()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($customer && $customer->getId()) {
            $this->_rewardInstance = Mage::getModel('itanium_referral/reward')
                ->setCustomer($customer)
                ->setWebsiteId(Mage::app()->getWebsite()->getId())
                ->loadByCustomer();
            if ($this->_rewardInstance->getId()) {
                $this->_prepareTemplateData();
                return parent::_toHtml();
            }
        }
        return '';
    }

    /**
     * Set various variables requested by template
     */
    protected function _prepareTemplateData()
    {
        $maxBalance = (int)Mage::helper('itanium_referral')->getGeneralConfig('max_points_balance');
        $minBalance = (int)Mage::helper('itanium_referral')->getGeneralConfig('min_points_balance');
        $balance = $this->_rewardInstance->getPointsBalance();
        $this->addData(array(
            'points_balance' => $balance,
            'currency_balance' => $this->_rewardInstance->getCurrencyAmount(),
            'pts_to_amount_rate_pts' => $this->_rewardInstance->getRateToCurrency()->getPoints(true),
            'pts_to_amount_rate_amount' => $this->_rewardInstance->getRateToCurrency()->getCurrencyAmount(),
            'amount_to_pts_rate_amount' => $this->_rewardInstance->getRateToPoints()->getCurrencyAmount(),
            'amount_to_pts_rate_pts' => $this->_rewardInstance->getRateToPoints()->getPoints(true),
            'max_balance' => $maxBalance,
            'is_max_balance_reached' => $balance >= $maxBalance,
            'min_balance' => $minBalance,
            'is_min_balance_reached' => $balance >= $minBalance,
            'expire_in' => (int)Mage::helper('itanium_referral')->getGeneralConfig('expiration_days'),
            'is_history_published' => (int)Mage::helper('itanium_referral')->getGeneralConfig('publish_history'),
        ));
    }
}
