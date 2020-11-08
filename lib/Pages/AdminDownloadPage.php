<?php

namespace WHMCS\Module\Addon\LegalEntities\Pages;

use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;
use WHMCS\Module\Addon\LegalEntities\Models\DocModel;
use WHMCS\View\Menu\MenuFactory;
use WHMCS\Module\Addon\LegalEntities\Interfaces\PageInterface;

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