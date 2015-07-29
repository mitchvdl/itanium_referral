<?php

/**
 * Referral update points form
 *
 * @category    Itanium
 * @package     Itanium_Referral

 */
class Itanium_Referral_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Update
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Getter
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::registry('current_customer');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Itanium_Referral_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Update
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('referral_');
        $form->setFieldNameSuffix('referral');
        $fieldset = $form->addFieldset('update_fieldset', array(
            'legend' => Mage::helper('itanium_referral')->__('Update Referral Points Balance')
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store', 'select', array(
                'name'  => 'store_id',
                'title' => Mage::helper('itanium_referral')->__('Store'),
                'label' => Mage::helper('itanium_referral')->__('Store'),
                'values' => $this->_getStoreValues()
            ));
        }

        $fieldset->addField('points_delta', 'text', array(
            'name'  => 'points_delta',
            'title' => Mage::helper('itanium_referral')->__('Update Points'),
            'label' => Mage::helper('itanium_referral')->__('Update Points'),
            'note'  => Mage::helper('itanium_referral')->__('Enter a negative number to subtract from balance.')
        ));

        $fieldset->addField('comment', 'text', array(
            'name'  => 'comment',
            'title' => Mage::helper('itanium_referral')->__('Comment'),
            'label' => Mage::helper('itanium_referral')->__('Comment')
        ));

        $fieldset = $form->addFieldset('notification_fieldset', array(
            'legend' => Mage::helper('itanium_referral')->__('Referral Points Notifications')
        ));

        $fieldset->addField('update_notification', 'checkbox', array(
            'name'    => 'reward_update_notification',
            'label'   => Mage::helper('itanium_referral')->__('Subscribe for balance updates'),
            'checked' => (bool)$this->getCustomer()->getRewardUpdateNotification(),
            'value'   => 1
        ));

        $fieldset->addField('warning_notification', 'checkbox', array(
            'name'    => 'reward_warning_notification',
            'label'   => Mage::helper('itanium_referral')->__('Subscribe for points expiration notifications'),
            'checked' => (bool)$this->getCustomer()->getRewardWarningNotification(),
            'value' => 1
        ));

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Retrieve source values for store drop-dawn
     *
     * @return array
     */
    protected function _getStoreValues()
    {
        $customer = $this->getCustomer();
        if (!$customer->getWebsiteId()
            || Mage::app()->isSingleStoreMode()
            || $customer->getSharingConfig()->isGlobalScope())
        {
            return Mage::getModel('adminhtml/system_store')->getStoreValuesForForm();
        }

        $stores = Mage::getModel('adminhtml/system_store')
            ->getStoresStructure(false, array(), array(), array($customer->getWebsiteId()));
        $values = array();

        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
        foreach ($stores as $websiteId => $website) {
            $values[] = array(
                'label' => $website['label'],
                'value' => array()
            );
            if (isset($website['children']) && is_array($website['children'])) {
                foreach ($website['children'] as $groupId => $group) {
                    if (isset($group['children']) && is_array($group['children'])) {
                        $options = array();
                        foreach ($group['children'] as $storeId => $store) {
                            $options[] = array(
                                'label' => str_repeat($nonEscapableNbspChar, 4) . $store['label'],
                                'value' => $store['value']
                            );
                        }
                        $values[] = array(
                            'label' => str_repeat($nonEscapableNbspChar, 4) . $group['label'],
                            'value' => $options
                        );
                    }
                }
            }
        }
        return $values;
    }
}
