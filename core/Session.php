<?php


namespace app\core;


use DateTime;
use DateTimeZone;

class Session
{
    protected const MESSAGE = 'messages';
    protected const ERROR = 'errors';
    protected const SESSION_KEY = 'sessionKey';

    /**
     * Session constructor.
     */
    public function __construct()
    {
        session_start();
        $_SESSION[self::MESSAGE] = $_SESSION[self::MESSAGE] ?? [];
    }

    public function setMessage($type, $title, $message)
    {
        $date = new DateTime("now", new DateTimeZone('Asia/Dhaka') );
        $_SESSION[self::MESSAGE][self::randomString()] = [
            "type" => $type,
            "title" => $title,
            "message" => $message,
            "time" => $date->format("h:i:s A")
        ];
    }

    public function getMessage($key)
    {
        if (array_key_exists($key, $_SESSION[self::MESSAGE])){
            $message = $_SESSION[self::MESSAGE][$key];
            unset($_SESSION[self::MESSAGE][$key]);
            return $message;
        } else {
            return null;
        }
    }

    public function getMessageKeys()
    {
        return array_keys($_SESSION[self::MESSAGE]);
    }

    public static function randomString(int $strength = 10)
    {
        $allowed_string = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $input_length = strlen($allowed_string);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $allowed_string[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }
        return $random_string;
    }

    public function getSessionKey()
    {
        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    public function setSessionKey()
    {
        $_SESSION[self::SESSION_KEY] = self::randomString(100);
        return $this->getSessionKey();
    }
    public function destroySessionKey()
    {
        unset($_SESSION[self::SESSION_KEY]);
    }
}