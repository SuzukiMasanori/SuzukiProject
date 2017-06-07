<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/04/28
 * Time: 13:52
 */

/**
 * The User Input Controller.
 *
 * 会員登録
 *
 * @package  app
 * @extends  Controller
 */
class Controller_User_Input extends Controller
{
	/**
	 * 会員登録の値検証
	 *
	 * @access  private
	 * @return  Validation
	 */
	private function user_validate()
	{
		$password_confirm = Input::post('password_confirm');
		$email = Input::post('email');
		$my_agree = Input::post('agree');
		$val = Validation::forge();
		$val->add_callable('Component_Validation');
		$val->add('username', 'ユーザー名')
			->add_rule('required')
			->add_rule('max_length', 50)
			->add_rule(
				'username_double_check'
			);
		$val->add('password', 'パスワード')
			->add_rule('required')
			->add_rule('password_same', $password_confirm)
			->add_rule(
				'max_length', 50
			);
		$val->add('email', 'メールアドレス')
			->add_rule('required')
			->add_rule('valid_email')
			->add_rule(
				'email_double_check'
			/*[
				'closure_m1' => function () use ($email)
				{
					$user = Model_Users::find_one_by('email', $email);
					if ($user)
					{
						Validation::active()
							->set_message('closure_m1', '入力されたメールはすでに登録されています');

						return false;
					}
					else
					{
						returnrue;
					}
				},
			]*/
			);
		$val->add('agree', '利用規約に同意する')
			->add_rule(
				[
					'closure_a1' => function () use ($my_agree)
					{
						if ($my_agree == 'on')
						{
							return true;
						}
						else
						{
							Validation::active()
								->set_message('closure_a1', '利用規約の同意がありません');

							return false;
						}
					},
				]
			);

		return $val;
	}

	/**
	 * 会員登録時最初に表示される入力画面
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_index()
	{
		$data['title'] = 'ユーザ登録';

		return View_Smarty::forge('user/input', $data);
	}

	/**
	 * 会員登録時確認画面。値検証エラーの場合は入力画面表示
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_confirm()
	{
		$val = $this->user_validate();

		if ($val->run())
		{
			$data['title'] = 'ユーザ登録確認';

			return View_Smarty::forge('user/confirm', $data);
		}
		else
		{
			$error_arr = array();
			foreach ($val->error() as $key => $error) $error_arr[$key] = $error . '';
			$error_arr['title'] = 'ユーザ登録エラー';

			return View_Smarty::forge('user/input', $error_arr);
		}
	}

	/**
	 * 会員登録、成功時はログイン画面へリダイレクト。値検証エラーの場合は入力画面表示
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_register()
	{
		$val = $this->user_validate();
		$error_arr = array();

		if ($val->run())
		{
			//echo "登録";
			try
			{
				$is_create_user = Auth::create_user(
					Input::post('username'),
					Input::post('password'),
					Input::post('email')
				);

				if ($is_create_user)
				{
					Response::redirect("/user/login?create=true");
				}
				else
				{
					$error_arr['username'] = 'データを登録できませんでした';
				}
			}
			catch (SimpleUserUpdateException $e)
			{
				if ($e->getCode() == 2)
				{
					$error_arr['email'] = '入力されたメールアドレスはすでに登録されています';
				}
				else if ($e->getCode() == 3)
				{
					$error_arr['username'] = '入力されたユーザー名はすでに登録されています';
				}
				else
				{
					$error_arr['username'] = $e->getMessage();
				}
			}
		}
		else
		{
			foreach ($val->error() as $key => $error) $error_arr[$key] = $error . '';
		}
		$error_arr['title'] = 'ユーザ登録エラー';

		return View_Smarty::forge('user/input', $error_arr);
	}
}