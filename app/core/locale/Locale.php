<?php

namespace app\core;

use DirectoryIterator;

class Locale
{
    use Singleton;
    private string $lang;
    private array $translations;

    public function getLang(): string
    {
        return $this->lang;
    }

    protected function init(): void
    {
        if (isset($_GET['lang']) && in_array($_GET['lang'], config('locale.languages'))) {
            $this->lang = $_GET['lang'];
        } else {
            $this->lang = config('locale.default');
        }
        $this->load();
    }

    private function load(): void
    {
        $path = WWW_PATH . DIRECTORY_SEPARATOR . 'locale' . DIRECTORY_SEPARATOR;
        $files = scandir($path);
        $files = array_filter($files, function ($file) use ($path) {
            return is_file($path . DIRECTORY_SEPARATOR . $file) && $file != '.' && $file != '..';
        });

        foreach ($files as $file) {
            $translations = include $path . '/' . $file;
            foreach ($translations as $id => $values) {
                $key = str_replace('.php', '', $file) . '.' . $id;
                if (strlen(i($values, $this->lang))) {
                    $this->translations[$key] = $values[$this->lang];
                }
            }
        }
    }

    public function translate(string $code, array $data = []): string
    {
        $message = i($this->translations, $code);
        if (!strlen($message)) {
            return $code;
        }
        if (count($data)) {
            foreach ($data as $key => $item) {
                $message = str_replace(':' . $key, $item, $message);
            }
        }
        return $message ?? '';
    }

    public function getUntranslated(): array
    {
        $directoryToScan = WWW_PATH . '/app';
        $textValues = [];

        $this->scanFiles($directoryToScan, $textValues);

        $textValues = array_unique($textValues);
        $untranslated = [];
        foreach ($textValues as $value) {
            $value = current(explode("'", $value));
            if (!\app\core\Locale::getInstance()->hasTranslation($value)) {
                $untranslated[] = $value;
            }
        }

        return $untranslated;
    }

    private function scanFiles(string $directory, array &$textValues): void
    {
        $dir = new DirectoryIterator($directory);
        foreach ($dir as $fileInfo) {
            if (!$fileInfo->isDot()) {
                if ($fileInfo->isDir()) {
                    $this->scanFiles($fileInfo->getPathname(), $textValues);
                } elseif ($fileInfo->isFile() && $fileInfo->getExtension() === 'php') {
                    $contents = file_get_contents($fileInfo->getPathname());
                    $pattern = '/__\((["\'])(.*?)\\1\)/';
                    preg_match_all($pattern, $contents, $matches);
                    $textValues = array_merge($textValues, $matches[2]);
                }
            }
        }
    }

    public function hasTranslation(string $key): bool
    {
        return isset($this->translations[$key]);
    }
}