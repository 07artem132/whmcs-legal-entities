<?php

namespace WHMCS\Module\Addon\LegalEntities\HtmlHelper;

use WHMCS\Module\Addon\LegalEntities\Abstracts\ItemTextConfigAbstracts;

class ItemTextHtmlHelper extends ItemTextConfigAbstracts
{
    public function setVal($val)
    {
        $this->val = $val;
        return $this;
    }

    public function getVal()
    {
        return $this->val;
    }
}