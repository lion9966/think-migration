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

/**
 * Wrapper Interface.包装器接口。
 *
 * @author Woody Gilk <woody.gilk@gmail.com>
 */
interface WrapperInterface
{
    /**
     * Class constructor, must always wrap another adapter.
     * 类构造函数,必须始终把另一个适配器。
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter);

    /**
     * Sets the database adapter to proxy commands to.
     * 设置数据库适配器代理命令。
     * @param AdapterInterface $adapter
     * @return AdapterInterface
     */
    public function setAdapter(AdapterInterface $adapter);

    /**
     * Gets the database adapter.
     * 获取数据库适配器
     * @return AdapterInterface
     * @throws \RuntimeException if the adapter has not been set
     */
    public function getAdapter();
}
