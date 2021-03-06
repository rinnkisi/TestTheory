<?php
class AdvicesController extends AppController{
	public $uses = array('Advice','Description');
	public $helpers = array('Form', 'Html', 'Js', 'Time');

	public function form()
	{
		$this->set('data', $this->Session->read('session_data'));
		if($this->Session->read('session_data'))
		{
			$data = array('Advice' => array('advice' => $this->Session->read('session_data')));
			// 登録する項目（フィールド指定）
			$fields = array('advice');
			// 新規登録
			$this->Advice->save($data, false, $fields);
			//$this->Advice->save($this->Session->read('session_data'));
			$this->Session->delete('session_data');
		}
		else{
			if($this->request->is('post'))
			{
			 	//var_dump($this->request->data);
				$this->Session->write('session_data', $this->request->data['Advice']['advice']);
				$this->set('data', $this->Session->read('session_data'));

				$this->redirect('form');
				//$this->Article->save($this->data);
			}
		}
	}
    public function login()
	{

    }
	public function csv_file()
	{

	}
    public function index()
	{
		if($this->Session->read('error_log'))
		{
			$this->set('error_log', $this->Session->read('error_log'));
		}
		$this->Session->delete('error_log');
    }
    public function advice()
	{
		//csv形式のファイルがない場合にはindexに飛ばす
		if(empty($this->request->data['csv']))
		{
			$this->redirect('index');
		}
		//空の場合にはエラーとしてint(4)が帰ってくる
		if($this->request->data['csv']['error'] == 4)
		{
			$this->Session->write('error_log', 'CSVファイルを選択してください');
			$this->redirect('index');
		}
		//csvやエクセルファイル意外であればエラーが帰ってくる。sizeのチェックも行う
		//debug($this->request->data['csv']);
		//ファイルをチェックする。
		if($this->Advice->file_check($this->request->data['csv']['name']) == 1)
		{
			$this->Session->write('error_log', 'CSVファイルを選択してください');
			$this->redirect('index');
		}
	    // ポストデータがあれば保存をする（保存ボタンが押された場合）
	    $this->Advice->set($this->request->data);
	    //debug($this->request->data)
        //$this->set('Advices',$this->Advice->find('all'));
		//postがtrueの時
        if ($this->request->is('post'))
		{
        	//echo debug($this->request->data);
            //$this->set('Data',$this->request->data);
            //Dataをmodelに送っている
            $data = $this->request->data;
            //modelを呼びだしreturnの値を返している
            $result = $this->Advice->calculator($data);
            //ビューに値をおくっている。↓
            $this->set("Data", $result);
            //アドバイスDBから情報をもってきている
            $advices = $this->Advice->find('all');
            //アドバイスDBをセットする
            $this->set('Advice',$advices);
			$description = $this->Description->find('all');
			foreach($description as $key => $value):
				 $description[$key] = $value['Description'];
			endforeach;
			$item_description =  $this->Advice->description_group($result);
			foreach($description as $key => $value):
				$description_index[$value['description_id']] = $value['description'];
			endforeach;
			$this->set('item_desc', $item_description );
			$this->set('description', $description_index);

			$item_connect = $this->Advice->item_connect($item_description);
			foreach($advices as $key => $value):
				$advice_id[$value['Advice']['advice_id']] = $value['Advice']['advice'];
			endforeach;
			$this->set('item_connect', $item_connect);
			$this->set('advice', $advice_id);

            if($this->Advice->save($this->request->data))
			{
                // メッセージをセットしてリダイレクトする
                //$this->Session->setFlash('データを登録しました！');
                //return $this->redirect(array('action'=>'advice'));
            }
			else
			{
                //保存が失敗した場合のメッセージ
                //$this->Session->setFlash('登録に失敗しました');
            }
        }
		else
		{
            //ポストデータがない場合の処理
        }
            //set('送信する変数名',$this->Model名->find('条件'));
    }
    public function rule()
	{

    }

}
