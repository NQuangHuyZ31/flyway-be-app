<?php

/*
	Author: Huy Nguyen
	Date: 2026-04-11
	File: ProductBatchService.php
	Description: This file contains the implementation of the ProductBatchService, which is responsible for handling
*/

namespace App\Services\ProductBatch;

use App\Repositories\ProductBatch\ProductBatcheRepositoryInterface;

class ProductBatcheService {

	public function __construct(protected ProductBatcheRepositoryInterface $productBatchRepository)
	{
	}

	public function getBatchesByProductId($productId, $perPage = 15)
	{
		return $this->productBatchRepository->getBatchesByProductId($productId, $perPage);
	}
}