<?php
namespace Filesystem\Services;

/**
 * Class LocalStorageService
 * @package App\Services
 */
class LocalStorageService
{
	/**
	 * @param $disk
	 * @param $fileName
	 * @return mixed
	 */
	public function get($disk, $fileName, $download)
	{
		$file['path'] = $disk->path($fileName);
		$file['mimeType'] = $disk->mimeType($fileName);

		$disposition = '';

		if ($download){
			$disposition = "attachment; filename='{$fileName}'";
		}

		header("Content-Type: {$file['mimeType']}");
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
	 * @param $fileName
	 * @return bool
	 */
	public function checkIfFileExists($bucket, $fileName)
    {
        return file_exists($bucket.$fileName);
    }
}
