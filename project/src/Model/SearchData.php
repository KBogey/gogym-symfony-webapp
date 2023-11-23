<?php

namespace App\Model;

class SearchData
{
    /**
     * @var int
     */
    public int $page = 1;

    /**
     * @var string
     */
    public string $q = '';

    /**
     * @var array
     */
    public array $categories = [];

}