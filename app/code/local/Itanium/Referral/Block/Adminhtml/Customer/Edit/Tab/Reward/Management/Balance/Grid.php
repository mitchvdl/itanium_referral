<?php
/**
 * Referral points balance grid
 *
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Flag to store if customer has orphan points
     *
     * @var boolean
     */
    protected $_customerHasOrphanPoints = false;

    /**
     * Internal constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('referralPointsBalanceGrid');
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
    }

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
     * Prepare grid collection
     *
     * @return Itanium_Referral_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('itanium_referral/reward')
            ->getCollection()
            ->addFieldToFilter('customer_id', $this->getCustomer()->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * After load collection processing
     *
     * @return Itanium_Referral_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance_Grid
     */
    protected function _afterLoadCollection()
    {
        parent::_afterLoadCollection();
        /* @var $item Itanium_Referral_Model_Referral */
        foreach ($this->getCollection() as $item) {
            $website = $item->getData('website_id');
            if ($website !== null) {
                $minBalance = Mage::helper('itanium_referral')->getGeneralConfig('min_points_balance', (int)$website);
                $maxBalance = Mage::helper('itanium_referral')->getGeneralConfig('max_points_balance', (int)$website);
                $item->addData(array(
                    'min_points_balance' => (int)$minBalance,
                    'max_points_balance' => (!((int)$maxBalance)?Mage::helper('adminhtml')->__('Unlimited'):$maxBalance)
                ));
            } else {
                $this->_customerHasOrphanPoints = true;
                $item->addData(array(
                    'min_points_balance' => Mage::helper('adminhtml')->__('No Data'),
                    'max_points_balance' => Mage::helper('adminhtml')->__('No Data')
                ));
            }
            $item->setCustomer($this->getCustomer());
        }
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return Itanium_Referral_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('website_id', array(
            'header'   => Mage::helper('itanium_referral')->__('Website'),
            'index'    => 'website_id',
            'sortable' => false,
            'type'     => 'options',
            'options'  => Mage::getModel('itanium_referral/source_website')->toOptionArray(false)
        ));

        $this->addColumn('points_balance', array(
            'header'   => Mage::helper('itanium_referral')->__('Balance'),
            'index'    => 'points_balance',
            'sortable' => false,
            'align'    => 'center'
        ));

        $this->addColumn('currency_amount', array(
            'header'   => Mage::helper('itanium_referral')->__('Currency Amount'),
            'getter'   => 'getFormatedCurrencyAmount',
            'align'    => 'right',
            'sortable' => false
        ));

        $this->addColumn('min_balance', array(
            'header'   => Mage::helper('itanium_referral')->__('Minimum Referral Points Balance to be able to Redeem'),
            'index'    => 'min_points_balance',
            'sortable' => false,
            'align'    => 'center'
        ));

        $this->addColumn('max_balance', array(
            'header'   => Mage::helper('itanium_referral')->__('Cap Referral Points Balance At'),
            'index'    => 'max_points_balance',
            'sortable' => false,
            'align'    => 'center'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Return url to delete orphan points
     *
     * @return string
     */
    public function getDeleteOrphanPointsUrl()
    {
        return $this->getUrl('*/customer_reward/deleteOrphanPoints', array('_current' => true));
    }

    /**
     * Processing block html after rendering.
     * Add button to delete orphan points if customer has such points
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        $html = parent::_afterToHtml($html);
        if ($this->_customerHasOrphanPoints) {
            $deleteOrhanPointsButton = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('itanium_referral')->__('Delete Orphan Points'),
                'onclick'   => 'setLocation(\'' . $this->getDeleteOrphanPointsUrl() .'\')',
                'class'     => 'scalable delete',
            ));
            $html .= $deleteOrhanPointsButton->toHtml();
        }
        return $html;
    }

    /**
     * Return grid row url
     *
     * @param Itanium_Referral_Model_Reward $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }
}
