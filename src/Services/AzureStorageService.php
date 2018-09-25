<?php

namespace Filesystem\Services;

use Filesystem\Interfaces\FileSystemInterface;
use League\Flysystem\Config;
use MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter;
use League\Flysystem\Filesystem;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;

/**
 * Class AzureStorageService
 * @package Filesystem\Services
 */
class AzureStorageService implements FileSystemInterface
{

	/**
	 * @var Filesystem
	 */
	public $filesystem;
	/**
	 * @var AzureBlobStorageAdapter
	 */
	public $adapter;
	/**
	 * @var BlobRestProxy
	 */
	public $client;
	/**
	 * @var array
	 */
	public $config;

	/**
	 * @var string
	 */
	public $container;

	/**
	 * AzureStorageService constructor.
	 * @param $config
	 */
	public function __construct($config)
	{
		$endpoint = sprintf(
			'DefaultEndpointsProtocol=https;AccountName=%s;AccountKey=%s',
			$config['name'],
			$config['key']
		);

		$this->config = $config;
		$this->container = $config['container'];

		$this->client = BlobRestProxy::createBlobService($endpoint, $config);
		$this->adapter = new AzureBlobStorageAdapter($this->client, $this->container);
		$this->filesystem = new Filesystem($this->adapter);
	}

	/**
	 * @param $file
	 * @return bool|mixed
	 */
	public function checkIfFileExists($file)
	{
		return $this->filesystem->has($file);
	}

	/**
	 * @param $fileName
	 * @param $download
	 * @return \League\Flysystem\Directory|\League\Flysystem\File|\League\Flysystem\Handler|mixed
	 */
	public function get($fileName, $download)
	{
		$blob = $this->client->getBlob($this->container, $fileName);
		$blobProps = $this->client->getBlobProperties($this->container, $fileName);
		$contentType = $blobProps->getProperties()->getContentType();
		$lastModified = $blobProps->getLastModified()->getTimestamp();
		$disposition = $blobProps->getProperties()->getContentDisposition();

		if($download){
			$disposition = "attachment";
		}

		header("Last-Modified: {$lastModified}");
		header("ETag: {$blobProps->getETag()}");
		header("Content-Type: $contentType");
		header("Content-Disposition: $disposition");
		header("Cache-Control: private, max-age=31536000");
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Credentials: true ");
		header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
		header("Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control");

		fpassthru($blob->getContentStream());
	}

	/**
	 * @param $path
	 * @return array|mixed
	 */
	public function browse($path)
	{
		return $this->filesystem->listContents($path);
	}

	/**
	 * @param UploadedFile $file
	 * @param $path
	 * @return bool|mixed
	 */
	public function put(UploadedFile $file, $path)
	{
		if($path === '/'){
			$path = '';
		}
		$path = $path.$file->getClientOriginalName();

		$this->filesystem->write($path, fopen($file, "r"), $this->config);

		$blob = $this->client->getBlob($this->container, $path);

		$properties = $this->client->getBlobProperties($this->container, $path);

		$content = stream_get_contents($blob->getContentStream());
		$finfo = new \finfo(FILEINFO_MIME);
		$mime = $finfo->buffer($content);
		$blobOption = new SetBlobPropertiesOptions();
		$blobOption->setContentType($mime);

		$this->client->setBlobProperties($this->container, $path, $blobOption);
	}

	/**
	 * @param $fileName
	 * @return bool|mixed
	 */
	public function delete($fileName)
	{
		return $this->filesystem->delete($fileName);
	}

	/**
	 * @param $path
	 * @return bool|mixed
	 */
	public function createDir($path)
	{
		return $this->filesystem->createDir($path, $this->config);
	}


	/**
	 * @param $path
	 * @return bool
	 */
	public function deleteDir($path)
	{
		return $this->filesystem->deleteDir($path);
	}
}
