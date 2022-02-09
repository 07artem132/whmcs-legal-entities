<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.12.2019, 23:40
 *
 */

use WHMCS\Billing\Invoice;
use WHMCS\CustomField;
use WHMCS\Gateways;
use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;
use WHMCS\Module\Addon\LegalEntities\Controllers\LogController;
use WHMCS\Module\Addon\LegalEntities\Controllers\PdfController;
use WHMCS\Module\Addon\LegalEntities\Models\SettingModel;
use WHMCS\Session;
use WHMCS\User\Client;
use WHMCS\View\Menu\Item as MenuItem;

add_hook('AdminAreaHeadOutput', 99999999, function ($vars) {
    try {
        if (!isset($_GET['module']) || $_GET['module'] != ModuleConfig::getModuleName()) {
            return null;
        }
        foreach (scandir(ModuleConfig::getWhmcsRootDir() . '/modules/addons/' . ModuleConfig::getModuleName() . '/templates/css/admin') as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            echo '<link rel="stylesheet" type="text/css" href="/modules/addons/' . ModuleConfig::getModuleName() . '/templates/css/admin/' . $item . '">';
        }

        foreach (scandir(ModuleConfig::getWhmcsRootDir() . '/modules/addons/' . ModuleConfig::getModuleName() . '/templates/js/admin') as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            echo '<script type="text/javascript" charset="utf8" src="/modules/addons/' . ModuleConfig::getModuleName() . '/templates/js/admin/' . $item . '"></script>';
        }
    } catch (Exception $e) {
        LogController::addError('AdminAreaHeadOutput', json_encode($vars), $e);
    }
});

add_hook('ClientAreaPrimaryNavbar', 1, function ($primaryNavbar) {

    $client = Menu::context('client');
    if (is_null($client)) {
        return;
    }
    $settings = SettingModel::where('key', 'like', 'client_%')
        ->get()
        ->keyBy('key')
        ->transform(function ($item, $key) {
            return $item->val;
        })->toArray();
    if (count($settings) == 0)
        return;

    if (is_null($primaryNavbar->getChild('Billing'))) {
        return;
    }

    if ($client->groupid != $settings['client_legal_entities_group_id']) {
        return;
    }

    $primaryNavbar->getChild('Billing')
        ->addChild('ts3resell', array(
            'label' => 'Акты',
            'uri' => '/?m=LegalEntities&page=acts',
            'order' => '11',
        ));
    $primaryNavbar->getChild('Billing')
        ->addChild('ts3resell2', array(
            'label' => 'Документы',
            'uri' => '/?m=LegalEntities&page=docs',
            'order' => '12',
        ));
    $primaryNavbar->getChild('Billing')
        ->addChild('ts3resell23', array(
            'label' => 'Акт сверки',
            'uri' => '/?m=LegalEntities&page=actVerify',
            'order' => '13',
        ));

});

add_hook('ClientAreaPageProfile', 1, function ($vars) {
    try {
        $customfields = array_column($vars['customfields'], null, 'id');

        $settings = SettingModel::where('key', 'like', 'client_%')
            ->get()
            ->keyBy('key')
            ->transform(function ($item, $key) {
                return $item->val;
            })->toArray();
        if (count($settings) == 0)
            return [];

        $hiddenAll = false;
        if ($settings['client_legal_entities_group_id'] != $vars['client']->groupid)
            $hiddenAll = true;

        if ($customfields[$settings['client_account_type_id']]['value'] != 'on')
            $hiddenAll = true;

        unset($settings['client_legal_entities_group_id']);

        foreach ($customfields as &$customfield) {
            if (!in_array($customfield['id'], $settings)) {
                continue;
            }
            if ($hiddenAll == true) {
                $customfield['input'] .= "<script>$(\"input[name='customfield[" . $customfield['id'] . "]']\").parent().parent().parent().hide()</script>";
                continue;
            }
            if (strpos($customfield['input'], 'checkbox')) {
                $customfield['input'] = substr_replace(
                        $customfield['input'],
                        'disabled',
                        strlen($customfield['input']) - 2,
                        0
                    ) . substr_replace(
                        $customfield['input'],
                        'style="display: none;"',
                        strlen($customfield['input']) - 2,
                        0
                    );
                continue;
            }
            $customfield['input'] = substr_replace(
                $customfield['input'],
                'readonly',
                strlen($customfield['input']) - 2,
                0
            );
        }
        return [
            'customfields' => $customfields
        ];
    } catch (\Throwable $e) {
        LogController::addError('ClientAreaPageProfile', json_encode($vars), $e);
        return [];
    }
});

add_hook('ClientAreaPageRegister', 1, function ($vars) {
    try {
        $customfields = $vars['customfields'];
        $settings = SettingModel::where('key', 'like', 'client_%')
            ->get()
            ->keyBy('key')
            ->transform(function ($item, $key) {
                return $item->val;
            })->toArray();
        if (count($settings) == 0)
            return [];

        foreach ($customfields as &$customfield) {
            if (!in_array($customfield['id'], $settings)) {
                continue;
            }
            if ($customfield['id'] != $settings['client_account_type_id']) {
                $customfield['input'] .= "<script>$(\"input[name='customfield[" . $customfield['id'] . "]']\").parent().parent().hide()</script>";
                continue;
            } else {
                $customfield['input'] .= '<script>window.hiddeInputs = [];$( document ).ready(function() {$(".form-group[style=\'display: none;\']").each(function(index) {  window.hiddeInputs.push($(this)); if($("input[name=\'customfield[' . $customfield['id'] . ']\']").is(":checked")) $(this).show();});});$(\'input[type="checkbox"][name="customfield[' . $customfield['id'] . ']"]\').on(\'change\', function() {if($(this).is(":checked")) {$(".form-group[style=\'display: none;\']").each(function(index) {$(this).show();})} else {for(var i = 0; i < window.hiddeInputs.length; i++) {$(window.hiddeInputs[i]).hide()}}});</script>';
            }
        }

        return [
            'customfields' => $customfields
        ];
    } catch (\Throwable $e) {
        LogController::addError('ClientAreaPageRegister', json_encode($vars), $e);
        return [];
    }
});

add_hook('ClientDetailsValidation', 1, function ($vars) {
    try {
        $settings = SettingModel::where('key', 'like', 'client_%')
            ->get()
            ->keyBy('key')
            ->transform(function ($item, $key) {
                return $item->val;
            })->toArray();

        if (count($settings) == 0)
            return [];

        $CustomFields = CustomField::ClientFields()->get()->keyBy('id');

        unset($settings['client_legal_entities_group_id']);
        $errors = [];

        if (!array_key_exists($settings['client_account_type_id'], $vars['customfield']))
            return [];

        if ($vars['customfield'][$settings['client_account_type_id']] != 'on')
            return [];

        foreach ($settings as $setting => $val) {
            if ($setting == 'client_edf_exits_id')
                continue;

            if ($vars['customfield'][$val] == '') {
                $errors[] = sprintf('Поле "%s" не может быть пустым для юр лица', $CustomFields[$val]->fieldname);
            }
        }

        return $errors;
    } catch (\Throwable $e) {
        LogController::addError('ClientDetailsValidation', json_encode($vars), $e);
        return [];
    }
});

add_hook('ClientDetailsValidation', 1, function ($vars) {
    try {
        if (strpos($_SERVER['SCRIPT_NAME'], 'clientarea.php') === false) {
            return [];
        }
        $errors = [];
        $settings = SettingModel::where('key', 'like', 'client_%')
            ->get()
            ->keyBy('key')
            ->transform(function ($item, $key) {
                return $item->val;
            })->toArray();

        if (count($settings) == 0)
            return [];

        $CustomFields = CustomField::ClientFields()->get()->keyBy('id');

        if (!array_key_exists($settings['client_account_type_id'], $vars['customfield']))
            return [];

        if ($vars['customfield'][$settings['client_account_type_id']] != 'on')
            return [];

        unset($settings['client_legal_entities_group_id']);

        foreach ($settings as $setting => $val) {
            $dbVal = $CustomFields[$val]
                ->customFieldValues()
                ->where('relid', '=', (int)Session::get("uid"))
                ->firstOrFail()
                ->value;

            if (strcasecmp($vars['customfield'][$val], $dbVal) !== 0) {
                $errors[] = sprintf('Поле "%s" не может быть изменено', $CustomFields[$val]->fieldname);
                LogController::addError('ClientAreaRegister', sprintf('client id->%s попытался изменить свои данные', (int)Session::get("uid")));
            }
        }

        return $errors;
    } catch (\Throwable $e) {
        LogController::addError('ClientDetailsValidation', json_encode($vars), $e);
        return [];
    }
});

add_hook('ClientAreaRegister', 1, function ($vars) {
    try {
        $settings = SettingModel::where('key', 'like', 'client_%')
            ->get()
            ->keyBy('key')
            ->transform(function ($item, $key) {
                return $item->val;
            })->toArray();

        if (count($settings) == 0)
            return;

        $CustomFields = CustomField::ClientFields()->get()->keyBy('id');
        $dbVal = $CustomFields[$settings['client_account_type_id']]
            ->customFieldValues()
            ->where('relid', '=', $vars['userid'])
            ->first();

        if (empty($dbVal) || $dbVal->value != 'on') {
            return;
        }

        $client = Client::findOrFail($vars['userid']);
        $client->groupid = $settings['client_legal_entities_group_id'];
        $client->saveOrFail();
        LogController::addSuccess('ClientAreaRegister', sprintf('client id->%s добавлен в группу->%s', $client->id, $client->groupid));

    } catch (\Throwable $e) {
        LogController::addError('ClientAreaRegister', json_encode($vars), $e);
        return;
    }
});

add_hook('ClientAreaPageCart', 1, function ($vars) {
    try {
        if ($_GET['a'] != 'checkout')
            return [];
        $customfields = $vars['customfields'];
        $settings = SettingModel::where('key', 'like', 'client_%')
            ->get()
            ->keyBy('key')
            ->transform(function ($item, $key) {
                return $item->val;
            })->toArray();
        if (count($settings) == 0)
            return [];
        foreach ($customfields as &$customfield) {
            if (!in_array($customfield['id'], $settings)) {
                continue;
            }
            if ($customfield['id'] != $settings['client_account_type_id']) {
                $customfield['input'] .= "<script>$(\"input[name='customfield[" . $customfield['id'] . "]']\").parent().parent().hide()</script>";
                continue;
            } else {
                $customfield['input'] .= '<script>window.hiddeInputs = [];$(document).ready(function() {$(".form-horizontal[style=\'display: none;\']").each(function(index) {window.hiddeInputs.push($(this));if($("input[name=\'customfield[' . $customfield['id'] . ']\']").is(":checked")) $(this).show();});if($("input[name=\'customfield[' . $customfield['id'] . ']\']").is(":checked")) {$("input[name=\'paymentmethod\']").parent().hide();$("input[value=\'LegalEntities\']").parent().show();} else {$("input[name=\'paymentmethod\']").parent().show();$("input[value=\'LegalEntities\']").parent().hide();} $("input[name=\'paymentmethod\']:visible").click();});$(\'input[type="checkbox"][name="customfield[' . $customfield['id'] . ']"]\').on(\'change\', function() {if($(this).is(":checked")) {$(".form-horizontal[style=\'display: none;\']").each(function(index) {$(this).show();});$("input[name=\'paymentmethod\']").parent().hide();$("input[value=\'LegalEntities\']").parent().show();} else {for(var i = 0; i < window.hiddeInputs.length; i++) {$(window.hiddeInputs[i]).hide()}$("input[name=\'paymentmethod\']").parent().show();$("input[value=\'LegalEntities\']").parent().hide();}$("input[name=\'paymentmethod\']:visible").click();});</script>';
            }
        }
        return [
            'customfields' => $customfields
        ];
    } catch (\Throwable $e) {
        LogController::addError('ClientAreaPageCart', json_encode($vars), $e);
        return [];
    }
});

add_hook('ClientAreaPage', 1, function ($vars) {
    try {
        if (strpos($_SERVER['SCRIPT_NAME'], 'viewinvoice.php') === false) {
            return [];
        }

        $settings = SettingModel::where('key', 'like', 'client_%')
            ->get()
            ->keyBy('key')
            ->transform(function ($item, $key) {
                return $item->val;
            })->toArray();

        $hide = false;

        if (count($settings) == 0)
            $hide = true;

        if ($settings['client_legal_entities_group_id'] != $vars['clientsdetails']['groupid'])
            $hide = true;

        $dbVal = CustomField::ClientFields()->findOrFail($settings['client_account_type_id'])
            ->customFieldValues()
            ->where('relid', '=', $vars['clientsdetails']['userid'])
            ->first();

        if (empty($dbVal) || $dbVal->value != 'on') {
            $hide = true;
        }
        $selectLegalEntitiesPay = preg_match(
                '/<option value="LegalEntities" .*selected.*?<\/option>/',
                $vars['gatewaydropdown'],
                $matches,
                PREG_OFFSET_CAPTURE,
                0) !== 0;

        if ($hide) {
            if ($selectLegalEntitiesPay) {
                $vars['gatewaydropdown'] .= '<script>document.getElementsByName("gateway")[0].parentElement.submit()</script>';
            }
            return [
                'gatewaydropdown' => preg_replace('/<option value="LegalEntities".*?<\/option>/', '', $vars['gatewaydropdown'])
            ];
        }
        if (!$selectLegalEntitiesPay) {
            return [
                'gatewaydropdown' => $vars['gatewaydropdown'] .= '<script>var selector=document.getElementsByName("gateway")[0];var opts = selector.options;for (var opt, j = 0; opt = opts[j]; j++) {if (opt.value == \'LegalEntities\'){selector.selectedIndex = j;break;}};document.getElementsByName("gateway")[0].parentElement.submit()</script>'
            ];
        }
        return [
            "allowchangegateway" => false,
        ];
    } catch (\Throwable $e) {
        LogController::addError('ClientAreaPage', json_encode($vars), $e);
        return [];
    }
});

add_hook('ClientAreaPage', 1, function ($vars) {
    try {
        if (strpos($_SERVER['SCRIPT_NAME'], 'clientarea.php') === false) {
            return [];
        }

        if (!array_key_exists('action', $_GET) || $_GET['action'] != 'addfunds') {
            return [];
        }

        $settings = SettingModel::where('key', 'like', 'client_%')
            ->get()
            ->keyBy('key')
            ->transform(function ($item, $key) {
                return $item->val;
            })->toArray();

        $hide = false;

        if (count($settings) == 0)
            $hide = true;

        if ($settings['client_legal_entities_group_id'] != $vars['clientsdetails']['groupid'])
            $hide = true;

        $dbVal = CustomField::ClientFields()->findOrFail($settings['client_account_type_id'])
            ->customFieldValues()
            ->where('relid', '=', $vars['clientsdetails']['userid'])
            ->first();

        if (empty($dbVal) || $dbVal->value != 'on') {
            $hide = true;
        }
        $gateways = $vars['gateways'];

        if ($hide) {
            unset($gateways['LegalEntities']);
        } else {
            foreach ($gateways as $key => $val) {
                if ($key == 'LegalEntities')
                    continue;
                unset($gateways[$key]);
            }
        }

        return [
            "gateways" => $gateways,
        ];
    } catch (\Throwable $e) {
        LogController::addError('ClientAreaPage', json_encode($vars), $e);
        return [];
    }
});

add_hook('InvoiceChangeGateway', 1, function ($vars) {
    try {
        $invoice = Invoice::findOrFail($vars['invoiceid']);
        $client = $invoice->client()->first();
        $AllowSelectLegalEntitiesPay = true;
        $Gateways = new Gateways();
        $allowGateways = $Gateways->getAvailableGateways($vars['invoiceid']);

        $settings = SettingModel::where('key', 'like', 'client_%')
            ->get()
            ->keyBy('key')
            ->transform(function ($item, $key) {
                return $item->val;
            })->toArray();

        if (count($settings) == 0)
            return;

        if ($settings['client_legal_entities_group_id'] != $client->groupid)
            $AllowSelectLegalEntitiesPay = false;

        $dbVal = CustomField::ClientFields()->findOrFail($settings['client_account_type_id'])
            ->customFieldValues()
            ->where('relid', '=', $client->id)
            ->first();

        if (empty($dbVal) || $dbVal->value != 'on')
            $AllowSelectLegalEntitiesPay = false;

        if ($vars['paymentmethod'] == 'LegalEntities' && !$AllowSelectLegalEntitiesPay) {
            unset($allowGateways['LegalEntities']);
            $invoice->paymentmethod = array_key_first($allowGateways);
            $invoice->saveOrFail();
        }

        if (strcasecmp($vars['paymentmethod'], 'LegalEntities') !== 0 && $AllowSelectLegalEntitiesPay) {
            if (array_key_exists('LegalEntities', $allowGateways)) {
                $invoice->paymentmethod = 'LegalEntities';
                $invoice->saveOrFail();
            }
        }
    } catch (\Throwable $e) {
        LogController::addError('InvoiceChangeGateway', json_encode($vars), $e);
        return;
    }
});

add_hook('EmailPreSend', 1, function ($vars) {
    global $CONFIG;
    try {
        if (strcasecmp($vars['messagename'], 'Invoice Created') !== 0)
            return [];


        $settings = SettingModel::all()
            ->keyBy('key')
            ->transform(function ($item, $key) {
                return $item->val;
            })->toArray();

        if (count($settings) == 0)
            return [];

        if (intval($settings['sendPdf']) === 0)
            return [];

        $invoice = Invoice::findOrFail($vars['relid']);
        $client = $invoice->client()->firstOrFail();

        if ($settings['client_legal_entities_group_id'] != $client->groupid)
            return [];

        $path = sprintf('%s/%s.pdf', ModuleConfig::geTempPath(), $vars['relid']);


        if (file_exists($path))
            return [];


        if (file_put_contents($path, PdfController::renderInvoice($vars['relid'])) === false) {
            LogController::addError('EmailPreSend', sprintf('ошибка записи в файл->%s', $path));
            return [];
        }
        $attachments = [
            [
                'displayname' => sprintf(
                    '%s - счет %s от %s.pdf',
                    $CONFIG["CompanyName"],
                    $vars['relid'],
                    $invoice->date->format('Y-m-d')
                ),
                'path' => $path
            ]
        ];
        $result = sendMessage("Invoice Created", $vars['relid'], "", "", $attachments);
        unlink($path);
        if ($result !== true) {
            LogController::addError('EmailPreSend', $result);
            return [];
        }
        //
        LogController::addSuccess('EmailPreSend', sprintf('invoice id->%s с pdf отправлен client id->%s', $vars['relid'], $client->id));
        echo 'send inc pdf';
        return [
            'abortsend' => true
        ];
    } catch (\Throwable $e) {
        LogController::addError('EmailPreSend', json_encode($vars), $e);
        return [];
    }
});

add_hook('ClientAreaFooterOutput', 2, function ($vars) {
    if (!array_key_exists('a', $_GET) || $_GET['a'] != 'checkout')
        return '';

    if ($vars['client'] == null)
        return '';

    $settings = SettingModel::where('key', 'like', 'client_%')
        ->get()
        ->keyBy('key')
        ->transform(function ($item, $key) {
            return $item->val;
        })->toArray();

    if (count($settings) == 0)
        return '';

    if ((int)$settings['client_legal_entities_group_id'] != $vars['client']['groupid'])
        return "<script>$(\"input[value=\'LegalEntities\']\").parent().hide();</script>";
    return '';
});
