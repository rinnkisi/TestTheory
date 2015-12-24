<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>ParamQuery gridのデモ</title>
<!--jQuery dependencies-->
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/themes/base/jquery-ui.css" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js"></script>
<!--PQ Grid files-->
<?php
    echo $this->Html->css("../../Plugin/grid/pqgrid.min.css");
    echo $this->Html->script("../../Plugin/grid/pqgrid.min.js");
?>
<script>
    $(function () {
        var data = [[1, 'Exxon Mobil', '車', '36,130'],
            [2, 'Wal-Mart Stores', '自動車', '11,231'],
			[3, 'Royal Dutch Shell', 'バス', '25,311'],
			[4, 'BP', '電車	', '22,341'],
			[5, 'General Motors', '人力車', '10,567'],
			[6, 'Chevron', '牛車', '14,099'],
			[7, 'DaimlerChrysler', 'タイヤ', '3,536'],
			[8, 'Toyota Motor', 'ハンドル', '12,119'],
			[9, 'Ford Motor', 'タイヤ', '2,024'],
			[10, 'ConocoPhillips', 'フロントガラス', '13,529'],
			[11, 'General Electric', 'ワイパー', '16,353'],
			[12, 'Total', '自動車', '15,250'],
			[13, 'ING Group', '自動車', '8,958'],
			[14, 'Citigroup', '自動車', '24,589'],
			[15, 'AXA', '自動車', '5,186'],
			[16, 'Allianz', '自動車', '5,442'],
			[17, 'Volkswagen', '自動車', '1,391'],
			[18, 'Fortis', '自動車', '4,896'],
			[19, 'Cr馘it Agricole', '自動車', '7,434'],
			[20, 'American Intl. Group', '自動車', '10,477']];


        var obj = { width: 700, height: 400, title: "ParamQuery Grid Example",resizable:true,draggable:true };
        obj.colModel = [{ title: "メーカーID", width: 100, dataType: "integer" },
        { title: "会社名", width: 200, dataType: "string" },
        { title: "商品名", width: 150, dataType: "float", align: "right" },
        { title: "商品価格", width: 150, dataType: "float", align: "right"}];
        obj.dataModel = { data: data };
        $("#grid_array").pqGrid(obj);

    });

</script>
</head>
<body>
<div id="grid_array" style="margin:100px;"></div>

</body>

</html>

<?php
    echo "ここではCSVがどのようなファイルでCSVにどのようなデータがあれば良いかの解説をします。";
?>
