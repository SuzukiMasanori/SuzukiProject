<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/05/12
 * Time: 16:24
 */

/**
 * The Validation Component.
 *
 * 独自値検証
 *
 * @package  app
 * @extends  Controller
 */
class Component_Validation
{
	/**
	 * パスワードの確認値が同じかを検証
	 *
	 * @access  public
	 * @param String $data1
	 * @param String $data2
	 * @return  bool
	 */
	public static function _validation_password_same($data1, $data2)
	{
		if ($data1 != $data2)
		{
			Validation::active()->set_message('password_same', 'パスワードの確認入力に差異があります');

			return false;
		}

		return true;
	}

	/**
	 * パスワードがあっているか検証
	 *
	 * @access  public
	 * @param String $password
	 * @return  bool
	 */
	public static function _validation_password_confirm($password)
	{
		if (Auth::validate_user(Auth::get_screen_name(), $password))
		{
			return true;
		}
		else
		{
			Validation::active()->set_message('password_confirm', 'パスワードが違います');

			return false;
		}
	}

	/**
	 * ユーザー名が重複しているか検証
	 *
	 * @access  public
	 * @param String $username
	 * @return  bool
	 */
	public static function _validation_username_double_check($username, $edit = false)
	{
		// プロフィール変更時にユーザ名の変更がかからない場合は検証しない
		if ($edit and $username == Auth::get_screen_name())
		{
			return true;
		}

		$user = Model_Users::find_one_by('username', $username);
		if ($user)
		{
			Validation::active()->set_message('username_double_check', '入力されたユーザー名はすでに登録されています');

			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * メールが重複しているか検証
	 *
	 * @access  public
	 * @param String $username
	 * @return  bool
	 */
	public static function _validation_email_double_check($email, $edit = false)
	{
		// プロフィール変更時にユーザ名の変更がかからない場合は検証しない
		if ($edit and $email == Auth::get('email'))
		{
			return true;
		}

		$user = Model_Users::find_one_by('email', $email);
		if ($user)
		{
			Validation::active()
				->set_message('email_double_check', '入力されたメールアドレスはすでに登録されています');

			return false;
		}
		else
		{
			return true;
		}
	}
}