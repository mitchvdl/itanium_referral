<?php
/**
 * Referral admin rate controller
 *
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Adminhtml_Referral_RateController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check if module functionality enabled
     *
     * @return Itanium_Referral_Adminhtml_Referral_RateController
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::helper('itanium_referral')->isEnabled() && $this->getRequest()->getActionName() != 'noroute') {
            $this->_forward('noroute');
        }
        return $this;
    }

    /**
     * Initialize layout, breadcrumbs
     *
     * @return Itanium_Referral_Adminhtml_Referral_RateController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('itaniumextensions/itanium_menu')
            ->_addBreadcrumb(Mage::helper('itanium_referral')->__('Itanium'),
                Mage::helper('itanium_referral')->__('Itanium'))
            ->_addBreadcrumb(Mage::helper('itanium_referral')->__('Manage Referral Exchange Rates'),
                Mage::helper('itanium_referral')->__('Manage Referral Exchange Rates'));
        return $this;
    }

    /**
     * Initialize rate object
     *
     * @return Itanium_Referral_Model_Reward_Rate
     */
    protected function _initRate()
    {
        $this->_title($this->__('Itanium'))->_title($this->__('Referral Exchange Rates'));

        $rateId = $this->getRequest()->getParam('rate_id', 0);
        $rate = Mage::getModel('itanium_referral/reward_rate');
        if ($rateId) {
            $rate->load($rateId);
        }
        Mage::register('current_referral_rate', $rate);
        return $rate;
    }

    /**
     * Index Action
     */
    public function indexAction()
    {
        $this->_title($this->__('Itanium'))->_title($this->__('Referral Exchange Rates'));

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * New Action.
     * Forward to Edit Action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit Action
     */
    public function editAction()
    {
        $rate = $this->_initRate();

        $this->_title($rate->getRateId() ? sprintf("#%s", $rate->getRateId()) : $this->__('New Referral Rate'));

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Save Action
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('rate');

        if ($data) {
            $rate = $this->_initRate();

            if ($this->getRequest()->getParam('rate_id') && ! $rate->getId()) {
                return $this->_redirect('*/*/');
            }

            $rate->addData($data);

            try {
                $rate->save();
                $this->_getSession()->addSuccess(Mage::helper('itanium_referral')->__('The rate has been saved.'));
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($this->__('Cannot save Rate.'));
                return $this->_redirect('*/*/edit', array('rate_id' => $rate->getId(), '_current' => true));
            }
        }

        return $this->_redirect('*/*/');
    }

    /**
     * Delete Action
     */
    public function deleteAction()
    {
        $rate = $this->_initRate();
        if ($rate->getId()) {
            try {
                $rate->delete();
                $this->_getSession()->addSuccess(Mage::helper('itanium_referral')->__('The rate has been deleted.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            }
        }

        return $this->_redirect('*/*/');
    }

    /**
     * Validate Action
     *
     */
    public function validateAction()
    {
        $response = new Varien_Object(array('error' => false));
        $post     = $this->getRequest()->getParam('rate');
        $message  = null;

        if (!isset($post['customer_group_id'])
            || !isset($post['website_id'])
            || !isset($post['direction'])
            || !isset($post['value'])
            || !isset($post['equal_value'])) {
            $message = $this->__('Please enter all Rate information.');
        } elseif ($post['direction'] == Itanium_Referral_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY
                  && ((int) $post['value'] <= 0 || (float) $post['equal_value'] <= 0)) {
              if ((int) $post['value'] <= 0) {
                  $message = $this->__('Please enter a positive integer number in the left rate field.');
              } else {
                  $message = $this->__('Please enter a positive number in the right rate field.');
              }
        } elseif ($post['direction'] == Itanium_Referral_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS
                  && ((float) $post['value'] <= 0 || (int) $post['equal_value'] <= 0)) {
              if ((int) $post['equal_value'] <= 0) {
                  $message = $this->__('Please enter a positive integer number in the right rate field.');
              } else {
                  $message = $this->__('Please enter a positive number in the left rate field.');
              }
        } else {
            $rate       = $this->_initRate();
            $isRateUnique = $rate->getIsRateUniqueToCurrent(
                $post['website_id'],
                $post['customer_group_id'],
                $post['direction']
            );

            if (!$isRateUnique) {
                $message = $this->__('Referral Rate with the same website, customer group and direction or covering rate already exists.');
            }
        }

        if ($message) {
            $this->_getSession()->addError($message);
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Acl check for admin
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
//        return Mage::getSingleton('admin/session')->isAllowed('customer/rates');
        return true;
    }
}
