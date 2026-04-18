<?php

namespace App\Services\StorageImage;

interface StorageImageServiceInterface
{
	public function getPresignedUrl($file, $folder);
}