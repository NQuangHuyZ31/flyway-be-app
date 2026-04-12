<?php

namespace App\Traits;

trait LangMapping
{
	public static function mapLang($filters, $model)
	{
		return collect($filters)->map(function ($config, $key) use ($model) {

			// Nếu là dạng ['price', 'name']
			if (is_numeric($key)) {
				$key = $config;
				$config = [];
			}

			return [
				'key'   => $key,
				'label' => __("$model.$key"),
				'type'  => $config['type'] ?? 'string',
			];
		})->values();
	}
}