<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

namespace alroniks\mspklarna\dto\order;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class Address extends FlexibleDataTransferObject
{
    public ?string $attention;
    public ?string $city;

    /**
     * Customer’s country. This value overrides the purchase country if they are different.
     * Should follow the standard of ISO 3166 alpha-2. E.g. GB, US, DE, SE.
     */
    public ?string $country;

    public ?string $email;

    public ?string $family_name;
    public ?string $given_name;

    public ?string $organization_name;
    public ?string $phone;
    public ?string $postal_code;
    public ?string $region;

    public ?string $street_address;
    public ?string $street_address2;

    public ?string $title;

    public function createFromOrder(): self {}
}
