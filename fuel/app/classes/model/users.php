<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/05/10
 * Time: 15:01
 */

/**
 * デフォルトの認証ユーザテーブル
 *
 * @package  app
 * @extends  Model_Crud
 */
class Model_Users extends Model_Crud
{
	protected static $_table_name = 'users';
	protected static $_primary_key = 'id';
}