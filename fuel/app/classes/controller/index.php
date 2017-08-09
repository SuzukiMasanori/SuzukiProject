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
		//		$data['params'] = $this->request->route->method_params;
		$data['title'] = 'トップ';

		//		if ($data['params']) return View::forge('index/index', $data);
		//		else return View_Smarty::forge('index/index', $data);
		$data['rank_map'] = Model_Registermap::find(
			array(
				'where'    => array('status_flag' => 1,),
				'order_by' => array('views' => 'desc',),
				'limit'    => 5,
			)
		);
		$data['new_map'] = Model_Registermap::find(
			array(
				'where'    => array('status_flag' => 1,),
				'order_by' => array('created_at' => 'desc',),
				'limit'    => 5,
			)
		);
//		$data['rand_map'] = Model_Registermap::find(
//			array(
//				'select' => array('image'),
//				'where' => array(
//					'status_flag' => 1,
//					array('image', 'is', DB::expr('NOT NULL')),
//				),
//				'order_by' => array(DB::expr('RAND()'),),
//				'limit' => 5,
//			)
//		);
		$data['rand_map_images'] = DB::select()
			->from('register_map')
			->where(
				array(
					'status_flag' => 1,
					array('image', 'is', DB::expr('NOT NULL')),
				)
			)
			->order_by(DB::expr('RAND()'))
			->limit(10)
			->execute()
			->as_array();
		error_log(var_export($query, true) . PHP_EOL . 'registermap_edit');

		return View_Smarty::forge('index/index', $data);
	}
}