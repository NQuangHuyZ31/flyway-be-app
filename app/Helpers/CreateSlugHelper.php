<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class CreateSlugHelper {

	public static function createSlug($string) {
		return Str::slug($string);
	}
}