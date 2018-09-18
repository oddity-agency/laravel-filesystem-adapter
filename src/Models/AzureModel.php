<?php
/**
 * Created by PhpStorm.
 * User: milosradojicic
 * Date: 9/6/18
 * Time: 10:44 AM
 */

namespace Filesystem\Models;

use Filesystem\Adapters\AzureFileAdapter;
use Filesystem\Interfaces\FileSystemInterface;
use Filesystem\Services\AzureStorageService;

class AzureModel implements Storage
{
    public $container;

    public function bind($app, $config)
	{
        $app->bind(Storage::class, function ($app) use ($config) {
            $azureModel = new AzureModel();
            return $azureModel->build($config);
        });

	    $app->bind(FileSystemInterface::class, function ($app) use ($config) {
	       return new AzureStorageService($config);
        });
	}

	public function build($config)
    {
        $this->container = $config['container'];
        return $this;
    }
}
