<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class DeleteS3FileJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $filePath)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $s3Client = new \Aws\S3\S3Client([
            'region' => config('filesystems.disks.s3.region'),
            'version' => 'latest',
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);

        try {
            $s3Client->deleteObject([
                'Bucket' => config('filesystems.disks.s3.bucket'),
                'Key' => $this->filePath,
            ]);
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            Log::error('Failed to delete S3 file: ' . $e->getMessage());
        }
    }
}
