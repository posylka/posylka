<?php

namespace app\test\public;

use app\core\Json;
use app\core\RestController;
use app\core\router\Response;

class TestController extends RestController
{
    public function get(): Response
    {

        $n = [];
        $j = file_get_contents(config('app.migrate_files_path') . '/' . 'cities.json');
        foreach (Json::decode($j) as $ru => $kz) {
            $n[] = [
                'ru' => trim($ru),
                'kz' => trim($kz)
            ];
        }
        file_put_contents(config('app.migrate_files_path') . '/' . 'cities.json', Json::encode($n));
        dd(213);
        return $this->response->setIsSuccess(true)
            ->setMessage('success123')
            ->setContent([]);
    }
}