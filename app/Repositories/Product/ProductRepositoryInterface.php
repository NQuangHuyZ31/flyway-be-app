<?php
namespace App\Repositories\Product;

use App\Repositories\RepositoryInterface;

interface ProductRepositoryInterface extends RepositoryInterface {
	public function getAllWithFilterPaginate($perPage, $filters);
	public function getAllTotal();
	public function checkDuplicate($field, $value);
}