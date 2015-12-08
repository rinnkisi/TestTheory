<?php
App::uses('File', 'Utility');
class Advice extends AppModel{
    public $useTable = 'advices';
    //Data にはcsvファイルの情報が入っている
    public function calculator($Data = null)
    {
        //ここで使うのはAdvicesテーブル
        $Advice = $this->find('all');
        /* アドバイスの参照を行っている部分
        foreach($Advice as $advice_value):
        endforeach;
        */
        //cakephpの File ユーティリティ(ファイルの読み書きやフォルダ内のファイル名一覧の取得)
        $file = new File($Data['csv']['tmp_name']);
        // explode で改行がある時の列を配列として代入
        $array_file = explode("\r", $file->read());
        //1行毎に配列にしてくる
        foreach ($array_file as $key => $value):
            //行ごとに配列にする
            $array_line[$key] = explode(",", $array_file[$key]);
            // 1人毎の素点が帰ってくる
            $score[$key] = $this->student_score($array_line, $key);
        endforeach;

        //人数や、平均点、最高点、など
        $people = $key + 1;
        $average = $this->score_average($score, $people);
        //普遍分散を返す
        $score_dispersion = $this->score_dispersion($score, $average);
        $top_score = max($score);
        $low_score = min($score);
        // 問題数をとってくる関数
        $item_sum = $this->item_sum($array_line);
        debug($item_sum);
        //項目困難度の算出
        $item_difficulty = $this->item_difficulty($item_sum, $people);
        debug($item_difficulty);

        //項目の得点合計や平均点のところ
        //ここは項目の困難度とクロンバックで使う項目分散のところ
        //各項目の分散値を表す式
        // 1問2点の問題があったとすると
        // 100人中98人正解したとすると
        // 196点となる。また、2人が不正解で不正解者は0点となっている
        // ここでの平均点は合計点の200 / 100は2つまり配点を定める必要がでてくる。1.98点となる
        // ただし、1点を基準としてやる問題であれば 1 で問題はない。
        $item_sum_dispersion = $this->item_sum_dispersion($people, $item_sum, 1);
        debug($item_sum_dispersion);
        
        /*
        foreach ($array_file as $firstkey => $firstvalue)
        {
            $Array[$firstkey] = explode(",", $array_file[$firstkey]);
            $score[$firstkey] = 0;
            foreach ($Array[$firstkey] as $key => $value)
            {
                if($key == 0)
                {
                }
                else
                {
                    $score[$firstkey] += $value;//素点を求める
                    //値がはいっているかの条件文
                    if(isset($kourui[$key]))
                    {
                        //項目ー平均点
                        $vtmp = $value - $difficulty[$key];
                        $kourui[$key] += ($vtmp*$vtmp);
                    }
                    else
                    {
                        $kourui[$key] = 0;
                        $vtmp = $value - $difficulty[$key];
                        $kourui[$key] += ($vtmp*$vtmp);
                    }
                }
            }
        }
        foreach($kourui as $koukey => $value1)
        {
            $result = $value1/$firstkey;//分散をだしている
            if(isset($result_sum))
            {//値がはいっているときの処理
                $result_sum += $result;//各項目の分散の合計
            }
            else
            {//$result_sumに値がはいっている。
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
        foreach($scoresort as $key =>$value)
        {
            if($key+1 <= $basic)
            {
                $sumkey= $key+1;
                $sumvalue=$value;
            }
            else if($key+1 >= $basickey)
            {
                $totalkey= $key+1;
                if($basickey == $key)
                {
                    $totalvalue=$value;
                }
            }
        }
        $totalkey = $totalkey-$basic;
        $savecount=0;
        $hojicount=0;
        foreach($score as $key =>$value)
        {
            if($value <= $sumvalue)
            {
                    $savekey[] = $key;
                    $savecount+=1;
            }
            else if($value >= $totalvalue)
            {
                    $hojikey[] = $key;
                    $hojicount+=1;
            }
        }
        foreach ($array_file as $firstkey => $firstvalue)
        {
            $Array[$firstkey] = explode(",", $array_file[$firstkey]);
            foreach ($Array[$firstkey] as $key => $value)
            {
                if($key == 0)
                {
                }
                else
                {
                    foreach($savekey as $ley => $x)
                    {
                        if($firstkey == $x)
                        {
                            if(isset($validity_save[$key]))
                            {
                                $validity_save[$key] += $value;
                            }
                            else
                            {
                                $validity_save[$key] = 0;
                                $validity_save[$key] += $value;
                            }
                        }
                    }
                    //上位を求める
                    foreach ($hojikey as $ley => $y)
                    {
                        if($firstkey == $y)
                        {
                            if(isset($validity_hoji[$key]))
                            {
                                $validity_hoji[$key] += $value;
                            }
                            else
                            {
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
        foreach ($validity_hoji as $key => $value)
        {
            $result_hoji = $value/$hojicount . "<BR>";//成績上位者の正答率hojicountの値が必ずしもbasicと同じではない
            $result_save = $validity_save[$key]/$savecount;//成績下位者の正答率
            $discernment[$key] = $result_hoji - $result_save;
        }
        //項目特性図を書くために必要なこと
        foreach($score as $key =>$value)
        {
            $count[$key]=$key;
        }
        for($j=0;$j<$firstkey+1;$j++)
        {
            $score[$j];
            for($i=$j;$i<$firstkey+1;$i++)
            {//ループで最小値を値をさがす
                if($score[$i] < $score[$j])
                {
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
        $basic_number = ceil($firstkey/5);

        //５群にわけた正答率をだす部分↓
        foreach($count as $key=>$value)
        {
            foreach ($Array[$value] as $secondkey => $secondvalue)
            {
                if(0!=$secondkey)
                {
                    if($key < $basic_number)
                    {
                        if(isset($zu1[$secondkey]))
                        {
                            $zu1[$secondkey]+=$secondvalue;
                        }
                        else
                        {
                            $zu1[$secondkey] = 0;
                            $zu1[$secondkey]+=$secondvalue;
                        }
                    }elseif($key < $basic_number*2)
                    {
                        if(isset($zu2[$secondkey]))
                        {
                            $zu2[$secondkey]+=$secondvalue;
                        }
                        else
                        {
                            $zu2[$secondkey] = 0;
                            $zu2[$secondkey]+=$secondvalue;
                        }
                    }elseif($key < $basic_number*3)
                    {
                        if(isset($zu3[$secondkey]))
                        {
                            $zu3[$secondkey]+=$secondvalue;
                        }
                        else{
                            $zu3[$secondkey] = 0;
                            $zu3[$secondkey]+=$secondvalue;
                        }
                    }
                    elseif($key < $basic_number*4)
                    {
                        if(isset($zu4[$secondkey]))
                        {
                            $zu4[$secondkey]+=$secondvalue;
                        }
                        else
                        {
                            $zu4[$secondkey] = 0;
                            $zu4[$secondkey]+=$secondvalue;
                        }
                    }
                    else
                    {
                        if(isset($zu5[$secondkey]))
                        {
                            $zu5[$secondkey]+=$secondvalue;
                        }
                        else
                        {
                            $zu5[$secondkey] = 0;
                            $zu5[$secondkey]+=$secondvalue;
                        }
                    }
                }
            }
        }
        for($j=1;$j < $secondkey+1;$j++)
        {
            if(isset($hozon1[$j]))
            {
                $hozon1[$j] = $zu1[$j]/$basic_number;
            }
            else
            {
                $hozon1[$j] = 0;
                $hozon1[$j] = $zu1[$j]/$basic_number;
            }
        }
        for($j=1;$j < $secondkey+1;$j++)aaaaaaaaaaa
        {
            if(isset($hozon2[$j]))
            {
                $hozon2[$j] = $zu2[$j]/$basic_number;
            }
            else
            {
                $hozon2[$j] = 0;
                $hozon2[$j] = $zu2[$j]/$basic_number;
            }
        }
        for($j=1;$j < $secondkey+1;$j++)
        {
            if(isset($hozon3[$j]))
            {
                $hozon3[$j] = $zu3[$j]/$basic_number;
            }
            else
            {
                $hozon3[$j] = 0;
                $hozon3[$j] = $zu3[$j]/$basic_number;
            }
        }
        for($j=1;$j < $secondkey+1;$j++)
        {
            if(isset($hozon4[$j]))
            {
                $hozon4[$j] = $zu4[$j]/$basic_number;
            }
            else
            {
                $hozon4[$j] = 0;
                $hozon4[$j] = $zu4[$j]/$basic_number;
            }
        }
        for($j=1;$j < $secondkey+1;$j++)
        {
            if(isset($hozon5[$j]))
            {
                $hozon5[$j] = $zu5[$j]/$basic_number;
            }
            else
            {
                $hozon5[$j] = 0;
                $hozon5[$j] = $zu5[$j]/$basic_number;
            }
        }
*/
        $file->close();
        //return array($discernment, $difficulty, $cronbach);
    }
    // 素点返す関数
    public function student_score($array_line = array(), $key)
    {
        //素点を記録用配列
        $score[$key] = 0;
        //配列の中身をさらに問題毎に取り出す
        foreach ($array_line[$key] as $problem_number => $problem_point):
            //問題番号が0以外の場合のみ値を足す
            if($problem_number != 0)
            {
                //素点を求める
                $score[$key] += $problem_point;
            }
        endforeach;
        return $score[$key];
    }
    // 平均点を返す
    public function score_average($score = array(), $basic)
    {
        $sum = 0;
        foreach ($score as $key => $value)
        {
            $sum += $value;
        }
        return $sum / $basic;
    }
    // 不偏分散を返す(分散の場合はkeyをプラスしない)
    public function score_dispersion($score = array(), $average = null)
    {
        $sum = 0;
        foreach ($score as $key => $value)
        {
            $tmp[$key] = (($value - $average) * ($value - $average));
            $sum += $tmp[$key];
        }
        echo $key;
        return $sum / ($key + 1);
    }
    public function item_difficulty($item = array(), $basic = null)
    {
        foreach ($item as $key => $value):
            $item_difficulty[$key] = $value / $basic;
        endforeach;
        return $item_difficulty;
    }
    public function item_sum($array_line)
    {
        foreach ($array_line as $array_key => $array_value):
            foreach($array_value as $key => $value):
                if(!empty($item_sum[$key]))
                {
                    $item_sum[$key] += $value;
                    continue;
                }
                //値がなかった場合には最初人の解答データを与える
                $item_sum[$key] = $value;
            endforeach;
        endforeach;
        unset($item_sum[0]);
        $item_sum = array_values($item_sum);
        return $item_sum;
    }
    // 項目毎の分散値を求めてその合計値を返す関数 項目合計分散値
    public function item_sum_dispersion($people = null, $right_user = array(), $basic_point)
    {
        foreach($right_user as $key => $right_value):
            $right_average = pow(($basic_point - ($right_value / $people)), 2) * $right_value;
            $incorrect_average = pow(- ($right_value / $people), 2) * ($people - $right_value);
            $item_dispersion[$key] = ($right_average + $incorrect_average) / $people;
        endforeach;
        return array_sum($item_dispersion);
    }
    // CSVファイルの場合は0を返す
    public function file_check($file_name = null)
    {
        //ファイル名をチェックしている
        $csv_file = explode(".", $file_name);
        //countで配列の数を返す
        $file_count = count($csv_file);
        //配列の数を１つ減らしたものが.◯◯以下である
        if($csv_file[$file_count - 1] == "csv")
        {
            return 0;
        }
        else
        {
            return 1;
        }
    }
}
