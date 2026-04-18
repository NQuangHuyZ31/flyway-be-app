<?php

namespace App\Services\StorageImage;

use Illuminate\Support\Str;
use Aws\S3\S3Client;

class S3StorageImageService implements StorageImageServiceInterface {

	public function getPresignedUrl($file, $folder)
	{
		$s3Client = new S3Client([
			'region' => config('filesystems.disks.s3.region'),
			'version' => 'latest',
			'credentials' => [
				'key' => config('filesystems.disks.s3.key'),
				'secret' => config('filesystems.disks.s3.secret'),
			],
		]);

		$fileName = $folder . '/' . Str::random(40) . '.' . $file->getClientOriginalExtension();

		$command = $s3Client->getCommand('PutObject', [
			'Bucket' => config('filesystems.disks.s3.bucket'),
			'Key' => $fileName,
			'ContentType' => $file->getMimeType(),
		]);

		$request = $s3Client->createPresignedRequest($command, '+1 hour');

		return [
			'presignedURL' => (string) $request->getUri(),
			'path' => $fileName,
		];
	}
}