<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/08/09
 * Time: 15:20
 *
 * The Listmap Index Controller.
 *
 * マイ地点初期表示
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Listmap_Index extends Controller
{
	/**
	 * マイページのプロフィール
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_index()
	{
		//		$data = DB::select(
		//			'register_map.register_map_id', 'lat', 'lng', 'place', 'address', 'description', 'image',
		//			'register_map.created_at', 'username'
		//		)
		//			->from('register_map')
		//			->join('users', 'inner')
		//			->on('register_map.users_id', '=', 'id')
		//			->join('my_point', 'inner')
		//			->on('register_map.register_map_id', '=', 'my_point.register_map_id')
		//			->where(
		//				array(
		//					//					array('lat', '>=', Input::post('f_b')),
		//					//					array('lat', '<=', Input::post('f_f')),
		//					//					array('lng', '>=', Input::post('b_b')),
		//					//					array('lng', '<=', Input::post('b_f')),
		//					'status_flag' => 1,
		//					'my_point.users_id'    => Auth::get_user_id()[1],
		//				)
		//			)
		$data['map_list'] = DB::select(
			'register_map.register_map_id', 'lat', 'lng', 'place', 'address', 'description', 'image',
			'register_map.created_at', 'username'
		)
			->from('register_map')
			->join('users', 'inner')
			->on('register_map.users_id', '=', 'id')
			->and_on('status_flag', '=', DB::expr(1))
			->join('my_point', 'inner')
			->on('register_map.register_map_id', '=', 'my_point.register_map_id')
			->and_on('my_point.users_id', '=', DB::expr(Auth::get_user_id()[1]))/*->where(
				array(
					//					array('lat', '>=', Input::post('f_b')),
					//					array('lat', '<=', Input::post('f_f')),
					//					array('lng', '>=', Input::post('b_b')),
					//					array('lng', '<=', Input::post('b_f')),
					'status_flag'       => 1,
					'my_point.users_id' => Auth::get_user_id()[1],
				)
			)*/
			->limit(10)
			->execute()
			->as_array();

		$data['map_list_2'] = DB::select(
			'register_map.register_map_id', 'lat', 'lng', 'place', 'address', 'description', 'image',
			'register_map.created_at', 'username'
		)
			->from('register_map')
			->join('users', 'inner')
			->on('register_map.users_id', '=', 'id')
			->and_on('status_flag', '=', DB::expr(1))
			->join('my_point', 'inner')
			->on('register_map.register_map_id', '=', 'my_point.register_map_id')
			->and_on('my_point.users_id', '=', DB::expr(Auth::get_user_id()[1]))/*->where(
				array(
					//					array('lat', '>=', Input::post('f_b')),
					//					array('lat', '<=', Input::post('f_f')),
					//					array('lng', '>=', Input::post('b_b')),
					//					array('lng', '<=', Input::post('b_f')),
					'status_flag'       => 1,
					'my_point.users_id' => Auth::get_user_id()[1],
				)
			)*/
			->execute()
			->as_array();
		$map_list_2_cnt = count($data['map_list_2']);
		$content_cnt = 2.0;//実装が終わったら10にする
		$pages_tmp = $map_list_2_cnt / $content_cnt;
		$data['pages'] = 0;
		if (1 < $pages_tmp) $data['pages'] = ceil($pages_tmp);
		$data['current'] = 1;
		if (Input::get('current')) $data['current'] = Input::get('current');
		$data['start'] = 0;
		if(1 < $data['current']) $data['start'] = ($data['current'] -1) * $content_cnt;

		$data['title'] = 'マイ地点一覧';

		return View_Smarty::forge('listmap/index', $data);
	}

	/*public function action_my_point()
	{
		echo 'ページング';
	}*/
}