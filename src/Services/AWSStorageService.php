<?php
namespace Filesystem\Services;

use Aws\Laravel\AwsFacade;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class AWSStorageService
 * @package App\Services
 */
class AWSStorageService
{
	/**
	 * @var
	 */
	public $client;

	/**
	 * AWSStorageService constructor.
	 */
	public function __construct()
	{
		$this->client = AwsFacade::createClient('s3');
	}

	/**
	 * @param $bucket
	 * @param $fileName
	 * @return mixed
	 */
	public function checkIfFileExists($bucket, $fileName)
	{
		$fileName = ltrim($fileName, '/');
		return $this->client->headObject([
			'Bucket' => $bucket,
			'Key'    => $fileName,
		]);
	}

	/**
	 * @param $bucket
	 * @param $fileName
	 * @param bool $download
	 * @return mixed
	 */
	public function get($bucket, $fileName, $download = null)
	{
		$file = $this->client->getObject([
			'Bucket' => $bucket,
			'Key'    => $fileName,
		]);

		$disposition = $file['ContentDisposition'];

		if($download){
			$disposition = 'attachment';
		}

		http_response_code($file['@metadata']['statusCode']);
		header("Last-Modified: {$file['ContentLength']}");
		header("ETag: {$file['ETag']}");
		header("Content-Type: {$file['ContentType']}");
		header("Content-Disposition: {$disposition}");
		header("Cache-Control: private, max-age=31536000");
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Credentials: true ");
		header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
		header("Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control");

		if (ob_get_level()) {
			ob_end_flush();
		}

		flush();

		return $file;
	}

	/**
	 * @param $bucket
	 * @param UploadedFile $file
	 * @return mixed
	 */
	public function putFile($bucket, UploadedFile $file, $path)
	{
		if($path != ''){
			$this->client->putObject([
				'Bucket'        => $bucket,
				'Key'           => $path,
				'Body'          => '',
				'ACL'           => 'public-read',
			]);
		}


		return $this->client->putObject([
			'Bucket'        => $bucket,
			'Key'           => $path.$file->getClientOriginalName(),
			'Body'          => file_get_contents($file),
			'ACL'           => 'public-read',
			'ContentType'   => $file->getMimeType(),
			'Prefix'        => $path,
		]);
	}

	/**
	 * @param $bucket
	 * @param $path
	 * @return mixed
	 */
	public function browse($bucket, $path)
	{
		$objects = $this->client->listObjects([
			'Bucket' => $bucket,
			'Prefix' => $path,
		]);

		return $objects['Contents'];
	}

	/**
	 * @param $bucket
	 * @param $fileName
	 * @return mixed
	 */
	public function delete($bucket, $fileName)
	{
		return $this->client->deleteObject([
			'Bucket' => $bucket,
			'Key'    => $fileName,
		]);
	}

	/**
	 * @param $bucket
	 * @param $path
	 * @return mixed
	 */
	public function createDir($bucket, $path)
	{
		return $this->client->putObject([
			'Bucket'        => $bucket,
			'Key'           => $path,
			'Body'          => '',
			'ACL'           => 'public-read',
		]);
	}

	/**
	 * @param $bucket
	 * @param $path
	 * @return mixed
	 */
	public function deleteDir($bucket, $path)
	{
		return $this->client->deleteMatchingObjects($bucket, $path);
	}
}
