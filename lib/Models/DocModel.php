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

class DocModel extends AbstractModel
{
    protected $table = "mod_addon_legal_entities_doc";
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = [
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function getClientNameAttribute(): string
    {
        $client = Client::findOrFail($this->client_id);
        return $client->firstname . ' ' . $client->lastname;
    }

    public function getClientCompanyAttribute(): string
    {
        $client = Client::findOrFail($this->client_id);
        return $client->companyname;
    }

    public function getClientIdAttribute(): int
    {
        switch ($this->rel_type) {
            case 1:
                return (int)Service::findOrFail($this->rel_id)->userid;
            case 2:
                return (int)\WHMCS\Service\Addon::findOrFail($this->rel_id)->userid;
            case 3:
                return (int)Domain::findOrFail($this->rel_id)->userid;
            case 4:
                return (int)$this->rel_id;
            default:
                throw  new \Exception("Неизвестный тип связи");
        }
    }

    public function getServiceUrlAttribute(): string
    {
        switch ($this->rel_type) {
            case 1:
                return 'clientsservices.php?productselect=' . $this->rel_id;
            case 2:
                return 'clientsservices.php?aid=' . $this->rel_id;
            case 3:
                return 'clientsdomains.php?id=' . $this->rel_id;
            case 4:
                return 'clientsservices.php?userid=' . $this->rel_id;
        }
    }

    public function getProductNameAttribute(): string
    {
        try {
            switch ($this->rel_type) {
                case 1:
                    $rel_id = Service::findOrFail($this->rel_id)->packageid;
                    $product = Product::findOrFail($rel_id);
                    return Group::find($product->gid)->name . '\\' . $product->name;
                    break;
                case 2:
                    $rel_id = \WHMCS\Service\Addon::findOrFail($this->rel_id)->addonid;
                    return 'Дополнение\\' . Addon::findOrFail($rel_id)->name;
                    break;
                case 3:
                    $rel_id = Capsule::table('tbldomainpricing')->where('extension', '.' . Domain::findOrFail($this->rel_id)->tld)->first()->id;
                    $domain = Capsule::table('tbldomainpricing')->where('id', $rel_id)->first();
                    if (empty($domain)) {
                        throw new \Exception('Вероятно удален домен');
                    }
                    return 'Домен\\' . $domain->extension;
                    break;
                case 4:
                    return 'Нет связи с услугой';
                    break;
                default:
                    return 'unknown  type';

            }
        } catch (\Throwable $e) {
            return 'Вероятно удален продукт';
        }
    }

}