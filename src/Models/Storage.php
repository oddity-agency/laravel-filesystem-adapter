<?php

namespace Filesystem\Models;

use Filesystem\Services\FileSystemS3Service;

interface Storage
{
	/**
	 * @param $app
	 * @param $config
	 * @return mixed
	 */
	public function bind($app, $config);
}