<?php
/**
 * File: Data.php
 *
 * User: Mitch Vanderlinden
 * email: mitchvdl@gmail.com
 * Date: 19.06.15 14:15
 * Package: Itanium_Referral
 */

/**
 * Class Itanium_Referral_Helper_Data
 */
class Itanium_Referral_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_SECTION_GENERAL = 'itanium_referral/general/';
    const XML_PATH_SECTION_POINTS = 'itanium_referral/points/';
    const XML_PATH_SECTION_NOTIFICATIONS = 'itanium_referral/notification/';

    const XML_PATH_ENABLED = 'itanium_referral/general/is_enabled';
    const XML_PATH_PERMISSION_BALANCE = 'customer/manage/reward_balance';

    protected $_expiryConfig;
    protected $_hasRates = true;
    protected $_ratesArray = null;
    /**
     * Check whether referral module is enabled in system config
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED);
    }

    /**
     * Check whether referral module is enabled in system config on front per website
     *
     * @param integer $websiteId
     * @return boolean
     */
    public function isEnabledOnFront($websiteId = null)
    {
        if ($websiteId === null) {
            $websiteId = Mage::app()->getStore()->getWebsiteId();
        }
        return ($this->isEnabled() && $this->getGeneralConfig('is_enabled_on_front', (int)$websiteId));
    }
    /**
     * Retrieve value of given field and website from config
     *
     * @param string $section
     * @param string $field
     * @param integer $websiteId
     * @return mixed
     */
    public function getConfigValue($section, $field, $websiteId = null)
    {
        if ($websiteId === null) {
            $websiteId = Mage::app()->getWebsite()->getId();
        }
        return (string)Mage::app()->getConfig()->getNode($section . $field, 'website', (int)$websiteId);
    }

    /**
     * Retrieve config value from General section
     *
     * @param string $field
     * @param integer $websiteId
     * @return mixed
     */
    public function getGeneralConfig($field, $websiteId = null)
    {
        return $this->getConfigValue(self::XML_PATH_SECTION_GENERAL, $field, $websiteId);
    }

    /**
     * Retrieve config value from Points section
     *
     * @param string $field
     * @param integer $websiteId
     * @return mixed
     */
    public function getPointsConfig($field, $websiteId = null)
    {
        return $this->getConfigValue(self::XML_PATH_SECTION_POINTS, $field, $websiteId);
    }

    /**
     * Retrieve config value from Notification section
     *
     * @param string $field
     * @param integer $websiteId
     * @return mixed
     */
    public function getNotificationConfig($field, $websiteId = null)
    {
        return $this->getConfigValue(self::XML_PATH_SECTION_NOTIFICATIONS, $field, $websiteId);
    }


    /**
     * Getter for hasRates flag
     *
     * @return boolean
     */
    public function getHasRates()
    {
        return $this->_hasRates;
    }

    /**
     * Return acc array of websites expiration points config
     *
     * @return array
     */
    public function getExpiryConfig()
    {
        if ($this->_expiryConfig === null) {
            $result = array();
            foreach (Mage::app()->getWebsites() as $website) {
                $websiteId = $website->getId();
                $result[$websiteId] = new Varien_Object(array(
                    'expiration_days' => $this->getGeneralConfig('expiration_days', $websiteId),
                    'expiry_calculation' => $this->getGeneralConfig('expiry_calculation', $websiteId),
                    'expiry_day_before' => $this->getNotificationConfig('expiry_day_before', $websiteId)
                ));
            }
            $this->_expiryConfig = $result;
        }

        return $this->_expiryConfig;
    }

    /**
     * Format (add + or - sign) before given points count
     *
     * @param integer $points
     * @return string
     */
    public function formatPointsDelta($points)
    {
        $formatedPoints = $points;
        if ($points > 0) {
            $formatedPoints = '+'.$points;
        } elseif ($points < 0) {
            $formatedPoints = '-'.(-1*$points);
        }
        return $formatedPoints;
    }

    /**
     * Render a reward message as X points Y money
     *
     * @param int $points
     * @param float|null $amount
     * @param int|null $storeId
     * @param string $pointsFormat
     * @param string $amountFormat
     */
    public function formatReward($points, $amount = null, $storeId = null, $pointsFormat = '%s', $amountFormat = '%s')
    {
        $points = sprintf($pointsFormat, $points);
        if ((null !== $amount) && $this->getHasRates()) {
            $amount = sprintf($amountFormat, $this->formatAmount($amount, true, $storeId));
            return $this->__('%s Referral points (%s)', $points, $amount);
        }
        return $this->__('%s Referral points', $points);
    }

    /**
     * Format an amount as currency or rounded value
     *
     * @param float|string|null $amount
     * @param bool $asCurrency
     * @param int|null $storeId
     * @return string|null
     */
    public function formatAmount($amount, $asCurrency = true, $storeId = null)
    {
        if (null === $amount) {
            return  null;
        }
        return $asCurrency ?
            Mage::app()->getStore($storeId)->convertPrice($amount, true, false) :
            sprintf('%.2F', $amount);
    }

    /**
     * Format points to currency rate
     *
     * @param int $points
     * @param float $amount
     * @param string $currencyCode
     * @return string
     */
    public function formatRateToCurrency($points, $amount, $currencyCode = null)
    {
        return $this->_formatRate('%1$s points = %2$s', $points, $amount, $currencyCode);
    }

    /**
     * Format currency to points rate
     *
     * @param int $points
     * @param float $amount
     * @param string $currencyCode
     * @return string
     */
    public function formatRateToPoints($points, $amount, $currencyCode = null)
    {
        return $this->_formatRate('%2$s = %1$s points', $points, $amount, $currencyCode);
    }

    /**
     * Format rate according to format
     *
     * @param string $format
     * @param int $points
     * @param float $amount
     * @param string $currencyCode
     * @return string
     */
    protected function _formatRate($format, $points, $amount, $currencyCode)
    {
        $points = (int)$points;
        if (!$currencyCode) {
            $amountFormatted = sprintf('%.2F', $amount);
        } else {
            $amountFormatted = Mage::app()->getLocale()->currency($currencyCode)->toCurrency((float)$amount);
        }
        return sprintf($format, $points, $amountFormatted);
    }

    /**
     * Loading history collection data
     * and Setting up rate to currency array
     *
     * @return array
     */
    protected function _loadRatesArray()
    {
        $ratesArray = array();
        $collection = Mage::getModel('itanium_referral/reward_rate')->getCollection()
            ->addFieldToFilter('direction', Itanium_Referral_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY);
        foreach ($collection as $rate) {
            $ratesArray[$rate->getCustomerGroupId()][$rate->getWebsiteId()] = $rate;
        }
        return $ratesArray;
    }

    /**
     * Fetch rate for given website_id and group_id from index_array
     * @param int $points
     * @param int $websiteId
     * @param int $customerGroupId
     * @return string|null
     */
    public function getRateFromRatesArray($points, $websiteId, $customerGroupId)
    {
        if (!$this->_ratesArray) {
            $this->_ratesArray = $this->_loadRatesArray();
        }
        $rate = null;
        if (isset($this->_ratesArray[$customerGroupId])) {
            if (isset($this->_ratesArray[$customerGroupId][$websiteId])) {
                $rate = $this->_ratesArray[$customerGroupId][$websiteId];
            } else if (isset($this->_ratesArray[$customerGroupId][0])){
                $rate = $this->_ratesArray[$customerGroupId][0];
            }
        } else if (isset($this->_ratesArray[0])) {
            if (isset($this->_ratesArray[0][$websiteId])) {
                $rate = $this->_ratesArray[0][$websiteId];
            } else if (isset($this->_ratesArray[0][0])) {
                $rate = $this->_ratesArray[0][0];
            }
        }
        if ($rate !== null) {
            return $rate->calculateToCurrency($points);
        }
        return null;
    }


}
