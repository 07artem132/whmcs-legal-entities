<?php

namespace WHMCS\Module\Addon\LegalEntities\Abstracts;

use WHMCS\Module\Addon\LegalEntities\Models\SettingModel;

abstract class ItemConfigAbstracts
{
    protected $label;
    protected $name;
    protected $description = '';
    protected $class = '';
    protected $required = false;
    protected $type = '';
    protected $val = null;

    public function getType()
    {
        return $this->type;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function setLabel(string $label)
    {
        $this->label = $label;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    abstract function toArray(): array;

    abstract function setVal($val);

    abstract function getVal();

    public function setClass(string $class)
    {
        $this->class = $class;
        return $this;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function required()
    {
        $this->required = true;
        return $this;
    }

    public function isRequired()
    {
        return $this->required;
    }

    protected function serialiseArray($array)
    {
        return implode(',', $array);
    }

    protected function deserialiseArray($array)
    {
        return explode(',', $array);
    }
}