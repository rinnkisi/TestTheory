<h2>分析結果表示</h2>
<ul>
    	<!--データベースの情報をadviceにもってきている-->
<?php
    //echo debug($Advice);
    foreach($Advice as $advice):
    //echo debug($advice['Advice']['advice']); //アドバイスの参照を行っている部分
    endforeach;
    //この下の処理でdata[]に項目困難度を入力している。
    foreach($Data[1] as $data[]):
    endforeach;
    //クロンバックのα係数を記述
    echo "<BR>";
    echo "クロンバックのα係数：";
    echo '<font color="red">'.$Data[2]['cronbach'].'</font>'."<BR><BR>";
    echo "受験者人数:".'<font color="red">'.$Data[2]['people'].'</font>　名'."<BR>";
    echo "　　平均点:".'<font color="red">'.$Data[2]['average'].'</font>'."<BR>";
    echo "　　　分散:".'<font color="red">'.$Data[2]['score_dispersion'].'</font>'."<BR>";
    echo "　　最高点:".'<font color="red">'.$Data[2]['top_score'].'</font>'."<BR>";
    echo "　　最低点:".'<font color="red">'.$Data[2]['low_score'].'</font>'."<BR>";
    echo "<BR>";
    echo $this->Html->image('/img/zu1.png',array('width'=>'300','height'=>'200'));
    ?>
	<table border="1">
		<?php
    foreach($Data[0] as $key => $value){
	echo '<tr>';
    // echo '<td>'. "素点".$score[$key+1].'</td>';
    $number = $key + 1;
    echo '<td>'. $number ."問目：項目識別度".$value.'</td>';
    echo '<td>'. $number ."問目：項目困難度".$data[$key].'</td>';
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
