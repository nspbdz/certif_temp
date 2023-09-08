<?php

namespace App\Library;

use App\Library\DetikCDN;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;


class Uploader
{

    /**
     * The cdn instance.
     *
     * @var App\Libraries\DetikCDN
     */
    private $cdn;

    /**
     * The Module Name. default: from app.env configuration.
     *
     * @var string
     */
    private $moduleName;


    /**
     * Create Uploader Class
     *
     * @param string|null $moduleName
     */
    public function __construct(?string $moduleName = null)
    {
        $this->cdn = new DetikCDN();
        $this->moduleName = $moduleName ?? Config::get('app.name');
    }

    public static function make(?string $moduleName = null){
        return new static($moduleName);
    }

    /**
     * Put the files into the storage or cdn.
     *
     * @param UploadedFile|UploadedFile[]|array|array[] $files
     * files contains `array of files (UploadedFile)` or `array of sanitized files`
     * or `single file` or `single sanitized file`.
     * @param string|null|null $target the target of upload destination. default is `null`. options: [`"cdn"`, `null`].
     * @return Collection|string return `Collection` of file names or `string` if only one file is uploaded.
     */
    public function put(UploadedFile|array $files, ?string $target = null): Collection|string
    {
        $isAssoc = is_array($files) && Arr::isAssoc($files);

        $data = collect($this->arrWrap($files, $isAssoc))
            ->reject(fn ($file) => !$file)
            ->map(fn ($collection) => $this->upload($collection, $this->moduleName, $target));

        return $data->containsOneItem() ? $data->first() : $data;
    }

    /**
     * INTERNAL USE. upload file.
     *
     * @param UploadedFile|array $file
     * @param string $moduleName
     * @param string|null $target
     * @return string name of file uploaded.
     */
    private function upload(UploadedFile|array $file, string $moduleName, ?string $target): string
    {
        if ($file instanceof UploadedFile) {
            $fileName    = $file->hashName();
            $fileContent = $file->get();
            $fileType    = $this->mimeImageChecker($file->getMimeType());
        } else {
            $fileName    = $file['filename'];
            $fileContent = $file['binary'];
            $fileType    = $file['type'];
        }

        if ($target == 'cdn') {
            if ($fileType === 'image') {
                $this->cdn->sendImage($moduleName, $fileName, $fileContent);
            } else {
                $this->cdn->sendFile($moduleName, $fileName, $fileContent);
            }
        } else {
            Storage::put($fileName, $fileContent);
        }

        return $fileName;
    }

    /**
     * mime type image checker.
     *
     * @param string $mimeType
     * @return void
     */
    private function mimeImageChecker($mimeType)
    {
        $isImage = $this->mimeChecker($mimeType, ['image/*']);
        return $isImage ? 'image' : 'file';
    }

    /**
     * general mime type checker.
     *
     * @param string $mimeType
     * @param string[] $parameters
     * @return bool
     */
    private function mimeChecker($mimeType, $parameters)
    {
        return (in_array($mimeType, $parameters) ||
            in_array(explode('/', $mimeType)[0] . '/*', $parameters));
    }

    /**
     * If the given value is not an array and not null, wrap it in one, if wrap assoc true, wrap it.
     *
     * @param  mixed  $value
     * @param bool $isAssoc
     * @return array
     */
    private static function arrWrap($value, $isWrapAssoc = false)
    {
        if (is_null($value)) {
            return [];
        }

        if (is_array($value) && !$isWrapAssoc) {
            return $value;
        }

        return [$value];
    }
}
