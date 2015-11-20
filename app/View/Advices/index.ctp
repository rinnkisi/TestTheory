<p style="text-align:center;font-size:20pt">分析したいcsvファイルを選択してください</p>
<div style="text-align:center;">
<?php
	echo $this->Form->create('csv',
	array( 'type'=>'> file', 'enctype' => 'multipart/form-data', 'url'=>'/advices/advice/'));
    echo $this->Form->input('',array( 'type' => 'file'));
	//タブでリンクを開くは別で変える
    echo $this->Html->link('Csvファイル形式内容について',
	array('controller' => 'Advices', 'action' => 'csv_file'), array('target' => '_blank'));
    echo $this->Form->submit( ('分析スタート'));
    echo $this->Form->end();
    echo "※分析には時間がかかる場合があります。";
?>
</div>
