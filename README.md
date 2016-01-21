### Laravel PHP Framework project guide
===

【安装laravel】

  通过composer安装：composer create-project laravel/laravel --prefer-dist

  # 移除Laravel 自带了用户注册和认证的脚手架: `php artisan fresh`

    # 目录简单解释
  
      config/ 配置目录
      bootstrap/ 是启动依赖目录
        |- autoload.php 启动最开始加载，注册自动加载/包含编译过的类文件(在cache目录)
        |- app.php 创建应用
        |- cache/ 缓存编译后的类文件？
      
      app/ 应用程序基础命名空间目录
      database/ 数据库基础目录
      resources/ 模板等资源目录
        |- views/
        |- assets/
        |- lang/
      storage/ 存储？
        |- app/
        |- framework/
        |- logs/

【配置】

  1.给目录权限：
          
    给storage/ 和 bootstrap/cache 读写权限，运行 server.php；复制一份.env.example为.env ，并把APP_DEBUG设为true

  2.加密key：
          
    如果通过composer或laravel安装器安装，这个key已经被 key:generate 命令生成，一般32位长，这个key可以在 .env 文件中设置，
  
    如果没有把 .env.example 重命名为 .env，需要重命名一下。这个key用来保证会话安全。

  3.额外配置：
    
    config/app.php     
  
    laravel其余组件 Cache, Database, Session

  4.访问配置值：
      
    访问和设置配置值用config( )函数，$val = config(‘app.timezone’);  config([‘app.timezone’ => ‘PRC']);
     
  5.环境配置：
    
    配置文件中的配置项，是当没有 .env 文件时的默认配置，如：’debug’ => env(‘APP_DEBUG’, false),
    .env 不应该包含在版本控制中，但 .env.example 可以加入，这样别人可以清楚看到，运行你的应用需要哪些配置项。

  6.确定当前环境：
    
    当前的环境由 .env 中的 APP_ENV 变量决定。
    通过这样来访问 $env = App::environment();
    
    判断当前环境的方式：
    ```
      if (App::environment(‘local’)) { 
      
      }
      
      if (App::environment(‘local’, ’staging’)) {
        // local 或者 staging 环境
      }
    ```

    另一种访问方式：$env = app()->environment();

  7.配置的缓存：
    
    运行 php artisam config:cache 命令应该作为生产部署的常规操作。
    
    当本地环境的配置项需要经常改变时，不应该运行这个命令。

  8.维护模式：
    
    如果应用在维护模式下，HttpException 会抛出503状态码。

    要开启维护模式，运行 `php artisan down`

    关闭维护模式，运行 `php artisan up`

    维护模式响应的模板可以自由编辑 `resources/views/errors/503.blade.php`

    维护模式下队列任务不会被处理，直到恢复正常。

    维护模式的替代品，用 "Envoyer" 达到零停机部署。

【Eloquent ORM】

  1.定义模型：

  用model生成工具生成，默认生成的文件是在app目录下，但可以指定是在app的哪个目录:

  `php artisan make:model Models/Member`

  生成的文件如下：
    
  ```
    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Member extends Model
    {
        //
    }
  ```

  2.惯例：

    表名：Eloquent假定Member模型在members表中存储记录。所以需要在model里指定私有属性`$table`。  

    主键：Eloquent假定每个表有一个叫id的主键，所以需要定义私有属性`$primaryKey`覆盖这个惯例。  

    时间戳：默认，Eloquent预计表中有 `created_at` 和`updated_at`，  
    如果不想让Eloquent自动管理这些字段，设置model内的私有属性`$timestamps`为false。  
    如果需要自定义时间戳的格式，设置私有属性`$dateFormat`，这条属性决定日期属性在数据库中如何存储，`$dateFormat = ‘U’` 表示时间戳。  

    数据库连接：默认所有的Eloquent模型使用应用配置的默认数据库连接。  
    如果需要为模型指定不同的连接，使用私有属性`$connection`。需要在 config/database.php 配置多个mysql项。  

    ```
    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Member extends Model
    {
        protected $table = ‘my_members';
        protected $primaryKey = ‘member_id';
     
        public $timestamps = false;
        // protected $dateFormat = ‘U';
        
        // protected $connection = ‘mysql2’;
    }
    ```

  config/database.php

  ```
  'mysql' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', 'localhost'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
    'strict' => false,
  ],

  'mysql2' => [
    'driver' => 'mysql',
    'host' => 'db.abc.com',
    'database' => ‘abcdef_dev',
    'username' => 'admin',
    'password' => 'admin',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
    'strict' => false,
  ],
  ```

  读/写 分离
   
  ``` 
  'mysql' => [
    'read' => [
      'host' => '192.168.1.1',
    ],
    'write' => [
      'host' => '196.168.1.2'
    ],
    'driver'    => 'mysql',
    'database'  => 'database',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
  ],
  ```
  
  3.索引多个模型

  4.事务
  自动控制：

  在`Closure`(匿名函数)中检测到异常自动回滚，Closure中执行成功，则自动提交事务：
  
  ```
  DB::transaction(function ( ) {
      DB::table(‘users’)->update([‘votes’ => 1]);

      DB::table(‘posts’)->delete( );
  })
  ```

  手动控制：
    
    开始事务， DB::beginTransaction( );
    
    回滚，DB::rollBack( );
    
    提交，DB::commit( );


  5.使用多个数据库连接

    先在 config/database.php 中配置新的连接，然后有两种方式使用
    > 可以在model中定义
    > 或者直接写，$users = DB::connection(‘foo’)->select( );
    
    
