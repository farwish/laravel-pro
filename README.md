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
    
    
【路由】  
  
  app/Http/routes.php  
  https://laravel.com/docs/5.2/routing  

  1. 基本路由  
    基础路由简单的接收一个URI和匿名函数.  
    默认，routes文件包含一个单独的路由和路由分组，路由分组提供session状态和CSRF保护。  

  ```
    可用的路由方法：
      Route::get($uri, $callback);
      Route::post($uri, $callback);
      Route::put($uri, $callback);
      Route::patch($uri, $callback);
      Route::delete($uri, $callback);
      Route::options($uri, $callback);

    使用match响应多个HTTP规则，用any响应任何HTTP规则：
      Route::match([‘get’, ‘post’], ‘/‘, function() {
      });
      Route::any(‘foo’, function() {
      });
  ```

  路由参数  
  ```
    请求的参数：
     Route::get(‘user/{id}’, function($id) {
     });
     Route::get(‘posts/{post}/comments/{comment}’, function($postId, $commentId) {
     });
     注意：路由参数不能包含 `-` 字符。使用下划线( `_` )代替。

    可选的参数：
     使存在的参数可选，在参数名后使用 `?` ，确保给一个默认值
     Route::get(‘user/{name?}’, function($name = null) {
          return $name;
     })
     Route::get(‘user/{name?}’, function($name = ‘John') {
          return $name;
     })
  ```

  2.有名字的路由  
  ```
    使用as作为数组的key为路由指定一个名字：
     Route::get(‘profile’, [‘as’ => ‘profile’, function() {
     }]);

    指定路由名到控制器方法：
     Route::get(‘prifile’, [
          ‘as’ => ‘profile’, ‘uses’ => ‘UserController@showProfile'
     ]);

    或者用`name` 方法：
     Route::get(‘user/profile’, ‘UserController@showProfile’)->name(‘profile');
  ```

【中间件】  

  提供过滤http请求的机制，所有中间件放在app/Http/Moddleware目录。  

  1.定义中间件：  
  ```
    php artisan make:middleware AgeMiddleware, 在生成类的handle中返回请求前加入过滤代码。

    before/after中间件：BeforeMiddleware，AfterMiddleware
  ```

  2.注册中间件：
  ```
    全局中间件：把中间件类列入app/Http/Kernel.php的 `$middleware` 属性中。  
    分配中间件至路由：追加到app/Http/Kernel.php的 `$routeMiddle` 属性中，并分配一个key。  
    一旦定义了这个中间件，就可以在路由选项中使用这个key，  
    
      Route::get(‘admin/profile’, [‘middleware’ => ‘auth’, function () {
      }]);
      
    用数组分配多个中间件到路由：
      Route::get(‘/‘, [‘middleware’ => [‘first’, ’second’], function () {
      }]);
    
    不用数组的方式，用middleware方法：
      Route::get(‘/‘, function () {
          // ….
      })->middleware([‘first’, ’second']);

    中间件分组：在 `$middlewareGroups` 中定义, 如下使用
       Route::group([‘middleware’ => [‘web’]], function () {
       });

    中间件参数：
      额外的中间件参数在 `$next` 参数之后，
        
          namespace App\Http\Middleware;

          use Closure;

          class RoleMiddleware
          {
               public function handle($request, Closure $next, $role)
               {
                    if (! $request->user()->hasRole($role)) {
                         // Redirect
                    }

                    return $next($request);
               }
          }

          中间件名字和参数间用 `:` 分隔
          Route::put(‘post/{id}’, [‘middleware’ => ‘role:editor’, function ($id) {
               //
          }])

     有期限的中间件：
          在中间件中使用 `terminate` 方法
          namespace Illuminate\Session\Middleware;

          use Closure;

          class StartSession
          {
               public function handle($request, Closure $next)
               {
                    return $next($request);
               }

               public function terminate($request, $response)
               {
                    // Store the session data...
               }
          }
          一旦定义了有期限的中间件，需要加入到全局中间件中。
  ```

【控制器】  
    
  用控制器组织行为，代替在 `routes.php` 中定义所有的请求。  
    
  控制器放在 `app/Http/Controller` 目录。  

  1. 基本控制器:  
    
     ```
          namespace App\Http\Controller

          use App\User;
          use App\Http\Controllers\Controller;

          class UserController extends Controller
          {
               public function showProfile($id)
               {
                    return view(‘user.profile’, [‘user’ => User::findOrFail($id)]);
               }
          }
     ```

  2.路由至控制器：`Route::get(‘user/{id}’, ‘UserController@showProfile');`  
     命名空间`App\Http\Controllers\Photos\AdminController`对应路由： 
          `Route::get(‘foo’, ‘Photos\AdminController@method');`  

     控制器中间件：  
          ```
          Route::get(‘profile’, [
               ‘middleware’ => ‘auth’,
               ‘uses’ => ‘UserController@showProfile'
          ]);
          ```

     在控制器中用 `middleware` 方法调用中间件：  
          ```
          class UserController extends Controller
          {
               public function __construct()
               {
                    $this->middleware(‘auth');

                    $this->middleware(‘log’, [‘only’ => [
                         ‘fooAction’,
                         ‘barAction’,
                    ] ]);

                    $this->middleware(’subscribed’, [‘except’ => [
                         ‘fooAction’,
                         ‘barAction’,
                    ]]);
               }
          }
          ```

     RESTful资源控制器：  
          `php artisan make:controller PhotoController --resource`  
          `php artisan make:controller Photo/PhotoController --resource`  

          注册路由到控制器  
          `Route::resource(‘photo’, ‘PhotoController');`  

     路由缓存  
          `php artisan route:cache` 生成bootstrap/cache/routes.php路由缓存文件  

          `php artisan route:clear` 清除路由缓存  

【FAQs】  
laravel的Filesystem.php第81行报错，storage没有写入权限或需要清除缓存。  
chmod -R 777 storage  
php artisan cache:clear  
