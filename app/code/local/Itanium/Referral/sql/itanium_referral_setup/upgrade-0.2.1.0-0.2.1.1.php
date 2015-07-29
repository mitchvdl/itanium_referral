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

$installer->addAttribute('customer', 'itanium_linked_referral_uuid',
    array(
        'type' => 'varchar',
        'visible' => 0,
        'visible_on_front' => 1,
        'is_user_defined' => 0,
        'is_system' => 1,
        'is_hidden' => 1
    )
);

/**
 * Create table 'itanium_referral_customer'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('itanium_referral/referral_customer'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Itanium Referral Customer Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Website Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Store Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Parent Customer Id')
    ->addColumn('customer_uuid', Varien_Db_Ddl_Table::TYPE_TEXT, 42, array(
    ), 'Customer UUID')
    ->addColumn('referral_customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Referral Customer Id')
    ->addColumn('referral_customer_uuid', Varien_Db_Ddl_Table::TYPE_TEXT, 42, array(
    ),  'Referral Customer UUID')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    ), 'Created At')
    ->addIndex($installer->getIdxName('itanium_referral/referral_customer', array('customer_id', 'website_id', 'referral_customer_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('customer_id', 'website_id', 'referral_customer_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('itanium_referral/referral_customer', array('website_id')),
        array('website_id'))
    ->addIndex($installer->getIdxName('itanium_referral/referral_customer', array('customer_id')),
        array('customer_id'))
    ->addIndex($installer->getIdxName('itanium_referral/referral_customer', array('referral_customer_id')),
        array('referral_customer_id'))
    ->addIndex($installer->getIdxName('itanium_referral/referral_customer', array('customer_uuid')),
        array('customer_uuid'))
    ->addIndex($installer->getIdxName('itanium_referral/referral_customer', array('referral_customer_uuid')),
        array('referral_customer_uuid'))
    ->addForeignKey($installer->getFkName('itanium_referral/referral_customer', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('itanium_referral/referral_customer', 'referral_customer_id', 'customer/entity', 'entity_id'),
        'referral_customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Itanium Customer Referral');
$installer->getConnection()->createTable($table);

$installer->endSetup();