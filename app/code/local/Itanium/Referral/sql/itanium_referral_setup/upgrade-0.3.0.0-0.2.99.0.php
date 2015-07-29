<?php
/**
 * File: upgrade-0.2.0.0-0.2.1.0.php
 *
 * User: Mitch Vanderlinden
 * email: mitchvdl@gmail.com
 * Date: 21.07.15 15:51
 * Package: Itanium_Referral
 */

/** @var $installer Itanium_Referral_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();


$installer->addAttribute('quote', 'use_referral_points',
    array('type' => Varien_Db_Ddl_Table::TYPE_INTEGER)
);
$installer->addAttribute('quote', 'referral_points_balance',
    array('type' => Varien_Db_Ddl_Table::TYPE_INTEGER)
);
$installer->addAttribute('quote', 'base_referral_currency_amount', array('type' => Varien_Db_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('quote', 'referral_currency_amount', array('type' => Varien_Db_Ddl_Table::TYPE_DECIMAL));

$installer->addAttribute('quote_address', 'referral_points_balance',
    array('type' => Varien_Db_Ddl_Table::TYPE_INTEGER)
);
$installer->addAttribute('quote_address', 'base_referral_currency_amount', array('type' => Varien_Db_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('quote_address', 'referral_currency_amount', array('type' => Varien_Db_Ddl_Table::TYPE_DECIMAL));

$installer->addAttribute('order', 'referral_points_balance',
    array('type' => Varien_Db_Ddl_Table::TYPE_INTEGER)
);
$installer->addAttribute('order', 'base_referral_currency_amount', array('type' => Varien_Db_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('order', 'referral_currency_amount', array('type' => Varien_Db_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('order', 'base_rwrd_crrncy_amt_invoiced', array('type' => Varien_Db_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('order', 'rwrd_currency_amount_invoiced', array('type' => Varien_Db_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('order', 'base_rwrd_crrncy_amnt_refnded', array('type' => Varien_Db_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('order', 'rwrd_crrncy_amnt_refunded', array('type' => Varien_Db_Ddl_Table::TYPE_DECIMAL));

$installer->addAttribute('invoice', 'base_referral_currency_amount', array('type' => Varien_Db_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('invoice', 'referral_currency_amount', array('type' => Varien_Db_Ddl_Table::TYPE_DECIMAL));

$installer->addAttribute('creditmemo', 'base_referral_currency_amount', array('type' => Varien_Db_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('creditmemo', 'referral_currency_amount', array('type' => Varien_Db_Ddl_Table::TYPE_DECIMAL));

$installer->addAttribute('invoice', 'referral_points_balance',
    array('type' => Varien_Db_Ddl_Table::TYPE_INTEGER)
);

$installer->addAttribute('creditmemo', 'referral_points_balance',
    array('type' => Varien_Db_Ddl_Table::TYPE_INTEGER)
);

$installer->addAttribute('order', 'referral_points_balance_refund',
    array('type' => Varien_Db_Ddl_Table::TYPE_INTEGER)
);
$installer->addAttribute('creditmemo', 'referral_points_balance_refund',
    array('type' => Varien_Db_Ddl_Table::TYPE_INTEGER)
);

$installer->addAttribute('quote', 'base_referral_currency_amount', array('type' => Varien_Db_Ddl_Table::TYPE_DECIMAL));
$installer->addAttribute('quote', 'referral_currency_amount', array('type' => Varien_Db_Ddl_Table::TYPE_DECIMAL));

$installer->addAttribute('order', 'referral_points_balance_refunded',
    array('type' => Varien_Db_Ddl_Table::TYPE_INTEGER)
);

$installer->addAttribute('order', 'referral_salesrule_points',
    array('type' => Varien_Db_Ddl_Table::TYPE_INTEGER)
);

$installer->endSetup();