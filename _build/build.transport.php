<?php
/** @noinspection AutoloadingIssuesInspection */

/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

use function alroniks\mspklarna\helpers\xml\xmlToArray;
use function alroniks\mspklarna\helpers\xml\arrayToXml;

set_time_limit(0);
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');
ini_set('date.timezone', 'Europe/Minsk');

require_once __DIR__ . '/vendor/autoload.php';

$composer = json_decode(file_get_contents(__DIR__ . '/composer.json'), true, 512, JSON_THROW_ON_ERROR);

[, $package] = explode('/', $composer['name']);

define('PKG_NAME_LOWER', $package);
define('PKG_VERSION', $composer['version']);
define('PKG_RELEASE', $composer['minimum-stability']);

require_once __DIR__ . '/vendor/modx/revolution/core/xpdo/xpdo.class.php';

/* instantiate xpdo instance */
$xpdo = new xPDO(
    'mysql:host=localhost;dbname=modx;charset=utf8', 'root', '',
    [xPDO::OPT_TABLE_PREFIX => 'modx_', xPDO::OPT_CACHE_PATH => __DIR__ . '/../../../core/cache/'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]
);
$cacheManager = $xpdo->getCacheManager();
$xpdo->setLogLevel(xPDO::LOG_LEVEL_INFO);
$xpdo->setLogTarget();

$signature = implode('-', [PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE]);

if (!empty($argv) && $argc > 1) {
    [, $release, $encryption] = array_replace(array_fill(0, 3,null), $argv);
}

$directory = (isset($release) && $release === 'release') ? dirname(__DIR__) . '/_packages/' : __DIR__ . '/../../../core/packages/';
$filename = $directory . $signature . '.transport.zip';

/* remove the package if it's already been made */
if (file_exists($filename)) {
    unlink($filename);
}
if (file_exists($directory . $signature) && is_dir($directory . $signature)) {
    $cacheManager = $xpdo->getCacheManager();
    if ($cacheManager) {
        $cacheManager->deleteTree($directory . $signature, true, false, []);
    }
}

$xpdo->loadClass('transport.xPDOTransport', XPDO_CORE_PATH, true, true);
$xpdo->loadClass('transport.xPDOVehicle', XPDO_CORE_PATH, true, true);
$xpdo->loadClass('transport.xPDOObjectVehicle', XPDO_CORE_PATH, true, true);
$xpdo->loadClass('transport.xPDOFileVehicle', XPDO_CORE_PATH, true, true);
$xpdo->loadClass('transport.xPDOScriptVehicle', XPDO_CORE_PATH, true, true);

$credentials = $encryption ?? file_get_contents(__DIR__ . '/../.encryption');
if ($credentials) {
    [$username, $key] = explode(':', $credentials);
}

if (empty($username) || empty($key)) {
    $xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Credentials not found');
    exit;
}

$params = [
    'api_key' => trim($key),
    'username' => trim($username),
    'http_host' => 'any-site.local.docker',
    'package' => PKG_NAME_LOWER,
    'version' => PKG_VERSION . '-' . PKG_RELEASE,
    'vehicle_version' => '2.0.0',
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://modstore.pro/extras/package/encode');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/xml']);
curl_setopt($ch, CURLOPT_POSTFIELDS, arrayToXml(['request' => $params])->outputMemory());
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$xml = trim(curl_exec($ch));
curl_close($ch);

$answer = xmlToArray($xml);

if (isset($answer['message'])) {
    $xpdo->log(xPDO::LOG_LEVEL_ERROR, $answer['message']);
    exit;
}

$xpdo->log(xPDO::LOG_LEVEL_INFO, 'Encryption key is: ' . $answer['key']);

define('PKG_ENCODE_KEY', $answer['key']);

$package = new xPDOTransport($xpdo, $signature, $directory);

$xpdo->setPackage('modx', __DIR__ . '/vendor/modx/revolution/core/model/');
$xpdo->loadClass(modAccess::class);
$xpdo->loadClass(modAccessibleObject::class);
$xpdo->loadClass(modAccessibleSimpleObject::class);
$xpdo->loadClass(modPrincipal::class);
$xpdo->loadClass(modElement::class);
$xpdo->loadClass(modScript::class);

// Put files into package
$package->put(
    [
        'source' => __DIR__ . '/../assets/' . PKG_NAME_LOWER,
        'target' => "return MODX_ASSETS_PATH . 'components/';",
    ],
    ['vehicle_class' => xPDOFileVehicle::class]
);
$package->put(
    [
        'source' => __DIR__ . '/../core/' . PKG_NAME_LOWER,
        'target' => "return MODX_CORE_PATH . 'components/';",
    ],
    ['vehicle_class' => xPDOFileVehicle::class]
);
$package->put(
    ['source' => __DIR__ . '/resolvers/encryption.php'],
    ['vehicle_class' => xPDOScriptVehicle::class]
);

class EncryptedVehicle extends xPDOObjectVehicle {}

$namespace = $xpdo->newObject(modNamespace::class);
$namespace->set('name', PKG_NAME_LOWER);
$namespace->fromArray(
    [
        'path' => '{core_path}components/' . PKG_NAME_LOWER . '/',
        'assets_path' => '{assets_path}components/' . PKG_NAME_LOWER . '/',
    ]
);

$package->put(
    $namespace,
    [
        'vehicle_class' => EncryptedVehicle::class,
        xPDOTransport::UNIQUE_KEY => 'name',
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::ABORT_INSTALL_ON_VEHICLE_FAIL => true,
        'validate' => [
            ['type' => 'php', 'source' => __DIR__ . '/validators/php-extensions.php'],
        ],
    ]
);

$settings = include __DIR__ . '/data/settings.php';
foreach ($settings as $setting) {
    $package->put(
        $setting,
        [
            'vehicle_class' => EncryptedVehicle::class,
            xPDOTransport::UNIQUE_KEY => 'key',
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
            'class' => modSystemSetting::class,
            'namespace' => PKG_NAME_LOWER,
        ]
    );
}

$category = $xpdo->newObject(modCategory::class);
$category->fromArray(['id' => 1, 'category' => 'mspKlarna', 'parent' => 0]);

$package->put(
    $category,
    [
        'vehicle_class' => EncryptedVehicle::class,
        xPDOTransport::UNIQUE_KEY => 'category',
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::ABORT_INSTALL_ON_VEHICLE_FAIL => true,
        xPDOTransport::NATIVE_KEY => true,
        'resolve' => [
            ['type' => 'php', 'source' => __DIR__ . '/resolvers/services.php'],
            ['type' => 'php', 'source' => __DIR__ . '/resolvers/payments.php'],
        ],
    ]
);

$package->setAttribute('changelog', file_get_contents(__DIR__ . '/../changelog.md'));
$package->setAttribute('license', file_get_contents(__DIR__ . '/../license'));
$package->setAttribute('readme', file_get_contents(__DIR__ . '/../docs/about.md'));
$package->setAttribute(
    'requires',
    [
        'php' => '>=7.4',
        'modx' => '>=2.8',
        'miniShop2' => '>=2.5',
        'msPaymentProps' => '>=0.3.4-stable',
    ]
);

if ($package->pack()) {
    $xpdo->log(xPDO::LOG_LEVEL_INFO, 'Package built');
}
