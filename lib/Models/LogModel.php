<?php
namespace WHMCS\Module\Addon\LegalEntities\Models;

use WHMCS\Model\AbstractModel;

class LogModel extends AbstractModel
{
    protected $table = "mod_addon_legal_entities_log";
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = [
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];
}