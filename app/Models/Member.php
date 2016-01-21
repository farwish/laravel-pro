<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Member extends Model
{
	//use SoftDeletes;

	protected $table = 'sp_sites';
	protected $primaryKey = 'site_id';

	public $timestamps = false;
	protected $dateFormat = 'U';

	// protected $connection = 'mysql2'; // 多库连接

	protected $filllable = ['employee_id']; // create时允许插入

	protected $guarded = ['nickname']; // create时限制插入

	// protected $dates = ['deleted_at'];
	// protected $hidden = ['passwd']; // 该字段将不包含在返回的数据中
	// protected $visible = ['first_name', 'last_name']; // 设置字段白名单

	public function show()
	{
		return DB::select("select * from {$this->table}");
	}

	public function add($data)
	{
		return DB::insert("insert into {$this->table} (url, title) values(?, ?)", $data);	
	}

	public function edit($url, $site_id)
	{
		return DB::update("update {$this->table} set url = '{$url}' where site_id = '{$site_id}'");	
	}

	public function del($where)
	{
		return DB::delete("delete from {$this->table} where {$where}");	
	}
}
