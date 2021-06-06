<?php
namespace WHMCS\Module\Addon\LegalEntities\Configs;

use ArrayAccess;
use WHMCS\Module\Addon\LegalEntities\Abstracts\ItemConfigAbstracts;

class GroupConfig implements ArrayAccess
{
    private $name;
    private $sortId;

    /**
     * @var ItemConfigAbstracts[]
     */
    private $items;

    function __construct(string $name, int $sortId)
    {
        $this->name = $name;
        $this->sortId = $sortId;
    }

    function getName()
    {
        return $this->name;
    }

    function addItem(ItemConfigAbstracts $item)
    {
        $this->items[$item->getName()] = $item;
        return $this;
    }

    function toArray()
    {
        return [
            'items' => array_map(function ($itemConfig) {
                return $itemConfig->toArray();
            }, $this->items),
            'name' => $this->name,
            'sortId' => $this->getSortId(),
        ];
    }

    function getSortId()
    {
        return $this->sortId;
    }

    public function &iterate()
    {
        foreach ($this->items as &$v) {
            yield $v;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
}