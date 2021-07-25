<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

namespace alroniks\mspklarna;

use msOrder;

interface KlarnaGatewayInterface
{
    public const AREA_CREDENTIALS = 'credentials';
    public const OPTION_USERNAME = 'username';
    public const OPTION_PASSWORD = 'password';

    public const AREA_GATEWAYS = 'gateways';
    public const OPTION_GATEWAY_REGION = 'region';
    public const OPTION_GATEWAY_URL = 'gateway_url';
    public const OPTION_GATEWAY_URL_TEST = 'gateway_url_test';
    public const OPTION_DEVELOPER_MODE = 'developer_mode';

    public const AREA_MERCHANT = 'merchant';
    public const OPTION_PURCHASE_LOCALE = 'locale';
    public const OPTION_PURCHASE_COUNTRY = 'purchase_country';
    public const OPTION_PURCHASE_CURRENCY = 'purchase_currency';

    public const AREA_STATUSES = 'statuses';
    public const OPTION_SUCCESS_STATUS = 'success_status';
    public const OPTION_FAILURE_STATUS = 'failure_status';

    public const AREA_PAGES = 'pages';
    public const OPTION_SUCCESS_PAGE = 'success_page';
    public const OPTION_FAILURE_PAGE = 'failure_page';

    public function getPaymentLink(msOrder $order): string;

    public function send(msOrder $order);
}
