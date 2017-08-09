<?php

/**
 * Created by PhpStorm.
 * User: gelsh
 * Date: 2017/06/05
 * Time: 13:15
 * The Registermap result Controller.
 *
 * 地点検索結果表示
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Searchmap_Result extends Controller
{
	/**
	 * 地点検索結果初期表示
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_index()
	{
		$data['title'] = 'マップ検索';

		return View_Smarty::forge('searchmap/result', $data);
	}

	/**
	 * 地点検索結果表示と投稿表示と投稿フォーム
	 *
	 * @access  public
	 * @return  View_Smarty
	 */
	public function action_place()
	{
		// 地点検索
		if ( ! is_numeric(Input::get('register_map_id'))) Response::redirect('/searchmap/index');
		$result_map = DB::select(
			'register_map_id', 'users_id', 'lat', 'lng', 'place', 'address', 'description', 'num', 'type', 'parking',
			'feed', 'friendly', 'image', 'views', 'register_map.created_at', 'username'
		)
			->from('register_map')
			->join('users', 'inner')
			->on('users_id', '=', 'id')
			->where(
				array(
					'register_map_id' => Input::get('register_map_id'),
				)
			)
			->execute()
			->current();

		// マイ地点の登録の有無
		$is_my_point = Model_Mypoint::find_one_by(
			array(
				'where' => array(
					array(
						'register_map_id' => Input::get('register_map_id'),
						'users_id'        => Auth::get_user_id()[1],
					),
				),
			)
		);
		$data['is_my_point'] = $is_my_point;

		// 閲覧数をカウントアップ
		Model_Registermap::forge()
			->set(
				array(
					'register_map_id' => $result_map['register_map_id'],
					//'views' => 'views + 1',
					'views' => DB::expr('views + 1'),
				)
			)
			->is_new(false)
			->save(false);

		// データがないときはリダイレクト（登録者が閲覧中に削除した場合はリダイレクトがあり得る）
		if ( ! $result_map) Response::redirect('/searchmap/index');

		$result_comment = DB::select(
			'comment_id', 'users_id', 'comment', 'image', 'comment.created_at', 'username'
		)
			->from('comment')
			->join('users', 'inner')
			->on('users_id', '=', 'id')
			->where(
				array(
					'register_map_id' => Input::get('register_map_id'),
					'status_flag' => 1,
				)
			)
			->order_by('comment_id')
			->execute()
			->as_array();

		$data['register_map'] = $result_map;
		$data['comments'] = $result_comment;
		$data['title'] = '地点詳細';
		// 登録者用のテンプレートでの表示判定
		$data['is_modify'] = Auth::get_user_id()[1] == $result_map['users_id'];
		$data['user_id'] = Auth::get_user_id()[1];

		return View_Smarty::forge('searchmap/place', $data);
	}
}