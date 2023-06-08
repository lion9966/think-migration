<?php
/**
 * Phinx
 *
 * (The MIT license)
 * Copyright (c) 2015 Rob Morgan
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated * documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @package    Phinx
 * @subpackage Phinx\Db\Adapter
 */

namespace Phinx\Db\Adapter;

use think\console\Input as InputInterface;
use think\console\Output as OutputInterface;
use Phinx\Db\Table;
use Phinx\Db\Table\Column;
use Phinx\Db\Table\Index;
use Phinx\Db\Table\ForeignKey;
use Phinx\Migration\MigrationInterface;

/**
 * Adapter Interface.
 *
 * @author Rob Morgan <robbym@gmail.com>
 */
interface AdapterInterface
{
    const PHINX_TYPE_STRING      = 'string';
    const PHINX_TYPE_CHAR        = 'char';
    const PHINX_TYPE_TEXT        = 'text';
    const PHINX_TYPE_INTEGER     = 'integer';
    const PHINX_TYPE_BIG_INTEGER = 'biginteger';
    const PHINX_TYPE_FLOAT       = 'float';
    const PHINX_TYPE_DECIMAL     = 'decimal';
    const PHINX_TYPE_DATETIME    = 'datetime';
    const PHINX_TYPE_TIMESTAMP   = 'timestamp';
    const PHINX_TYPE_TIME        = 'time';
    const PHINX_TYPE_DATE        = 'date';
    const PHINX_TYPE_BINARY      = 'binary';
    const PHINX_TYPE_VARBINARY   = 'varbinary';
    const PHINX_TYPE_BLOB        = 'blob';
    const PHINX_TYPE_BOOLEAN     = 'boolean';
    const PHINX_TYPE_JSON        = 'json';
    const PHINX_TYPE_JSONB       = 'jsonb';
    const PHINX_TYPE_UUID        = 'uuid';
    const PHINX_TYPE_FILESTREAM  = 'filestream';

    // Geospatial database types
    const PHINX_TYPE_GEOMETRY   = 'geometry';
    const PHINX_TYPE_POINT      = 'point';
    const PHINX_TYPE_LINESTRING = 'linestring';
    const PHINX_TYPE_POLYGON    = 'polygon';

    // only for mysql so far
    const PHINX_TYPE_ENUM = 'enum';
    const PHINX_TYPE_SET  = 'set';

    /**
     * Get all migrated version numbers.
     * 得到所有迁移版本号。
     * @return array
     */
    public function getVersions();

    /**
     * Get all migration log entries, indexed by version number.
     * 得到所有迁移日志条目,按版本号。
     * @return array
     */
    public function getVersionLog();

    /**
     * Set adapter configuration options.
     * 设置适配器配置选项
     * @param array $options
     * @return AdapterInterface
     */
    public function setOptions(array $options);

    /**
     * Get all adapter options.
     * 得到所有适配器选项
     * @return array
     */
    public function getOptions();

    /**
     * Check if an option has been set.
     * 检查是否已设置的一个选择
     * @param string $name
     * @return boolean
     */
    public function hasOption($name);

    /**
     * Get a single adapter option, or null if the option does not exist.
     * 得到一个适配器选项,或null如果选择不存在。
     * @param string $name
     * @return mixed
     */
    public function getOption($name);

    /**
     * Sets the console input.
     * 设置控制台输入
     * @param InputInterface $input Input
     * @return AdapterInterface
     */
    public function setInput(InputInterface $input);

    /**
     * Gets the console input.
     * 得到了控制台输入
     * @return InputInterface
     */
    public function getInput();

    /**
     * Sets the console output.
     * 设置控制台输出
     * @param OutputInterface $output Output
     * @return AdapterInterface
     */
    public function setOutput(OutputInterface $output);

    /**
     * Gets the console output.
     * 得到了控制台输出
     * @return OutputInterface
     */
    public function getOutput();

    /**
     * Records a migration being run.
     * 记录迁移正在运行
     * @param MigrationInterface $migration Migration
     * @param string $direction Direction
     * @param int $startTime Start Time
     * @param int $endTime End Time
     * @return AdapterInterface
     */
    public function migrated(MigrationInterface $migration, $direction, $startTime, $endTime);

    /**
     * Toggle a migration breakpoint.
     * 迁移切换断点
     * @param MigrationInterface $migration
     *
     * @return AdapterInterface
     */
    public function toggleBreakpoint(MigrationInterface $migration);

    /**
     * Reset all migration breakpoints.
     * 重置所有迁移断点
     * @return int The number of breakpoints reset
     */
    public function resetAllBreakpoints();

    /**
     * Does the schema table exist?
     * 模式表存在吗?
     * @return boolean
     * @deprecated use hasTable instead.
     */
    public function hasSchemaTable();

    /**
     * Creates the schema table.
     * 创建模式表
     * @return void
     */
    public function createSchemaTable();

    /**
     * Returns the adapter type.
     * 返回的适配器类型。
     * @return string
     */
    public function getAdapterType();

    /**
     * Initializes the database connection.
     * 初始化数据库连接
     * @return void
     * @throws \RuntimeException When the requested database driver is not installed.
     */
    public function connect();

    /**
     * Closes the database connection.
     * 关闭数据库连接
     * @return void
     */
    public function disconnect();

    /**
     * Does the adapter support transactions?
     * 适配器是否支持事件?
     * @return boolean
     */
    public function hasTransactions();

    /**
     * Begin a transaction.
     * 开始一个事件。
     * @return void
     */
    public function beginTransaction();

    /**
     * Commit a transaction.
     * 提交一个事件
     * @return void
     */
    public function commitTransaction();

    /**
     * Rollback a transaction.
     * 回滚一个事件
     * @return void
     */
    public function rollbackTransaction();

    /**
     * Executes a SQL statement and returns the number of affected rows.
     * 执行一个SQL语句并返回受影响的行数。
     * @param string $sql SQL
     * @return int
     */
    public function execute($sql);

    /**
     * Executes a SQL statement and returns the result as an array.
     * 执行一个SQL语句,并返回结果为一个数组。
     * @param string $sql SQL
     * @return array
     */
    public function query($sql);

    /**
     * Executes a query and returns only one row as an array.
     * 执行查询,并返回数组只有一行。
     * @param string $sql SQL
     * @return array
     */
    public function fetchRow($sql);

    /**
     * Executes a query and returns an array of rows.
     * 执行一个查询,返回一个行数组。
     * @param string $sql SQL
     * @return array
     */
    public function fetchAll($sql);

    /**
     * Inserts data into a table.
     * 将数据插入一个表。
     * @param Table $table where to insert data
     * @param array $row
     * @return void
     */
    public function insert(Table $table, $row);

    /**
     * Quotes a table name for use in a query.
     * 引用用于查询的表名-点符号
     * @param string $tableName Table Name
     * @return string
     */
    public function quoteTableName($tableName);

    /**
     * Quotes a column name for use in a query.
     * 引用用于查询的列名-斜点符号
     * @param string $columnName Table Name
     * @return string
     */
    public function quoteColumnName($columnName);

    /**
     * Checks to see if a table exists.
     * 检查表是否存在。
     * @param string $tableName Table Name
     * @return boolean
     */
    public function hasTable($tableName);

    /**
     * Creates the specified database table.
     * 创建指定的数据库表。
     * @param Table $table Table
     * @return void
     */
    public function createTable(Table $table);

    /**
     * Renames the specified database table.
     * 重命名指定的数据库表。
     * @param string $tableName Table Name
     * @param string $newName New Name
     * @return void
     */
    public function renameTable($tableName, $newName);

    /**
     * Drops the specified database table.
     * 删除指定的数据库表
     * @param string $tableName Table Name
     * @return void
     */
    public function dropTable($tableName);

    /**
     * Returns table columns
     * 返回表的列
     * @param string $tableName Table Name
     * @return Column[]
     */
    public function getColumns($tableName);

    /**
     * Checks to see if a column exists.
     * 检查是否存在一个列。字段
     * @param string $tableName Table Name
     * @param string $columnName Column Name
     * @return boolean
     */
    public function hasColumn($tableName, $columnName);

    /**
     * Adds the specified column to a database table.
     * 将指定的列添加到一个数据库表中。添加字段
     * @param Table $table Table
     * @param Column $column Column
     * @return void
     */
    public function addColumn(Table $table, Column $column);

    /**
     * Renames the specified column.
     * 重命名指定的列。字段改名
     * @param string $tableName Table Name
     * @param string $columnName Column Name
     * @param string $newColumnName New Column Name
     * @return void
     */
    public function renameColumn($tableName, $columnName, $newColumnName);

    /**
     * Change a table column type.
     * 修改一个表列包含修改类型。
     * @param string $tableName Table Name
     * @param string $columnName Column Name
     * @param Column $newColumn New Column
     * @return Table
     */
    public function changeColumn($tableName, $columnName, Column $newColumn);

    /**
     * Drops the specified column.
     * 删除指定的列。删除字段
     * @param string $tableName Table Name
     * @param string $columnName Column Name
     * @return void
     */
    public function dropColumn($tableName, $columnName);

    /**
     * Checks to see if an index exists.
     * 检查是否存在一个索引。
     * @param string $tableName Table Name
     * @param mixed $columns Column(s)
     * @return boolean
     */
    public function hasIndex($tableName, $columns);

    /**
     * Checks to see if an index specified by name exists.
     * 通过名称判断是否有索引 结果真假
     * @param string $tableName Table Name
     * @param string $indexName
     * @return boolean
     */
    public function hasIndexByName($tableName, $indexName);

    /**
     * Adds the specified index to a database table.
     * 将指定索引添加到一个数据库表中。
     * @param Table $table Table
     * @param Index $index Index
     * @return void
     */
    public function addIndex(Table $table, Index $index);

    /**
     * Drops the specified index from a database table.
     * 从数据库表中索引移除
     * @param string $tableName
     * @param mixed $columns Column(s)
     * @return void
     */
    public function dropIndex($tableName, $columns);

    /**
     * Drops the index specified by name from a database table.
     * 通过名称删除索引
     * @param string $tableName
     * @param string $indexName
     * @return void
     */
    public function dropIndexByName($tableName, $indexName);

    /**
     * Checks to see if a foreign key exists.
     * 检查是否存在一个外键。
     * @param string $tableName
     * @param string[] $columns Column(s)
     * @param string $constraint Constraint name
     * @return boolean
     */
    public function hasForeignKey($tableName, $columns, $constraint = null);

    /**
     * Adds the specified foreign key to a database table.
     * 将指定的外键添加到一个数据库表中。
     * @param Table $table
     * @param ForeignKey $foreignKey
     * @return void
     */
    public function addForeignKey(Table $table, ForeignKey $foreignKey);

    /**
     * Drops the specified foreign key from a database table.
     * 移除从数据库表中指定的外键。
     * @param string $tableName
     * @param string[] $columns Column(s)
     * @param string $constraint Constraint name
     * @return void
     */
    public function dropForeignKey($tableName, $columns, $constraint = null);

    /**
     * Returns an array of the supported Phinx column types.
     * 返回一个数组的支持Phinx列类型。
     * @return array
     */
    public function getColumnTypes();

    /**
     * Checks that the given column is of a supported type.
     * 检查给定的列是一个受支持的类型
     * @param Column $column
     * @return boolean
     */
    public function isValidColumnType(Column $column);

    /**
     * Converts the Phinx logical type to the adapter's SQL type.
     * 转换Phinx逻辑类型适配器的SQL类型。获取sql字段类型名称或长度
     * @param string $type
     * @param integer $limit
     * @return string
     */
    public function getSqlType($type, $limit = null);

    /**
     * Creates a new database.
     * 创建一个新的数据库
     * @param string $name Database Name
     * @param array $options Options
     * @return void
     */
    public function createDatabase($name, $options = array());

    /**
     * Checks to see if a database exists.
     * 检查数据库是否存在。
     * @param string $name Database Name
     * @return boolean
     */
    public function hasDatabase($name);

    /**
     * Drops the specified database.
     * 删除指定名称的数据库
     * @param string $name Database Name
     * @return void
     */
    public function dropDatabase($name);
}
