<?php

namespace think\migration\command\stubs\Adapter;

interface AdapterFace
{

    /**
     * 获取表格的头部
     * @return array
     */
    public function getTableHeader(): array;

    /**
     * 获取表格身体-字段信息
     * @return array
     */
    public function getTableBody(): array;

    /**
     * 获取索引
     * @return array
     */
    public function getTableIndex(): array;

    /**
     * 获取PK
     * @return $this
     */
    public function getPk();

    /**
     * 获取FK
     * @return $this
     */
    public function getFk();

    /**
     * 计算FK
     * @return $this
     */
    public function getForeign();

    /**
     * 获取结果字符串
     * @return string
     */
    public function init(): string;
}
