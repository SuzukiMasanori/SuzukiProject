<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/04/28
 * Time: 13:52
 */

/**
 * The Register Controller.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_User_Input extends Controller
{
	/**
	 * @access  private
	 * @return  Validation
	 */
	private function user_validate()
	{
		$my_agree = Input::post('agree');
		$password = Input::post('password');
		$password_confirm = Input::post('password_confirm');
		$val = Validation::forge();
		$val->add('username', 'ユーザー名')->add_rule('required');
		$val->add('password', 'パスワード')->add_rule('required')->add_rule(['closure1' => function ($password) use ($password, $password_confirm)
		{
			if ($password == $password_confirm)
			{
				return true;
			}
			else
			{
				echo 'false';
				Validation::active()->set_message('closure1', 'パスワードが確認入力に差異があります');

				return false;
			}
		},]);
		$val->add('email', 'メールアドレス')->add_rule('required')->add_rule('valid_email');
		$val->add('agree', '利用規約に同意する')->add_rule(['closure2' => function ($agree) use ($my_agree)
		{
			if ($my_agree == 'on')
			{
				return true;
			}
			else
			{
				Validation::active()->set_message('closure2', '利用規約の同意がありません');

				return false;
			}
		},]);

		return $val;
	}

	/**
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_index()
	{
		$data = array();
		return View_Smarty::forge('user/input', $data);
	}

	/**
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_confirm()
	{
		$val = $this->user_validate();

		if ($val->run())
		{
			return View_Smarty::forge('user/confirm');
		}
		else
		{
			$error_arr = array();
			foreach ($val->error() as $key => $error)
			{
				$error_arr[$key] = $error . '';
			}

			return View_Smarty::forge('user/input', $error_arr);
		}
	}

	/**
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_register()
	{
		$val = $this->user_validate();

		if ($val->run())
		{
			echo "登録";
		}
		else
		{
			$error_arr = array();
			foreach ($val->error() as $key => $error)
			{
				$error_arr[$key] = $error . '';
			}

			return View_Smarty::forge('user/input', $error_arr);
		}
	}
}