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
        $data['people'] = count($array_file);
        $data['average'] = $this->score_average($score, $data['people']);
        $data['median'] = $this->score_median($score);
        $tmp = $this->score_mode($score);
        $data['mode'] = $tmp[0];
        $data['mode_number'] = $tmp[1]; 
        //普遍分散を返す
        $data['score_dispersion'] = $this->score_dispersion($score, $data['average']);
        $data['top_score'] = max($score);
        $data['low_score'] = min($score);
        $data['field'] = $data['top_score'] - $data['low_score'];
        // 問題数をとってくる関数
        $item_sum = $this->item_sum($array_line);
        //項目困難度の算出
        $item_difficulty = $this->item_difficulty($item_sum, $data['people']);
        /* 項目の得点合計や平均点のところ */
        $item_dispersion = $this->item_dispersion($data['people'], $item_sum, 1);
        $data['cronbach'] = $this->cronbach(count($item_sum), $item_dispersion, $data['score_dispersion']);
        $student_top = $this->student_divide($score, $data['people'], 0);
        $student_under = $this->student_divide($score, $data['people'], 1);
        $top_difficulty = $this->accuracy_rate($array_file, $student_top);
        $under_difficulty = $this->accuracy_rate($array_file, $student_under);
        $item_discrimination = $this->item_discrimination($top_difficulty, $under_difficulty);
        $select = $this->student_group($data['people'], 5);
        $analysis = $this->group_divide($score, $select);
        //設問解答率分析図
        foreach($analysis as $analysis_key => $analysis_value):
            $student_difficulty[] = $this->accuracy_rate($array_file, $analysis_value);
        endforeach;
        $data['student_difficulty'] = $student_difficulty;
        //debug($data);
        $file->close();
        return array($item_discrimination, $item_difficulty, $data);
    }
    // 素点返す関数
    public function student_score($array_line = array(), $key)
    {
        //素点を記録用配列
        $score[$key] = 0;
        //配列の中身を取り出す
        foreach ($array_line[$key] as $problem_number => $problem_point):
            //問題番号が0以外の場合のみ値を足す
            if($problem_number != 0)
            {
                //素点を求める
                $score[$key] += (int)$problem_point;
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
    // 分散を返す(分散の場合はkeyをプラスしない)
    public function score_dispersion($score = array(), $average = null)
    {
        $sum = 0;
        foreach ($score as $key => $value)
        {
            $tmp[$key] = (($value - $average) * ($value - $average));
            $sum += $tmp[$key];
        }
        return $sum / count($score);
    }
    public function item_difficulty($item = array(), $basic = null)
    {
        foreach ($item as $key => $value):
            $item_difficulty[$key] = $value / $basic;
        endforeach;
        return $item_difficulty;
    }
    //array_valuesで配列の添え字を0にして代入している
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
    // powは2乗の計算を行う
    public function item_dispersion($people = null, $right_user = array(), $basic_number)
    {
        foreach($right_user as $key => $right_value):
            $right_average = pow(($basic_number - ($right_value / $people)), 2) * $right_value;
            $incorrect_average = pow(- ($right_value / $people), 2) * ($people - $right_value);
            $item_dispersion[$key] = ($right_average + $incorrect_average) / $people;
        endforeach;
        return array_sum($item_dispersion);
    }
    // クロンバックのα係数を求める式がα＝項目数 /（項目数-1）×（1-(各項目の分散の合計/合計点の分散）
    public function cronbach($item = null, $item_dispersion, $score_dispersion)
    {
        $cronbach = ($item / ($item - 1)) * (1 - ($item_dispersion / $score_dispersion));
        return $cronbach;
    }
    // iはループカウンタ,arsortは降順、asortは昇順
    public function student_divide($score = array(), $people = null, $bool = 0)
    {
        if($bool == 1)
            asort($score);
        else
            arsort($score);
        $basic_value = ceil($people * 0.27);
        $i = 0;
        foreach($score as $key => $value):
            if($i == $basic_value){
                break;
            }
            $student_divide[$i] = $key;
            $i++;
        endforeach;
        sort($student_divide);
        return $student_divide;
    }
    // 群に分けたやつの項目毎の難易度を返す
    public function accuracy_rate($array_file = array(), $basic = array())
    {
        $i = 0;
        foreach($array_file as $key => $value):
            if(isset($basic[$i]) && $key == $basic[$i])
            {
                $array_line[$i] = explode(",", $value);
                $tmp_line[$i] = $array_line[$i];
                $i++;
            }
        endforeach;
        $item_sum = $this->item_sum($tmp_line);
        $item_difficulty = $this->item_difficulty($item_sum, $i);
        return $item_difficulty;
    }
    public function item_discrimination($top_difficulty, $under_difficulty)
    {
        for($i = 0;$i < count($top_difficulty); $i++)
        {
            $item_discrimination[$i] = $top_difficulty[$i] - $under_difficulty[$i];
        }
        return $item_discrimination;
    }
    // $nはループカウンタ　divide_numberには何郡に分けるか数字
    // ここでは添え字を決める
    public function student_group($people = null, $divide_number = null)
    {
        $n = $divide_number;
        for($i = 0;$i < $n; $i++)
        {
            if(0 == ($people % $divide_number)){
                $reference_value[$i] = $people / $divide_number;
                continue;
            }
            $reference_value[$i] = ceil($people / $divide_number);
            $people = $people - $reference_value[$i];
            $divide_number = $divide_number - 1;
        }
        return $reference_value;
    }
    public function group_divide($score = array(), $basic_value = array())
    {
        arsort($score);
        $i = 0;
        $j = 0;
        foreach($score as $key => $value):
            if($i == $basic_value[$j]){
                $basic_value[$j] = $basic_value[$j++] + $basic_value[$j];
            }
            $group_divide_key[$j][$i] = $key;
            $i++;
        endforeach;
        //$jは$basic_value の値-1
        for($i = 0;$i <= $j; $i++){
            sort($group_divide_key[$i]);
        }
        return $group_divide_key;
    }
    //中央値を求める。
    public function score_median($score = array()){
		sort($score);
		if (count($score) % 2 == 0){
			return (($score[(count($score) / 2) - 1] + $score[((count($score) / 2))]) / 2);
		}else{
			return ($score[floor(count($score)/2)]);
		}
	}
    //最頻値を求める
    public function score_mode($score = array())
    {
    	//最頻値を求める。その値の出現回数を値とした配列。値はkeyになる。
    	$data = array_count_values($score);
        //配列から出現回数の最大を取得する。
    	$max = max($data);
        //$dataの中から$max回数のkeyを取り出すkeyには値が入っている。
    	$result = array_keys($data, $max);
    	return array($result, $max);
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
            return 1;
    }
}
