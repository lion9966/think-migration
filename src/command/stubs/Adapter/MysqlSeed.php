<?php

namespace think\migration\command\stubs\Adapter;

use think\migration\command\stubs\builder;
use think\facade\Db;

class MysqlSeed extends builder
{
    public function getData()
    {
        $data    = Db::table($this->tableName)->select()->toArray();
        $result  = Db::getFields($this->tableName);
        $type    = array_column($result, 'type', 'name');
        $pattern = '/([a-z]+)/';
        foreach ($type as $keyType => $valueType) {
            preg_match($pattern, $valueType, $mathces);
            $type[$keyType] = $mathces[1];
        }
        foreach ($data as $k => $v) {
            foreach ($v as $key => $value) {
                if (in_array($type[$key], ['char', 'varchar', 'text', 'tinytext', 'mediumtext', 'longtext', 'binary', 'varbinary', 'tinyblob', 'blob', 'mediumblob', 'longblob', 'json'])) {
                    if ($arryValue = $this->isJson($value)) {
                        $data[$k][$key] = $arryValue;
                        //$data[$k][$key] = "json_encode([" . $this->formatChar($arryValue) . "])";
                    }
                }
            }
        }
        $tempString = [];
        foreach ($data as $array) {
            $temp = $this->formatChar($array);
            //$str[] = $this->formatChar($array);
            //dump($str);
            $tempArray = $this->getStringArray($temp);
            foreach ($tempArray as $value) {
                $replace = "json_encode(" . $value . ")";
                $temp    = str_replace($value, $replace, $temp);
            }
            $tempString[] = $temp;
        }
        return $tempString;
    }

    /**
     * 获取字符串中数组串
     * @param $str
     * @return array
     */
    protected function getStringArray($str)
    {
        $len  = strlen($str);
        $temp = '';
        $tag  = [];
        $data = [];
        for ($i = 0; $i < $len; $i++) {
            $string = mb_substr($str, $i, 1);
            if ($string == "[") {
                $temp .= $string;
                array_push($tag, "[");
                continue;
            }
            if (!empty($temp) && $string <> "]") {
                $temp .= $string;
                continue;
            }
            if (!empty($temp) && $string == "]") {
                $temp .= $string;
                array_pop($tag);
                if (empty($tag)) {
                    $data[] = $temp;
                    $temp   = '';
                }
            }
        }
        return $data;
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

    protected function isJson($data = '', $assoc = false)
    {
        $data = json_decode($data, $assoc);
        if (($data && is_object($data)) || (is_array($data) && !empty($data))) {
            return $data;
        }
        return false;
    }

    public function init()
    {
        $str  = '         $data = array(' . "\n";
        $data = $this->getData();
        foreach ($data as $datum) {
            $str .= '            [' . "\n";
            $str .= "                " . $datum . "\n";
            $str .= '            ],' . "\n";
        }
        $str .= "        );";
        $str .= "\n";
        $str .= '        $posts = $this->table(' . $this->tableName . ');' . "\n";
        $str .= '        $posts->insert($data)' . "\n";
        $str .= '              ->save();' . "\n";
        return $str;
    }
}
