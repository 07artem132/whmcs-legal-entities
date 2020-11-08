<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 11.01.2020, 17:21
 *
 */

namespace WHMCS\Module\Addon\LegalEntities\Pages;

use WHMCS\Billing\Invoice;
use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;
use WHMCS\Module\Addon\LegalEntities\Controllers\InvoiceFormatterController;
use WHMCS\Module\Addon\LegalEntities\Controllers\PdfController;
use WHMCS\Module\Addon\LegalEntities\Interfaces\PageInterface;
use WHMCS\View\Menu\MenuFactory;

class AdminDemoPage implements PageInterface
{
    private $templateName = 'admin_demo.tpl';
    private $vars = [];

    function __construct()
    {
        setlocale(LC_TIME, 'ru_RU.UTF-8', 'Rus');
        $var = [
            'payeesBank' => 'ЗАО "БАНК", г.Москва',
            'bik' => '000000000',
            'accountNumber1' => '00000000000000000000',
            'accountNumber2' => '00000000000000000000',
            'headerVar' => 'Зона коментария 1',
            'reciver' => 'ООО "Компания"',
            'inn' => '0000000000',
            'kpp' => '000000000',
            'midleVar' => 'Зона коментария 2',
            'invoiceID' => '10',
            'invoiceDate' => strftime('%d %B %G г.', time()),
            'provider' => 'ООО "Компания", ИНН 0000000000, КПП 000000000, 125009, Москва г, Тверская ул, дом № 9',
            'customer' => 'ООО "Покупатель", ИНН 0000000000, КПП 000000000, 119019, Москва г, Новый Арбат ул,
дом № 10',
            'items' => [
                [
                    'name' => 'Плита CERAMAGUARD FINE FISSURED (100 RH) 600*600*15',
                    'count' => 1,
                    'unit' => 'усл',
                    'price' => InvoiceFormatterController::format_price(1210),
                    'price_raw' => 1210,
                    'nds' => 18,
                ],
                [
                    'name' => 'Профиль 20*20',
                    'count' => 1,
                    'unit' => 'усл',
                    'price' => InvoiceFormatterController::format_price(550),
                    'price_raw' => 550,
                    'nds' => 10,
                ],
            ],
        ];
        $var['total_raw'] = 0;
        $var['nds_raw'] = 0;
        foreach ($var['items'] as $item) {
            $var['total_raw'] += $item['price_raw'] * $item['count'];
            $var['nds_raw'] += ($item['price_raw'] * $item['nds'] / 100) * $item['count'];
        }
        $var['total'] = InvoiceFormatterController::format_price($var['total_raw']);
        $var['nds'] = InvoiceFormatterController::format_price($var['nds_raw']);
        $var['count'] = count($var['items']);
        $var['stringTotal'] = InvoiceFormatterController::str_price($var['total_raw']);
        $var['footerVar'] = 'Зона коментария 3';
        $var['Leader'] = 'Иванов А.А';
        $var['bookkeeper'] = 'Сидоров Б.Б.';
        $var['sign1'] = ModuleConfig::getBaseFullPath() . '/templates/image/demo_sign-1.png';
        $var['sign2'] = ModuleConfig::getBaseFullPath() . '/templates/image/demo_sign-2.png';
        $var['printing'] = ModuleConfig::getBaseFullPath() . '/templates/image/demo_seal.png';
        if (array_key_exists('invoice_id', $_POST)) {
            $this->vars['pdf'] = base64_encode(PdfController::renderInvoice(intval($_POST['invoice_id'])));
            return;
        }
        $this->vars['pdf'] = base64_encode(PdfController::renderInvoice(null, $var));
    }

    function getTemplateName(): string
    {
        return $this->templateName;
    }

    /**
     * @return array
     */
    function getVars(): array
    {
        return $this->vars;
    }

    function getSubMenu(): ?MenuFactory
    {
        return null;
    }

    /**
     * @return array
     */
    public function getBreadcrumb(): array
    {
        return [
            'Превью pdf' => '',
        ];
    }
}