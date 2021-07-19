<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

require __DIR__ . '/vendor/autoload.php';

use alroniks\mspklarna\KlarnaGatewayInterface;

if (!class_exists('ConfigurablePaymentHandler')) {
    $path = MODX_CORE_PATH. 'components/mspaymentprops/ConfigurablePaymentHandler.class.php';
    if (is_readable($path)) {
        /** @noinspection PhpIncludeInspection */
        require_once $path;
    }
}

class KlarnaHandler extends ConfigurablePaymentHandler implements KlarnaGatewayInterface
{
    public static function getPrefix(): string
    {
        return 'oplati';
    }

    /**
     * @throws \ReflectionException
     */
    public function send(msOrder $order)
    {
        if (!$link = $this->getPaymentLink($order)) {
            return $this->error('Token and redirect url can not be requested. Please, look at error log.');
        }

        return $this->success('', ['redirect' => $link]);
    }

    /**
     * @throws \ReflectionException
     */
    public function getPaymentLink(msOrder $order): string
    {
        /** @var msPayment $payment */
        $payment = $order->getOne('Payment');

        $this->config = $this->getProperties($payment);

        return $this->modx->makeUrl(
            $this->config[self::OPTION_UNPAID_PAGE],
            $this->modx->context->get('key'),
            modX::toQueryString(['msorder' => $order->get('id')]),
            'full'
        );
    }
}
