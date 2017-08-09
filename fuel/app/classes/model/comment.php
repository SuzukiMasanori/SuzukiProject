<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/06/27
 * Time: 11:00
 *
 *  コメント登録用テーブル
 *
 * @package  app
 * @extends  Model_Crud
 */

class Model_Comment extends Model_Crud
{
	protected static $_table_name = 'comment';
	protected static $_primary_key = 'comment_id';
	protected static $_created_at = 'created_at';
	protected static $_updated_at = 'updated_at';
	protected static $_mysql_timestamp = false;
}