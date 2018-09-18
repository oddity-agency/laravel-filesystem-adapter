<?php
namespace Filesystem;

use Exception;
use Filesystem\Models\AzureModel;
use Filesystem\Models\LocalStorageModel;
use Filesystem\Models\S3Model;
use Illuminate\Support\ServiceProvider;
use Filesystem\Interfaces\FileSystemInterface;

class FileStorageServiceProvider extends ServiceProvider
{

	protected $supported = ['s3', 'azure', 'local'];

	protected $models = [
		's3'    => S3Model::class,
		'azure' => AzureModel::class,
		'local' => LocalStorageModel::class,
	];

	protected $model;

	protected $config;

	protected $driver;

	protected $app;

	/**
	 * FileStorageServiceProvider constructor.
	 * @param \Illuminate\Contracts\Foundation\Application $app
	 */
	public function __construct(\Illuminate\Contracts\Foundation\Application $app)
	{
		parent::__construct($app);
		$this->app = $app;
	}

	/**
	 * @throws Exception
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__.'provider/FileStorageServiceProvider.php'
		], 'provider');

		$this->config = config('filesystems');

		$this->isSupportedStorage();
		$this->setCorrectModel();

		$driver = new $this->model();

		$driver->bind($this->app, $this->config['disks'][$this->config['default']]);
	}

	/**
	 *
	 */
	public function register()
	{
	    //
	}


	/**
	 * @throws Exception
	 */
	protected function isSupportedStorage()
	{
		if(!in_array($this->config['default'], $this->supported)){
			throw new Exception('We do not support this storage');
		}
	}

	/**
	 *
	 */
	protected function setCorrectModel()
	{
		$this->model = $this->models[$this->config['default']];
	}

}
