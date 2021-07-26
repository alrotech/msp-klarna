<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

namespace alroniks\mspklarna\dto;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class PageResponse extends FlexibleDataTransferObject
{
    public ?string $distribution_url;
    public ?string $qr_code_url;
    public ?string $redirect_url;
    public ?string $session_id;
    public ?string $session_url;
}
