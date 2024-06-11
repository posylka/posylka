<?php

namespace app\media;

class File
{
    public static function upload(UploadObject $upload): string
    {
        $dir = sprintf('%s/%s/%s/%s/',
            config('app.cnt_path'),
            date('Y'),
            date('m'),
            date('d'));
        if (!file_exists($dir)) {
            mkdir($dir, 0700, true);
        }
        $filename = md5($upload->name . $upload->size . $upload->mime_type);
        $path = $dir . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($path, file_get_contents($upload->tmp_name));
        return $path;
    }
}
