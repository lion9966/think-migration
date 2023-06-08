<?php

namespace think\migration\command\stubs\Adapter;

use think\contract\Jsonable;
use think\facade\Db;
use think\migration\command\stubs\builder;
use Phinx\Db\Adapter\MysqlAdapter as MysqlAdapterData;

class MysqlAdapter extends builder implements AdapterFace
{


    /**
     * {@inheritdoc}
     * @return array
     */
    public function getTableHeader(): array
    {
        $sql    = "SHOW TABLE STATUS LIKE  '" . $this->tableName . "'";
        $result = Db::query($sql);
        if ($result) {
            $tableName = $result[0]['Name'];
            $str       = [
                'engine'     => $result[0]['Engine'],//InnoDB
                'Row_format' => $result[0]['Row_format'],
                'collation'  => $result[0]['Collation'],//utf8_general_ci
                'comment'    => $result[0]['Comment'],
            ];
            if (empty($this->pk)) {
                $this->getPk();
            }
            if (!empty($this->pk)) {
                if (count($this->pk) > 1) {
                    $str = $str + [
                            'id'          => false,
                            'primary_key' => $this->pk
                        ];
                } else {
                    $str = $str + [
                            'id'          => $this->pk[0],
                            'primary_key' => $this->pk[0]
                        ];
                }
            }
            return [$this->tableName, $str];
        } else {
            return [];
        }
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function getTableBody(): array
    {
        $result = Db::getFields($this->tableName);

        $str = [];
        foreach ($result as $key => $res) {
            $column  = [];
            $classes = [];
            $type    = $this->getCloumnTypes($res['type']);
            $column  = [$res['name'], $type['name']];
            if (!empty($type['limit'])) {
                $classes = $classes + ['limit' => $type['limit']];
            }
            if (!$res['notnull']) {
                $classes = $classes + ['null' => $res['notnull']];
            }
            if ($res['default'] <> null) {
                if (preg_match("/^\d*$/", $res['default'])) {
                    $res['default'] = (int)$res['default'];
                }
                $classes = $classes + ['default' => $res['default']];
            }
            if (false !== strpos(strtolower($type['name']), 'int') || in_array(strtolower($type['name']), ['decimal', 'float', 'dobule']) && empty($res['signed'])) {
                $classes = $classes + ['signed' => 'unsigned'];
            }
            if ($res['autoinc']) {
                $classes = $classes + ['increment' => 'AUTO_INCREMENT'];
            }
            if (!empty($res['comment'])) {
                $classes = $classes + ['comment' => $res['comment']];
            }
            if (!empty($classes)) {
                $str[] = array_merge($column, [$classes]);
            } else {
                $str[] = $column;
            }
        }
        return $str;
    }

    /**
     * {@inheritdoc}
     * @return this
     */
    public function getPk()
    {
        $table    = $this->tableName;
        $database = $this->database;
        $pk       = "SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE  WHERE constraint_schema = '" . $database . "'  AND table_name = '" . $table . "'";
        $result   = Db::query($pk);
        foreach ($result as $res) {
            if ($res['CONSTRAINT_NAME'] == "PRIMARY") {
                $this->pk = $this->pk + [$res['COLUMN_NAME']];
            }
        }
        return $this->pk;
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function getForeign()
    {
        $table  = $this->tableName;
        $fk     = "SHOW CREATE TABLE `" . $table . "`";
        $result = Db::query($fk);
        //dump($result);
        $string    = $result[0]['Create Table'];
        $delimiter = "\n ";
        $array     = explode($delimiter, $string);
        //CONSTRAINT `sv_product_ibfk_1` FOREIGN KEY (`id`) REFERENCES `sv_backup` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE\n
        $pattern = '/FOREIGN\sKEY\s\(\`([A-Za-z\_0-9]+)\`\)\sREFERENCES\s\`([A-Za-z\_0-9]+)\`\s\(\`([A-Za-z\_0-9]+)\`\)\sON\sDELETE\s(CASCADE|SET\sNULL|NO\sACTION|RESTRICT)\sON\sUPDATE\s(CASCADE|SET\sNULL|NO\sACTION|RESTRICT)/';
        foreach ($array as $value) {
            preg_match($pattern, $value, $matches);
            //dump($value, $matches);
            if (!empty($matches)) {
                $this->foreign[] = array_merge($this->foreign, [$matches[1], $matches[2], $matches[3], ['delete' => $matches[4], 'update' => $matches[5]]]);
            }
        }
        return $this->foreign;
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function getFk()
    {
        if (empty($this->foreign)) {
            $this->foreign = $this->getForeign();
        }
        //dump($this->foreign);
        return $this->foreign;
    }


    /**
     * {@inheritdoc}
     * @return array
     */
    public function getTableIndex(): array
    {
        $sql = "SHOW INDEX FROM " . $this->tableName . "";
        $res = Db::query($sql);
        if (!empty($res)) {
            $keyName = array_column($res, "Key_name");
            $index   = array_unique($keyName);
            $pk      = array_search('PRIMARY', $index);
            if (!($pk === false)) {
                unset($index[$pk]);
                if (!isset($index)) {
                    return [];
                }
            };

            $result = [];
            foreach ($index as $value) {
                $addIndex = [];
                $limit    = [];
                $type     = [];
                foreach ($res as $item) {
                    if ($value == $item['Key_name']) {
                        if (!empty($item['Sub_part'])) {
                            $limit = array_merge($limit, [$item['Column_name'] => $item['Sub_part']]);
                        }
                        $addIndex = array_merge($addIndex, [$item['Column_name']]);
                        if ($item['Index_type'] != 'BTREE') {
                            $type = array_merge($type, [$item['Column_name'] => $item['Index_type']]);
                        }
                    }
                }
                $temp = [];
                if (!empty($limit)) {
                    $temp = array_merge($temp, ['limit' => $limit]);
                }
                if (!empty($type)) {
                    $type = array_unique($type);
                    if (count($type) == 1) {
                        $type = $type[0];
                    }
                    $temp = array_merge($temp, ['type' => $type]);
                }
                if (!empty($temp)) {
                    $result[$value] = array_merge([$addIndex], [$temp]);
                } else {
                    $result[$value] = array_merge([$addIndex]);
                }
            }
            return $result;
        }
        return [];
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function init(): string
    {
        $note   = "        // create the table  //May need to modify the part //可能需要修改部分内容" . "\n";
        $string = "";
        $header = $this->getTableHeader();
        if (empty($header)) {
            return '';
        }
        $header = $this->formatChar($header);
        $string .= '        $table  =  $this->table(' . $header . ");" . "\n";
        //dump("A",$string);
        $body = $this->getTableBody();
        //dump("B", $body);
        if (!empty($body)) {
            $string .= '        $table';
            foreach ($body as $value) {
                $bodyStr = $this->formatChar($value);
                $string  .= "->addColumn(" . $bodyStr . ")" . "\n			";
            }
        }
        $string .= "\n";
        $index  = $this->getTableIndex();
        //dump("c", $index);
        if (!empty($index)) {
            foreach ($index as $v) {
                $indexStr = $this->formatChar($v);
                $string   .= "			->addIndex(" . $indexStr . ")" . "\n";
            }
        }
        $string .= "\n";
        $fk     = $this->getFk();
        //dump("d", $fk);
        if (!empty($fk)) {
            foreach ($fk as $item) {
                $fkStr  = $this->formatChar($item);
                $string .= "			->addForeignKey(" . $fkStr . ")" . "\n";
            }
        }
        if (!empty($string)) {
            $string .= "			->create();" . "\n";
        }
        return empty($string) ? '' : $note . $string;
    }

    /**
     * 获取字段类型长度//长度如果不准确需要手动修改//长度目前不健全
     * @param $type
     * @return array
     */
    private function getCloumnTypes($type)
    {
        $pattern = '/([a-z]+)\(([\S]+)\)/';
        preg_match($pattern, $type, $mathces);
        $typeName = empty($mathces) ? $type : $mathces[1];
        if (!empty($mathces[2]) && preg_match("/^\d*$/", $mathces[2])) {
            $mathces[2] = (int)$mathces[2];
        }
        switch ($typeName) {
            case 'varchar':
                $res['name']  = 'string';
                $res['limit'] = $mathces[2];
                break;
            default:
                $res['name']  = $typeName;
                $res['limit'] = empty($mathces[2]) ? '' : $mathces[2];
                break;
        }

        return $res;
    }

    protected function formatChar($array)
    {
        $str = json_encode($array, JSON_UNESCAPED_UNICODE);
        //dump($str);
        $str = preg_replace("/\"[0-9]+\"\:/", "", $str);
        $str = preg_replace("/\:/", '=>', $str);
        $str = preg_replace("/\{/", '[', $str);
        $str = preg_replace("/\}/", ']', $str);
        return substr($str, 1, -1);
    }

}
