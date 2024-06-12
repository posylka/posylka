<?php

//---------constants---------//
define('IS_REQUEST', isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST');
define('IS_PUT', isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'PUT');
define('IS_GET', isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'GET');
define('IS_DELETE', isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'DELETE');
define('IS_OPTIONS', isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS');
define('SYSTEM_VERSION', file_get_contents(WWW_PATH . '/system/VERSION'));
setlocale(LC_ALL, 'en_US.UTF-8');

//---------unicode---------//
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');

require_once WWW_PATH . str_replace('/', DIRECTORY_SEPARATOR, '/app/core/Singleton.php');
require_once WWW_PATH . str_replace('/', DIRECTORY_SEPARATOR, '/app/core/Config.php');

set_include_path(WWW_PATH . PATH_SEPARATOR . get_include_path());

//---------autoload---------//
require_once __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/autoload.php');

if (!function_exists('jd'))
{
    function jd($mValues)
    {
        header('Content-Type: application/json');
        echo json_encode(func_get_args(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if (!function_exists('config')) {
    function config(string $key, $default = null)
    {
        return \app\core\Config::getInstance()->get($key, $default);
    }
}

if (!function_exists('i')) {
    function i($a, $s, $m = '')
    {
        return (isset($a[$s]))
            ? $a[$s]
            : $m;
    }
}

if (!function_exists('__')) {
    /**
     * Переводчик
     */
    function __(string $message, array $data = [], ?string $lang = null): string
    {
        return \app\core\locale\Locale::getInstance($lang)->translate($message, $data);
    }
}

if (!function_exists('dd')) {
    /**
     * Debug
     */
    function dd($mValues)
    {
        $iNumArgs = func_num_args();
        ?>
        <style>
            table.debug {
                width: 100%;
            }

            td.debug_title {
                background-color: #f5f5f5;
                font-weight: bold;
                text-align: left;
                width: 10%;
                padding: 10pt;
            }

            td.debug_message {
                text-align: left;
                width: 90%;
                padding: 10pt;
            }

            td.debug_debug {
                text-align: left;
                font-weight: bold;
                padding: 10pt;
            }
        </style>
        <table border="1" bordercolor="#000000" cellpadding="0" cellspacing="0"
               width="100%">
            <tr>
                <td class="debug_debug" colspan="2">[ Debug: ]</td>
            </tr>
            <?php
            for ($i = 0; $i < $iNumArgs; $i++) {
                ?>
                <?php
                $mValue = func_get_arg($i) ?>
                <tr>
                    <td class="debug_title">Debug[<?= $i ?>]:</td>
                    <td class="debug_message"><i>(<?= ((is_object($mValue)) ? get_class($mValue) : gettype($mValue)) ?>)</i>
                        <pre><?php
                            ((is_null($mValue) || is_bool($mValue) ? var_dump($mValue) : print_r($mValue))) ?></pre>
                    </td>
                </tr>
                <?php
            }
            ?>

            <tr>
                <td class="debug_title">Backtrace:</td>
                <td class="debug_message">
                    <?php
                    $aDebugBacktrace = debug_backtrace();
                    ?>
                    <?php
                    for ($i = 0; $i < count($aDebugBacktrace); $i++) {
                        ?>
                        <div><b>
                                <?= isset ($aDebugBacktrace [$i] ["class"]) ? $aDebugBacktrace [$i] ["class"] . "::" : "" ?>
                                <?= $aDebugBacktrace [$i] ["function"] . "()" ?>
                            </b>
                            <?= $aDebugBacktrace [$i] ["file"] ?? "sometime.php" ?>
                            <b>
                                <?= "#" . ($aDebugBacktrace [$i] ["line"] ?? "some-line") ?>
                            </b></div>
                        <?php
                    }
                    ?>

                </td>
            </tr>

        </table>
        <br/>
        <br/>
        <?php exit;
    }
}