<?php
namespace rein\telegram;

use danog\MadelineProto\API;
use danog\MadelineProto\Logger;
use danog\MadelineProto\Settings;

class Helper
{
    public const API_APP_ID = '18273723';
    public const API_APP_HASH = 'a1ae77207f454ea000c6d678e5366247';

    public static $workPath = __DIR__;

    protected static function safeRun(callable $callback)
    {
        $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? null;
        $getcwd = getcwd();

        $workPath = static::$workPath;
        if(!is_dir("$workPath/madeline"))
        {
            if(!is_writable($workPath))
                throw new \Exception(sprintf("Unable to write session to directory '%s'", $workPath));

            $mask = umask(002);
            mkdir("$workPath/madeline", 0775, true);
            copy(__DIR__ . "/madeline.php", "$workPath/madeline/madeline.php");
            umask($mask);
        }

        chdir("$workPath/madeline");
        $_SERVER['DOCUMENT_ROOT'] = "$workPath/madeline";

        ob_start();
        try{
            $result = $callback();
            ob_end_clean();
            chdir($getcwd);
            $_SERVER['DOCUMENT_ROOT'] = $documentRoot;
        }
        catch(\Exception $e)
        {
            ob_end_clean();
            chdir($getcwd);
            $_SERVER['DOCUMENT_ROOT'] = $documentRoot;

            throw new \Exception($e->getMessage());
        }
        
        return $result;
    }

    public static function getMadelineProto():API
    {
        static $madelineProto = null;
        if(!$madelineProto)
        {
            include_once static::$workPath . "/madeline/madeline.php";

            $settings = new Settings();
            $settings->getLogger()
                ->setType(Logger::LOGGER_FILE)
                ->setLevel(Logger::LEVEL_VERBOSE);
    
            $settings->getAppInfo()
                ->setApiId(static::API_APP_ID)
                ->setApiHash(static::API_APP_HASH);
            
            $madelineProto = new API('bot.madeline', $settings);
            $madelineProto->start();
            // $madelineProto->setNoop();
        }
        
        return $madelineProto;
    }

    public static function isGuest():bool
    {
        $workPath = static::$workPath;
        return !file_exists("$workPath/madeline/bot.madeline");
    }

    public static function isInstalled():bool
    {
        $workPath = static::$workPath;
        return is_dir("$workPath/madeline");
    }

    public static function init():void
    {
        static::safeRun(function(){
            static::getMadelineProto();
            // nothing to do
        });
    }

    /**
     * @method string createGroup(string $title, string $desc)
     * create new telegram private group and create export link
     *
     * @param string $title group title
     * @param string $desc group description
     * 
     * @return string invite link to group
     */
    public static function createGroup(string $title, string $desc):string
    {
        return (string) static::safeRun(function() use($title, $desc){
            $api = static::getMadelineProto();
            $updates = $api->channels->createChannel([
                'broadcast' => false,
                'megagroup' => true,
                'title' => $title,
                'about' => $desc,
            ]);
    
            $channelId = $updates['updates'][1]['channel_id'] ?? null;
            if(is_null($channelId))
                throw new \Exception("An error occurred while creating the group");
    
            $peerId = [
                "_" => "peerChannel",
                "channel_id" => $channelId,
            ];
    
            $info = $api->messages->exportChatInvite(['peer' => $peerId]);
            if(!isset($info['link']))
                throw new \Exception("An error occurred while creating invite link");
    
            return $info['link'];
        });
    }
}