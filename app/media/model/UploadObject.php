<?php

namespace app\media;

class UploadObject
{
    public function __construct(
        public string $error,
        public string $name,
        public int $size,
        public int $mime_type,
        public bool $tmp_name
    )
    {}
}