<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.02.2020, 5:57
 *
 */
namespace WHMCS\Module\Addon\LegalEntities\Interfaces;


interface TaskInterfaces
{
    public function getName(): string;

    public function run(): void;

    public function getFrequency(): string;

}