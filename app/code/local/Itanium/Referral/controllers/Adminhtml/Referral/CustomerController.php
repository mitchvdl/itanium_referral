<?php
/**
 * Referral admin overview controller
 *
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Adminhtml_Referral_CustomerController extends Mage_Adminhtml_Controller_Action
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
            ->_addBreadcrumb(Mage::helper('itanium_referral')->__('Overview Customer Referrals'),
                Mage::helper('itanium_referral')->__('Overview Customer Referrals'));
        return $this;
    }

    /**
     * Initialize rate object
     *
     * @return Itanium_Referral_Model_Reward_Rate
     */
    protected function _initCustomerReferral()
    {
        $this->_title($this->__('Itanium'))->_title($this->__('Overview Customer Referrals'));

        $rateId = $this->getRequest()->getParam('entity_id', 0);
        $rate = Mage::getModel('itanium_referral/referral_customer');
        if ($rateId) {
            $rate->load($rateId);
        } else {
            $customerUUID = Mage::helper('itanium_referral/tools')->v4();
            $customerReferralUUID = Mage::helper('itanium_referral/tools')->v5($customerUUID, $customerUUID);
            $rate->addData([
                'customer_uuid' => $customerUUID,
                'referral_customer_uuid' => $customerReferralUUID,
            ]);
        }
        Mage::register('current_referral_customer', $rate);
        return $rate;
    }

    /**
     * Index Action
     */
    public function indexAction()
    {
        $this->_title($this->__('Itanium'))->_title($this->__('Overview Customer Referrals'));

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
        $rate = $this->_initCustomerReferral();

        $this->_title($rate->getEntityId() ? sprintf("#%s", $rate->getRateId()) : $this->__('Customer Referral'));

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Save Action
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('referral_customer');

        if ($data) {
            $cmrReferral = $this->_initCustomerReferral();

            if ($this->getRequest()->getParam('entity_id') && ! $cmrReferral->getId()) {
                return $this->_redirect('*/*/');
            }

            $cmrReferral->addData($data);
            $cmrReferral->setData('customer_id', Mage::getModel('customer/customer')->setWebsiteId($data['website_id'])->loadByEmail($data['customer_email'])->getId());
            $cmrReferral->setData('referral_customer_id', Mage::getModel('customer/customer')->setWebsiteId($data['website_id'])->loadByEmail($data['referral_customer_email'])->getId());

            try {
                $cmrReferral->save();
                $this->_getSession()->addSuccess(Mage::helper('itanium_referral')->__('The Referral Customer entry has been saved.'));
            } catch (Exception $e) {
                Mage::logException($e);
                if ( stristr($e->getMessage(),'SQLSTATE[23000]: Integrity constraint violation:') !== false ) {
                    $this->_getSession()->addError($this->__('This combination of customer and referral customer already exist.'));
//                    Mage::register('current_referral_customer', $cmrReferral);
                }
                $this->_getSession()->addError($this->__('Cannot save Referral Customer entry.'));
                return $this->_redirect('*/*/edit', array('entity_id' => $cmrReferral->getId(), '_current' => true));
            }
        }

        return $this->_redirect('*/*/');
    }

    /**
     * Delete Action
     */
    public function deleteAction()
    {
        $cmrReferral = $this->_initCustomerReferral();
        if ($cmrReferral->getId()) {
            try {
                $cmrReferral->delete();
                $this->_getSession()->addSuccess(Mage::helper('itanium_referral')->__('The Referral Customer has been deleted.'));
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
        $post     = $this->getRequest()->getParam('referral_customer');
        $message  = null;

        if (!isset($post['website_id'])
            || !isset($post['store_id'])
            || !isset($post['customer_email'])
            || !isset($post['customer_uuid'])
            || !isset($post['referral_customer_email'])
            || !isset($post['referral_customer_uuid'])
            ) {
            $message = $this->__('Please enter all information.');
        } elseif ( !Mage::getModel('customer/customer')->setWebsiteId($post['website_id'])->loadByEmail($post['customer_email'])->getId() ) {
            $message = $this->__('Customer email does not exist - Make sure that the users are listed in the same Website scope.');
        } elseif ( !Mage::getModel('customer/customer')->setWebsiteId($post['website_id'])->loadByEmail($post['referral_customer_email'])->getId() ) {
            $message = $this->__('Customer Referral email does not exist - Make sure that the users are listed in the same Website scope.');
        } else {
            $rate = $this->_initCustomerReferral();
//            $isRateUnique = $rate->getIsRateUniqueToCurrent(
//                $post['website_id'],
//                $post['customer_group_id'],
//                $post['direction']
//            );
//
//            if (!$isRateUnique) {
//                $message = $this->__('Referral Rate with the same website, customer group and direction or covering rate already exists.');
//            }
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
