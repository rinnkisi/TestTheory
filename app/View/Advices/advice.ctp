<div style="margin-left:100px;">
<h2>分析結果表示</h2>
<ul>

<?php
    echo $this->Html->script("./highcharts.js");
    echo $this->Html->script("./modules/highcharts.js");
    echo $this->Html->script("./modal.js");
    //echo debug($Advice);
    //foreach($Advice as $advice):
    //echo debug($advice['Advice']['advice']); //アドバイスの参照を行っている部分
    //endforeach;
    //この下の処理でdata[]に項目困難度を入力している。
?>

<?php
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
    echo "データ範囲:".'<font color = "red">'.$Data[3]['field'].'</font>'."<BR>";
    echo "クロンバックのα係数：";
    echo '<font color = "red">'.$Data[3]['cronbach'].'</font>'."<BR>";
    echo '<font size = "1px">'."※クロンバックのα係数とはテスト全体の信頼性を表す指標です。<BR>";
    echo "一般的にこの数値が0.71以上の場合には信頼性が高い良いテストだと言えます。<BR>";
    echo "0.71以下だとテストの問題数や受験者が少ないか、テスト問題に信頼性を下げている問題があるといえるでしょう。".'</font>';
    echo "<BR>";

?>
<div id = "container1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
<script type = "text/javascript">
$(function () {
    var charts1 = "<?php echo '#container1';?>"
    var base = "<?php echo count($Data[3]['score']['count']);?>"
    //文字列でとってきているため数値型に変換しないといけない。1が上位5が下位
    var average = "<?php echo $Data[3]['average'];?>";
    $(charts1).highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'テスト全体のヒストグラム'
        },
        subtitle: {
            text: '今回の平均点'+average
        },
        xAxis: {
            min: 0,
            title: {
                text: '点数'
            },
                categories:[
                    <?php //グラフのデータを書き込み
                    for($i = 0; $i < count($Data[3]['score']['number']); $i++) {
                        echo $Data[3]['score']['number'][$i].',';
                    }
                ?>
                ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: '度数分布'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} 回</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '{point.y}'
                },
            },
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'データ範囲',
            data:[
                <?php //グラフのデータを書き込み
                for($i = 0; $i < count($Data[3]['score']['count']); $i++) {
                    echo $Data[3]['score']['count'][$i].',';
                }
            ?>
            ]
        }],
    });
});
</script>
</div>

<?php
    //debug($Data[3]['bad']);
    echo "・能力を判定する上で不適切と思われる問題が".count($Data[3]['bad'])."個ありました"."<BR>";
    echo "※項目番号をクリックで詳細閲覧できます<BR>";
    foreach($Data[3]['bad'] as $key => $value):
        $value = $value-1;
        ?>
        <div id="<?php echo "modal-content-".$value;?>" class="modal-content">
        <p>
            <div id = "<?php echo "page".$value;?>" style = "margin-left: 30px;margin-right: 15px;width:400px; height:550px; float: left;">
                <div id = "<?php echo "con2".$value;?>" style = "margin-left: 40px;width: 310px; height: 400px; float: left;"></div>
                <script type = "text/javascript">
                $(function(){
                    var charts = "<?php echo "#con2".$value;?>";
                    var item_number = "<?php echo $value+1;?>";
                    //文字列でとってきているため数値型に変換しないといけない。1が上位5が下位
                    var item_1 = Number("<?php echo $Data[3]['student_difficulty'][0][$value];?>");
                    var item_2 = Number("<?php echo $Data[3]['student_difficulty'][1][$value];?>");
                    var item_3 = Number("<?php echo $Data[3]['student_difficulty'][2][$value];?>");
                    var item_4 = Number("<?php echo $Data[3]['student_difficulty'][3][$value];?>");
                    var item_5 = Number("<?php echo $Data[3]['student_difficulty'][4][$value];?>");
                    var item = [item_1,item_2,item_3,item_4,item_5];
                    $(charts).highcharts({
                        chart: {
                            type: 'line'
                        },
                        title: {
                            text: '設問解答率分析図 項目'+item_number
                        },
                        xAxis: {
                            categories: ['Lv1','Lv2','Lv3','Lv4','Lv5'],
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
                                //color:'#000000',
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
            echo '<table>';
            echo '<tr>';
            echo '<td>'. "正解者数:".$Data[3]['item_right_sum'][$value].'</br>';
            echo '<td>'. "不正解者数:".$Data[3]['item_incorrect'][$value].'</br>';
            $Data[0][$value] = $Data[0][$value]*100;
            echo '<td>'. "正答率: ".$Data[0][$value].'%</br>';
            echo '</tr>';
            echo '</table>';
            echo '<table border="1">';
            echo '<tr>';
            echo '<td>'. "項目難易度".$Data[0][$value].'</br>';
            echo '<td>'. "項目識別度".$Data[1][$value].'</br>';
            echo '<td>'. "項目注意度".$Data[2][$value].'</br>';
            echo '</tr>';
            echo '</table>';
            echo '</div>';
            echo "設問解答率分析図は受験者を5群に分けて群ごとに正答率をグラフにしたものです。";
            echo "左側が低く、右側が高いと良い問題だといえるでしょう";
            echo "<BR><BR>";
            echo "<BR>難易度：　".$description[$item_desc[0][$value]];
            echo "<BR><BR>識別度：　".$description[$item_desc[1][$value]];
            echo "<BR><BR>注意係数：　".$description[$item_desc[2][$value]];
?>
        </p>
        <p><a id="modal-close" class="button-link">閉じる</a></p>
        </div>

        <table>
        <tr>
            <td>
                <a class="modal button-link" data-target="<?php echo "modal-content-".$value;?>">
        <?php
        $key++;
        $value++;
        echo $key .". 項目 : ".$value;
        ?>
                </a>
            <?php echo "<BR>"."次回時の問題作成アドバイス：　"; ?>
            <?php echo "<BR>".$advice[$item_connect[$value-1]]; ?>
            </tr>
        </table>
        </p>
        <?php
    endforeach;
    echo "<BR><BR><BR> ";
    //debug($Data[3]['very_bad']);
    //debug($Data[3]['good']);
    echo "・能力を判定する上で適切と思われる問題が".count($Data[3]['very_good'])."個ありました"."<BR><BR>";
    foreach($Data[3]['very_good'] as $key => $value):
        $value = $value-1;
    ?>
    <div id="<?php echo "modal-content-".$value;?>" class="modal-content">
    <p>
        <div id = "<?php echo "page".$value;?>" style = "margin-left: 30px;margin-right: 15px;width:400px; height:550px; float: left;">
            <div id = "<?php echo "con2".$value;?>" style = "margin-left: 40px;width: 310px; height: 400px; float: left;"></div>
            <script type = "text/javascript">
            $(function(){
                var charts = "<?php echo "#con2".$value;?>";
                var item_number = "<?php echo $value+1;?>";
                //文字列でとってきているため数値型に変換しないといけない。1が上位5が下位
                var item_1 = Number("<?php echo $Data[3]['student_difficulty'][0][$value];?>");
                var item_2 = Number("<?php echo $Data[3]['student_difficulty'][1][$value];?>");
                var item_3 = Number("<?php echo $Data[3]['student_difficulty'][2][$value];?>");
                var item_4 = Number("<?php echo $Data[3]['student_difficulty'][3][$value];?>");
                var item_5 = Number("<?php echo $Data[3]['student_difficulty'][4][$value];?>");
                var item = [item_1,item_2,item_3,item_4,item_5];
                $(charts).highcharts({
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: '設問解答率分析図 項目'+item_number
                    },
                    xAxis: {
                        categories: ['Lv1','Lv2','Lv3','Lv4','Lv5'],
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
                            //color:'#000000',
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
        echo '<table>';
        echo '<tr>';
        echo '<td>'. "正解者数:".$Data[3]['item_right_sum'][$value].'</br>';
        echo '<td>'. "不正解者数:".$Data[3]['item_incorrect'][$value].'</br>';
        $Data[0][$value] = $Data[0][$value]*100;
        echo '<td>'. "正答率: ".$Data[0][$value].'%</br>';
        echo '</tr>';
        echo '</table>';
        echo '<table border="1">';
        echo '<tr>';
        echo '<td>'. "項目難易度".$Data[0][$value].'</br>';
        echo '<td>'. "項目識別度".$Data[1][$value].'</br>';
        echo '<td>'. "項目注意度".$Data[2][$value].'</br>';
        echo '</tr>';
        echo '</table>';
        echo '</div>';
        echo "設問解答率分析図は受験者を5群に分けて群ごとに正答率をグラフにしたものです。";
        echo "左側が低く、右側が高いと良い問題だといえるでしょう";
        echo "<BR><BR>";
        echo "<BR>".$description[$item_desc[0][$value]];
        echo "<BR>".$description[$item_desc[1][$value]];
        echo "<BR>".$description[$item_desc[2][$value]];
?>
    </p>
    <p><a id="modal-close" class="button-link">閉じる</a></p>
    </div>

    <table>
    <tr>
        <td>
            <a class="modal button-link" data-target="<?php echo "modal-content-".$value;?>">
    <?php
    $key++;
    $value++;
    echo $key .". 項目 : ".$value;
    ?>
            </a>
    <?php echo "<BR>"."次回時の問題作成アドバイス：　"; ?>
    <?php echo "<BR>".$advice[$item_connect[$value-1]]; ?>
        </tr>
    </table>
    </p>
    <?php
    endforeach;
    echo "<BR>";
    //debug($Data[3]['very_good']);
?>
</ul>
<div id="font_button", style="text-align:center;">
<?php
    //echo $this->Form->postButton('作問アドバイス生成', array('controller' => 'Advices', 'action' => 'rule'));
    //echo $this->Form->end();
?>
</div>

<!--データベースの情報をadviceにもってきている-->
