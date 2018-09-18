<?php

namespace Filesystem\Models;

use Filesystem\Interfaces\FileSystemInterface;
use Filesystem\Services\FileSystemS3Service;

/**
 * Class S3Model
 * @package Filesystem\Models
 */
class S3Model implements Storage
{
	/**
	 * @var string
	 */
    public $bucket;

	/**
	 * @param $app
	 * @param $config
	 * @return mixed|void
	 */
	public function bind($app, $config)
	{
        $app->bind(Storage::class, function ($app) use ($config) {
            $s3Model = new S3Model();
            return $s3Model->build($config);
        });

		$app->bind(FileSystemInterface::class, function ($app) use ($config) {
			return new FileSystemS3Service(new \Filesystem\Services\AWSStorageService(), $config);
		});
	}

	/**
	 * @param $config
	 * @return $this
	 */
	public function build($config)
    {
        $this->bucket = $config['bucket'];
        return $this;
    }
}
