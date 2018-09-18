<?php
namespace Filesystem\Models;

use Filesystem\Interfaces\FileSystemInterface;
use Filesystem\Services\FileSystemLocalService;
use Filesystem\Services\LocalStorageService;

/**
 * Class LocalStorageModel
 * @package Filesystem\Models
 */
class LocalStorageModel implements Storage
{
	/**
	 * @param $app
	 * @param $config
	 */
	public function bind($app, $config)
	{
		$app->bind(FileSystemInterface::class, function ($app) use ($config) {
			return new FileSystemLocalService(new LocalStorageService());
		});
	}
}
