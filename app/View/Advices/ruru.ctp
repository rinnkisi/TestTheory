<h2>作問アドバイス生成</h2>
	<table border="1"style="width:1000px;margin-left:200px;">
	<?php
    for($i=1;$i<101;$i++){
	echo '<tr>';
//  	echo '<td>'. "素点".$score[$key+1].'</td>';
	if($i<6){
		if($i<3){
    echo '<td style="width:15px">'.$i ."問目".'</td>';
    echo '<td style="width:300px">'."作問アドバイス<BR>"."・かなり易しい問題<BR>
・能力に全く関係なく解ける問題<BR>
・常識問題といわれる問題であるため、難しくする必要がある<BR>"
    .'</td>';
		}else{
			echo '<td style="width:15px">'.$i ."問目".'</td>';
    echo '<td style="width:300px">'."作問アドバイス<BR>"."・かなり難しい問題<BR>
・能力に全く関係なく解ける問題<BR>
・当て推量の可能性有り<BR>
・問題分や選択肢を見直す必要がある<BR>"
    .'</td>';
		}
    }elseif(5<$i && $i<15){
    	if($i<6){
    echo '<td style="width:15px">'.$i ."問目".'</td>';
    echo '<td style="width:300px">'."作問アドバイス<BR>"."・かなり難しい問題<BR>
・能力に全く関係なく解ける問題<BR>
・当て推量の可能性有り<BR>　　　　　　　
・問題分や選択肢を見直す必要がある<BR>"
    .'</td>';
}else{
    echo '<td style="width:15px">'.$i ."問目".'</td>';
    echo '<td style="width:300px">'."作問アドバイス<BR>"."・易しい問題<BR>
・能力に関係なく解ける問題<BR>
・問題文が易しすぎるか誤答選択肢に明らかな間違いがある為見直しが必要な問題<BR>"
    .'</td>';
}
    }elseif(15<$i && $i <20){
    echo '<td style="width:15px">'.$i ."問目".'</td>';
    echo '<td style="width:300px">'."作問アドバイス<BR>"."・易しい問題<BR>
    ・能力に関係なく解ける問題<BR>
・問題文が易しすぎるか誤答選択肢に明らかな間違いがある為見直しが必要な問題<BR>"
    .'</td>';
    }elseif(20<$i &&$i <25){
    echo '<td style="width:15px">'.$i ."問目".'</td>';
    echo '<td style="width:300px">'."作問アドバイス<BR>"."・難しい問題<BR>
・能力に関係なく解ける問題<BR>
・問題文が難しすぎるか選択肢間の類似度が高い問題で有る為、問い方を変えるか選択肢を変えることが望ましい問題<BR>"
    .'</td>';
    }else{
     echo '<td style="width:15px">'.$i ."問目".'</td>';
    echo '<td style="width:300px">'."作問アドバイス<BR>"."・易しい問題<BR>
・能力に関係なく解ける問題<BR>
・問題文が易しすぎるか誤答選択肢に明らかな間違いがある為見直しが必要な問題<BR>"
    .'</td>';
    }
	echo '</tr>';
	}
?>
</table>
</ul>
</div>