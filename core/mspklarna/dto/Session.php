<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

namespace alroniks\mspklarna\dto;

use alroniks\mspklarna\dto\order\OrderLine;
use alroniks\mspklarna\KlarnaGatewayInterface as Klarna;
use Brick\Money\Currency;
use Brick\Money\Money;
use msOrder;
use msOrderProduct;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

final class Session extends FlexibleDataTransferObject
{
    # required fields
    public string $locale;
    public string $purchase_country;
    public string $purchase_currency;

    public int $order_amount;
    public int $order_tax_amount;

    # extended fields
    public ?Address $billing_address;
    public ?Address $shipping_address;

    public ?Customer $customer;

    /** @var string|int|null  */
    public $merchant_reference1;

    /** @var string|int|null */
    public $merchant_reference2;

    /** @var \alroniks\mspklarna\dto\order\OrderLine[] */
    public array $order_lines;

    public function withBillingAddress(Address $address): self
    {
        $this->shipping_address = $address;

        return $this;
    }

    public function withShippingAddress(Address $address): self
    {
        $this->shipping_address = $address;

        return $this;
    }

    public function withCustomer(Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public static function createFromOrder(msOrder $order, array $config = [], $withAddress = true, $withCustomer = true): self
    {
        $currency = Currency::of($config[Klarna::OPTION_PURCHASE_CURRENCY]);

        $session = new self(array_merge($config, [
            'order_amount' => Money::of($order->get('cost'), $currency)
                ->getMinorAmount()->abs()->toInt(),
            'order_tax_amount' => 0,
            'merchant_reference1' => $order->get('id'),
            'merchant_reference2' => $order->get('num'),
            'order_lines' => array_values(array_map(static function(msOrderProduct $product) use($currency) {

                $product->set('cost', Money::of($product->get('cost'), $currency)->getMinorAmount()->abs()->toInt());
                $product->set('price', Money::of($product->get('price'), $currency)->getMinorAmount()->abs()->toInt());

                return OrderLine::createFromProduct($product);
            }, $order->getMany('Products')))
        ]));

        return $session;
    }
}
