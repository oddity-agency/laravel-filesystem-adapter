### Filesystem for Laravel

Services for Laravel 5.x for storage manipulation, it currently supports: local, s3, azure.

## Installation

Add to require block in your projects composer.json:

* `"oddity-agency/laravel-filesystem-adapter": "dev-master"`

### Configuration

For Laravel 5.3 and before version you will need to register
* `Filesystem\FileStorageServiceProvider::class`
to providers array in config/app.php

For Laravel 5.4 and after
* `package:discover` will automatically discover ServiceProvider from package

Set config in filesystems.php

``` 'default' => 'desired-supported-storage', // 'local', 's3', azure'```

```
  'disks' => [

      // if default is local
      'local' => [
          'driver' => 'local',
          'root' => storage_path('app/public'),
      ],

      // if default is s3
      's3' => [
          'driver' => 's3',
          'key' => env('AWS_KEY'),
          'secret' => env('AWS_SECRET'),
          'region' => env('AWS_REGION'),
          'bucket' => env('AWS_BUCKET'),
          'credentials' => [ env('AWS_KEY'), env('AWS_SECRET')],
      ],

      // if default is azure
      'azure' => [
          'driver'    => 'azure',
          'name'      => env('AZURE_STORAGE_NAME'),
          'key'       => env('AZURE_STORAGE_KEY'),
          'container' => env('AZURE_STORAGE_CONTAINER'),
      ],
  ],
```

Then create controller which constructor will accept
* `Filesystem\Interfaces\FileSystemInterface ` who is already binded in FileSystemServiceProvider and will return instance of ```default``` storage service.

### Methods

```

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


 ```

 All of this method are implemented in S3StorageService, AzureStorageService, LocalStorageService

 ```public function get($fileName, $download);```

 Will return file or download it if download param is set to true in request params.


```public function put(UploadedFile $file, $path);```

Note: If is set GET['path'] files will be uploaded in taht path (folder), not in root of storage.
