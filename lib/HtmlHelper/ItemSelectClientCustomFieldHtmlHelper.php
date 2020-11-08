<?php

namespace WHMCS\Module\Addon\LegalEntities\HtmlHelper;

use WHMCS\CustomField;
use WHMCS\Module\Addon\LegalEntities\Abstracts\ItemSelectConfigAbstracts;

class ItemSelectClientCustomFieldHtmlHelper extends ItemSelectConfigAbstracts
{
    protected $defaultName;
    protected $customFields;
    protected $typeCustomFieldDefault;
    protected $allowTypeCustomFields;
    protected $defaultDescription = "";
    protected $defaultFieldOptions = [];
    protected $defaultSortOrder = 0;
    protected $defaultShowInvoice = "";
    protected $defaultShowOrder = "";
    protected $defaultAdminOnly = "";
    protected $defaultRegexpr = "";
    protected $defaultRequired = "";

    function __construct()
    {
        $result = CustomField::ClientFields()->get()->keyBy('id');

        $this->customFields = $result;
    }

    public function filtered()
    {
        $this->clearSelectAllow()
            ->clearSelectDisable();

        if ($this->createOptionMakeAChoice)
            $this->addSelectDisable('', 'Не выбрано');

        foreach ($this->customFields as $group_id => $group) {
            if (in_array($group->fieldtype, $this->allowTypeCustomFields))
                $this->addSelectAllow($group_id, sprintf("%s (%s)", $group->fieldname, $group->fieldtype));
            else
                $this->addSelectDisable($group_id, sprintf("%s (%s)", $group->fieldname, $group->fieldtype));
        }
        $this->setDescription(sprintf(
            'Разрешены типы: %s',
            implode(',', $this->allowTypeCustomFields)
        ));

        if ($this->val != null && $this->customFields[$this->val]->fieldname == $this->defaultName)
            $this->addSelectAllow(-1, sprintf("Обновить с именем \"%s\"", $this->defaultName));
        else
            $this->addSelectAllow(-1, sprintf("Создать с именем \"%s\"", $this->defaultName));

    }

    public function setVal($val)
    {
        if ($val == -1 && $this->defaultName != null && $this->typeCustomFieldDefault != null) {
            $customField = CustomField::firstOrNew([
                "fieldName" => $this->defaultName,
                "type" => "client",
                "fieldType" => $this->typeCustomFieldDefault,
            ]);
            $customField->regexpr = $this->defaultRegexpr;
            $customField->description = $this->defaultDescription;
            $customField->fieldoptions = implode(',', $this->defaultFieldOptions);
            $customField->adminonly = $this->defaultAdminOnly;
            $customField->required = $this->defaultRequired;
            $customField->showinvoice = $this->defaultShowInvoice;
            $customField->showorder = $this->defaultShowOrder;
            $customField->sortorder = $this->defaultSortOrder;
            $customField->saveOrFail();
            $this->customFields[$customField->id] = $customField;
            $val = $customField->id;
        }

        if (!$this->customFields->has($val))
            $val = '';

        parent::setVal($val);
        $this->filtered();
        return $this;
    }

    public function setDefaultName($name)
    {
        $this->defaultName = $name;
        $this->filtered();
        return $this;
    }

    public function setDefaultRequired()
    {
        $this->defaultRequired = 'on';
        return $this;
    }

    public function setDefaultRegexpr($name)
    {
        $this->defaultRegexpr = $name;
        return $this;
    }

    public function setDefaultAdminOnly()
    {
        $this->defaultAdminOnly = 'on';
        return $this;
    }

    public function setDefaultShowOrder()
    {
        $this->defaultShowOrder = 'on';
        return $this;
    }

    public function setDefaultShowInvoice()
    {
        $this->defaultShowInvoice = 'on';
        return $this;
    }

    public function setDefaultSortOrder(int $orderId)
    {
        $this->defaultSortOrder = $orderId;
        return $this;
    }

    public function setDefaultAddFieldOption(string $option)
    {
        $this->defaultFieldOptions[] = $option;
        return $this;
    }

    public function setDefaultDescription($name)
    {
        $this->defaultDescription = $name;
        return $this;
    }

    public function setDefaultType($type)
    {
        $this->typeCustomFieldDefault = $type;
        $this->filtered();
        return $this;
    }

    public function textBox()
    {
        $this->allowTypeCustomFields[] = 'text';
        $this->filtered();
        return $this;
    }

    public function url()
    {
        $this->allowTypeCustomFields[] = 'link';
        $this->filtered();
        return $this;
    }

    public function password()
    {
        $this->allowTypeCustomFields[] = 'password';
        $this->filtered();
        return $this;
    }

    public function dropDown()
    {
        $this->allowTypeCustomFields[] = 'dropdown';
        $this->filtered();
        return $this;
    }

    public function yesNo()
    {
        $this->allowTypeCustomFields[] = 'tickbox';
        $this->filtered();
        return $this;
    }

    public function textArea()
    {
        $this->allowTypeCustomFields[] = 'textarea';
        $this->filtered();
        return $this;
    }

}