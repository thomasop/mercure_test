<?php

namespace App\Tool;

class Search
{
    public function test(mixed $data): mixed
    {
        stripslashes($data);
        htmlspecialchars($data);
        trim($data);
        return $data;
    }
}