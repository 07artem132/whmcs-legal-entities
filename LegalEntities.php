<?php

use WHMCS\Domain\Domain;
use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;
use WHMCS\Module\Addon\LegalEntities\Controllers\PageController;
use WHMCS\Module\Addon\LegalEntities\Controllers\PdfController;
use WHMCS\Module\Addon\LegalEntities\Menu\AdminAreaMenu;
use WHMCS\Module\Addon\LegalEntities\Controllers\InstallController;
use WHMCS\Module\Addon\LegalEntities\Controllers\UninstallController;
use WHMCS\Module\Addon\LegalEntities\Models\DocModel;
use WHMCS\Module\Addon\Setting;
use WHMCS\Service\Addon;
use WHMCS\Service\Service;
use WHMCS\Session;


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
    //dd(UninstallController::dropTable('mod_addon_legal_entities_doc'),InstallController::createTableDoc());
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
    if (array_key_exists('fid', $_GET)) {
        $id = decrypt(base64_decode(rawurldecode($_GET['fid'])));
        ob_end_clean();
        ob_implicit_flush();
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
    $user_id = (int)Session::get("uid");
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
        $item['down_id'] = urlencode(base64_encode(encrypt((string)$item['id'])));
        return $item;
    });
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
}