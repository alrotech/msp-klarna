<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2019
 */

define('MODX_API_MODE', true);

require_once __DIR__ . '/../../../index.php';

/** @var modX $modx */
$modx->initialize('mgr');

$modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
$modx->setLogTarget();

$modx->runProcessor('workspace/packages/scanlocal');

$composer = json_decode(file_get_contents(__DIR__ . '/composer.json'), true, 512, JSON_THROW_ON_ERROR);
[, $packageName] = explode('/', $composer['name']);

$signature = implode('-', [$packageName, $composer['version'], $composer['minimum-stability']]);

$answer = $modx->runProcessor('workspace/packages/install', ['signature' => $signature]);

$response = $answer->getResponse();

echo $response['message'], PHP_EOL;

$modx->getCacheManager()->refresh(['system_settings' => []]);
$modx->reloadConfig();
