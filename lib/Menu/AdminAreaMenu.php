<?php

namespace WHMCS\Module\Addon\LegalEntities\Menu;

use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;
use WHMCS\View\Menu\MenuFactory;

class AdminAreaMenu extends MenuFactory
{
    protected $rootItemName = "custom nav bar";

    public function navbar()
    {
        return $this->loader->load($this->buildMenuStructure($this->getNavBarStructure()));
    }

    protected function getNavBarStructure()
    {
        $menuItems = [
            [
                "name" => "index",
                "label" => 'Документы',
                "uri" => ModuleConfig::getModuleLink() . "&action=index",
                "order" => 1,
                "attributes" => [
                    "class" => !array_key_exists('action', $_GET) || $_GET['action'] === 'index' ? 'active' : ''
                ]
            ],[
                "name" => "shared",
                "label" => 'Общие документы',
                "uri" => ModuleConfig::getModuleLink() . "&action=shared",
                "order" => 1,
                "attributes" => [
                    "class" => array_key_exists('action', $_GET) &&$_GET['action'] === 'shared' ? 'active' : ''
                ]
            ],[
                "name" => "acts",
                "label" => 'Акты',
                "uri" => ModuleConfig::getModuleLink() . "&action=acts",
                "order" => 1,
                "attributes" => [
                    "class" => array_key_exists('action', $_GET) &&$_GET['action'] === 'acts' ? 'active' : ''
                ]
            ],
            [
                "name" => "settings",
                "label" => 'Настройки',
                "uri" => ModuleConfig::getModuleLink() . "&action=settings",
                "order" => 1,
                "attributes" => [
                    "class" => array_key_exists('action', $_GET) &&$_GET['action'] === 'settings' ? 'active' : ''
                ]
            ],
            [
                "name" => "demo",
                "label" => 'Превью счета',
                "uri" => ModuleConfig::getModuleLink() . "&action=demo",
                "order" => 1,
                "attributes" => [
                    "class" => array_key_exists('action', $_GET) && $_GET['action'] === 'demo' ? 'active' : ''
                ]
            ],
            [
                "name" => "demo2",
                "label" => 'Превью акта сверки',
                "uri" => ModuleConfig::getModuleLink() . "&action=demo2",
                "order" => 1,
                "attributes" => [
                    "class" => array_key_exists('action', $_GET) && $_GET['action'] === 'demo2' ? 'active' : ''
                ]
            ],
            [
                "name" => "cron",
                "label" => 'Крон',
                "uri" => ModuleConfig::getModuleLink() . "&action=cron",
                "order" => 1,
                "attributes" => [
                    "class" => array_key_exists('action', $_GET) && $_GET['action'] === 'cron' ? 'active' : ''
                ]
            ],
            [
                "name" => "log",
                "label" => 'Лог',
                "uri" => ModuleConfig::getModuleLink() . "&action=log",
                "order" => 1,
                "attributes" => [
                    "class" => array_key_exists('action', $_GET) && $_GET['action'] === 'log' ? 'active' : ''
                ]
            ],
        ];

        return $menuItems;
    }

}