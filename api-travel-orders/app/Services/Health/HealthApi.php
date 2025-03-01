<?php

namespace App\Services\Health;

class HealthApi
{
    public function getInfoPod(): array
    {
        $podName = getHostName();
        $podIp = getHostByName($podName);
        return [
            "pod" => $podName,
            "ip" => $podIp
        ];
    }

    public function loadGenerator()
    {
        // Simulando o uso de memória
        $largeArray = [];
        for ($i = 0; $i < 299999; $i++) {
            $largeArray[] = str_repeat("A", 1024);
        }


        $factorial = 1;
        for ($i = 1; $i <= 10000; $i++) {
            $factorial *= $i;
        }
        unset($largeArray, $factorial);
    }
}
