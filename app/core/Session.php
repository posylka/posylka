<?php


namespace app\core;

class Session
{
    public static function start($sSessionId = null): void
    {
        if (session_id() == '') {
            session_name('sid');
            if ($sSessionId !== null) {
                session_id($sSessionId);
            }
            session_start();
        }
    }
    public static function close(): void
    {
        session_write_close();
    }

}
