<?php

namespace App\Repositories\ProductBatch;

interface ProductBatcheRepositoryInterface
{
	public function getBatchesByProductId($productId, $perPage = 15);
}