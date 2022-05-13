<?php

namespace App\Tool;

class Search
{
    public function test($data): mixed
    {
        stripslashes($data);
        htmlspecialchars($data);
        trim($data);
        return $data;
    }
}