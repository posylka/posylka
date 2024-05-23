<?php


namespace app\core\db;

use app\core\enums\MigrationStatus;
use app\core\Util;
use app\migrations\AbstractMigration;
use app\migrations\Migrations;

class Migration
{
    use \app\core\Singleton;

    private string $path = WWW_PATH . '/app/migrations';
    private ?Migrations $migration;

    public function execute(): void
    {
        if ($this->hasAccess()) {
            $haveErrors = false;
            $haveMigrations = false;

            foreach (scandir($this->path) as $sItem) {
                if($sItem === '.' || $sItem === '..' || $sItem === 'Migrations.php' || $sItem === 'AbstractMigration.php') {
                    continue;
                }
                $sSubPath = $this->path . DIRECTORY_SEPARATOR . $sItem;
                if(is_dir($sSubPath)) {
                    foreach (scandir($sSubPath) as $sItem2) {
                        if($sItem2 === '.' || $sItem2 === '..' || is_dir($sSubPath . DIRECTORY_SEPARATOR . $sItem2)) {
                            continue;
                        }
                        $sClassName = str_replace('.php', '', $sSubPath . DIRECTORY_SEPARATOR . $sItem2);
                        $this->runMigration($sSubPath . DIRECTORY_SEPARATOR . $sItem2, $haveMigrations, $haveErrors);
                    }
                } else {
                    $sClassName = str_replace('.php', '', $sSubPath);
                    $this->runMigration($sSubPath, $haveMigrations, $haveErrors);
                }
            }

            Util::clearTmp();

            if (!$haveMigrations) {
                echo "\n Nothing to migrate \n";
            } elseif($haveErrors) {
                echo "\n Migrations completed with errors \n";
            } else {
                echo "\n All migrations completed successfully \n";
            }
        } else {
            echo "\n Dont have access to migrate \n";
        }
    }

    private function runMigration(string $filePath, &$haveMigrations, &$haveErrors): void
    {
        $name = (str_replace(
            '/app/migrations',
            '',
            str_replace(WWW_PATH, '', $filePath)
        ));

        if($this->needRun($name)) {
            /**
             * @var AbstractMigration $oMigration
             */
            $oMigration = include $filePath;
            $haveMigrations = true;
            $currentMigration = $this->migration ?? new Migrations();
            $this->migration = null;
            echo sprintf("\n =====START %s %s migration===== \n", $currentMigration->version, $currentMigration->name);
            ob_start();
            $currentMigration->name = $name;
            $currentMigration->status = $oMigration->run() ? MigrationStatus::SUCCESS : MigrationStatus::ERROR;
            $currentMigration->output = ob_get_clean();
            $currentMigration->save();

            if($currentMigration->status === MigrationStatus::SUCCESS) {
                echo sprintf("\n =====END %s %s migration===== \n", $currentMigration->version, $currentMigration->name);
            } else {
                $haveErrors = true;
                echo sprintf("\n =====ERROR %s %s migration===== \n", $currentMigration->version, $currentMigration->name);
            }
        }
    }

    private function needRun(string $name): bool
    {
        $this->migration = Migrations::query()
            ->where('name', $name)
            ->first();
        if ($this->migration) {
            return $this->migration->status !== MigrationStatus::SUCCESS;
        }
        return true;
    }

    private function hasAccess(): bool
    {
        return APP_MODE == 'mgrt';
    }
}
