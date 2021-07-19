<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

/** @var xPDO $xpdo */

require __DIR__ . '/../../core/mspklarna/KlarnaGatewayInterface.php';

use alroniks\mspklarna\KlarnaGatewayInterface;

$list = [
    KlarnaGatewayInterface::AREA_CREDENTIALS => [
        KlarnaGatewayInterface::OPTION_CASH_REGISTER_NUMBER => ['xtype' => 'textfield', 'value' => ''],
        KlarnaGatewayInterface::OPTION_CASH_PASSWORD => ['xtype' => 'text-password', 'value' => ''],
    ],
    KlarnaGatewayInterface::AREA_GATEWAYS => [
        KlarnaGatewayInterface::OPTION_GATEWAY_URL => ['xtype' => 'textfield', 'value' => 'https://cashboxapi.o-plati.by/ms-pay/'],
        KlarnaGatewayInterface::OPTION_GATEWAY_URL_TEST => ['xtype' => 'textfield', 'value' => 'https://bpay-testcashdesk.lwo.by/ms-pay/'],
        KlarnaGatewayInterface::OPTION_DEVELOPER_MODE => ['xtype' => 'combo-boolean', 'value' => true],
    ],
    KlarnaGatewayInterface::AREA_RECEIPT => [
        KlarnaGatewayInterface::OPTION_TITLE_TEXT => ['xtype' => 'textfield', 'value' => ''],
        KlarnaGatewayInterface::OPTION_HEADER_TEXT => ['xtype' => 'textfield', 'value' => ''],
        KlarnaGatewayInterface::OPTION_FOOTER_TEXT => ['xtype' => 'textfield', 'value' => ''],
        KlarnaGatewayInterface::OPTION_PRINT_CASH_REGISTER_NUMBER => ['xtype' => 'combo-boolean', 'value' => false],
    ],
    KlarnaGatewayInterface::AREA_STATUSES => [
        KlarnaGatewayInterface::OPTION_SUCCESS_STATUS => ['xtype' => 'mspp-combo-status', 'value' => 2],
        KlarnaGatewayInterface::OPTION_FAILURE_STATUS => ['xtype' => 'mspp-combo-status', 'value' => 4],
    ],
    KlarnaGatewayInterface::AREA_PAGES => [
        KlarnaGatewayInterface::OPTION_SUCCESS_PAGE => ['xtype' => 'mspp-combo-resource', 'value' => 0],
        KlarnaGatewayInterface::OPTION_FAILURE_PAGE => ['xtype' => 'mspp-combo-resource', 'value' => 0],
        KlarnaGatewayInterface::OPTION_UNPAID_PAGE => ['xtype' => 'mspp-combo-resource', 'value' => 0],
    ]
];

$settings = [];
foreach ($list as $area => $set) {
    foreach ($set as $key => $config) {
        $setting = $xpdo->newObject(modSystemSetting::class);
        $setting->fromArray(array_merge([
            'key' =>  'oplati_' . $key,
            'area' => 'oplati_' . $area,
            'namespace' => PKG_NAME_LOWER,
        ], $config), '', true, true);

        $settings[] = $setting;
    }
}

return $settings;
