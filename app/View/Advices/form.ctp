<?php
	echo $this->Form->create('Advice',array('action' => 'form'));
	echo "<p>本文</p>",$this->Form->textarea('advice', array('cols' => '60', 'rows' => '3',
        'placeholder' => 'アドバイス内容を入力してください'));
	echo $this->Form->end('投稿'),"</div>";
    var_dump($data);
