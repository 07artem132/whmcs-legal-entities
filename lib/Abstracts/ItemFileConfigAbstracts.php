<?php

namespace WHMCS\Module\Addon\LegalEntities\Abstracts;

use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;
use WHMCS\Module\Addon\LegalEntities\Models\SettingModel;

abstract class ItemFileConfigAbstracts extends ItemConfigAbstracts
{
    protected $isLoaded = false;
    protected $fileUrl;
    protected $type = 'file';
    protected $val = null;

    public function getFileUrl()
    {
        return $this->fileUrl;
    }

    public function loaded()
    {
        $this->isLoaded = true;
    }

    public function isLoaded()
    {
        return $this->isLoaded;
    }

    public function uploadFile(bool $nameAsHash = false)
    {
        if ($_FILES[$this->name]['error'] != 0)
            return $this;

        $ext = pathinfo($_FILES[$this->name]['name'], PATHINFO_EXTENSION);

        if ($nameAsHash)
            $uploadFile = ModuleConfig::geUploadPath() . '/' . hash_file('sha512', $_FILES[$this->name]['tmp_name']) . '.' . $ext;
        else
            $uploadFile = ModuleConfig::geUploadPath() . '/' . $this->name . '.' . $ext;

        if (!move_uploaded_file($_FILES[$this->name]['tmp_name'], $uploadFile))
            throw new \Exception('Возможная атака с помощью файловой загрузки!');

        $this->val = $uploadFile;
        return $this;
    }

    public function setVal($val)
    {
        $this->val = $val;
        if ($this->val != null && file_exists($this->val)) {
            $this->loaded();
            $this->fileUrl = ModuleConfig::geLinkUploadedFile(basename($this->val));
        }

        return $this;
    }

    public function getVal()
    {
        return $this->val;
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
            'isLoaded' => $this->isLoaded(),
            'fileUrl' => $this->getFileUrl(),
        ];
    }


}