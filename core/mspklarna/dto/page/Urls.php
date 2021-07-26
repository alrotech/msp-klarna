<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

namespace alroniks\mspklarna\dto;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

final class Urls extends FlexibleDataTransferObject
{
    public ?string $back;
    public ?string $cancel;
    public ?string $error;
    public ?string $failure;
    public ?string $privacy_policy;
    public ?string $status_update;
    public ?string $success;
    public ?string $terms;
}
