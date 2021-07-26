<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

namespace alroniks\mspklarna\dto\order;

use msOrderProduct;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

final class OrderLine extends FlexibleDataTransferObject
{
    # required properties
    public string $name;
    public int $quantity;
    public int $total_amount;
    public int $unit_price;

//    public ?string $image_url;
//    public ?string $merchant_data;

    public ?string $product_url;
    public ?string $quantity_unit;
    public ?string $reference;

//    total_discount_amount
//    total_tax_amount
//    tax_rate

    public static function createFromProduct(msOrderProduct $product): self
    {
        return new self([
            'name' => $product->get('name'),
            'quantity' => $product->get('count'),
            'total_amount' => (int)$product->get('cost'),
            'unit_price' => (int)$product->get('price')
        ]);
    }
}
