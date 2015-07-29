<?php
/**
 * File: upgrade-0.1.0.0-0.2.0.0.php
 *
 * User: Mitch Vanderlinden
 * email: mitchvdl@gmail.com
 * Date: 21.07.15 15:51
 * Package: Itanium_Referral
 */

/** @var $installer Itanium_Referral_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'itanium_referral/reward_salesrule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('itanium_referral/reward_salesrule'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Rule Id')
    ->addColumn('points_delta', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Points Delta')
    ->addIndex($installer->getIdxName('itanium_referral/reward_salesrule', array('rule_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('rule_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey($installer->getFkName('itanium_referral/reward_salesrule', 'rule_id', 'salesrule/rule', 'rule_id'),
        'rule_id', $installer->getTable('salesrule/rule'), 'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Itanium Customer Referral Reward Salesrule');
$installer->getConnection()->createTable($table);

$installer->endSetup();