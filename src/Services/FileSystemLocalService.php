<?php

namespace Filesystem\Services;

use App\Tenant;
use Filesystem\Interfaces\FileSystemInterface;
use Filesystem\Services\LocalStorageService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileSystemLocalService implements FileSystemInterface
{
	/**
	 * @var \Illuminate\Filesystem\FilesystemAdapter
	 */
	private $disk;

	/**
	 * @var string
	 */
	private $driver = 'local';

	/**
	 * @var mixed
	 */
	private $path = '';

    /**
     * @var \Filesystem\Services\LocalStorageService
     */
	private $localStorageClient;

	/**
	 * FileSystemLocalService constructor.
	 * @param Tenant $tenant
	 * @param LocalStorageService $localStorageService
	 */
	public function __construct(LocalStorageService $localStorageService)
	{
		$this->disk = \Storage::disk($this->driver);
		$this->localStorageClient = $localStorageService;
	}

    /**
     * @param $file
     * @throws FileNotFoundException
     */
	public function checkIfFileExists($file)
    {
        $file = ltrim($file, '/');
        if(!file_exists($this->path.'/'.$file)){
            throw new FileNotFoundException();
        }
    }

	/**
	 * @return array|mixed
	 */
	public function browse($path)
	{
		$files = $this->disk->allFiles($path);
		$directories = $this->disk->allDirectories($path);
		return ['files' => $files, 'directories' => $directories];
	}

	/**
	 * @param UploadedFile $file
	 * @param $path
	 * @return false|mixed|string
	 */
	public function put(UploadedFile $file, $path)
	{
		ini_set('max_execution_time', 360); // it may take long for files to upload

		return $this->disk->putFileAs(
			$path, $file, $file->getClientOriginalName()
		);
	}

	/**
	 * @param $fileName
	 * @return mixed|string
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function get($fileName, $download)
	{
		try{
			$file = $this->localStorageClient->get($this->disk, $fileName, $download);
		} catch (\Exception $e) {
			throw new FileNotFoundException();
		}

		readfile($file['path']);
	}

	/**
	 * @param $fileName
	 * @return bool|mixed
	 */
	public function delete($fileName)
	{
		if($this->disk->exists($fileName)){
			return $this->disk->delete($fileName);
		}

		return false;
	}
}
