<?php

namespace WHMCS\Module\Addon\LegalEntities\Pages;

use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;
use WHMCS\Module\Addon\LegalEntities\Interfaces\PageInterface;
use WHMCS\Module\Addon\LegalEntities\Models\LogModel;
use WHMCS\View\Menu\MenuFactory;

class AdminCronPage implements PageInterface
{
    protected $templateName = 'admin_cron.tpl';
    protected $vars = [];
    function __construct()
    {
        $this->vars['lastCronEvent'] = LogModel::where('status', 1)->where('module', 'cron')->orderBy('created_at', 'DESC')->first();
        $this->vars['cronPath'] = ModuleConfig::getWhmcsRootDir() . '/modules/addons/' . ModuleConfig::getModuleName() . '/cron.php';
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
            'Главная' => '',
        ];
    }
}