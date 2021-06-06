<?php

namespace WHMCS\Module\Addon\LegalEntities\Pages;

use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;
use WHMCS\Module\Addon\LegalEntities\Interfaces\PageInterface;
use WHMCS\View\Menu\MenuFactory;
use WHMCS\Billing\Invoice;

class AdminActsPage implements PageInterface
{
    protected $templateName = 'admin_acts.tpl';
    protected $vars = [];

    function __construct()
    {
       $this->vars['docList'] = Invoice::with('client')
           ->where('paymentmethod','=','LegalEntities')
           ->where('status','=','Paid')
           ->select(
               'mod_addon_legal_entities_acts.id as act_id',
               'mod_addon_legal_entities_acts.send_edf',
               'mod_addon_legal_entities_acts.send_mail',
               'tblinvoices.id',
               'tblinvoices.userid',
               'tblinvoices.date',
               'tblinvoices.datepaid',
               'tblinvoices.subtotal',
           )
           ->leftJoin('mod_addon_legal_entities_acts', 'tblinvoices.id', '=', 'mod_addon_legal_entities_acts.rel_id')
           ->get();
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
            'Главная' => ModuleConfig::getModuleLink(),
            'Акты' => '',
        ];
    }
}