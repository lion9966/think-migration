# think-migration

thinkphp6 数据库迁移工具改进版

在原有的topthink/think-migration原有的基础上增加自动获取对应表的结构，自动获取已存储的数据，从而方便搬家，安装，操作方法一样。

目前只针对mysql数据库，其他数据库可以参考自行增加。

## 安装

~~~
composer require lion9966/think-migration
~~~

## 使用方法(同topthink/think-migration)

~~~
命令：
#查询命令: php think 

#结构 migrate 命令：

migrate:breakpoint                     #Manage breakpoints

migrate:create Model名（首字母大写）     #Create a new migration

migrate:rollback                       #Rollback the last or to a specific migration

migrate:run 或 Model名                  #Migrate the database

migrate:status                         #Show migration status



#数据 seed 命令：

seed:create Model名（首字母大写）         #Create a new database seeder

seed:run 或 Model名                     #Run database seeders
~~~
