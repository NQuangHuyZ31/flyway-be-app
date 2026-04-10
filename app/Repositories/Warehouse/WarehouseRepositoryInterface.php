<?php

namespace App\Repositories\Warehouse;

use App\Repositories\RepositoryInterface;

interface WarehouseRepositoryInterface extends RepositoryInterface
{
    public function getAllWithPagination($perPage);
    public function getAllActive($perPage);
}
