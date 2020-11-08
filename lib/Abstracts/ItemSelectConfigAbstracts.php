<?php

namespace WHMCS\Module\Addon\LegalEntities\Abstracts;

use WHMCS\Module\Addon\LegalEntities\Models\SettingModel;

abstract class ItemSelectConfigAbstracts extends ItemConfigAbstracts
{
    protected $type = 'select';
    protected $selected = null;
    protected $selectDisable;
    protected $selectAllow;
    protected $allowMultiple = false;
    protected $val = null;
    protected $createOptionMakeAChoice = false;

    public function makeAChoice()
    {
        $this->createOptionMakeAChoice = true;
        return $this;
    }

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
            'selected' => $this->getSelected(),
            'selectDisable' => $this->getSelectDisable(),
            'allowMultiple' => $this->isAllowMultiple(),
            'selectAllow' => $this->getSelectAllow(),
        ];
    }

    public function getSelected()
    {
        return $this->selected;
    }

    function addSelected(string $var)
    {
        $this->selected[] = $var;
        return $this;
    }

    function isAllowMultiple()
    {
        return $this->allowMultiple;
    }

    function allowMultiple()
    {
        $this->allowMultiple = true;
        return $this;
    }

    function addSelectDisable(string $value, string $text)
    {
        $this->selectDisable[] = ['value' => $value, 'text' => $text];
        return $this;
    }

    function getSelectDisable()
    {
        return $this->selectDisable;
    }

    function addSelectAllow(string $value, string $text)
    {
        $this->selectAllow[] = ['value' => $value, 'text' => $text];
        return $this;
    }

    function clearSelectAllow()
    {
        $this->selectAllow = [];
        return $this;
    }

    function clearSelectDisable()
    {
        $this->selectDisable = [];
        return $this;
    }

    function getSelectAllow()
    {
        return $this->selectAllow;
    }

    public function setVal($val)
    {
        if (is_array($val)) {
            $this->selected = $val;
            $this->val = $this->serialiseArray($val);
        } else if (strpos($val, ',', 0)) {
            $this->val = $val;
            $this->selected = $this->deserialiseArray($val);
        } else {
            $this->selected = [$val];
            $this->val = $val;
        }

        return $this;
    }

    public function getVal()
    {
        return $this->val;
    }
}