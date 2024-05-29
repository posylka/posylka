<?php

namespace app\migrations\vbeta;

use app\core\Json;
use app\migrations\AbstractMigration;
use app\reference\CitiesReference;
use Exception;

return new class () extends AbstractMigration
{
    public function run(): bool
    {
        try {
            if (!$this->schema->hasTable('cities_reference')) {
                return false;
            }
            $cities = Json::decode(file_get_contents(config('app.migrate_files_path') . '/' . 'cities.json'));
            foreach ($cities as $vals) {
                CitiesReference::query()->updateOrCreate($vals);
            }
            $this->out('cities_reference table filled');
            return true;
        } catch (Exception $e) {
            $this->out($e->getMessage());
            return false;
        }
    }
};