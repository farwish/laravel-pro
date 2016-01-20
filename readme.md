## Laravel PHP Framework project guide
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

    用model生成工具生成，默认生成的文件是在app目录下，但可以指定是在app的哪个目录:
    `php artisan make:model Models/Member`

    生成的文件如下：（注意需要额外指定表明和主键id）
    ```
      namespace App\Models;

      use Illuminate\Database\Eloquent\Model;

      class Member extends Model
      {
          protected $table = 'pre_member’; // 关联数据库表名
          protected $primaryKey = 'id’; // 数据库主键id
              
          //public $timestamps = false; // 数据库时间戳
      }
    ```
