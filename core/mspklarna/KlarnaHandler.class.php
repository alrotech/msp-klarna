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
    $path = MODX_CORE_PATH . 'components/mspaymentprops/ConfigurablePaymentHandler.class.php';
    if (is_readable($path)) {
        /** @noinspection PhpIncludeInspection */
        require_once $path;
    }
}

class KlarnaHandler extends ConfigurablePaymentHandler implements KlarnaGatewayInterface
{
    public static function getPrefix(): string
    {
        return 'klarna';
    }

    public function send(msOrder $order)
    {
        if (!$link = $this->getPaymentLink($order)) {
            return $this->error('[Klarna]: Token and redirect url can not be requested. Please, look at error log.');
        }

        return $this->success('', ['redirect' => $link]);
    }

    /**
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \League\Uri\Contracts\UriException
     */
    public function getPaymentLink(msOrder $order): string
    {
        /** @var KlarnaService $service */
        $service = $this->modx->getService('klarna', KlarnaService::class);

        return $service->getHostedPaymentPage($order);
    }
}
