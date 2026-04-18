<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\BaseRepository;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface {
	
	public function getModel()
	{
		return Product::class;
	}

	public function getAllWithFilterPaginate($perPage, $filters)
	{
		$query = $this->model->newQuery();

		if (!empty($filters)) {
			$query = $this->scopeFilter($query, $filters, $this->model);
		}

		return $query->paginate($perPage);
	}

	public function getAllTotal()
	{
		return count($this->all());
	}

	public function checkDuplicate($field, $value)
	{
		return $this->model->where($field, $value)->exists();
	}
}