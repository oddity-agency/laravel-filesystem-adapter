<?php
namespace Filesystem\Services;

use Filesystem\Interfaces\FileSystemInterface;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileSystemS3Service
 * @package App\Services
 */
class FileSystemS3Service implements FileSystemInterface
{

	/**
	 * @var AWSStorageService
	 */
	private $awsClient;

	/**
	 * @var mixed
	 */
	private $bucket;

	/**
	 * FileSystemS3Service constructor.
	 * @param AWSStorageService $storageService
	 * @param $config
	 */
	public function __construct(AWSStorageService $storageService, $config)
	{
		$this->bucket = $config['bucket'];
		$this->awsClient = $storageService;
	}

	/**
	 * @param $path
	 * @return mixed
	 */
	public function checkIfFileExists($path)
	{
		try{
			return $this->awsClient->checkIfFileExists($this->bucket, $path);
		} catch (\Exception $e) {
			get_headers($path);
		}
	}

	/**
	 * @param $path
	 * @return array|mixed
	 */
	public function browse($path)
	{
		return $this->awsClient->browse($this->bucket, $path);
	}

	/**
	 * @param $fileName
	 * @param $download
	 * @return mixed|void
	 * @throws FileNotFoundException
	 */
	public function get($fileName, $download)
	{
		try{
			$file = $this->awsClient->get($this->bucket,$fileName,$download);

			if ($file['@metadata']['statusCode'] === 200) {
				readfile($file['@metadata']['effectiveUri']);
			}
		} catch (\Exception $e) {
			throw (new FileNotFoundException());
		}
	}

	/**
	 * @param UploadedFile $file
	 * @param $path
	 * @return bool|mixed
	 */
	public function put(UploadedFile $file, $path)
	{
		return $this->awsClient->putFile($this->bucket,$file,$path);
	}

	/**
	 * @param $fileName
	 * @return bool|mixed
	 */
	public function delete($fileName)
	{
		return $this->awsClient->delete($this->bucket, $fileName);
	}

	/**
	 * @param $path
	 * @return mixed
	 */
	public function createDir($path)
	{
		return $this->awsClient->createDir($this->bucket, $path);
	}

	/**
	 * @param $path
	 * @return mixed
	 */
	public function deleteDir($path)
	{
		return $this->awsClient->deleteDir($this->bucket, $path);
	}

}
