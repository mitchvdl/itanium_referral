<?php
/**
 * Referral customer frontend controller
 *
 * @category    Itanium
 * @package     Itanium_Referral

 */
class Itanium_Referral_CustomerController extends Mage_Core_Controller_Front_Action
{
    /**
     * Predispatch
     * Check is customer authenticate
     * Check is RP enabled on frontend
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
        if (!Mage::helper('itanium_referral')->isEnabledOnFront()) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    /**
     * Info Action
     */
    public function infoAction()
    {
        Mage::register('current_referral', $this->_getReward());
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::helper('itanium_referral')->__('Referral Points'));
        }
        $this->renderLayout();
    }

    /**
     * Save settings
     */
    public function saveSettingsAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/info');
        }

        $customer = $this->_getCustomer();
        if ($customer->getId()) {
            $customer->setData('itanium_referral_update_not', $this->getRequest()->getParam('subscribe_updates'))
                ->setData('itanium_referral_warn_not', $this->getRequest()->getParam('subscribe_warnings'));

            $customer->getResource()->saveAttribute($customer, 'itanium_referral_update_not');
            $customer->getResource()->saveAttribute($customer, 'itanium_referral_warn_not');

            $this->_getSession()->addSuccess(
                $this->__('The settings have been saved.')
            );
        }
        $this->_redirect('*/*/info');
    }

    /**
     * Unsubscribe customer from update/warning balance notifications
     */
    public function unsubscribeAction()
    {
        $notification = $this->getRequest()->getParam('notification');
        if (!in_array($notification, array('update','warning'))) {
            $this->_forward('noroute');
        }

        try {
            /* @var $customer Mage_Customer_Model_Session */
            $customer = $this->_getCustomer();
            if ($customer->getId()) {
                if ($notification == 'update') {
                    $customer->setData('itanium_referral_update_not', false);
                    $customer->getResource()->saveAttribute($customer, 'itanium_referral_update_not');
                } elseif ($notification == 'warning') {
                    $customer->setData('itanium_referral_warn_not', false);
                    $customer->getResource()->saveAttribute($customer, 'itanium_referral_warn_not');
                }
                $this->_getSession()->addSuccess(
                    $this->__('You have been unsubscribed.')
                );
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Failed to unsubscribe.'));
        }

        $this->_redirect('*/*/info');
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomer()
    {
        return $this->_getSession()->getCustomer();
    }

    /**
     * Load referral by customer
     *
     * @return Itanium_Referral_Model_Reward
     */
    protected function _getReward()
    {
        $referral = Mage::getModel('itanium_referral/reward')
            ->setCustomer($this->_getCustomer())
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByCustomer();
        return $referral;
    }
}
