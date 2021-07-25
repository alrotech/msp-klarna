<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

namespace alroniks\mspklarna\dto;

use msOrder;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

class Session extends FlexibleDataTransferObject
{
    # required fields
    public string $locale;
    public string $purchase_country;
    public string $purchase_currency;
    public int $order_amount;
    public int $order_tax_amount;

    # optional fields
    public ?string $acquiring_channel;

    //attachment

    public ?string $authorization_token;

    # extended fields
    public ?Address $billing_address;
    public ?Address $shipping_address;

    public ?string $client_token;

    //custom_payment_method_ids

//    public $customer;

    public ?string $design;



    //merchant_data

    //merchant_reference1
    //merchant_reference2


//    /** @var \alroniks\mspklarna\dto\OrderLines[] $order_lines */
//    public array $order_lines;

    public function withCountry(string $country): self
    {
        $this->purchase_country = $country;

        return $this;
    }

    public static function createFromOrder(msOrder $order, array $config = []): self
    {
        // address
        // customer

        return new static(array_merge($config, [
            'order_amount' => $order->get('cost'), // *100 как в bepaid
            'order_tax_amount' => 0,
        ]));
    }
}
