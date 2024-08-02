<?php

namespace App\Model;

class ProductListItem
{
    private int $id;
    private string $name;
    private string $cost;

    public function __construct(int $id, string $name, string $cost)
    {
        $this->id = $id;
        $this->name = $name;
        $this->cost = $cost;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
//
    public function getCost(): string
    {
        return $this->cost;
    }


}