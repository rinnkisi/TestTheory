<p style = "text-align:center;font-size:20pt">分析したいcsvファイルを選択してください</p>
<div style = "text-align:center;">
<?php
	//ファイルの選択
	echo $this->Form->create('csv', array('url' => array('controller' => 'Advices', 'action' => 'advice'),
	'type' => 'file'));?>
	<div style = "width: auto;margin-left: 25%;">
		<div style = "text-align:left;color:red;">
	<?php
		//エラーの場合のメッセージ
		if(!empty($error_log))
			echo $error_log;
	?>
		</div>
<?php echo $this->Form->input('',array('type' => 'file'));?>
	</div>
<?php
	//タブでリンクを開く
	echo $this->Html->link('Csvファイル形式内容について',
	array('controller' => 'Advices', 'action' => 'csv_file'), array('target' => '_blank'));
    echo $this->Form->submit(('分析スタート'));
    echo $this->Form->end();
    echo "※分析には時間がかかる場合があります。";
?>
</div>
