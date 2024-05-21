<?php

namespace app\core;

class Config
{
    use Singleton;
    private array $configs = [];
    protected function init(): void
    {
        $this->load();
    }

    /**
     * Загрузка с конфиг файлов
     */
    private function load(): void
    {
        $configsPath = WWW_PATH . '/config';

        $files = scandir($configsPath);
        $files = array_filter($files, function ($file) use ($configsPath) {
            return is_file($configsPath . '/' . $file) && $file != '.' && $file != '..';
        });

        foreach ($files as $file) {
            $configs = include $configsPath . '/' . $file;

            foreach ($configs as $configKey => $configValue) {
                $this->configs[str_replace('.php', '', $file) . '.' . $configKey] = $configValue;
            }
        }
    }


    /**
     * Получение конфига по ключу
     * @use config(string $key, mixed $default = null)
     *
     * @param string $key Ключ конфига
     * @param mixed $default Значение по умолчанию
     *
     * @return mixed Значение конфига
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->configs[$key] ?? $default;
    }

}
