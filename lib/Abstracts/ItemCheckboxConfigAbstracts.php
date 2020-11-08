<?php

namespace WHMCS\Module\Addon\LegalEntities\Abstracts;

use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;
use WHMCS\Module\Addon\LegalEntities\Models\SettingModel;

abstract class ItemCheckboxConfigAbstracts extends ItemConfigAbstracts
{
    protected $type = 'checkbox';
    protected $val = null;

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'label' => $this->getLabel(),
            'class' => $this->getClass(),
            'type' => $this->getType(),
            'required' => $this->isRequired(),
            'val' => $this->getVal(),
        ];
    }
    public function setVal($val)
    {
        $this->val = $val;
        return $this;
    }

    public function getVal()
    {
        return $this->val;
    }

}