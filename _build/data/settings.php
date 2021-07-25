<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

/** @var xPDO $xpdo */

require __DIR__ . '/../../core/mspklarna/KlarnaGatewayInterface.php';

use alroniks\mspklarna\KlarnaGatewayInterface as Klarna;

$list = [
    Klarna::AREA_CREDENTIALS => [
        Klarna::OPTION_USERNAME => ['xtype' => 'textfield', 'value' => 'PK42065_b861dfe97ec6'],
        Klarna::OPTION_PASSWORD => ['xtype' => 'text-password', 'value' => '2KFZjGO1uMpaLTzN'],
    ],
    Klarna::AREA_GATEWAYS => [
//        Klarna::OPTION_GATEWAY_REGION => ['xtype' => 'klarna-combo-region', 'value' => ''],
        Klarna::OPTION_GATEWAY_REGION => ['xtype' => 'textfield', 'value' => ''],
        Klarna::OPTION_GATEWAY_URL => ['xtype' => 'textfield', 'value' => 'https://api<region>.klarna.com'],
        Klarna::OPTION_GATEWAY_URL_TEST => ['xtype' => 'textfield', 'value' => 'https://api<region>.playground.klarna.com'],
        Klarna::OPTION_DEVELOPER_MODE => ['xtype' => 'combo-boolean', 'value' => true],
    ],
    Klarna::AREA_MERCHANT => [
        Klarna::OPTION_PURCHASE_LOCALE => ['xtype' => 'textfield', 'value' => 'en-GB'],
        Klarna::OPTION_PURCHASE_COUNTRY => ['xtype' => 'textfield', 'value' => 'DE'],
        Klarna::OPTION_PURCHASE_CURRENCY => ['xtype' => 'textfield', 'value' => 'EUR'],
    ],
    Klarna::AREA_STATUSES => [
        Klarna::OPTION_SUCCESS_STATUS => ['xtype' => 'mspp-combo-status', 'value' => 2],
        Klarna::OPTION_FAILURE_STATUS => ['xtype' => 'mspp-combo-status', 'value' => 4],
    ],
    Klarna::AREA_PAGES => [
        Klarna::OPTION_SUCCESS_PAGE => ['xtype' => 'mspp-combo-resource', 'value' => 0],
        Klarna::OPTION_FAILURE_PAGE => ['xtype' => 'mspp-combo-resource', 'value' => 0],
    ]
];

$settings = [];
foreach ($list as $area => $set) {
    foreach ($set as $key => $config) {
        $setting = $xpdo->newObject(modSystemSetting::class);
        $setting->fromArray(array_merge([
            'key' =>  'klarna_' . $key,
            'area' => 'klarna_' . $area,
            'namespace' => PKG_NAME_LOWER,
        ], $config), '', true, true);

        $settings[] = $setting;
    }
}

return $settings;
