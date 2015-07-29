<?php

/**
 * Referral model
 * 
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Model_Reward extends Mage_Core_Model_Abstract
{
    const XML_PATH_BALANCE_UPDATE_TEMPLATE = 'itanium_referral/notification/balance_update_template';
    const XML_PATH_BALANCE_WARNING_TEMPLATE = 'itanium_referral/notification/expiry_warning_template';
    const XML_PATH_EMAIL_IDENTITY = 'itanium_referral/notification/email_sender';
    const XML_PATH_MIN_POINTS_BALANCE = 'itanium_referral/general/min_points_balance';


    const REFERRAL_ACTION_ADMIN               = 0;
    const REFERRAL_ACTION_ORDER_EXTRA         = 8;


    protected $_modelLoadedByCustomer = false;

    static protected $_actionModelClasses = array();

    protected $_rates = array();

    /**
     * Identifies that reward balance was updated or not
     *
     * @var boolean
     */
    protected $_rewardPointsUpdated = false;

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('itanium_referral/reward');
        self::$_actionModelClasses = self::$_actionModelClasses + [
                self::REFERRAL_ACTION_ADMIN               => 'itanium_referral/action_admin',
            ];
    }

    /**
     * Set action Id and action model class.
     * Check if given action Id is not integer throw exception
     *
     * @param integer $actionId
     * @param string $actionModelClass
     */
    public static function setActionModelClass($actionId, $actionModelClass)
    {
        if (!is_int($actionId)) {
            Mage::throwException(Mage::helper('itanium_referral')->__('Given action ID has to be an integer value.'));
        }
        self::$_actionModelClasses[$actionId] = $actionModelClass;
    }

    /**
     * Processing object before save data.
     * Load model by customer and website,
     * prepare points data
     *
     * @return Itanium_Referral_Model_Reward
     */
    protected function _beforeSave()
    {
        $this->loadByCustomer()
            ->_preparePointsDelta()
            ->_preparePointsBalance();
        return parent::_beforeSave();
    }

    /**
     * Processing object after save data.
     * Save reward history
     *
     * @return Itanium_Referral_Model_Reward
     */
    protected function _afterSave()
    {
        if ((int)$this->getPointsDelta() != 0 || $this->getCappedReward()) {
            $this->_prepareCurrencyAmount();
            $this->getHistory()
                ->prepareFromReward()
                ->save();
            $this->sendBalanceUpdateNotification();
        }
        return parent::_afterSave();
    }

    /**
     * Return instance of action wrapper
     *
     * @param string|int $action Action code or a factory name
     * @return Itanium_Referral_Model_Action_Abstract|null
     */
    public function getActionInstance($action, $isFactoryName = false)
    {
        if ($isFactoryName) {
            $action = array_search($action, self::$_actionModelClasses);
            if (!$action) {
                return null;
            }
        }
        $instance = Mage::registry('_reward_actions' . $action);
        if (!$instance && array_key_exists($action, self::$_actionModelClasses)) {
            $instance = Mage::getModel(self::$_actionModelClasses[$action]);
            // setup invariant properties once
            $instance->setAction($action);
            $instance->setReward($this);
            Mage::register('_reward_actions' . $action, $instance);
        }
        if (!$instance) {
            return null;
        }
        // keep variable properties up-to-date
        $instance->setHistory($this->getHistory());
        if ($this->getActionEntity()) {
            $instance->setEntity($this->getActionEntity());
        }
        return $instance;
    }

    /**
     * Check if can update reward
     *
     * @return boolean
     */
    public function canUpdateRewardPoints()
    {
        return $this->getActionInstance($this->getAction())->canAddRewardPoints();
    }

    /**
     * Getter
     *
     * @return boolean
     */
    public function getRewardPointsUpdated()
    {
        return $this->_rewardPointsUpdated;
    }

    /**
     * Save reward points
     *
     * @return Itanium_Referral_Model_Reward
     */
    public function updateRewardPoints()
    {
        $this->_rewardPointsUpdated = false;
        if ($this->canUpdateRewardPoints()) {
            try {
                $this->save();
                $this->_rewardPointsUpdated = true;
            } catch (Exception $e) {
                $this->_rewardPointsUpdated = false;
                throw $e;
            }
        }
        return $this;
    }

    /**
     * Setter.
     * Set customer id
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Itanium_Referral_Model_Reward
     */
    public function setCustomer($customer)
    {
        $this->setData('customer_id', $customer->getId());
        $this->setData('customer_group_id', $customer->getGroupId());
        $this->setData('customer', $customer);
        return $this;
    }

    /**
     * Getter
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (!$this->_getData('customer') && $this->getCustomerId()) {
            $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
            $this->setCustomer($customer);
        }
        return $this->_getData('customer');
    }

    /**
     * Getter
     *
     * @return integer
     */
    public function getCustomerGroupId()
    {
        if (!$this->_getData('customer_group_id') && $this->getCustomer()) {
            $this->setData('customer_group_id', $this->getCustomer()->getGroupId());
        }
        return $this->_getData('customer_group_id');
    }

    /**
     * Getter for website_id
     * If website id not set, get it from assigned store
     *
     * @return int
     */
    public function getWebsiteId()
    {
        if (!$this->_getData('website_id') && ($store = $this->getStore())) {
            $this->setData('website_id', $store->getWebsiteId());
        }
        return $this->_getData('website_id');
    }

    /**
     * Getter for store (for emails etc)
     * Trying get store from customer if its not assigned
     *
     * @return Mage_Core_Model_Store|null
     */
    public function getStore()
    {
        $store = null;
        if ($this->hasData('store') || $this->hasData('store_id')) {
            $store = $this->getDataSetDefault('store', $this->_getData('store_id'));
        } elseif ($this->getCustomer() && $this->getCustomer()->getStoreId()) {
            $store = $this->getCustomer()->getStore();
            $this->setData('store', $store);
        }
        if ($store !== null) {
            return is_object($store) ? $store : Mage::app()->getStore($store);
        }
        return $store;
    }

    /**
     * Getter
     *
     * @return integer
     */
    public function getPointsDelta()
    {
        if ($this->_getData('points_delta') === null) {
            $this->_preparePointsDelta();
        }
        return $this->_getData('points_delta');
    }

    /**
     * Getter.
     * Recalculate currency amount if need.
     *
     * @return float
     */
    public function getCurrencyAmount()
    {
        if ($this->_getData('currency_amount') === null) {
            $this->_prepareCurrencyAmount();
        }
        return $this->_getData('currency_amount');
    }

    /**
     * Getter.
     * Return formated currency amount in currency of website
     *
     * @return string
     */
    public function getFormatedCurrencyAmount()
    {
        $currencyAmount = Mage::app()->getLocale()->currency($this->getWebsiteCurrencyCode())
                ->toCurrency($this->getCurrencyAmount());
        return $currencyAmount;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getWebsiteCurrencyCode()
    {
        if (!$this->_getData('website_currency_code')) {
            $this->setData('website_currency_code', Mage::app()->getWebsite($this->getWebsiteId())
                ->getBaseCurrencyCode());
        }
        return $this->_getData('website_currency_code');
    }

    /**
     * Getter
     *
     * @return Itanium_Referral_Model_Reward_History
     */
    public function getHistory()
    {
        if (!$this->_getData('history')) {
            $this->setData('history', Mage::getModel('itanium_referral/reward_history'));
            $this->getHistory()->setReward($this);
        }
        return $this->_getData('history');
    }

    /**
     * Initialize and fetch if need rate by given direction
     *
     * @param integer $direction
     * @return Itanium_Referral_Model_Reward_Rate
     */
    protected function _getRateByDirection($direction)
    {
        if (!isset($this->_rates[$direction])) {
            $this->_rates[$direction] = Mage::getModel('itanium_referral/reward_rate')
                ->fetch($this->getCustomerGroupId(), $this->getWebsiteId(), $direction);
        }
        return $this->_rates[$direction];
    }

    /**
     * Return rate depend on action
     *
     * @return Itanium_Referral_Model_Reward_Rate
     */
    public function getRate()
    {
        return $this->_getRateByDirection($this->getRateDirectionByAction());
    }

    /**
     * Return rate to convert points to currency amount
     *
     * @return Itanium_Referral_Model_Reward_Rate
     */
    public function getRateToCurrency()
    {
        return $this->_getRateByDirection(Itanium_Referral_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY);
    }

    /**
     * Return rate to convert currency amount to points
     *
     * @return Itanium_Referral_Model_Reward_Rate
     */
    public function getRateToPoints()
    {
        return $this->_getRateByDirection(Itanium_Referral_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS);
    }

    /**
     * Return rate direction by action
     *
     * @return integer
     */
    public function getRateDirectionByAction()
    {
        switch($this->getAction()) {
            case self::REFERRAL_ACTION_ORDER_EXTRA:
                $direction = Itanium_Referral_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS;
                break;
            default:
                $direction = Itanium_Referral_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY;
                break;
        }
        return $direction;
    }

    /**
     * Load by customer and website
     *
     * @return Itanium_Referral_Model_Reward
     */
    public function loadByCustomer()
    {
        if (!$this->_modelLoadedByCustomer && $this->getCustomerId() && $this->getWebsiteId()) {
            $this->getResource()->loadByCustomerId($this,
                $this->getCustomerId(), $this->getWebsiteId());
            $this->_modelLoadedByCustomer = true;
        }
        return $this;
    }

    /**
     * Estimate available points reward for specified action
     *
     * @param Itanium_Referral_Model_Action_Abstract $action
     * @return int|null
     */
    public function estimateRewardPoints(Itanium_Referral_Model_Action_Abstract $action)
    {
        $websiteId = $this->getWebsiteId();
        $uncappedPts = (int)$action->getPoints($websiteId);
        $max = (int)Mage::helper('itanium_referral')->getGeneralConfig('max_points_balance', $websiteId);
        if ($max > 0) {
            return min(max($max - (int)$this->getPointsBalance(), 0), $uncappedPts);
        }
        return $uncappedPts;
    }

    /**
     * Estimate available monetary reward for specified action
     * May take points value or automatically determine from action
     *
     * @param Itanium_Referral_Model_Action_Abstract $action
     * @return float|null
     */
    public function estimateRewardAmount(Itanium_Referral_Model_Action_Abstract $action)
    {
        if (!$this->getCustomerId()) {
            return null;
        }
        $websiteId = $this->getWebsiteId();
        $rate = $this->getRateToCurrency();
        if (!$rate->getId()) {
            return null;
        }
        return $rate->calculateToCurrency($this->estimateRewardPoints($action), false);
    }

    /**
     * Prepare points delta, get points delta from config by action
     *
     * @return Itanium_Referral_Model_Reward
     */
    protected function _preparePointsDelta()
    {
        $delta = 0;
        $action = $this->getActionInstance($this->getAction());
        if ($action !== null) {
            $delta = $action->getPoints($this->getWebsiteId());
        }
        if ($delta) {
            if ($this->hasPointsDelta()) {
                $delta = $delta + $this->getPointsDelta();
            }
            $this->setPointsDelta((int)$delta);
        }
        return $this;
    }

    /**
     * Prepare points balance
     *
     * @return Itanium_Referral_Model_Reward
     */
    protected function _preparePointsBalance()
    {
        $points = 0;
        if ($this->hasPointsDelta()) {
            $points = $this->getPointsDelta();
        }
        $pointsBalance = (int)$this->getPointsBalance() + $points;
        $maxPointsBalance = (int)(Mage::helper('itanium_referral')
            ->getGeneralConfig('max_points_balance', $this->getWebsiteId()));
        if ($maxPointsBalance != 0 && ($pointsBalance > $maxPointsBalance)) {
            $pointsBalance = $maxPointsBalance;
            $pointsDelta   = $maxPointsBalance - (int)$this->getPointsBalance();
            $croppedPoints = (int)$this->getPointsDelta() - $pointsDelta;
            $this->setPointsDelta($pointsDelta)
                ->setIsCappedReward(true)
                ->setCroppedPoints($croppedPoints);
        }
        $this->setPointsBalance($pointsBalance);
        return $this;
    }

    /**
     * Prepare currency amount and currency delta
     *
     * @return Itanium_Referral_Model_Reward
     */
    protected function _prepareCurrencyAmount()
    {
        $amount = 0;
        $amountDelta = 0;
        if ($this->hasPointsDelta()) {
            $amountDelta = $this->_convertPointsToCurrency($this->getPointsDelta());
        }
        $amount = $this->_convertPointsToCurrency($this->getPointsBalance());
        $this->setCurrencyDelta((float)$amountDelta);
        $this->setCurrencyAmount((float)($amount));
        return $this;
    }

    /**
     * Convert points to currency
     *
     * @param integer $points
     * @return float
     */
    protected function _convertPointsToCurrency($points)
    {
        return $points && $this->getRateToCurrency()
            ? (float)$this->getRateToCurrency()->calculateToCurrency($points)
            : 0;
    }

    /**
     * Check is enough points (currency amount) to cover given amount
     *
     * @param float $amount
     * @return bool
     */
    public function isEnoughPointsToCoverAmount($amount)
    {
        return $this->getId() && $this->getCurrencyAmount() >= $amount;
    }

    /**
     * Return points equivalent of given amount.
     * Converting by 'to currency' rate and points round up
     *
     * @param float $amount
     * @return integer
     */
    public function getPointsEquivalent($amount)
    {
        $points = 0;
        if (!$amount) {
            return $points;
        }

        $ratePointsCount = $this->getRateToCurrency()->getPoints();
        $rateCurrencyAmount = $this->getRateToCurrency()->getCurrencyAmount();
        if ($rateCurrencyAmount > 0) {
            $delta = $amount / $rateCurrencyAmount;
            if ($delta > 0) {
                $points = $ratePointsCount * ceil($delta);
            }
        }

        return $points;
    }

    /**
     * Send Balance Update Notification to customer if notification is enabled
     *
     * @return Itanium_Referral_Model_Reward
     */
    public function sendBalanceUpdateNotification()
    {
        if (!$this->getCustomer()->getRewardUpdateNotification()) {
            return $this;
        }
        $delta = (int)$this->getPointsDelta();
        if ($delta == 0) {
            return $this;
        }
        $history = $this->getHistory();
        $store = Mage::app()->getStore($this->getStore());
        $mail  = Mage::getModel('core/email_template');
        /* @var $mail Mage_Core_Model_Email_Template */
        $mail->setDesignConfig(array('area' => 'frontend', 'store' => $store->getId()));
        $templateVars = array(
            'store' => $store,
            'customer' => $this->getCustomer(),
            'unsubscription_url' => Mage::helper('itanium_referral/customer')
                ->getUnsubscribeUrl('update', $store->getId()),
            'points_balance' => $this->getPointsBalance(),
            'reward_amount_was' => Mage::helper('itanium_referral')->formatAmount(
                $this->getCurrencyAmount() - $history->getCurrencyDelta()
                , true, $store->getStoreId()),
            'reward_amount_now' => Mage::helper('itanium_referral')->formatAmount(
                $this->getCurrencyAmount()
                , true, $store->getStoreId()),
            'reward_pts_was' => ($this->getPointsBalance() - $delta),
            'reward_pts_change' => $delta,
            'update_message' => $this->getHistory()->getMessage(),
            'update_comment' => $history->getComment()
        );
        $mail->sendTransactional(
            $store->getConfig(self::XML_PATH_BALANCE_UPDATE_TEMPLATE),
            $store->getConfig(self::XML_PATH_EMAIL_IDENTITY),
            $this->getCustomer()->getEmail(),
            null,
            $templateVars,
            $store->getId()
        );
        if ($mail->getSentSuccess()) {
            $this->setBalanceUpdateSent(true);
        }
        return $this;
    }

    /**
     * Send low Balance Warning Notification to customer if notification is enabled
     *
     * @param Itanium_Referral_Model_Reward_History $history
     * @return Itanium_Referral_Model_Reward
     * @see Itanium_Referral_Model_Mysql4_Reward_History_Collection::loadExpiredSoonPoints()
     */
    public function sendBalanceWarningNotification($item, $websiteId)
    {
        $mail  = Mage::getModel('core/email_template');
        /* @var $mail Mage_Core_Model_Email_Template */
        $mail->setDesignConfig(array('area' => 'frontend', 'store' => $item->getStoreId()));
        $store = Mage::app()->getStore($item->getStoreId());
        $amount = Mage::helper('itanium_referral')
            ->getRateFromRatesArray($item->getPointsBalanceTotal(),$websiteId, $item->getCustomerGroupId());
        $action = Mage::getSingleton('itanium_referral/reward')->getActionInstance($item->getAction());
        $templateVars = array(
            'store' => $store,
            'customer_name' => $item->getCustomerFirstname().' '.$item->getCustomerLastname(),
            'unsubscription_url' => Mage::helper('itanium_referral/customer')->getUnsubscribeUrl('warning'),
            'remaining_days' => $store->getConfig('itanium_referral/notification/expiry_day_before'),
            'points_balance' => $item->getPointsBalanceTotal(),
            'points_expiring' => $item->getTotalExpired(),
            'reward_amount_now' => Mage::helper('itanium_referral')->formatAmount($amount, true, $item->getStoreId()),
            'update_message' => ($action !== null ? $action->getHistoryMessage($item->getAdditionalData()) : '')
        );
        $mail->sendTransactional(
            $store->getConfig(self::XML_PATH_BALANCE_WARNING_TEMPLATE),
            $store->getConfig(self::XML_PATH_EMAIL_IDENTITY),
            $item->getCustomerEmail(),
            null,
            $templateVars,
            $store->getId()
        );
        return $this;
    }

    /**
     * Prepare orphan points by given website id and website base currency code
     * after website was deleted
     *
     * @param integer $websiteId
     * @param string $baseCurrencyCode
     * @return Itanium_Referral_Model_Reward
     */
    public function prepareOrphanPoints($websiteId, $baseCurrencyCode)
    {
        if ($websiteId) {
            $this->_getResource()->prepareOrphanPoints($websiteId, $baseCurrencyCode);
        }
        return $this;
    }

    /**
     * Delete orphan (points of deleted website) points by given customer
     *
     * @param Mage_Customer_Model_Customer | integer | null $customer
     * @return Itanium_Referral_Model_Reward
     */
    public function deleteOrphanPointsByCustomer($customer = null)
    {
        if ($customer === null) {
            $customer = $this->getCustomerId()?$this->getCustomerId():$this->getCustomer();
        }
        if (is_object($customer) && $customer instanceof Mage_Customer_Model_Customer) {
            $customer = $customer->getId();
        }
        if ($customer) {
            $this->_getResource()->deleteOrphanPointsByCustomer($customer);
        }
        return $this;
    }

    /**
     *  Override setter for setting customer group id  from order
     *
     *  @param mixed $entity
     *  @return Itanium_Referral_Model_Reward
     */
    public function setActionEntity($entity)
    {
        if ($entity->getCustomerGroupId()) {
            $this->setCustomerGroupId($entity->getCustomerGroupId());
        }
        return parent::setData('action_entity', $entity);
    }
}
