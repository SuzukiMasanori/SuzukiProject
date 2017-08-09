<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/07/07
 * Time: 11:30
 * マイ地点登録用テーブル
 *
 * @package  app
 * @extends  Model_Crud
 */

class Model_Mypoint extends Model_Crud
{
	protected static $_table_name = 'my_point';
	protected static $_primary_key = 'my_point_id';
	protected static $_created_at = 'created_at';
	protected static $_updated_at = 'updated_at';
	protected static $_mysql_timestamp = false;
}