<?php

namespace App\Traits;

use App\Models\Product;

trait FilterDataTrait
{
	public function filterData($query, $filters, $model)
	{
		foreach ($filters as $key => $value) {
			if (in_array($key, $model::FILTERS)) {
				$query->where($key, $value);
			}
		}
		return $query;
	}
}