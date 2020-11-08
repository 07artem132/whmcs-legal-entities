<?php

namespace WHMCS\Module\Addon\LegalEntities\Pages;

use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;
use WHMCS\Module\Addon\LegalEntities\Models\LogModel;
use WHMCS\View\Menu\MenuFactory;
use WHMCS\Module\Addon\LegalEntities\Interfaces\PageInterface;

class AdminLogPage implements PageInterface
{
    protected $templateName = 'admin_log.tpl';
    protected $vars = [];
    function __construct()
    {
        $this->vars['logs'] = LogModel::orderBy('id', 'desc')->get();
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