<?php
namespace WHMCS\Module\Addon\LegalEntities\Interfaces;

use WHMCS\View\Menu\MenuFactory;

interface  PageInterface
{
    /**
     * @return string
     */
    public function getTemplateName(): string;

    /**
     * @return array
     */
    public function getVars(): array;

    /**
     * @return MenuFactory|null
     */
    public function getSubMenu(): ?MenuFactory;

    /**
     * @return array
     */
    public function getBreadcrumb(): array;
}