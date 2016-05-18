//グローバル変数
var sX_Modal = 0 ;
var sY_Modal = 0 ;

$(function(){

//グローバル変数
var nowModal = null ;		//現在開かれているモーダルコンテンツ
var modalClass = "modal" ;		//モーダルを開くリンクに付けるクラス名

//モーダルのリンクを取得する
var modals = document.getElementsByClassName( modalClass ) ;

//モーダルウィンドウを出現させるクリックイベント
for(var i=0,l=modals.length; l>i; i++){

	//全てのリンクにタッチイベントを設定する
	modals[i].onclick = function(){

		//ボタンからフォーカスを外す
		this.blur() ;

		//ターゲットとなるコンテンツを確認
		var target = this.getAttribute( "data-target" ) ;

		//ターゲットが存在しなければ終了
		if( typeof( target )=="undefined" || !target || target==null ){
			return false ;
		}

		//コンテンツとなる要素を取得
		nowModal = document.getElementById( target ) ;

		//ターゲットが存在しなければ終了
		if( nowModal == null ){
			return false ;
		}

		//キーボード操作などにより、オーバーレイが多重起動するのを防止する
		if( $( "#modal-overlay" )[0] ) return false ;		//新しくモーダルウィンドウを起動しない
		//if($("#modal-overlay")[0]) $("#modal-overlay").remove() ;		//現在のモーダルウィンドウを削除して新しく起動する


		//スクロール位置を記録する
		var dElm = document.documentElement , dBody = document.body;
		sX_Modal = dElm.scrollLeft || dBody.scrollLeft;	//現在位置のX座標
		sY_Modal = dElm.scrollTop || dBody.scrollTop;		//現在位置のY座標
		//オーバーレイを出現させる
		$( "body" ).append( '<div id="modal-overlay"></div>' ) ;
		$( "#modal-overlay" ).fadeIn( "fast" ) ;

		//コンテンツをセンタリングする
		centeringModal() ;

		//コンテンツをフェードインする
		$( nowModal ).fadeIn( "slow" ) ;

		//[#modal-overlay]、または[#modal-close]をクリックしたら…
		$( "#modal-overlay,#modal-close" ).unbind().click( function() {

			//スクロール位置を戻す
			window.scrollTo( sX_Modal , sY_Modal );
			//[#modal-content]と[#modal-overlay]をフェードアウトした後に…
			$( "#" + target + ",#modal-overlay" ).fadeOut( "fast" , function() {

				//[#modal-overlay]を削除する
				$( '#modal-overlay' ).remove() ;

			} ) ;

			//現在のコンテンツ情報を削除
			nowModal = null ;

		} ) ;

	}

}

	//リサイズされたら、センタリングをする関数[centeringModal()]を実行する
	$( window ).resize( centeringModal ) ;

	//センタリングを実行する関数
	function centeringModal() {

		//モーダルウィンドウが開いてなければ終了
		if( nowModal == null ) return false ;

		//画面(ウィンドウ)の幅、高さを取得
		var w = $( window ).width() ;
		var h = $( window ).height() ;

		//コンテンツ(#modal-content)の幅、高さを取得
		// jQueryのバージョンによっては、引数[{margin:true}]を指定した時、不具合を起こします。
//		var cw = $( nowModal ).outerWidth( {margin:true} ) ;
//		var ch = $( nowModal ).outerHeight( {margin:true} ) ;
		var cw = $( nowModal ).outerWidth() ;
		var ch = $( nowModal ).outerHeight() ;

		//センタリングを実行する
		$( nowModal ).css( {"left": ((w - cw)/2) + "px","top": ((h - ch)/2) + "px"} ) ;

	}

} ) ;
