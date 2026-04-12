<?php

/*
	Author: Huy Nguyen
	Date: 2026-04-11
	File: ProductBatchRepository.php
	Description: This file contains the implementation of the ProductBatchRepositoryInterface, which is responsible for handling
*/

namespace App\Repositories\ProductBatch;

use App\Models\ProductBatche;
use App\Repositories\BaseRepository;

class ProductBatcheRepository extends BaseRepository implements ProductBatcheRepositoryInterface {

	public function getModel()
	{
		return ProductBatche::class;
	}

	public function getBatchesByProductId($productId, $perPage = 15)
	{
		return $this->model->where('product_id', $productId)->paginate($perPage);
	}
}

