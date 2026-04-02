<?php

namespace App\Repositories\Products;

use App\Models\Product;
use App\Repositories\BaseRepository;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface {
	
	public function getModel()
	{
		return Product::class;
	}

	public function getAllWithPagination($perPage)
	{
		return Product::paginate($perPage);
	}

	public function getAllTotal()
	{
		return count($this->all());
	}
}