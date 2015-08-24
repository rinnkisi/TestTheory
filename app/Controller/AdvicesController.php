<?php
class AdvicesController extends AppController{
	public $helpers = array('Form', 'Html', 'Js', 'Time');
    public function login(){

    }
    public function index(){

    }
    public function advice() {
    // ポストデータがあれば保存をする（保存ボタンが押された場合）
    $this->Advice->set( $this->request->data );
    //debug($this->request->data)
        //$this->set('Advices',$this->Advice->find('all'));
        if ($this->request->is('post')) {
        	//echo debug($this->request->data);
            //保存する
                //$this->set('Data',$this->request->data);
                //Dataをmodelに送っている
                $Data =$this->request->data;
                //modelを呼びだしreturnの値を返している
                $atai=$this->Advice->calculator($Data);
                //ビューに値をおくっている。↓
                $this->set("Data",$atai);
                //アドバイスDBから情報をもってきている
                $advices=$this->Advice->find('all');
                //アドバイスDBをセットする
                $this->set('Advice',$advices);
                if ($this->Advice->save($this->request->data)){
                    // メッセージをセットしてリダイレクトする
                    //$this->Session->setFlash('データを登録しました！');
                    //return $this->redirect(array('action'=>'advice'));
                }else{
                    //保存が失敗した場合のメッセージ
                    //$this->Session->setFlash('登録に失敗しました');
                }
        }else{
            //ポストデータがない場合の処理
        }
                //set('送信する変数名',$this->Model名->find('条件'));
    }
    public function rule(){

    }
}
