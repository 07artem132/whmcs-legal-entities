<?php

namespace WHMCS\Module\Addon\LegalEntities\HtmlHelper;

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\LegalEntities\Abstracts\ItemSelectConfigAbstracts;

class ItemSelectClientGroupHtmlHelper extends ItemSelectConfigAbstracts
{
    protected $defaultName;
    protected $groups;

    function __construct()
    {
        $result = array_column(Capsule::table("tblclientgroups")->get(), 'groupname', 'id');
        $this->groups = $result;
        foreach ($this->groups as $group_id => $group_name) {
            $this->addSelectAllow($group_id, $group_name);
        }
    }

    public function filtered()
    {
        $this->clearSelectAllow()
            ->clearSelectDisable();

        if ($this->createOptionMakeAChoice)
            $this->addSelectDisable('', 'Не выбрано');

        foreach ($this->groups as $group_id => $group) {
            $this->addSelectAllow($group_id, $group);
        }

        if ($this->val != null && $this->groups[$this->val] == $this->defaultName)
            $this->addSelectAllow(-1, sprintf("Обновить с именем \"%s\"", $this->defaultName));
        else
            $this->addSelectAllow(-1, sprintf("Создать с именем \"%s\"", $this->defaultName));
    }

    public function setVal($val)
    {
        if ($val == -1 && $this->defaultName != null) {
            $group = Capsule::table("tblclientgroups")
                ->where("groupname", '=', $this->defaultName)
                ->first();
            if ($group !== null)
                return $this;

            $id = Capsule::table("tblclientgroups")->insertGetId([
                "groupname" => $this->defaultName
            ]);

            $this->groups[$id] = $this->defaultName;
            $val = $id;
        }

        if (!array_key_exists($val, $this->groups))
            $val = '';

        parent::setVal($val);
        $this->filtered();
        return $this;
    }

    public function setDefaultName($groupName)
    {
        $this->defaultName = $groupName;
        $this->filtered();
        return $this;
    }

}