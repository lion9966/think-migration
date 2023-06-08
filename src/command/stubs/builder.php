<?php

namespace think\migration\command\stubs;

use think\facade\Db;

abstract class builder
{
    protected $database;

    protected $tableName;

    protected $prefix;

    protected $pk = [];

    protected $foreign = [];

    public function __construct($name)
    {
        $dataType        = config('database.default');
        $this->database  = config('database.connections.' . $dataType . '.database');
        $this->prefix    = config('database.connections.' . $dataType . '.prefix');
        $this->tableName = $this->prefix . strtolower($name);
    }


    public function tableName()
    {
        return $this->tableName;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }


}
