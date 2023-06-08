<?php

namespace think\migration\command\stubs;

use think\migration\command\stubs\Adapter\MysqlSeed;

class seedStubs
{
    protected $footer = <<<EOT

    }
}
EOT;

    /**
     * 数据库
     * @var string
     */
    protected $database;

    /**
     * 表
     * @var string
     */
    protected $table;

    public function __construct($name)
    {
        $this->database = config('database.default');
        $this->table    = $name;
    }

    public function seed()
    {
        if ($this->database == 'mysql') {
            $data = (new MysqlSeed($this->table))->init();
            dump($data);
        } else {
            $data = "";
        }
        $filePath    = __DIR__ . '/seed.stub';
        $contents    = file_get_contents($filePath);
        $contents    = str_replace("}", "", $contents);
        $contents    = trim($contents) . "\n";
        $contents    = $contents . $data . $this->footer;
        $newFilePath = __DIR__ . '/seedNew.stub';
        if (false === file_put_contents($newFilePath, $contents)) {
            throw new RuntimeException(sprintf('The file "%s" could not be written to', $newFilePath));
        }
    }

}
