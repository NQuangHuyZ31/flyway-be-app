<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait FilterDataTrait
{
    public function scopeFilter($query, array $filters, $model)
    {
        return $this->applyFilters($query, $filters, $model::FILTERS);
    }

	private function applyFilters($query, array $filters, array $filterable)
	{
		foreach ($filters as $key => $value) {

			if (!array_key_exists($key, $filterable) || $value === null || $value === '') {
				continue;
			}

			// skip "all"
			if ($key === 'is_active' && $value === 'all') {
				continue;
			}

			$type = $filterable[$key]['type'] ?? 'default';

			switch ($type) {
				case 'string':
					$query->where($key, 'like', "%$value%");
					break;

				case 'date':
					if ($key === 'created_at') {
						$query->whereDate('created_at', '=', $value);
					} elseif ($key === 'updated_at') {
						$query->whereDate('updated_at', '=', $value);
					}
					break;

				default:
					$query->where($key, $value);
					break;
			}
		}

		Log::info('SQL:', [$query->toSql()]);
		Log::info('Bindings:', $query->getBindings());

		return $query;
	}
}