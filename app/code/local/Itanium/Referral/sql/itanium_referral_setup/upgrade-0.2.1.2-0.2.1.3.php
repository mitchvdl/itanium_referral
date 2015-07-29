<?php
/**
 * File: upgrade-0.2.1.1-0.2.1.2.php
 *
 * User: Mitch Vanderlinden
 * email: mitchvdl@gmail.com
 * Date: 21.07.15 15:51
 * Package: Itanium_Referral
 */

/** @var $installer Itanium_Referral_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

//$installer->getConnection()
//    ->dropIndex($installer->getTable('itanium_referral/referral_customer'),
//        $installer->getIdxName('itanium_referral/referral_customer', array('customer_id', 'website_id', 'referral_customer_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
//    );
//
//$installer->getConnection()
//    ->addIndex($installer->getIdxName('itanium_referral/referral_customer', array('customer_id', 'website_id', 'referral_customer_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
//        array('customer_id', 'website_id', 'referral_customer_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
//    ;
//

$installer->endSetup();