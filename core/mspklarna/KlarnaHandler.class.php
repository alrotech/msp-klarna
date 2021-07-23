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
        return 'klarna';
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

        /** @var KlarnaService $service */
        $service = $this->modx->getService('klarna', KlarnaService::class);

        $res = $service->requestPayment($order);

        $arr = $this->modx->fromJSON($res);

        echo $arr['session_id'];

        $answ = $service->requestRedirect($arr['session_id']);
        $aw = $answ->getBody()->getContents();

        print_r($aw);

        die();

        // - get gateway links
        //

//        {
//            "purchase_country": "GB",
//  "purchase_currency": "GBP",
//  "locale": "en-GB",
//  "order_amount": 10,
//  "order_tax_amount": 0,
//  "order_lines": [{
//            "type": "physical",
//    "reference": "19-402",
//    "name": "Battery Power Pack",
//    "quantity": 1,
//    "unit_price": 10,
//    "tax_rate": 0,
//    "total_amount": 10,
//    "total_discount_amount": 0,
//    "total_tax_amount": 0,
//    "image_url": "https://www.exampleobjects.com/logo.png",
//    "product_url": "https://www.estore.com/products/f2a8d7e34"
//  }]
//}
        return '';
//        return $this->modx->makeUrl(
//            $this->config[self::OPTION_UNPAID_PAGE],
//            $this->modx->context->get('key'),
//            modX::toQueryString(['msorder' => $order->get('id')]),
//            'full'
//        );
    }
}
