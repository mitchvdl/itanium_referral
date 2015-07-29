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

$installer->addAttribute('customer', 'itanium_referral_update_not',
    array(
        'type' => 'int',
        'visible' => 0,
        'visible_on_front' => 1,
        'is_user_defined' => 0,
        'is_system' => 1,
        'is_hidden' => 1
    )
);

$installer->addAttribute('customer', 'itanium_referral_warn_not',
    array(
        'type' => 'int',
        'visible' => 0,
        'visible_on_front' => 1,
        'is_user_defined' => 0,
        'is_system' => 1,
        'is_hidden' => 1
    )
);

$installer->addAttribute('customer', 'itanium_referral_uuid',
    array(
        'type' => 'varchar',
        'visible' => 0,
        'visible_on_front' => 1,
        'is_user_defined' => 0,
        'is_system' => 1,
        'is_hidden' => 1
    )
);

$installer->endSetup();