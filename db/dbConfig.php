<?php

class dbConfig
{
    public $config;

    public function __construct()
    {

        // Set default database connection details
        $this->config = array('connections' => array(
                'mysql' => array(
                'driver'    => 'mysql',
                'host'      => 'localhost',
                'database'  => 'remindme',
                'username'  => 'reminderer',
                'password'  => 'simplepass',   //would need to be more complex on actual server obviously
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
                ),
            ),
        );

        return $this->config;
    }
}