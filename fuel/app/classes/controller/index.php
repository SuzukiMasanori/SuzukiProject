<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/04/14
 * Time: 15:21
 */

/**
 * The Index Controller.
 *
 * トップページ
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Index extends Controller
{
	/**
	 * トップページ表示
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_index()
	{
		$data = array();
		$data['params'] = $this->request->route->method_params;
		$data['title'] = 'トップ';

		if ($data['params']) return View::forge('index/index', $data);
		else return View_Smarty::forge('index/index', $data);
	}
}