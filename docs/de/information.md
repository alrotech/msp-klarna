## mspKlarna &mdash; miniShop2 payment module

#### Klarna payment system

**[Klarna][klarna.com]** is a leading global payment and shopping service providing smarter and more flexible shopping experiences for 90 million active consumers in more than 250,000 retail outlets in 17 countries. Klarna offers direct payments, post-delivery payment options and installments in a convenient one-click purchase mode that allows consumers to pay when and how they prefer.

#### Integration module capabilities

The **mspKlarna** module allows you to transfer information about the order from **miniShop2** to the **Klarna** payment system, as well as process requests from the payment system, and change order statuses in accordance with the settings.

So far, only the option with a separate payment page (Hosted Page in terms of a payment system) is supported, since this most closely matches the miniShop operating model. It is possible to send a link to payment in email, since the `getPaymentLink` method is implemented.

In the module settings, you can additionally configure the region, the country of the payer, the payment currency and other parameters necessary to conclude a transaction between the online store and the customer.

#### News & Updates

Significant updates will be posted on the [community][] site in the form of announcements, while detailed changes can always be found in the [changelog][].

— [mspKlarna - new payment module for miniShop2](#)

#### Installation, configuration and use

Installation of the module is carried out through the manager part of your site on MODX. To install the module, go to the *Packages* section and then to the *Installer* of the main menu, [connect the modstore.pro repository][connection], select `mspKlarna` from the list of packages and follow the next steps the installer's instructions.

The module requires: PHP 7.4+, MODX 2.8+, installed auxiliary component **[msPaymentProps][]**, as well as some PHP Extensions. A description of the installation and configuration of the component, help on all available configuration parameters, and an exhaustive list of dependencies are available in the [detailed documentation][documentation].

You can quickly check the operation of the module using the [Console][] (or [modalConsole][]) component. Open the **`Console`** component window and execute the below code.

```php
require_once MODX_CORE_PATH . 'components/mspklarna/KlarnaHandler.class.php';

if ($order = $modx->getObject(msOrder::class, 1)) { // 1 – id заказа
    echo (new KlarnaHandler($order))->getPaymentLink($order);
}
```

In response, you should receive a link that will open the payment window, or an error message if the module was configured incorrectly.

#### Technical support

To get technical support for this module, ask a question through the [special form][support] (available after authorization). Technical support is provided according to the [rules][] of **modstore**, i.e. upon purchase you get 1 year of technical support and module updates. After a year, it is possible to renew by paying the license again.

#### Authorship and License

The module code is distributed under the MIT license, however, the distribution and sale of the finished module, in the form of an assembled package for the MODX CMS, is prohibited. When using parts of the code, retention of the license and attribution are required.

Module source author and distribution rights owner: [Ivan Klimchuk](https://modstore.pro/authors/alroniks).

[Telegram](https://t.me/orlaskin) | [GitHub](https://github.com/alroniks) | [Twitter](https://twitter.com/iklimchuk) | [ivan@klimchuk.com](mailto:ivan@klimchuk.com) | [klimchuk.by](https://klimchuk.by/) | [alroniks.com](https://alroniks.com)

[klarna.com]: https://www.klarna.com/
[community]: https://modx.today

[msPaymentProps]: https://modstore.pro/packages/utilities/mspaymentprops
[modalConsole]: https://modstore.pro/packages/utilities/modalconsole
[Console]: https://modx.com/extras/package/console

[documentation]: https://mspay.github.io/msp-klarna/ru/documentation
[changelog]: https://modstore.pro/packages/payment-system/mspklarna#tab/changelog

[connection]: https://modstore.pro/info/connection
[support]: https://modstore.pro/office/support
[rules]: https://modstore.pro/info/rules
