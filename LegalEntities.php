<?php

use WHMCS\Domain\Domain;
use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;
use WHMCS\Module\Addon\LegalEntities\Controllers\PageController;
use WHMCS\Module\Addon\LegalEntities\Controllers\PdfController;
use WHMCS\Module\Addon\LegalEntities\Menu\AdminAreaMenu;
use WHMCS\Module\Addon\LegalEntities\Controllers\InstallController;
use WHMCS\Module\Addon\LegalEntities\Controllers\UninstallController;
use WHMCS\Module\Addon\LegalEntities\Models\DocModel;
use WHMCS\Module\Addon\LegalEntities\Models\SharedDocModel;
use WHMCS\Module\Addon\Setting;
use WHMCS\Service\Addon;
use WHMCS\Service\Service;
use WHMCS\Session;
use WHMCS\Billing\Invoice;


function LegalEntities_config()
{
    return [
        "name" => "Работа с юр лицами",
        "description" => "",
        "version" => "1",
        "author" => "service-voice",
        "fields" => [
            "DeleteTableWhenDisabled" => [
                "FriendlyName" => "Удалять данные модуля при отключении ?",
                "Type" => "yesno",
                "Description" => " Отметьте здесь дабы удалить данные модуля при отключении оного.",
            ]
        ]
    ];
}

function LegalEntities_activate()
{
    if (!empty($error = InstallController::installGatewayModule())) {
        return $error;
    }
    if (!empty($error = InstallController::createTableSettings())) {
        return $error;
    }
    if (!empty($error = InstallController::createTableDoc())) {
        return $error;
    }
    if (!empty($error = InstallController::createTableActs())) {
        return $error;
    }
    if (!empty($error = InstallController::createTableSharedDoc())) {
        return $error;
    }
    if (!empty($error = InstallController::createTableLog())) {
        return $error;
    }

    return array(
        'status' => 'success',
        'description' => 'Модуль успешно активирован',
    );
}

function LegalEntities_deactivate()
{
    if (!empty($dropTable = Setting::Module(ModuleConfig::getModuleName())->where('setting', '=', 'DeleteTableWhenDisabled')->first())) {
        if ($dropTable->value === 'on') {
            if (!empty($error = UninstallController::dropTable('mod_addon_legal_entities_setting'))) {
                return $error;
            }
            if (!empty($error = UninstallController::dropTable('mod_addon_legal_log'))) {
                return $error;
            }
            if (!empty($error = UninstallController::dropTable('mod_addon_legal_entities_doc'))) {
                return $error;
            }
            if (!empty($error = UninstallController::dropTable('mod_addon_legal_entities_acts'))) {
                return $error;
            }
            if (!empty($error = UninstallController::dropTable('mod_addon_legal_entities_shared_doc'))) {
                return $error;
            }
        }
    }
    if (!empty($error = UninstallController::deleteGatewayModule())) {
        return $error;
    }

    return array(
        'status' => 'success',
        'description' => 'Модуль успешно деактивирован'
    );
}

function LegalEntities_output($var)
{
    $PageController = new PageController($var);
    $PageController->setDefaultAction('index');
    $PageController->setSuffixTemplate('admin');
    $PageController->setMenuTemplate('include\navbar.tpl');
    $PageController->setBreadcrumbTemplate('include\breadcrumb.tpl');
    $PageController->setMenu((new AdminAreaMenu())->navbar());
    $PageController->run();
}

function LegalEntities_clientarea($vars)
{
    //

    if (array_key_exists('pdf', $_GET)) {
        $id = decrypt(base64_decode(rawurldecode($_GET['id'])));
        $result = PdfController::renderInvoice($id);
        if ($result == null) {
            return;
        }
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline');
        echo $result;
        die();
    }
    if (array_key_exists('rqs', $_GET)) {
        $id = decrypt(base64_decode(rawurldecode($_GET['id'])));
        $type = $_GET['tps'];
        sendAdminNotification("system", 'LegalEntities', "запрос отправки для счета " . $id . " тип:" . $type);
        return array(
            'pagetitle' => 'Запрос отправки акта',
            'breadcrumb' => array('index.php?m=LegalEntities' => 'Запрос отправки акта'),
            'templatefile' => 'templates/client_success.tpl',
            'requirelogin' => true,
            'forcessl' => false,
            'vars' => array(),
        );
    }

    if (array_key_exists('fid', $_GET)) {
        $id = decrypt(base64_decode(rawurldecode($_GET['fid'])));
        ob_end_clean();
        ob_implicit_flush();
        if (!array_key_exists('type', $_GET)) {
            die('error not found type');
        }
        if ($_GET['type'] == 'private') {
            $model = DocModel::findOrFail($id);
            $ext = pathinfo($model->file, PATHINFO_EXTENSION);
            $file_name = sprintf('%s %s от %s.%s', $model->type, $model->name, $model->updated_at->format('Y-m-d'), $ext);
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($model->file));
            readfile($model->file);
            die();
        }
        if ($_GET['type'] == 'public') {
            $model = SharedDocModel::findOrFail(intval($id));
            $ext = pathinfo($model->file, PATHINFO_EXTENSION);
            $file_name = sprintf('%s %s от %s.%s', $model->type, $model->name, $model->updated_at->format('Y-m-d'), $ext);
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($model->file));
            readfile($model->file);
            die();
        }
        if ($_GET['type'] == 'act') {
            $id = decrypt(base64_decode(rawurldecode($_GET['fid'])));
            $result = PdfController::renderReconciliationAct($id);
            if ($result == null) {
                return;
            }
            $invoice = Invoice::findOrFail($id);
            $file_name = sprintf('акт %s от %s.pdf', $id, $invoice->datepaid->format('d.m.Y'));
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $file_name . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            echo $result;
            die();
        }
    }
    $user_id = (int)Session::get("uid");
    if (!array_key_exists('page', $_GET)) {
        return;
    }
    if ($_GET['page'] == 'docs') {
        $sIds = Service::where('userid', '=', $user_id)->get()->pluck('id')->toArray();
        $aIds = Addon::where('userid', '=', $user_id)->get()->pluck('id')->toArray();
        $dIds = Domain::where('userid', '=', $user_id)->get()->pluck('id')->toArray();

        $docs = DocModel::where(function ($query) use ($sIds) {
            $query->where('rel_type', '=', 1)
                ->whereIn('rel_id', $sIds);
        })->orWhere(function ($query) use ($aIds) {
            $query->where('rel_type', '=', 2)
                ->whereIn('rel_id', $aIds);
        })->orWhere(function ($query) use ($dIds) {
            $query->where('rel_type', '=', 3)
                ->whereIn('rel_id', $dIds);
        })->orWhere(function ($query) use ($user_id) {
            $query->where('rel_type', '=', 4)
                ->where('rel_id', $user_id);
        })->get()->transform(function ($item) {
            $item = $item->toArray();
            $item['type_download'] = 'private';
            $item['down_id'] = urlencode(base64_encode(encrypt((string)$item['id'])));
            return $item;
        });

        $publicDoc = SharedDocModel::get()->transform(function ($item) {
            $item = $item->toArray();
            $item['type_download'] = 'public';
            $item['type'] = 'Общий документ';
            $item['down_id'] = urlencode(base64_encode(encrypt((string)$item['id'] . ':p')));
            return $item;
        });

        foreach ($publicDoc as $item) {
            $docs->add($item);
        }
        return array(
            'pagetitle' => 'Ваши документы',
            'breadcrumb' => array('index.php?m=LegalEntities' => 'Список документов'),
            'templatefile' => 'templates/client_doc.tpl',
            'requirelogin' => true,
            'forcessl' => false,
            'vars' => array(
                'docList' => $docs
            ),
        );
    } elseif ($_GET['page'] == 'acts') {
        //
        $docs = Invoice::where('paymentmethod', '=', 'LegalEntities')
            ->where('status', '=', 'Paid')
            ->where('userid', '=', $user_id)
            ->select(
                'mod_addon_legal_entities_acts.id as act_id',
                'mod_addon_legal_entities_acts.send_edf',
                'mod_addon_legal_entities_acts.send_mail',
                'tblinvoices.id',
                'tblinvoices.userid',
                'tblinvoices.datepaid',
                'tblinvoices.total',
                'tblinvoices.datepaid',
                'tblinvoices.subtotal',
            )
            ->leftJoin('mod_addon_legal_entities_acts', 'tblinvoices.id', '=', 'mod_addon_legal_entities_acts.rel_id')
            ->get()->transform(function ($item) {
                $item = $item->toArray();
                $item['down_id'] = urlencode(base64_encode(encrypt((string)$item['id'])));
                return $item;
            });;
        //  dump($docs);die();
        return array(
            'pagetitle' => 'Ваши акты',
            'breadcrumb' => array('index.php?m=LegalEntities' => 'Список актов'),
            'templatefile' => 'templates/client_acts.tpl',
            'requirelogin' => true,
            'forcessl' => false,
            'vars' => array(
                'docList' => $docs
            ),
        );
    }
}