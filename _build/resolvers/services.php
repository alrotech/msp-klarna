<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

/** @var xPDOTransport $transport */
/** @var array $options */

if (!$transport->xpdo && !$transport->xpdo instanceof modX) {
    return true;
}

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

        $transport->xpdo->addExtensionPackage('klarna', '[[++core_path]]components/mspklarna/services/');

        /** @var miniShop2 $shop */
        if ($shop = $transport->xpdo->getService('miniShop2')) {
            $shop->addService('payment', KlarnaHandler::class, '{core_path}components/mspklarna/KlarnaHandler.class.php');
        }

        break;

    case xPDOTransport::ACTION_UNINSTALL:

        $transport->xpdo->removeExtensionPackage('klarna');

        /** @var miniShop2 $shop */
        if ($shop = $transport->xpdo->getService('miniShop2')) {
            $shop->removeService('payment', KlarnaHandler::class);
        }

        break;
}
