<?php

namespace App\Service;

class MaxfieldHelper
{


    public function getGpx(string $rootDir)
    {
        $parser = new MaxfieldParser($rootDir);
    }
}
