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
