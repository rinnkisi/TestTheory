
<h2>分析結果表示</h2>
<ul>

<?php
    echo $this->Html->script("./highcharts.js");
    echo $this->Html->script("./modules/highcharts.js");
    //echo debug($Advice);
    foreach($Advice as $advice):
    //echo debug($advice['Advice']['advice']); //アドバイスの参照を行っている部分
    endforeach;
    //この下の処理でdata[]に項目困難度を入力している。
?>
<?php

    foreach($Data[1] as $data[]):
    endforeach;
    //クロンバックのα係数を記述
    echo "<BR>";
    echo "クロンバックのα係数：";
    echo '<font color = "red">'.$Data[2]['cronbach'].'</font>'."<BR><BR>";
    echo "受験者人数:".'<font color = "red">'.$Data[2]['people'].'</font>　名'."<BR>";
    echo "　　平均点:".'<font color = "red">'.$Data[2]['average'].'</font>'."<BR>";
    echo "　　中央値:".'<font color = "red">'.$Data[2]['median'].'</font>'."<BR>";
    foreach($Data[2]['mode'] as $mode_key => $mode_value):
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
    echo "　　　分散:".'<font color = "red">'.$Data[2]['score_dispersion'].'</font>'."<BR>";
    echo "　　最高点:".'<font color = "red">'.$Data[2]['top_score'].'</font>'."<BR>";
    echo "　　最低点:".'<font color = "red">'.$Data[2]['low_score'].'</font>'."<BR>";
    echo "データ範囲:".'<font color = "red">'.$Data[2]['field'].'</font>'."<BR>";
    echo "<BR>";

    foreach($Data[2]['student_difficulty'][0] as $key => $value):
    ?>
    <div id = "<?php echo $key; ?>" style = "width: 310px; height: 400px; margin: 0"></div>
    <?php  $key1 = '#'+$key;
    ?>
    <script type="text/javascript">
    $(function (){
        var key = "<?php echo $key; ?>";
        $(key).highcharts({
            chart: {
                type: 'line'
            },
            title: {
                text: '設問解答率分析図'.$
            },
            xAxis: {
                categories: [1,2,3,4,5]
            },
            yAxis: {
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
            series: [{
                name: '1',
                data: [7.0, 6.9, 9.5, 14.5, 2.3]
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

<!--データベースの情報をadviceにもってきている-->
