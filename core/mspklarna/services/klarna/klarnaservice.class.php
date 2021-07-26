<?php /** @noinspection AutoloadingIssuesInspection */
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

use alroniks\mspklarna\dto\MerchantResponse;
use alroniks\mspklarna\dto\PageResponse;
use alroniks\mspklarna\dto\PageSession;
use alroniks\mspklarna\dto\MerchantSession;
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
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     * @throws \League\Uri\Contracts\UriException
     */
    public function getHostedPaymentPage(msOrder $order): string
    {
        $this->setUpConfig($order);

        // getting payment session
        $merchantResponse = $this->createMerchantSession(
            MerchantSession::createFromOrder($order, $this->config)
        );

        // configuring hosted payment page
        $pageSession = new PageSession([
            'payment_session_url' => (string)Uri::createFromBaseUri(
                (new UriTemplate('/payments/v1/sessions/{sid}'))
                    ->expand(['sid' => $merchantResponse->session_id]),
                $this->config[Klarna::OPTION_GATEWAY_URL]
            )
        ]);

        // getting hosted page
        return $this->createHostedPageSession($pageSession)->redirect_url;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function createHostedPageSession(PageSession $session): PageResponse
    {
        $response = $this->makeRequest('/hpp/v1/sessions', $session);

        return new PageResponse($this->modx->fromJSON($response->getBody()->getContents()));
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function createMerchantSession(MerchantSession $session): MerchantResponse
    {
        $response = $this->makeRequest('/payments/v1/sessions', $session);

        return new MerchantResponse($this->modx->fromJSON($response->getBody()->getContents()));
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
