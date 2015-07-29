<?php

/**
 * Referral History grid
 *
 * @category    Itanium
 * @package     Itanium_Referral
  */
class Itanium_Referral_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
        $this->setId('referralPointsHistoryGrid');
    }

    /**
     * Prepare grid collection object
     *
     * @return Itanium_Referral_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Itanium_Referral_Model_Mysql4_Reward_History_Collection */
        $collection = Mage::getModel('itanium_referral/reward_history')->getCollection()
            ->addCustomerFilter($this->getCustomerId())
            ->setExpiryConfig(Mage::helper('itanium_referral')->getExpiryConfig())
            ->addExpirationDate()
            ->setOrder('history_id', 'desc');
        $collection->setDefaultOrder();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add column filter to collection
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Itanium_Referral_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            $field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();
            if ($field == 'website_id' || $field == 'points_balance') {
                $cond = $column->getFilter()->getCondition();
                if ($field && isset($cond)) {
                    $this->getCollection()->addFieldToFilter('main_table.'.$field , $cond);
                }
            } else {
                parent::_addColumnFilterToCollection($column);
            }
        }
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('points_balance', array(
            'type'     => 'number',
            'index'    => 'points_balance',
            'header'   => Mage::helper('itanium_referral')->__('Balance'),
            'sortable' => false,
            'filter'   => false,
            'width'    => 1,
        ));

        $this->addColumn('currency_amount', array(
            'type'     => 'currency',
            'currency' => 'base_currency_code',
            'rate'     => 1,
            'index'    => 'currency_amount',
            'header'   => Mage::helper('itanium_referral')->__('Amount Balance'),
            'sortable' => false,
            'filter'   => false,
            'width'    => 1,
        ));

        $this->addColumn('points_delta', array(
            'type'     => 'number',
            'index'    => 'points_delta',
            'header'   => Mage::helper('itanium_referral')->__('Points'),
            'sortable' => false,
            'filter'   => false,
            'show_number_sign' => true,
            'width'    => 1,
        ));

        $this->addColumn('currency_delta', array(
            'type'     => 'currency',
            'currency' => 'base_currency_code',
            'rate'     => 1,
            'index'    => 'currency_delta',
            'header'   => Mage::helper('itanium_referral')->__('Amount'),
            'sortable' => false,
            'filter'   => false,
            'show_number_sign' => true,
            'width'    => 1,
        ));

        $this->addColumn('rate', array(
            'getter' => 'getRateText',
            'header'   => Mage::helper('itanium_referral')->__('Rate'),
            'sortable' => false,
            'filter'   => false
        ));

// TODO: instead of source models move options to a getter
        $this->addColumn('website', array(
            'type'     => 'options',
            'options'  => Mage::getModel('itanium_referral/source_website')->toOptionArray(false),
            'index'    => 'website_id',
            'header'   => Mage::helper('itanium_referral')->__('Website'),
            'sortable' => false,
        ));

// TODO: custom renderer for reason, which includes comments
        $this->addColumn('message', array(
            'index'    => 'message',
            'type'     => 'text',
            'getter'   => 'getMessage',
            'header'   => Mage::helper('itanium_referral')->__('Reason'),
            'sortable' => false,
            'filter'   => false,
            'renderer' => 'itanium_referral/adminhtml_customer_edit_tab_reward_history_grid_column_renderer_reason',
        ));

        $this->addColumn('created_at', array(
            'type'     => 'datetime',
            'index'    => 'created_at',
            'header'   => Mage::helper('itanium_referral')->__('Created At'),
            'sortable' => false,
            'align'    => 'left',
            'html_decorators' => 'nobr',
        ));

        $this->addColumn('expiration_date', array(
            'type'     => 'datetime',
            'getter'   => 'getExpiresAt',
            'header'   => Mage::helper('itanium_referral')->__('Expires At'),
            'sortable' => false,
            'filter'   => false, // needs custom filter
            'align'    => 'left',
            'html_decorators' => 'nobr',
        ));

// TODO: merge with reason
        $this->addColumn('comment', array(
            'index'    => 'comment',
            'header'   => Mage::helper('itanium_referral')->__('Comment'),
            'sortable' => false,
            'filter'   => false,
            'align'    => 'left',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Return grid url for ajax actions
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/historyGrid', array('_current' => true));
    }

    /**
     * Return grid row url
     *
     * @param Itanium_Referral_Model_Reward_History $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }
}
