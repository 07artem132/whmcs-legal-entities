<?php

namespace WHMCS\Module\Addon\LegalEntities\Pages;

use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;
use WHMCS\Module\Addon\LegalEntities\Controllers\InvoiceFormatterController;
use WHMCS\Module\Addon\LegalEntities\Controllers\PdfController;
use WHMCS\Module\Addon\LegalEntities\Models\DocModel;
use WHMCS\Module\Addon\LegalEntities\Models\SharedDocModel;
use WHMCS\View\Menu\MenuFactory;
use WHMCS\Module\Addon\LegalEntities\Interfaces\PageInterface;
use WHMCS\Billing\Invoice;

class AdminDownloadPage implements PageInterface
{
    protected $templateName = 'admin_index.tpl';
    protected $vars = [];

    function __construct()
    {
        if (!array_key_exists('id', $_GET))
            redir(sprintf('module=%s&action=index', ModuleConfig::getModuleName()), 'addonmodules.php');
        ob_end_clean();
        ob_implicit_flush();
        if (!array_key_exists('type', $_GET)) {
            $model = DocModel::findOrFail($_GET['id']);
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
        } elseif ($_GET['type'] == 'shared') {
            $model = SharedDocModel::findOrFail($_GET['id']);
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
        } elseif ($_GET['type'] == 'act') {
            $invoice = Invoice::findOrFail(intval($_GET['id']));
            $file_name = "акт ".$_GET['id']." от ".$invoice->date->format('d.m.Y').".pdf";
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
            setlocale(LC_TIME, 'ru_RU.UTF-8', 'Rus');
            echo PdfController::renderReconciliationAct(intval($_GET['id']));
            die();
        }
    }

    /**
     * @return string
     */
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

    /**
     * @return MenuFactory|null
     */
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
            'Главная' => ModuleConfig::getModuleLink(),
            'Лог' => '',
        ];
    }
}