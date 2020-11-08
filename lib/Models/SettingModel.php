<?php

namespace WHMCS\Module\Addon\LegalEntities\Models;

use WHMCS\Model\AbstractModel;

class SettingModel extends AbstractModel
{
    protected $table = "mod_addon_legal_entities_setting";
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = [
        'key',
        'val',
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];

}