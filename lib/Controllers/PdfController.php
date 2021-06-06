<?php

namespace WHMCS\Module\Addon\LegalEntities\Controllers;

use WHMCS\Billing\Invoice;
use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;
use WHMCS\Module\Addon\LegalEntities\Configs\SmartyConfig;
use WHMCS\Module\Addon\LegalEntities\Models\SettingModel;
use WHMCS\Module\Addon\LegalEntities\vendor\dompdf\PdfControllerAbstract;

class PdfController extends PdfControllerAbstract
{
    /**
     * @var \Smarty
     */
    private static $view;

    public static function renderFromTpl($templateName, $vars = array()): string
    {
        global $customadminpath, $CONFIG;

        self::$view = new \Smarty();
        self::$view->setTemplateDir(SmartyConfig::GetTemplateDir());
        self::$view->setCompileDir(SmartyConfig::GetCompileDir());
        self::$view->assign('_CONFIG', $CONFIG);

        if (array_key_exists('_lang', $vars))
            self::$view->assign('LANG', $vars['_lang']);

        foreach ($vars as $key => $val)
            self::$view->assign($key, $val);

        self::$view->assign('customadminpath', $customadminpath);
        self::$view->assign('modulelink', ModuleConfig::getModuleLink());
        $result = self::$view->fetch($templateName);
        self::$view = null;
        return self::renderFromHtml($result);
    }

    public static function renderInvoice($invoiceID = null, $sampleData = null): ?string
    {
        setlocale(LC_TIME, 'ru_RU.UTF-8', 'Rus');
        PdfController::init();
        if ($invoiceID == null && $sampleData != null) {
            return PdfController::renderFromTpl('invoice_pdf.tpl', $sampleData);
        } elseif ($invoiceID != null) {
            $invoice = Invoice::findOrFail($invoiceID);
            $settings = SettingModel::all()
                ->keyBy('key')
                ->transform(function ($item, $key) {
                    return $item->val;
                })->toArray();
            $search = [
                '%invoice.date%',
                '%invoice.id%'
            ];
            $replace = [
                $invoice->date->format('d.m.Y'),
                $invoiceID
            ];
            $var = [
                'payeesBank' => $settings['payeesBank'],
                'bik' => $settings['bik'],
                'accountNumber1' => $settings['accountNumber1'],
                'accountNumber2' => $settings['accountNumber2'],
                'reciver' => $settings['reciver'],
                'inn' => $settings['inn'],
                'kpp' => $settings['kpp'],
                'invoiceID' => $invoiceID,
                'invoiceDate' => strftime('%d %B %G г.', $invoice->date->timestamp),
                'provider' => sprintf(
                    '%s, ИНН %s, КПП %s, %s, %s',
                    $settings['reciver'],
                    $settings['inn'],
                    $settings['kpp'],
                    $settings['index'],
                    $settings['adress']
                ),
                'Leader' => $settings['leader'],
                'bookkeeper' => $settings['bookkeeper'],
                'sign1' => $settings['leader-sign'],
                'sign2' => $settings['bookkeeper-sign'],
                'printing' => $settings['printing-sign'],
                'headerVar' => str_replace($search, $replace, $settings['comment1']),
                'midleVar' => str_replace($search, $replace, $settings['comment2']),
                'footerVar' => str_replace($search, $replace, $settings['comment3']),
            ];
            foreach ($invoice->items()->get() as $item) {
                $var['items'][] = [
                    'name' => $item->description,
                    'count_raw' => 1,
                    'count' => '-',
                    'unit' => '-',
                    'price' => InvoiceFormatterController::format_price(floatval($item->amount)),
                    'price_raw' => floatval($item->amount),
                    'nds' => intval($settings['nds']),
                ];
            }
            $var['total_raw'] = 0;
            $var['nds_raw'] = 0;
            foreach ($var['items'] as $item) {
                $var['total_raw'] += $item['price_raw'] * $item['count_raw'];
                $var['nds_raw'] += ($item['price_raw'] * $item['nds'] / 100) * $item['count_raw'];
            }
            $var['total'] = InvoiceFormatterController::format_price($var['total_raw']);
            $var['nds'] = InvoiceFormatterController::format_price($var['nds_raw']);
            $var['count'] = count($var['items']);
            $var['stringTotal'] = InvoiceFormatterController::str_price($var['total_raw']);
            $client = $invoice->client()->firstOrFail();
            $customFieldValues = $client->customFieldValues()->get()->keyBy('fieldid');
            $var['customer'] = sprintf(
                '%s, ИНН %s, КПП %s , %s',
                $client->companyname,
                $customFieldValues[$settings['client_inn_id']]->value,
                $customFieldValues[$settings['client_kpp_id']]->value,
                $customFieldValues[$settings['client_address_id']]->value,
            );

            return PdfController::renderFromTpl('invoice_pdf.tpl', $var);
        }
        return null;
    }

    public static function renderReconciliationAct($invoiceID = null, $sampleData = null): ?string
    {
        setlocale(LC_TIME, 'ru_RU.UTF-8', 'Rus');
        PdfController::init();
        if ($invoiceID == null && $sampleData != null) {
            return PdfController::renderFromTpl('reconciliation_act_pdf.tpl', $sampleData);
        } elseif ($invoiceID != null) {
            $invoice = Invoice::findOrFail($invoiceID);
            $settings = SettingModel::all()
                ->keyBy('key')
                ->transform(function ($item, $key) {
                    return $item->val;
                })->toArray();
            $search = [
                '%invoice.date%',
                '%invoice.id%'
            ];
            $replace = [
                $invoice->date->format('d.m.Y'),
                $invoiceID
            ];
            $var = [
                'payeesBank' => $settings['payeesBank'],
                'bik' => $settings['bik'],
                'accountNumber1' => $settings['accountNumber1'],
                'accountNumber2' => $settings['accountNumber2'],
                'reciver' => $settings['reciver'],
                'inn' => $settings['inn'],
                'kpp' => $settings['kpp'],
                'invoiceID' => $invoiceID,
                'invoicePaidDate' => strftime('%d %B %G г.', $invoice->date->timestamp),
                'provider' => sprintf(
                //ООО "Компания", ИНН 0000000000, р/c 0000000000, в банке %Банк получателя%, БИК 000000, к/c 00000000000000000000
                    '%s, ИНН %s, р/c %s, в банке %s, БИК %s, к/c %s',
                    $settings['reciver'],
                    $settings['inn'],
                    $settings['accountNumber1'],
                    $settings['payeesBank'],
                    $settings['bik'],
                    $settings['accountNumber2']
                ),
                'Leader' => $settings['leader'],
                'bookkeeper' => $settings['bookkeeper'],
                'sign1' => $settings['leader-sign'],
                'sign2' => $settings['bookkeeper-sign'],
                'printing' => $settings['printing-sign'],
                'headerVar' => str_replace($search, $replace, $settings['comment1']),
                'midleVar' => str_replace($search, $replace, $settings['comment2']),
                'footerVar' => str_replace($search, $replace, $settings['comment3']),
            ];
            foreach ($invoice->items()->get() as $item) {
                if ($item->type == "AddFunds") {
                    $var['items'][] = [
                        'name' => $settings['addFundsName'],
                        'count_raw' => 1,
                        'count' => '-',
                        'unit' => '-',
                        'price' => InvoiceFormatterController::format_price(floatval($item->amount)),
                        'price_raw' => floatval($item->amount),
                        'price_total' => InvoiceFormatterController::format_price(floatval($item->amount) * 1),
                        'nds' => intval($settings['nds']),
                    ];
                } else {
                    $var['items'][] = [
                        'name' => $item->description,
                        'count_raw' => 1,
                        'count' => '-',
                        'unit' => '-',
                        'price' => InvoiceFormatterController::format_price(floatval($item->amount)),
                        'price_raw' => floatval($item->amount),
                        'price_total' => InvoiceFormatterController::format_price(floatval($item->amount) * 1),
                        'nds' => intval($settings['nds']),
                    ];
                }
            }
            $var['total_raw'] = 0;
            $var['nds_raw'] = 0;
            foreach ($var['items'] as $item) {
                $var['total_raw'] += $item['price_raw'] * $item['count_raw'];
                $var['nds_raw'] += ($item['price_raw'] * $item['nds'] / 100) * $item['count_raw'];
            }
            $var['total'] = InvoiceFormatterController::format_price($var['total_raw']);
            $var['nds'] = InvoiceFormatterController::format_price($var['nds_raw']);
            $var['count'] = count($var['items']);
            $var['stringTotal'] = InvoiceFormatterController::str_price($var['total_raw']);
            $client = $invoice->client()->firstOrFail();
            $customFieldValues = $client->customFieldValues()->get()->keyBy('fieldid');
            $var['client_id'] = $client->id;
            $var['client_companyname'] = $client->companyname;
            $var['create_date'] = strftime('%d %B %G г.', strtotime($client->datecreated));
            $var['customer'] = sprintf(
            //'ООО "Покупатель", ИНН 0000000000, р/c 0000000000, 119019, Москва г, Новый Арбат ул, р/c 0000000000, в банке %Банк получателя%, БИК 000000, к/c 00000000000000000000'
                '%s, ИНН %s, р/c %s, %s, в банке %s,БИК %s, к/c %s',
                $client->companyname,
                $customFieldValues[$settings['client_inn_id']]->value,
                $customFieldValues[$settings['client_pc_id']]->value,
                $customFieldValues[$settings['client_address_id']]->value,
                $customFieldValues[$settings['client_payeesBank_id']]->value,
                $customFieldValues[$settings['client_bik_id']]->value,
                $customFieldValues[$settings['client_kc_id']]->value,
            );

            return PdfController::renderFromTpl('reconciliation_act_pdf.tpl', $var);
        }
        return null;
    }


}