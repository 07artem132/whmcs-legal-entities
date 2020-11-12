<?php

namespace WHMCS\Module\Addon\LegalEntities\Pages;

use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;
use WHMCS\Module\Addon\LegalEntities\Controllers\LogController;
use WHMCS\Module\Addon\LegalEntities\Models\DocModel;
use WHMCS\View\Menu\MenuFactory;
use WHMCS\Module\Addon\LegalEntities\Interfaces\PageInterface;

class AdminDeletePage implements PageInterface
{
    protected $templateName = 'admin_index.tpl';
    protected $vars = [];

    function __construct()
    {
        try {
            if (!array_key_exists('id', $_GET))
                redir(sprintf('module=%s&action=index', ModuleConfig::getModuleName()), 'addonmodules.php');

            $result = DocModel::findOrFail($_GET['id']);

            if (unlink($result->file))
                LogController::addSuccess(__CLASS__, sprintf('adminid->%s remove file doc->%s', $_SESSION['adminid'], $_GET['id']));
            else
                LogController::addError(__CLASS__, sprintf('adminid->%s error remove file doc->%s', $_SESSION['adminid'], $_GET['id']));

            $result->delete();
            LogController::addSuccess(__CLASS__, sprintf('adminid->%s remove doc->%s', $_SESSION['adminid'], $_GET['id']));

            redir(sprintf('module=%s&action=index', ModuleConfig::getModuleName()), 'addonmodules.php');
        } catch (\Throwable $e) {
            LogController::addError(__CLASS__, sprintf('adminid->%s', $_SESSION['adminid']), $e);
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