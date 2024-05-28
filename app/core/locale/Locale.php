<?php

namespace app\core;

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
}