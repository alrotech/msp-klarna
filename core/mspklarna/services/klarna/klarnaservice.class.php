<?php /** @noinspection AutoloadingIssuesInspection */
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

use alroniks\mspklarna\dto\merchant\MerchantSession;
use alroniks\mspklarna\dto\Session;
use alroniks\mspklarna\KlarnaGatewayInterface as Klarna;
use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\Client;
use League\Uri\Uri;
use League\Uri\UriTemplate;
use Psr\Http\Message\ResponseInterface;
use Spatie\DataTransferObject\DataTransferObject;

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

        /** @var miniShop2 $service */
        $service = $this->modx->getService('minishop2');
        $this->engine = $service;

        $this->modx->lexicon->load('mspklarna:default');
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getHostedPaymentPage(msOrder $order): string
    {
        $this->setUpConfig($order);

        print_r($order->toArray());

        // MerchantSession

//                    'order_lines' => [
//        [
//            'name' => 'Некий товар',
//            'quantity' => 2,
//            'total_amount' => 1000,
//            'unit_price' => 500,
//        ]
//    ]

        $session = Session::createFromOrder($order, $this->config);

        // debug, avoid one value var
        print_r($session);

        $paymentSession = $this->createPaymentSession($session);

        print_r($paymentSession);

        die();

        $answ = $this->createHostedPageSession($paymentSession);



        //        $arr = $this->modx->fromJSON($res);
//
//        echo $arr['session_id'];
//
//        $answ = $service->requestRedirect($arr['session_id']);
//        $aw = $answ->getBody()->getContents();
//
//        print_r($aw);

    }

    // SessionRequest//
    protected function createHostedPageSession(MerchantSession $session)
    {
        $response = $this->makeRequest('/hpp/v1/sessions', $session);

        $uri = Uri::createFromBaseUri(
            (new UriTemplate('/payments/v1/sessions/{kp_session_id}'))->expand(['kp_session_id' => $session]),
            $this->config[Klarna::OPTION_GATEWAY_URL]
        );

        echo $uri;

        return $this->getClient()->request(
            RequestMethodInterface::METHOD_POST,
            '/hpp/v1/sessions',
            [
                'auth' => [
                    $this->config[Klarna::OPTION_USERNAME],
                    $this->config[Klarna::OPTION_PASSWORD],
                ],
                'json' => [
                    'payment_session_url' => (string)$uri,
//                    'merchant_urls' => [
//                        'success' => '',
//                        'cancel' => '',
//                        'back' => '',
//                        'failure' => '',
//                        'error' => ''
//                    ]
                ]
            ]
        );
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function createPaymentSession(Session $session): MerchantSession
    {
        $response = $this->makeRequest('/payments/v1/sessions', $session);

        return new MerchantSession($this->modx->fromJSON($response->getBody()->getContents()));
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

        $this->config[Klarna::OPTION_GATEWAY_URL] = str_replace(
            '<region>',
            $this->config[Klarna::OPTION_GATEWAY_REGION],
            $this->config[Klarna::OPTION_GATEWAY_URL]
        );
    }

    protected function getClient(): Client
    {
        return new Client(['base_uri' => $this->config[Klarna::OPTION_GATEWAY_URL]]);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function makeRequest(string $uri, DataTransferObject $dto): ResponseInterface
    {
        return $this->getClient()->request(
            RequestMethodInterface::METHOD_POST,
            $uri,
            [
                'auth' => [
                    $this->config[Klarna::OPTION_USERNAME],
                    $this->config[Klarna::OPTION_PASSWORD],
                ],
                'json' => $dto->toArray()
            ]
        );
    }
}
