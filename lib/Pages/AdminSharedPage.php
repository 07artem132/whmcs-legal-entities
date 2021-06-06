<?php

namespace WHMCS\Module\Addon\LegalEntities\Pages;

use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;
use WHMCS\Module\Addon\LegalEntities\Interfaces\PageInterface;
use WHMCS\Module\Addon\LegalEntities\Models\DocModel;
use WHMCS\Module\Addon\LegalEntities\Models\LogModel;
use WHMCS\Module\Addon\LegalEntities\Models\SharedDocModel;
use WHMCS\View\Menu\MenuFactory;

class AdminSharedPage implements PageInterface
{
    protected $templateName = 'admin_shared.tpl';
    protected $vars = [];

    function __construct()
    {
         $this->vars['docList'] = SharedDocModel::all();
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