<?php

namespace App\Traits;

trait LangMapping
{
	public static function mapLang($data)
	{
		return collect($data)->map(function ($key) {
			return [
				'key' => $key,
				'label' => __("product.$key"),
			];
    	})->values();
	}
}