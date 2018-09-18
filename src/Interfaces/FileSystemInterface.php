<?php
namespace Filesystem\Interfaces;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface FileSystemInterface
 * @package App\Interfaces
 */
interface FileSystemInterface
{
	/**
	 * @param $file
	 * @return mixed
	 */
	public function checkIfFileExists($file);

	/**
	 * @param $path
	 * @return mixed
	 */
	public function browse($path);

	/**
	 * @param UploadedFile $file
	 * @param $path
	 * @return mixed
	 */
	public function put(UploadedFile $file, $path);

	/**
	 * @param $fileName
	 * @param $download
	 * @return mixed
	 */
	public function get($fileName, $download);

	/**
	 * @param $fileName
	 * @return mixed
	 */
	public function delete($fileName);
}
