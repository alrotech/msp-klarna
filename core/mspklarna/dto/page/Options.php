<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

namespace alroniks\mspklarna\dto;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

final class Options extends FlexibleDataTransferObject
{
    public $background_images;

    public ?string $logo_url;
    public ?string $page_title;
    public ?bool $payment_fallback;

    public ?array $payment_method_categories;
    public ?string $payment_method_category;

    public ?string $purchase_type;
}
