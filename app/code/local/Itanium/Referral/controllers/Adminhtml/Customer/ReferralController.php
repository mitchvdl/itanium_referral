<?php
/**
 * Referral admin customer controller
 *
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Adminhtml_Customer_ReferralController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check if module functionality enabled
     *
     * @return Itanium_Referral_Adminhtml_Reward_RateController
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
     * History Ajax Action
     */
    public function historyAction()
    {
        $customerId = $this->getRequest()->getParam('id', 0);
        $history = $this->getLayout()
            ->createBlock('itanium_referral/adminhtml_customer_edit_tab_reward_history', '',
                array('customer_id' => $customerId));
        $this->getResponse()->setBody($history->toHtml());
    }

    /**
     * History Grid Ajax Action
     *
     */
    public function historyGridAction()
    {
        $customerId = $this->getRequest()->getParam('id', 0);
        $history = $this->getLayout()
            ->createBlock('itanium_referral/adminhtml_customer_edit_tab_reward_history_grid', '',
                array('customer_id' => $customerId));
        $this->getResponse()->setBody($history->toHtml());
    }

    /**
     *  Delete orphan points Action
     */
    public function deleteOrphanPointsAction()
    {
        $customerId = $this->getRequest()->getParam('id', 0);
        if ($customerId) {
            try {
                Mage::getModel('itanium_referral/reward')
                    ->deleteOrphanPointsByCustomer($customerId);
                $this->_getSession()
                    ->addSuccess(Mage::helper('itanium_referral')->__('The orphan points have been removed.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/customer/edit', array('_current' => true));
    }

    /**
     * Acl check for admin
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
//        return Mage::getSingleton('admin/session')->isAllowed(Itanium_Referral_Helper_Data::XML_PATH_PERMISSION_BALANCE);
        return true;
    }
}
