<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

use alroniks\mspklarna\KlarnaGatewayInterface as Klarna;
use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\Client;

require __DIR__ . '/../../vendor/autoload.php';

class KlarnaService
{
    private modX $modx;

    private miniShop2 $engine;

    private array $config;

    public function __construct(modX $modx, array $config = [])
    {
        $this->modx = $modx;
        $this->config = $config;

        /** @var miniShop2 $ms */
        $service = $this->modx->getService('minishop2');
        $this->engine = $service;

        $this->modx->lexicon->load('mspklarna:default');
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestPayment(msOrder $order)
    {
        $this->setUpConfig($order);

        $response = $this->getClient()->request(
            RequestMethodInterface::METHOD_POST,
            '/payments/v1/sessions',
            [
                'auth' => [
                    $this->config[Klarna::OPTION_USERNAME],
                    $this->config[Klarna::OPTION_PASSWORD],
                ],
                // todo: dto
                'json' => [
                    'purchase_country' => 'GB',
                    'purchase_currency' => 'GBP',
                    'locale' => 'en-GB',
                    'order_amount' => 10,
                    'order_tax_amount' => 0,

                ]
            ]
        );

        return $response->getBody()->getContents();
    }

    protected function setUpConfig(msOrder $order): void
    {
        /** @var msPayment $payment */
        $payment = $order->getOne('Payment');
        $payment->loadHandler();

        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $this->config = $payment->handler->getProperties($payment);

        if ($this->config[Klarna::OPTION_DEVELOPER_MODE]) {
            $this->config[Klarna::OPTION_GATEWAY_URL] = $this->config[Klarna::OPTION_GATEWAY_URL_TEST];
        }

        // need replace region

    }

    protected function getClient(): Client
    {
        return new Client(['base_uri' => $this->config[Klarna::OPTION_GATEWAY_URL]]);
    }
}
