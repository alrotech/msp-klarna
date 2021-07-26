<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

namespace alroniks\mspklarna\dto;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

final class PageSession extends FlexibleDataTransferObject
{
    public string $payment_session_url;
    public ?Urls $merchant_urls;
    public ?Options $options;
}
