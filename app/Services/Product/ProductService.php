<?php

namespace App\Services\Product;

use App\Repositories\Products\ProductRepositoryInterface;

class ProductService {

	public function __construct(protected ProductRepositoryInterface $productRepository)
	{
	}

	public function getAllProductWithPagination($request) {
		$perPage = $request->input('per_page');

		return $this->productRepository->getAllWithPagination($perPage);
	}

	public function getAllTotal() {
		return $this->productRepository->getAllTotal();
	}
}