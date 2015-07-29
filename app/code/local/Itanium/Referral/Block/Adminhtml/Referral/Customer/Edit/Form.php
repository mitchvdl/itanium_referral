<?php

/**
 * Referral customer edit form
 *
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Block_Adminhtml_Referral_Customer_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Getter
     *
     * @return Itanium_Referral_Model_Reward_Rate
     */
    public function getRate()
    {
        return Mage::registry('current_referral_customer');
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
        $form->setFieldNameSuffix('referral_customer');
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('itanium_referral')->__('Referral Customer Information')
        ));

        $field = $fieldset->addField('website_id', 'select', array(
            'name'   => 'website_id',
            'title'  => Mage::helper('itanium_referral')->__('Website'),
            'label'  => Mage::helper('itanium_referral')->__('Website'),
            'class'     => 'required-entry',
            'required'  => true,
            'values' => Mage::getModel('itanium_referral/source_website')->toOptionArray(),
            'tabindex' => 10,
        ));
        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
        $field->setRenderer($renderer);

        $field = $fieldset->addField('store_id', 'select', array(
            'name'   => 'store_id',
            'title'  => Mage::helper('itanium_referral')->__('Store'),
            'label'  => Mage::helper('itanium_referral')->__('Store'),
            'class'     => 'required-entry',
            'required'  => true,
            'values' => Mage::getModel('itanium_referral/source_store')->toOptionArray(),
            'tabindex' => 20,
        ));

        $field = $fieldset->addField('customer_email', 'text', array(
            'name'   => 'customer_email',
            'title'  => Mage::helper('itanium_referral')->__('Customer Email'),
            'label'  => Mage::helper('itanium_referral')->__('Customer Email'),
            'class'     => 'required-entry',
            'required'  => true,
//            'readonly'=> true,
//            'disabled'=> true,
            'tabindex' => 30,
        ));

        $field = $fieldset->addField('customer_uuid', 'text', array(
            'name'   => 'customer_uuid',
            'title'  => Mage::helper('itanium_referral')->__('Customer UUID'),
            'label'  => Mage::helper('itanium_referral')->__('Customer UUID'),
            'class'     => 'required-entry',
            'required'  => true,
            'readonly'=> true,
//            'disabled'=> true,
//            'tabindex' => 30,
        ));

        $field = $fieldset->addField('referral_customer_email', 'text', array(
            'name'   => 'referral_customer_email',
            'title'  => Mage::helper('itanium_referral')->__('Referral Customer Email'),
            'label'  => Mage::helper('itanium_referral')->__('Referral Customer Email'),
            'class'     => 'required-entry',
            'required'  => true,
//            'readonly'=> true,
//            'disabled'=> true,
            'tabindex' => 40,
        ));

        $field = $fieldset->addField('referral_customer_uuid', 'text', array(
            'name'   => 'referral_customer_uuid',
            'title'  => Mage::helper('itanium_referral')->__('Referral Customer UUID'),
            'label'  => Mage::helper('itanium_referral')->__('Referral Customer UUID'),
            'class'     => 'required-entry',
            'required'  => true,
            'readonly'=> true,
//            'disabled'=> true,
//            'tabindex' => 50,
        ));


        $form->setUseContainer(true);
        $form->setValues($this->getRate()->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
