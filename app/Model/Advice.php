<?php
class Advice extends AppModel{
    public $useTable = 'advices';
    public function calculator($Data){
        $Advice = $this->find('all');
        foreach($Advice as $advice):
        ($advice['Advice']['advice']); //アドバイスの参照を行っている部分 ?>
        <?php endforeach; ?>
        <?php foreach($Data as $data): ?>
        <?php //debug($data);?>
        <?php //echo ($data['tmp_name']); ?></br>
        <?php endforeach;
        $file = new File($data['tmp_name']);
        //$files = $file->read();
        $formArray = explode("\r", $file->read());
            // ファイルの読み書きをここで行う
        //debug($files);
        //debug($formArray);
        foreach ($formArray as $firstkey => $firstvalue) {
            $Array[$firstkey] = explode(",", $formArray[$firstkey]);
            $score[$firstkey] = 0;
            foreach ($Array[$firstkey] as $key => $value) {
                if($key == 0){
                }else{
                    $score[$firstkey] += $value;//素点を求める
                    if(isset($koumoku[$key])){
                        $koumoku[$key] += $value;
                    }else{
                        $koumoku[$key] = 0;
                        $koumoku[$key] += $value;
                    }
                }
            }
            //echo "素点".$score[$firstkey]."<BR>";//素点,firstkeyは得点の配列番号
        }
        // ＄bangouでは配列番号、koumokuでは項目別の得点
        $scoresum = 0;
        foreach ($score as $key => $valuescore) {
            $scoresum += $valuescore;
        }
        $scoresum;//合計得点
        $heikin = $scoresum/($key+1);//echo "平均点".
        $scorehuhen = 0;//      echo "得点引く平均点<br>";
        foreach ($score as $key => $valuescore) {
            $scoreheikin[$key] = $valuescore - $heikin;
            $scorerui[$key] = $scoreheikin[$key]*$scoreheikin[$key];
            $scorehuhen += $scorerui[$key];
        }
        $scorebunsan = $scorehuhen/$key; //echo "スコアの分散です。".;
        //得点の分散を表示
        //項目の得点合計や平均点のところ
        foreach($koumoku as $bangou =>$sumitem){
            $sumitem;//echo "項目の合計得点".
            $difficulty[$bangou] = $sumitem/($key+1);//echo "平均点".
            //echo "得点引く平均点<br>";
        }
//ここは項目の困難度とクロンバックで使う項目分散のところ
        foreach ($formArray as $firstkey => $firstvalue) {
            $Array[$firstkey] = explode(",", $formArray[$firstkey]);
            $score[$firstkey] = 0;
            foreach ($Array[$firstkey] as $key => $value) {
                if($key == 0){
                }else{
                    $score[$firstkey] += $value;//素点を求める
                    if(isset($kourui[$key])){//値がはいっているかの条件文
                        //項目ー平均点
                        $vtmp = $value - $difficulty[$key];
                        $kourui[$key] += ($vtmp*$vtmp);
                    }else{
                        $kourui[$key] = 0;
                        $vtmp = $value - $difficulty[$key];
                        $kourui[$key] += ($vtmp*$vtmp);
                    }
                }
            }
        }
        foreach($kourui as $koukey => $value1){
            $result = $value1/$firstkey;//分散をだしている
            if(isset($result_sum)){//値がはいっているときの処理
                $result_sum += $result;//各項目の分散の合計
            }else{//$result_sumに値がはいっている。
                $result_sum = 0;//値がはいってないためエラー回避のため
                $result_sum += $result;//各項目の分散の合計
            }
        }
        //各項目の分散と素点の分散をだしている
        //クロンバックα係数をだしている
        $cronbach=($koukey/($koukey-1)*(1-($result_sum/$scorebunsan)));
        $scoresort=$score;
        sort($scoresort);//ここでソートをする
        //debug($scoresort);
        $basic = round(($firstkey+1)*0.27,0);//少数第１位以下を四捨五入
                //echo "<BR><BR>zzz".$basic."zz".$firstkey;
        $basickey =(($firstkey+1)-$basic);//合計の-基準値を計算し後ろのどこまでを分析するか決めている
        foreach($scoresort as $key =>$value){
            if($key+1 <= $basic){
                $sumkey= $key+1;
                $sumvalue=$value;
            }else if($key+1 >= $basickey){
                $totalkey= $key+1;
                if($basickey == $key){
                    $totalvalue=$value;
                }
            }
        }
        $totalkey = $totalkey-$basic;
        $savecount=0;
        $hojicount=0;
        foreach($score as $key =>$value){
            if($value <= $sumvalue){
                    $savekey[] = $key;
                    $savecount+=1;
            }else if($value >= $totalvalue){
                    $hojikey[] = $key;
                    $hojicount+=1;
            }
        }
        foreach ($formArray as $firstkey => $firstvalue) {
            $Array[$firstkey] = explode(",", $formArray[$firstkey]);
            foreach ($Array[$firstkey] as $key => $value) {
                if($key == 0){
                }else{
                    foreach($savekey as $ley =>$x){
                        if($firstkey == $x){
                            if(isset($validity_save[$key])){
                                $validity_save[$key] += $value;
                            }else{
                                $validity_save[$key] = 0;
                                $validity_save[$key] += $value;
                            }
                        }
                    }
                    //上位を求める
                    foreach ($hojikey as $ley => $y) {
                        if($firstkey == $y){
                            if(isset($validity_hoji[$key])){
                                $validity_hoji[$key] += $value;
                            }else{
                                $validity_hoji[$key] = 0;
                                $validity_hoji[$key] += $value;
                            }
                        }
                    }
                }
            }
        }
        //項目識別力を求める
        //上位の成績ひく下位の成績の人の正答率
        foreach ($validity_hoji as $key => $value) {
            $result_hoji = $value/$hojicount . "<BR>";//成績上位者の正答率hojicountの値が必ずしもbasicと同じではない
            $result_save = $validity_save[$key]/$savecount;//成績下位者の正答率
            $discernment[$key] = $result_hoji - $result_save;
        }
        //項目特性図を書くために必要なこと
        foreach($score as $key =>$value){
            $count[$key]=$key;
        }
        for($j=0;$j<$firstkey+1;$j++){
            $score[$j];
            for($i=$j;$i<$firstkey+1;$i++){//ループで最小値を値をさがす
                if($score[$i] < $score[$j]){
                    $tmp=$score[$j];
                    $t=$count[$j];
                    $score[$j]=$score[$i];
                    $count[$j]=$count[$i];
                    $score[$i]=$tmp;
                    $count[$i] =$t;
            //keyの値を保持する
                }
            }
        }
     //5グループに分ける処理
     /*
    foreach($count as $key=>$value){
        if($key < $firstkey/5){
            $first[$key] = $value;
        }elseif($key < $firstkey*2/5){
            $second[$key] = $value;
        }elseif($key < $firstkey*3/5){
            $third[$key] = $value;
        }elseif($key < $firstkey*4/5){
            $fouth[$key] = $value;
        }else{
            $last[$key] = $value;
        }
     }
     */
        //debug($count);
        //debug($scoresort);
        //debug($Array);
        $basic_number=ceil($firstkey/5);

        //５群にわけた正答率をだす部分↓
        foreach($count as $key=>$value){
            foreach ($Array[$value] as $secondkey => $secondvalue) {
                if(0!=$secondkey){
                    if($key < $basic_number){
                        if(isset($zu1[$secondkey])){
                            $zu1[$secondkey]+=$secondvalue;
                        }else{
                            $zu1[$secondkey] = 0;
                            $zu1[$secondkey]+=$secondvalue;
                        }
                    }elseif($key < $basic_number*2){
                        if(isset($zu2[$secondkey])){
                            $zu2[$secondkey]+=$secondvalue;
                        }else{
                            $zu2[$secondkey] = 0;
                            $zu2[$secondkey]+=$secondvalue;
                        }
                    }elseif($key < $basic_number*3){
                        if(isset($zu3[$secondkey])){
                            $zu3[$secondkey]+=$secondvalue;
                        }else{
                            $zu3[$secondkey] = 0;
                            $zu3[$secondkey]+=$secondvalue;
                        }
                    }elseif($key < $basic_number*4){
                        if(isset($zu4[$secondkey])){
                            $zu4[$secondkey]+=$secondvalue;
                        }else{
                            $zu4[$secondkey] = 0;
                            $zu4[$secondkey]+=$secondvalue;
                        }
                    }else{
                        if(isset($zu5[$secondkey])){
                            $zu5[$secondkey]+=$secondvalue;
                        }else{
                            $zu5[$secondkey] = 0;
                            $zu5[$secondkey]+=$secondvalue;
                        }
                    }
                }
            }
        }
        for($j=1;$j < $secondkey+1;$j++){
            if(isset($hozon1[$j])){
                $hozon1[$j] = $zu1[$j]/$basic_number;
            }else{
                $hozon1[$j] = 0;
                $hozon1[$j] = $zu1[$j]/$basic_number;
            }
        }
        for($j=1;$j < $secondkey+1;$j++){
            if(isset($hozon2[$j])){
                $hozon2[$j] = $zu2[$j]/$basic_number;
            }else{
                $hozon2[$j] = 0;
                $hozon2[$j] = $zu2[$j]/$basic_number;
            }
        }
        for($j=1;$j < $secondkey+1;$j++){
            if(isset($hozon3[$j])){
                $hozon3[$j] = $zu3[$j]/$basic_number;
            }else{
                $hozon3[$j] = 0;
                $hozon3[$j] = $zu3[$j]/$basic_number;
            }
        }
        for($j=1;$j < $secondkey+1;$j++){
            if(isset($hozon4[$j])){
                $hozon4[$j] = $zu4[$j]/$basic_number;
            }else{
                $hozon4[$j] = 0;
                $hozon4[$j] = $zu4[$j]/$basic_number;
            }
        }
        for($j=1;$j < $secondkey+1;$j++){
            if(isset($hozon5[$j])){
                $hozon5[$j] = $zu5[$j]/$basic_number;
            }else{
                $hozon5[$j] = 0;
                $hozon5[$j] = $zu5[$j]/$basic_number;
            }
        }
        $file->close();
        return array($discernment,$difficulty,$cronbach);
    }
}
