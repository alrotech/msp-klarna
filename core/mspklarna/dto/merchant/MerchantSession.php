<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

namespace alroniks\mspklarna\dto\merchant;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class MerchantSession extends FlexibleDataTransferObject
{
    public string $client_token;
    public string $session_id;

//    public payment_method_categories
}
