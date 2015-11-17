<h2>分析結果表示</h2>
<ul>
    	<!--データベースの情報をadviceにもってきている-->
<?php
    //echo debug($Data);
    //echo debug($Advice);
    foreach($Advice as $advice):
    //echo debug($advice['Advice']['advice']); //アドバイスの参照を行っている部分
?>
<?php endforeach;
    //この下の処理でdata[]に項目困難度を入力している。
    foreach($Data[1] as $data[]): ?>
<?php endforeach;
    //クロンバックのα係数を記述
    echo "<BR>";
    echo "クロンバックのα係数：";
    echo '<font color="red">'.$Data[2].'</font>'."<BR><BR>";
    echo "信頼性が高く、全体的に良いテストです。<BR>";
    echo "平均点:".'<font color="red">74.5点</font>'."<BR>";
    echo "最高点:".'<font color="red">98点</font>'."<BR>";
    echo "最低点:".'<font color="red">41点</font>'."<BR>";
    echo "<BR>";
    echo $this->Html->image('/img/zu1.png',array('width'=>'300','height'=>'200'));
    //echo "各問題の項目特性図を表示します。<BR>詳しい説明についてはこちらをクリック";
    //echo $html->image();
    //echo $this->link();
    // echo "<BR>問題1.<BR>";
    // //echo $this->Html->image('/img/zu1.png');
    // echo "<BR>問題2.<BR>";
    // //echo $this->Html->image('/img/zu2.png');
    // echo "<BR>問題3.<BR>";
    // echo $this->Html->image('/img/zu3.png');
    // echo "<BR>問題4.<BR>";
    // echo $this->Html->image('/img/zu4.png');
    // echo "<BR>問題5.<BR>";
    // echo $this->Html->image('/img/zu5.png');
    // foreach($Data[0] as $key => $value){
    //     if(5 < $key && $key < 10){
    //         echo "<BR>問題".$key.".<BR>";
    //         echo $this->Html->image('/img/zu1.png');
    //     }elseif(10 < $key && $key<20){
    //         echo "<BR>問題".$key.".<BR>";
    //         echo $this->Html->image('/img/zu2.png');
    //     }elseif(20 < $key && $key<30){
    //         echo "<BR>問題".$key.".<BR>";
    //         echo $this->Html->image('/img/zu3.png');
    //     }elseif(30 < $key&& $key<50){
    //         echo "<BR>問題".$key.".<BR>";
    //         echo $this->Html->image('/img/zu4.png');
    //     }elseif(50 < $key&& $key< 65){
    //         echo "<BR>問題".$key.".<BR>";
    //         echo $this->Html->image('/img/zu5.png');
    //     }else{
    //         echo "<BR>問題".$key.".<BR>";
    //         echo $this->Html->image('/img/zu1.png');
    //     }
    // }
    ?>
	<table border="1">
		<?php
    foreach($Data[0] as $key => $value){
	echo '<tr>';
//  	echo '<td>'. "素点".$score[$key+1].'</td>';
    echo '<td>'.$key1=$key ."問目：項目識別度".$value.'</td>';
    echo '<td>'.$key2=$key ."問目：項目困難度".$data[$key-1].'</td>';
	echo '</tr>';
	}
?>
</table>
</ul>
<div id="font_button", style="text-align:center;">
<?php
    echo $this->Form->postButton('作問アドバイス生成', array('controller' => 'Advices', 'action' => 'rule'));
    echo $this->Form->end();
?>
</div>
