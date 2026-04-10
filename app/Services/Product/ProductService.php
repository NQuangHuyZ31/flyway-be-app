<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Repositories\Products\ProductRepositoryInterface;

class ProductService {

	public function __construct(protected ProductRepositoryInterface $productRepository)
	{
	}

	public function getAllProductWithPagination($request) {
		$perPage = $request->input('per_page');

		return $this->productRepository->getAllWithPagination($perPage);
	}

	public function getProductById($id) {
		return $this->productRepository->find($id);
	}

	public function getAllTotal() {
		return $this->productRepository->getAllTotal();
	}

	public function createProduct(array $data): Product {
		return $this->productRepository->create($data);
	}

	public function updateProduct($id, array $data): bool {
		return $this->productRepository->update($id, $data);
	}

	public function deleteProduct($id) {
		if (!$this->productRepository->delete($id)) {
			return false;
		}

		return true;
	}
}