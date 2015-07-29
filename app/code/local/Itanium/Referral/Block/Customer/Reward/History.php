<?php
/**
 * Customer account reward history block
 *
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Block_Customer_Reward_History extends Mage_Core_Block_Template
{
    /**
     * History records collection
     *
     * @var Itanium_Referral_Model_Mysql4_Reward_History_Collection
     */
    protected $_collection = null;

    /**
     * Get history collection if needed
     *
     * @return Itanium_Referral_Model_Mysql4_Reward_History_Collection|false
     */
    public function getHistory()
    {
        if (0 == $this->_getCollection()->getSize()) {
            return false;
        }
        return $this->_collection;
    }

    /**
     * History item points delta getter
     *
     * @param Itanium_Referral_Model_Reward_History $item
     * @return string
     */
    public function getPointsDelta(Itanium_Referral_Model_Reward_History $item)
    {
        return Mage::helper('itanium_referral')->formatPointsDelta($item->getPointsDelta());
    }

    /**
     * History item points balance getter
     *
     * @param Itanium_Referral_Model_Reward_History $item
     * @return string
     */
    public function getPointsBalance(Itanium_Referral_Model_Reward_History $item)
    {
        return $item->getPointsBalance();
    }

    /**
     * History item currency balance getter
     *
     * @param Itanium_Referral_Model_Reward_History $item
     * @return string
     */
    public function getCurrencyBalance(Itanium_Referral_Model_Reward_History $item)
    {
        return Mage::helper('core')->currency($item->getCurrencyAmount());
    }

    /**
     * History item reference message getter
     *
     * @param Itanium_Referral_Model_Reward_History $item
     * @return string
     */
    public function getMessage(Itanium_Referral_Model_Reward_History $item)
    {
        return $item->getMessage();
    }

    /**
     * History item reference additional explanation getter
     *
     * @param Itanium_Referral_Model_Reward_History $item
     * @return string
     */
    public function getExplanation(Itanium_Referral_Model_Reward_History $item)
    {
        return $item->getComment(); // TODO load from additional data
    }

    /**
     * History item creation date getter
     *
     * @param Itanium_Referral_Model_Reward_History $item
     * @return string
     */
    public function getDate(Itanium_Referral_Model_Reward_History $item)
    {
        return Mage::helper('core')->formatDate($item->getCreatedAt(), 'short', true);
    }

    /**
     * History item expiration date getter
     *
     * @param Itanium_Referral_Model_Reward_History $item
     * @return string
     */
    public function getExpirationDate(Itanium_Referral_Model_Reward_History $item)
    {
        $expiresAt = $item->getExpiresAt();
        if ($expiresAt) {
            return Mage::helper('core')->formatDate($expiresAt, 'short', true);
        }
        return '';
    }

    /**
     * Return reword points update history collection by customer and website
     *
     * @return Itanium_Referral_Model_Mysql4_Reward_History_Collection
     */
    protected function _getCollection()
    {
        if (!$this->_collection) {
            $websiteId = Mage::app()->getWebsite()->getId();
            $this->_collection = Mage::getModel('itanium_referral/reward_history')->getCollection()
                ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId())
                ->addWebsiteFilter($websiteId)
                ->setExpiryConfig(Mage::helper('itanium_referral')->getExpiryConfig())
                ->addExpirationDate($websiteId)
                ->skipExpiredDuplicates()
                ->setDefaultOrder()
            ;
        }
        return $this->_collection;
    }

    /**
     * Instantiate Pagination
     *
     * @return Itanium_Referral_Block_Customer_Reward_History
     */
    protected function _prepareLayout()
    {
        if ($this->_isEnabled()) {
            $pager = $this->getLayout()->createBlock('page/html_pager', 'referral.history.pager')
                ->setCollection($this->_getCollection())->setIsOutputRequired(false)
            ;
            $this->setChild('pager', $pager);
        }
        return parent::_prepareLayout();
    }

    /**
     * Whether the history may show up
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_isEnabled()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Whether the history is supposed to be rendered
     *
     * @return bool
     */
    protected function _isEnabled()
    {
        return Mage::helper('itanium_referral')->isEnabledOnFront()
            && Mage::helper('itanium_referral')->getGeneralConfig('publish_history');
    }
}
