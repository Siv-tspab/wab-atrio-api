<?php

namespace App\Service;

class AgeService
{

    public function getAgeFromDate($date)
    {
        $age = date('Y') - $date->format('Y');

        return $age;
    }
}
