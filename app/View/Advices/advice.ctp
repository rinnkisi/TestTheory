
<h2>分析結果表示</h2>
<ul>

<?php
    echo $this->Html->script("./highcharts.js");
    echo $this->Html->script("./modules/highcharts.js");
//    echo $this->Html->script("./jquery.ui.touch-punch.js");
//    echo $this->Html->script("./jquery.ui.touch-punch.min.js");
    //echo debug($Advice);
    //foreach($Advice as $advice):
    //echo debug($advice['Advice']['advice']); //アドバイスの参照を行っている部分
    //endforeach;
    //この下の処理でdata[]に項目困難度を入力している。
?>

<?php

    foreach($Data[1] as $data[]):
    endforeach;
    //クロンバックのα係数を記述
    echo "<BR>";
    echo "　　問題数:".'<font color = "red">'.$Data[3]['item_sum'].'</font>　問'."<BR>";
    echo "受験者人数:".'<font color = "red">'.$Data[3]['people'].'</font>　名'."<BR>";
    echo "　　平均点:".'<font color = "red">'.$Data[3]['average'].'</font>'."<BR>";
    echo "　　中央値:".'<font color = "red">'.$Data[3]['median'].'</font>'."<BR>";
    foreach($Data[3]['mode'] as $mode_key => $mode_value):
        if($mode_key == 0)
        {
            echo "　　最頻値:".'<font color = "red">'.$mode_value;
        }
        else
        {
            echo "、".$mode_value;
        }
    endforeach;
    echo '</font>'."<BR>";
    echo "最頻値回数:".'<font color = "red">'.$Data[3]['mode_number'].'</font>'."<BR>";
    echo "　　　分散:".'<font color = "red">'.$Data[3]['score_dispersion'].'</font>'."<BR>";
    echo "　　最高点:".'<font color = "red">'.$Data[3]['top_score'].'</font>'."<BR>";
    echo "　　最低点:".'<font color = "red">'.$Data[3]['low_score'].'</font>'."<BR>";
    echo "データ範囲:".'<font color = "red">'.$Data[3]['field'].'</font>'."<BR><BR>";
    echo "クロンバックのα係数：";
    echo '<font color = "red">'.$Data[3]['cronbach'].'</font>'."<BR><BR>";
    echo "<BR>";

    foreach($Data[3]['student_difficulty'][0] as $key => $value):
        //echo $key;
        //echo "項目識別度".$Data[0][$key];
        //echo "項目困難度".$data[$key]."<BR>";
    ?>
    <div id = "<?php echo "con".$key;?>"; style = "margin-left: 50px;width: 310px; height: 400px; float: left;"></div>
    <script type = "text/javascript">
    $(function(){
        var charts = "<?php echo "#con".$key;?>";
        var item_number = "<?php echo $key+1;?>";
        //文字列でとってきているため数値型に変換しないといけない。1が上位5が下位
        var item_1 = Number("<?php echo $Data[3]['student_difficulty'][0][$key];?>");
        var item_2 = Number("<?php echo $Data[3]['student_difficulty'][1][$key];?>");
        var item_3 = Number("<?php echo $Data[3]['student_difficulty'][2][$key];?>");
        var item_4 = Number("<?php echo $Data[3]['student_difficulty'][3][$key];?>");
        var item_5 = Number("<?php echo $Data[3]['student_difficulty'][4][$key];?>");
        var item = [item_1,item_2,item_3,item_4,item_5];
        $(charts).highcharts({
            chart: {
                type: 'line'
            },
            title: {
                text: '設問解答率分析図 項目'+item_number
            },
            xAxis: {
                categories: [1,2,3,4,5]
            },
            yAxis: {
                max:1,
                min:0,
                title: {
                    text: '正答率'
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: true
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                name: "項目"+item_number,
                data: [item_5, item_4, item_3, item_2, item_1]
            }]
        });
    });
    </script>
<?php
endforeach;
?>
	<table border="1">
<?php

    foreach($Data[0] as $key => $value){
	echo '<tr>';
    // echo '<td>'. "素点".$score[$key+1].'</td>'配列ナンバーなので+1している;
    $number = $key + 1;
    echo '<td>'. $number ."問目：項目難易度".$value.'</td>';
    echo '<td>'. $number ."問目：項目識別度".$data[$key].'</td>';
    echo '<td>'. $number ."問目：項目注意度".$Data[2][$key].'</td>';
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

<!--データベースの情報をadviceにもってきている-->
