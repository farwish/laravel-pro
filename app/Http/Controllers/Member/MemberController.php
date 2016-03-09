<?php

namespace App\Http\Controllers\Member;

use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MemberController extends Controller
{
	public function index(Request $request)
	{
		$Member = new Member;

		//============================================

		// Eloquent ORM

		/* 插入一条数据

		$Member->nickname = $request->nickname;

		$Member->save();
		*/

		/* 先查找再更新的方式 
		$Member = Member::find('1');

		$Member->nickname = $request->nickname;

		$Member->save();
		*/

		/* 指定条件更新的方式: 把uid=1和employee_id=0020行的nickname更改为abc
		$res = Member::where('uid', 1)
				->where('employee_id', '0020')
				->update(['nickname' => 'abc']);
		*/

		/* create方式新增数据, $filltable的字段允许, $guarded 的字段不允许
		$res = Member::create(['uid' => '12', 'employee_id' => '00019', 'nickname' => 'aaa']);
		*/

		/* 通过属性取的数据, 没有则新增
		$res = Member::firstOrCreate(['uid' => '12', 'employee_id' => '00019']);
		*/

		/* 返回的对象没有插入数据库, 需要再调用save()才能写入
		$res = Member::firstOrNew(['uid' => '12', 'employee_id' => '00019']);
		*/

		/* 知道主键值的情况, 直接销毁
		$res = Member::destroy(12); // 删除模型主键为12的
		$res = Member:destroy([1, 12]); // 删除多个
		*/

		/* 条件删除
		$res = Member::where('employee_id', '00019')->delete();
		*/

		/* 软删除, 保证有字段deleted_at, 并设置下面三项, 软删后, 字段值为时间戳
		   1. use Illuminate\Database\Eloquent\SoftDeletes;
		   2. use SoftDeletes;
		   3. protected $dates = ['deleted_at'];

		$res = $Member->where('site_id', '1')->delete();
		*/

		/* 不含软删除的查询	
		$res = Member::where('site_id', 1)
					->get();
		*/

		/* 含有软删除数据的查询
		$res = Member::withTrashed()
					->where('site_id', 1)
					->get();
		*/

		/* 取回只含软删除的数据
		$res = $Member->onlyTrashed()
				->where('site_id', 1)
				->get();
		*/
		
		//============================================

		// SQL Queries (见模型中的DB操作)

		$res = $Member->show();

		/*
		$data = [$request->url, $request->title];
		$res = $Member->add($data);
		*/

		/*
		$res = $Member->edit($request->url, $request->site_id);
		*/

		/*
		$where = "site_id = {$request->site_id}";
		$res = $Member->del($where);
		*/
		
		echo '<pre>';
		print_r($res);
	}
}
