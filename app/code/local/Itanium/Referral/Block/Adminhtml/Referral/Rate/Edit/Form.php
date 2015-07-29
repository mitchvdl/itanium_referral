<?php

/**
 * Referral rate edit form
 *
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Block_Adminhtml_Referral_Rate_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Getter
     *
     * @return Itanium_Referral_Model_Reward_Rate
     */
    public function getRate()
    {
        return Mage::registry('current_referral_rate');
    }

    /**
     * Prepare form
     *
     * @return Itanium_Referral_Block_Adminhtml_Referral_Rate_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('_current' => true)),
            'method' => 'post'
        ));
        $form->setFieldNameSuffix('rate');
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('itanium_referral')->__('Reward Exchange Rate Information')
        ));

        $field = $fieldset->addField('website_id', 'select', array(
            'name'   => 'website_id',
            'title'  => Mage::helper('itanium_referral')->__('Website'),
            'label'  => Mage::helper('itanium_referral')->__('Website'),
            'values' => Mage::getModel('itanium_referral/source_website')->toOptionArray(),
        ));
        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
        $field->setRenderer($renderer);

        $fieldset->addField('customer_group_id', 'select', array(
            'name'   => 'customer_group_id',
            'title'  => Mage::helper('itanium_referral')->__('Customer Group'),
            'label'  => Mage::helper('itanium_referral')->__('Customer Group'),
            'values' => Mage::getModel('itanium_referral/source_customer_groups')->toOptionArray()
        ));

        $fieldset->addField('direction', 'select', array(
            'name'   => 'direction',
            'title'  => Mage::helper('itanium_referral')->__('Direction'),
            'label'  => Mage::helper('itanium_referral')->__('Direction'),
            'values' => $this->getRate()->getDirectionsOptionArray()
        ));

        $rateRenderer = $this->getLayout()
            ->createBlock('itanium_referral/adminhtml_referral_rate_edit_form_renderer_rate')
            ->setRate($this->getRate());
        $direction = $this->getRate()->getDirection();
        if ($direction == Itanium_Referral_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY) {
            $fromIndex = 'points';
            $toIndex = 'currency_amount';
        } else {
            $fromIndex = 'currency_amount';
            $toIndex = 'points';
        }
        $fieldset->addField('rate_to_currency', 'note', array(
            'title'             => Mage::helper('itanium_referral')->__('Rate'),
            'label'             => Mage::helper('itanium_referral')->__('Rate'),
            'value_index'       => $fromIndex,
            'equal_value_index' => $toIndex
        ))->setRenderer($rateRenderer);

        $form->setUseContainer(true);
        $form->setValues($this->getRate()->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
