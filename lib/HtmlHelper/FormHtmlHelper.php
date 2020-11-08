<?php


namespace WHMCS\Module\Addon\LegalEntities\HtmlHelper;

use WHMCS\Model\AbstractModel;
use WHMCS\Module\Addon\LegalEntities\Abstracts\ItemConfigAbstracts;
use WHMCS\Module\Addon\LegalEntities\Abstracts\ItemFileConfigAbstracts;
use WHMCS\Module\Addon\LegalEntities\Models\SettingModel;

class FormHtmlHelper
{
    /**
     * @var FormGroupHtmlHelper[]
     */
    private $groups;
    /**
     * @var AbstractModel
     */
    private $model;
    private $keyVal;
    private $keyID = null;

    function __construct(AbstractModel $model, bool $keyVal = false)
    {
        $this->model = $model;
        $this->keyVal = $keyVal;
    }

    public function addGroup(FormGroupHtmlHelper $group)
    {
        $this->groups[] = $group;
        return $this;
    }

    public function getSettings()
    {
        return $this->groups;
    }

    public function getSetting($group, $name)
    {
        return $this->groups[$group][$name]->getVal();
    }

    public function saveForm(array $array, array $files)
    {
        foreach ($this->groups as $groupConfig) {
            foreach ($groupConfig->iterate() as $item) {
                /**
                 * @var $item ItemConfigAbstracts
                 */
                if (array_key_exists($item->getName(), $array)) {
                    $item->setVal($array[$item->getName()]);
                } elseif ($item instanceof ItemFileConfigAbstracts && array_key_exists($item->getName(), $files)) {
                    /**
                     * @var $item ItemFileConfigAbstracts
                     */
                    $item->uploadFile(true);
                }
            }
        }

        if ($this->keyVal === true) {
            foreach ($this->groups as $groupConfig) {
                foreach ($groupConfig->iterate() as $item) {
                    $dbItem = $this->model->firstOrCreate(['key' => $item->getName()]);
                    $dbItem->val = $item->getVal();
                    $dbItem->saveOrFail();
                }
            }
        } else {
            if ($this->keyID != null) {
                $this->model = $this->model->findOrFail($this->keyID);
            }
            foreach ($this->groups as $groupConfig) {
                foreach ($groupConfig->iterate() as $item) {
                    $this->model->setAttribute($item->getName(), $item->getVal());
                }
            }
            $this->model->saveOrFail();
        }

        return $this->groups;
    }

    public function loadForm(?int $id)
    {
        if ($this->keyVal === true) {
            $modelResult = $this->model
                ->get()
                ->keyBy('key')
                ->transform(function ($item) {
                    return $item->val;
                })
                ->toArray();
        } else {
            $this->keyID = $id;
            $modelResult = $this->model->findOrFail($id)->toArray();
        }
        foreach ($this->groups as $groupConfig) {
            foreach ($groupConfig->iterate() as $item) {
                /**
                 * @var $item ItemConfigAbstracts
                 */
                if (array_key_exists($item->getName(), $modelResult)) {
                    $item->setVal($modelResult[$item->getName()]);
                }
            }
        }
    }

    public function getSettingsAsArray(): array
    {
        $result = [];
        foreach ($this->groups as $group) {
            $result[] = $group->toArray();
        }
        usort($result, function ($item1, $item2) {
            return $item1['sortId'] <=> $item2['sortId'];
        });
        $result = array_column($result, 'items', 'name');

        return $result;
    }
}