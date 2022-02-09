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
use Carbon\Carbon;

class AdminActsverifyPage implements PageInterface
{
    private $templateName = 'admin_demo3.tpl';
    private $vars = [];

    function __construct()
    {
        setlocale(LC_TIME, 'ru_RU.UTF-8', 'Rus');
        $var = [
            'payeesBank' => 'ЗАО "БАНК", г.Москва',
            'startDate' => '01.01.2021',
            'endDate' => '30.12.2021',
            'currentDate' => '01.01.2022',
            'bik' => '000000000',
            'accountNumber1' => '00000000000000000000',
            'accountNumber2' => '00000000000000000000',
            'headerVar' => 'Зона коментария 1',
            'reciver' => 'ООО "Компания"',
            'inn' => '0000000000',
            'kpp' => '000000000',
            'midleVar' => 'Зона коментария 2',
            'invoiceID' => '10',
            'invoicePaidDate' => strftime('%d %B %G г.', time()),
            'provider' => 'ООО "Компания", ИНН 0000000000, р/c 0000000000, в банке %Банк получателя%, БИК 000000, к/c 00000000000000000000',
            'customer' => 'ООО "Покупатель", ИНН 0000000000, р/c 0000000000, 119019, Москва г, Новый Арбат ул, р/c 0000000000, в банке %Банк получателя%, БИК 000000, к/c 00000000000000000000',
            'customer_name' => 'ООО "Покупатель"',
            'customer_inn' => '0000000000',
            'customer_head_position' => 'Генеральный НЕ директор',
            'customer_full_name_of_the_head' => 'Иванов Б.Б',


            'items' => [
                [
                    'name' => 'Плита CERAMAGUARD FINE FISSURED (100 RH) 600*600*15',
                    'count' => '-',
                    'unit' => '-',
                    'price' => InvoiceFormatterController::format_price(1210),
                    'price_total' => InvoiceFormatterController::format_price(1210*1),
                    'price_raw' => 1210,
                    'nds' => 18,
                ],
                [
                    'name' => 'Профиль 20*20',
                    'count' => '-',
                    'unit' => '-',
                    'price' => InvoiceFormatterController::format_price(550),
                    'price_total' => InvoiceFormatterController::format_price(550*1),
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
        if (array_key_exists('client_id', $_POST)&&array_key_exists('start_date', $_POST)&&array_key_exists('end_date', $_POST)) {
            $this->vars['pdf'] = base64_encode(PdfController::renderVerifyActs(
                intval($_POST['client_id']),
                Carbon::parse($_POST['start_date']),
                Carbon::parse($_POST['end_date'])
            ));
            return;
        }
        $this->vars['pdf'] = base64_encode(PdfController::renderVerifyActs(null,null,null, $var));
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