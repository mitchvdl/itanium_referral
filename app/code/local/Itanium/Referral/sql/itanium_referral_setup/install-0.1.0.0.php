<?php
/**
 * File: install-0.1.0.0.php
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
 * Create table 'itanium_reward'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('itanium_referral/reward'))
    ->addColumn('reward_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Reward Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Website Id')
    ->addColumn('points_balance', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Points Balance')
    ->addColumn('website_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
        ), 'Website Currency Code')
    ->addIndex($installer->getIdxName('itanium_referral/reward', array('customer_id', 'website_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('customer_id', 'website_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('itanium_referral/reward', array('website_id')),
        array('website_id'))
    ->addForeignKey($installer->getFkName('itanium_referral/reward', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Itanium Customer Referral');
$installer->getConnection()->createTable($table);

/**
 * Create table 'itanium_referral_history'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('itanium_referral/reward_history'))
    ->addColumn('history_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'History Id')
    ->addColumn('reward_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Reward Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Website Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Store Id')
    ->addColumn('action', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Action')
    ->addColumn('entity', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Entity')
    ->addColumn('points_balance', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Points Balance')
    ->addColumn('points_delta', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Points Delta')
    ->addColumn('points_used', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Points Used')
    ->addColumn('points_voided', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Points Voided')
    ->addColumn('currency_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Currency Amount')
    ->addColumn('currency_delta', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Currency Delta')
    ->addColumn('base_currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 5, array(
        'nullable'  => false,
        ), 'Base Currency Code')
    ->addColumn('additional_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => false,
        ), 'Additional Data')
    ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => true,
        ), 'Comment')
    ->addColumn('expired_at_static', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Expired At Static')
    ->addColumn('expired_at_dynamic', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Expired At Dynamic')
    ->addColumn('is_expired', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Expired')
    ->addColumn('is_duplicate_of', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Is Duplicate Of')
    ->addColumn('notification_sent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Notification Sent')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
        ), 'Created At')
    ->addIndex($installer->getIdxName('itanium_referral/reward_history', array('reward_id')),
        array('reward_id'))
    ->addIndex($installer->getIdxName('itanium_referral/reward_history', array('website_id')),
        array('website_id'))
    ->addIndex($installer->getIdxName('itanium_referral/reward_history', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('itanium_referral/reward_history', 'reward_id', 'itanium_referral', 'reward_id'),
        'reward_id', $installer->getTable('itanium_reward'), 'reward_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('itanium_referral/reward_history', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('itanium_referral/reward_history', 'website_id', 'core/website', 'website_id'),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Itanium Customer Referral History');
$installer->getConnection()->createTable($table);

/**
 * Create table 'itanium_referral_rate'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('itanium_referral/reward_rate'))
    ->addColumn('rate_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rate Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Website Id')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer Group Id')
    ->addColumn('direction', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
        ), 'Direction')
    ->addColumn('points', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Points')
    ->addColumn('currency_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Currency Amount')
    ->addIndex($installer->getIdxName('itanium_referral/reward_rate', array('website_id', 'customer_group_id', 'direction'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('website_id', 'customer_group_id', 'direction'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('itanium_referral/reward_rate', array('website_id')),
        array('website_id'))
    ->addIndex($installer->getIdxName('itanium_referral/reward_rate', array('customer_group_id')),
        array('customer_group_id'))
    ->addForeignKey($installer->getFkName('itanium_referral/reward_rate', 'website_id', 'core/website', 'website_id'),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Itanium Customer Referral Rate');
$installer->getConnection()->createTable($table);

$installer->endSetup();
