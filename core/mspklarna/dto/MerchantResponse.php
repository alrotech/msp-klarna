<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

namespace alroniks\mspklarna\dto;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class MerchantResponse extends FlexibleDataTransferObject
{
    public string $client_token;
    public string $session_id;

    /** @var \alroniks\mspklarna\dto\merchant\PaymentMethodCategory[]|null  */
    public ?array $payment_method_categories;
}
