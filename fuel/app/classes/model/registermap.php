<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/05/26
 * Time: 10:15
 */

/**
 * 地点登録用テーブル
 *
 * @package  app
 * @extends  Model_Crud
 */
class Model_Registermap extends Model_Crud
{
	protected static $_table_name = 'register_map';
	protected static $_primary_key = 'register_map_id';
	protected static $_created_at = 'created_at';
	protected static $_updated_at = 'updated_at';
	protected static $_mysql_timestamp = false;
}