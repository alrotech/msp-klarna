<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

/** @var xPDOTransport $transport */
/** @var array $options */

if (!$transport->xpdo && !$transport->xpdo instanceof modX) {
    return true;
}

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:

        $transport->xpdo->newObject(
            msPayment::class,
            [
                'name' => 'Klarna',
                'description' => null,
                'price' => 0,
                'rank' => 0,
                'active' => 1,
                'class' => KlarnaHandler::class,
                'properties' => null // todo: setup minimal default properties
            ]
        )->save();

        break;

    case xPDOTransport::ACTION_UNINSTALL:

        $transport->xpdo->removeObject(msPayment::class, ['class' => KlarnaHandler::class]);

        break;
}
