<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

/** @var $modx modX */

const MODX_CONNECTOR_INCLUDED = true;
const MODX_REQP = false;

require_once __DIR__ . '/../../../connectors/index.php';

$_SERVER['HTTP_MODAUTH'] = $modx->user->getUserToken($modx->context->get('key'));

$path = $modx->getOption(
    'mspklarna.core_path', null,
    $modx->getOption('core_path') . 'components/mspklarna/'
);

$modx->request->handleRequest(
    [
        'processors_path' => $path . 'processors/',
        'location' => '',
    ]
);

// https://s.ru/assets/mspklarna/connector.php?ctx=web&action=web/status_update&hppSessionId={{session_id}}&secretTocken=otkfddfhdfg


//https://docs.klarna.com/hosted-payment-page/api-documentation/status-callbacks/

//{
//    "merchant_urls": {
//    "status_update": "https://example.com/statsCallbackEndpoint?hppSessionId={{session_id}}&secretToken=7d1cbc3b-b30c-4be2-a8c4-dc76482d7bf6"
//    }
//}
