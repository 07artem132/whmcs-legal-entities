<?php

namespace WHMCS\Module\Addon\LegalEntities\Models;

use WHMCS\Database\Capsule;
use WHMCS\Domain\Domain;
use WHMCS\Model\AbstractModel;
use WHMCS\Product\Addon;
use WHMCS\Product\Group;
use WHMCS\Product\Product;
use WHMCS\Service\Service;
use WHMCS\User\Client;

class SharedDocModel extends AbstractModel
{
    protected $table = "mod_addon_legal_entities_shared_doc";
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = [
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];

}