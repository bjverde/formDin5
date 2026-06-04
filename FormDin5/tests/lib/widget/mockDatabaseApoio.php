<?php

/**
 * Mock de Form com FormDin sobre Adianti
 */
class mockDatabaseApoio extends TPage
{

    public static function getNameDatabaseApoio(){
        return 'bdApoio.s3db';
    }

    public static function getPathDatabaseApoio(){
        $path =  __DIR__.'/../../../';
        $name = $path.'database/'.self::getNameDatabaseApoio();
        if (!file_exists($name)) {
            throw new Exception("Database file not found: " . $name);
        }
        return $name;
    }
}