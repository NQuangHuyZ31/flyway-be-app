<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Repositories\Product\ProductRepositoryInterface;

class ProductService {

	public function __construct(protected ProductRepositoryInterface $productRepository)
	{
	}

	public function getAllProductWithPagination($request) {
		$perPage = $request->input('per_page' ?? 20);
		$filters = $request->input('filters' ?? []);

		return $this->productRepository->getAllWithFilterPaginate($perPage, $filters);
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

	// Check duplicate product code, sku
	public function checkDuplicate($field, $value, $id = null) {
		return $this->productRepository->checkDuplicate($field, $value, $id);
	}
}