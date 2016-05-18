<?php
App::uses('File', 'Utility');
class Advice extends AppModel{
    public $useTable = 'advices';
    /* Data にはcsvファイルの情報が入っている */


    public function calculator($Data = null)
    {
        /* ここで使うのはAdvicesテーブル */
        $Advice = $this->find('all');
        /* アドバイスの参照を行っている部分        */
        foreach($Advice as $advice_value):
            //debug($advice_value);
        endforeach;

        /* cakephpの File ユーティリティ(ファイルの読み書きやフォルダ内のファイル名一覧の取得) */
        $file = new File($Data['csv']['tmp_name']);
        /*  explode で改行がある時の列を配列として代入 */
        $array_file = explode("\r", $file->read());

        /* 1行毎に配列にしてくる */
        foreach ($array_file as $key => $value):
            /* 行ごとに配列にする */
            $array_line[$key] = explode(",", $array_file[$key]);
            /*  1人毎の素点が帰ってくる */
            $score[$key] = $this->student_score($array_line, $key);
        endforeach;
        /* 人数や、平均点、最高点、など */
        $data['people'] = count($array_file);
        $data['average'] = $this->score_average($score, $data['people']);

        /* 中央値 */
        $data['median'] = $this->score_median($score);

        /* 最頻値と最頻値の回数 */
        $score_mode = $this->score_mode($score);
        $data['mode'] = $score_mode[0];
        $data['mode_number'] = $score_mode[1];

        /* 普遍分散を返す */
        $data['score_dispersion'] = $this->score_dispersion($score, $data['average']);

        /* 最高点や最低点やデータの範囲を返す */
        $data['top_score'] = max($score);
        $data['low_score'] = min($score);
        $data['field'] = $data['top_score'] - $data['low_score'];

        /*  問題数をとってくる関数 item_sumから正答数がとってきている */
        $item_right_sum = $this->item_sum($array_line);
        $data['item_right_sum'] = $item_right_sum;
        $item_incorrect = $this->item_incorrect($item_right_sum, $data['people']);
        $data['item_incorrect'] = $item_incorrect;
        $data['item_sum'] = count($item_right_sum);

        /* 項目困難度の算出 */
        $item_difficulty = $this->item_difficulty($item_right_sum, $data['people']);

        /* クロンバックのα係数を求める */
        $item_dispersion = $this->item_dispersion($data['people'], $item_right_sum, 1);
        $data['cronbach'] = $this->cronbach($data['item_sum'], $item_dispersion, $data['score_dispersion']);

        /* 識別度判定の算出。受験者を上位群と下位群に分けて識別力を求める箇所 */
        $student_top = $this->student_divide($score, $data['people'], 0);
        $student_under = $this->student_divide($score, $data['people'], 1);
        $top_difficulty = $this->accuracy_rate($array_file, $student_top);
        $under_difficulty = $this->accuracy_rate($array_file, $student_under);
        $item_discrimination = $this->item_discrimination($top_difficulty, $under_difficulty);

        /* ヒストグラムを求める */
        $data['score'] = $this->histogram($score, $data['item_sum']);
        /* $scoreを代入スコアを昇順にソートした結果を以下では用いる */
        arsort($score);

        /* S-P表分析5群に分割 levelではそれぞれのソートで必要な値を決める */
        $right_sum = $this->student_level($array_line, $item_right_sum);
        $student_key = $this->student_sort($score, $right_sum, pow(count($score), 3));
        $item_level = $this->item_level($array_line, $right_sum);
        $spitem_key = $this->item_sort($item_right_sum, $item_level, pow(count($item_right_sum), 3));
        $sp_analysis = $this->sp_analysis($array_line, $student_key, $spitem_key);
        $item_caution_value = $this->item_caution_value($sp_analysis, $score, $item_right_sum, $spitem_key, $student_key);

        /* 良い問題か悪い問題かを判断する */
        $item_grouping = $this->item_grouping($item_difficulty, $item_discrimination, $item_caution_value);
        //debug($item_grouping);
        $data['bad'] = $item_grouping['bad'];
        $data['very_bad'] = $item_grouping['very_bad'];
        $data['good'] = $item_grouping['good'];
        $data['very_good'] = $item_grouping['very_good'];
        /* 設問解答率分析図 */
        $select = $this->student_group($data['people'], 5);
        $analysis = $this->group_divide($score, $select);
        foreach($analysis as $analysis_key => $analysis_value):
            $student_difficulty[] = $this->accuracy_rate($array_file, $analysis_value);
        endforeach;
        $data['student_difficulty'] = $student_difficulty;
        /* debug($data); */
        $file->close();
        return array($item_difficulty, $item_discrimination, $item_caution_value, $data);
    }
    /*  素点返す関数 */
    public function student_score($array_line = array(), $key)
    {
        /* 素点を記録用配列 */
        $score[$key] = 0;
        /* 配列の中身を取り出す */
        foreach ($array_line[$key] as $problem_number => $problem_point):
            /* 問題番号が0以外の場合のみ値を足す */
            if($problem_number != 0)
            {
                /* 素点を求める */
                $score[$key] += (int)$problem_point;
            }
        endforeach;
        return $score[$key];
    }
    /*  平均点を返す。小数点以下第３位まで */
    public function score_average($score = array(), $basic)
    {
        $sum = 0;
        foreach ($score as $key => $value)
        {
            $sum += $value;
        }
        return round($sum / $basic, 3);
    }
    /*  分散を返す(分散の場合はkeyをプラスしない) */
    public function score_dispersion($score = array(), $average = null)
    {
        $sum = 0;
        foreach ($score as $key => $value)
        {
            $tmp[$key] = (($value - $average) * ($value - $average));
            $sum += $tmp[$key];
        }
        return round($sum / count($score), 3);
    }
    public function item_difficulty($item = array(), $basic = null)
    {
        foreach ($item as $key => $value):
            $item_difficulty[$key] = round(($value / $basic), 3);
        endforeach;
        return $item_difficulty;
    }
    /* array_valuesで配列の値を全て表示した値を代入している */
    public function item_sum($array_line)
    {
        foreach ($array_line as $array_key => $array_value):
            foreach($array_value as $key => $value):
                if(!empty($item_right_sum[$key]))
                {
                    $item_right_sum[$key] += $value;
                    continue;
                }
                /* 値がなかった場合には最初人の解答データを与える */
                $item_right_sum[$key] = $value;
            endforeach;
        endforeach;
        unset($item_right_sum[0]);
        $item_right_sum = array_values($item_right_sum);
        return $item_right_sum;
    }
    /*  項目毎の分散値を求めてその合計値を返す関数 項目合計分散値 */
    /*  powは2乗の計算を行う */
    public function item_dispersion($people = null, $right_user = array(), $basic_number)
    {
        foreach($right_user as $key => $right_value):
            $right_average = pow(($basic_number - ($right_value / $people)), 2) * $right_value;
            $incorrect_average = pow(- ($right_value / $people), 2) * ($people - $right_value);
            $item_dispersion[$key] = ($right_average + $incorrect_average) / $people;
        endforeach;
        return array_sum($item_dispersion);
    }
    /*  クロンバックのα係数を求める式がα＝項目数 /（項目数-1）×（1-(各項目の分散の合計/合計点の分散） */
    public function cronbach($item = null, $item_dispersion, $score_dispersion)
    {
        $cronbach = ($item / ($item - 1)) * (1 - ($item_dispersion / $score_dispersion));
        return round($cronbach, 3);
    }
    /*  iはループカウンタ,arsortは降順、asortは昇順 */
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
    /*  群に分けたやつの項目毎の難易度を返す */
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
        $item_right_sum = $this->item_sum($tmp_line);
        $item_difficulty = $this->item_difficulty($item_right_sum, $i);
        return $item_difficulty;
    }
    public function item_discrimination($top_difficulty, $under_difficulty)
    {
        for($i = 0;$i < count($top_difficulty); $i++)
        {
            $item_discrimination[$i] = round(($top_difficulty[$i] - $under_difficulty[$i]), 3);
        }
        return $item_discrimination;
    }
    /*  $nはループカウンタ　divide_numberには何郡に分けるか数字 */
    /*  ここでは添え字を決める */
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
    /* 　グループに分けるときの関数 */
    public function group_divide($student_sort = array(), $basic_value = array())
    {
        $i = 0;
        $j = 0;
        foreach($student_sort as $key => $value):
            if($i == $basic_value[$j]){
                $basic_value[$j] = $basic_value[$j++] + $basic_value[$j];
            }
            $group_divide_key[$j][$i] = $key;
            $i++;
        endforeach;
        /* $jは$basic_value の値-1 */
        for($i = 0;$i <= $j; $i++){
            sort($group_divide_key[$i]);
        }
        return $group_divide_key;
    }
    /* 中央値を求める。 */
    public function score_median($score = array()){
		sort($score);
		if (count($score) % 2 == 0)
        {
			return (($score[(count($score) / 2) - 1] + $score[((count($score) / 2))]) / 2);
		}
        else
        {
			return ($score[floor(count($score)/2)]);
		}
	}
    /* 最頻値を求める */
    public function score_mode($score = array())
    {
    	/* 最頻値を求める。その値の出現回数を値とした配列。値はkeyになる。 */
    	$data = array_count_values($score);
        /* 配列から出現回数の最大を取得する。 */
    	$max = max($data);
        /* $dataの中から$max回数のkeyを取り出すkeyには値が入っている。 */
    	$result = array_keys($data, $max);
    	return array($result, $max);
    }
    /* 　正答数から生徒のレベルをみる(同点の場合にどちらが高いか分かる) */
    public function student_level($array_line = array(), $item_right_sum = array())
    {
        foreach($array_line as $key => $value)
        {
            $result[$key] = 0;
            foreach($value as $value_key => $value_number)
            {
                if($value_number != 0 && $value_key != 0)
                {
                    $result[$key] += $item_right_sum[$value_key - 1];
                }
            }
        }
        return $result;
    }
    /* ソートをしやすくするためにdefineで点数の重み付けを行った */
    public function student_sort($score = array(), $right_sum = array(), $define = null)
    {
        foreach($score as $key => $value):
            $score[$key] = (($value * $define) + $right_sum[$key]);
        endforeach;
        arsort($score);
        $result = array_keys($score);
        return $result;
    }
    /* 　生徒の正答数から問題のレベルをみる(同点の場合にどちらが高いか分かる) */
    public function item_level($array_line = array(), $right_sum = array())
    {
        foreach($array_line as $key => $value)
        {
            foreach($value as $value_key => $value_number)
            {
                /*  value_key配列の0番目意外と外れ意外のとき条件文に入る */
                if($value_number != 0 && $value_key != 0)
                {
                    /* 値があればそのまま足し算でなければ今のを入れる */
                    if(!empty($result[$value_key])){
                        $result[$value_key] += $right_sum[$key];
                        continue;
                    }
                    $result[$value_key] = $right_sum[$key];
                    continue;
                }
            }
        }
        return $result;
    }
    public function item_sort($item_right_sum = array(), $item_level = array(), $define = null)
    {
        arsort($item_right_sum);
        foreach($item_right_sum as $key => $value):
            $item_right_sum[$key] = (($value * $define) + $item_level[$key+1]);
        endforeach;
        arsort($item_right_sum);
        $result = array_keys($item_right_sum);
        return $result;
    }

    public function sp_analysis($array_line, $student_key, $spitem_key)
    {
        foreach($student_key as $s_key => $student_value):
            foreach($spitem_key as $i_key => $item_value):
                $result[$student_value][$item_value + 1] = $array_line[$student_value][$item_value + 1];
            endforeach;
        endforeach;
        return $result;
    }

    public function item_caution_value($sp_analysis, $score, $item_right_sum, $spitem_key, $student_key)
    {
        //[$spitem_key[$i]+1]はvalue配列が1からしか対応していないため加算
        for($i = 0; $i < count($spitem_key); $i++)
        {
            $j = 0;//ループカウンタ
            $result_a[$i] = 0;
            $result_b[$i] = 0;
            foreach($sp_analysis as $key => $value):
                if($j < $item_right_sum[$spitem_key[$i]])
                {
                    if($value[$spitem_key[$i]+1] == 0)
                    {
                        $result_a[$i] += $score[$key];
                    }
                }
                else
                {
                    if($value[$spitem_key[$i]+1] == 1)
                    {
                        $result_b[$i] += $score[$key];
                    }
                }
                $j++;
            endforeach;
        }
        /* cをもとめるための計算式 とついでにdを代入処理*/
        for($i = 0; $i < count($spitem_key); $i++)
        {
            $result_c[$i] = 0;
            $result_d[$i] = $item_right_sum[$spitem_key[$i]];
            foreach($student_key as $key => $value):
                if($key == $item_right_sum[$spitem_key[$i]])
                {
                    break;
                }
                $result_c[$i] += $score[$value];
            endforeach;
        }
        $result_e = $this->score_average($score, count($score));
        /* 注意係数をもとめる計算式 */
        for($i = 0; $i < count($spitem_key); $i++)
        {
            $tmp_left = $result_a[$i] - $result_b[$i];
            $tmp_right = $result_c[$i] - ($result_d[$i] * $result_e);
            $result_value[$i] = round($tmp_left / $tmp_right, 3);
        }
        foreach($spitem_key as $key => $value)
        {
            $result[$value] = $result_value[$key];
        }
        ksort($result);
        return $result;
    }
    public function histogram($score = array(), $item_sum = null)
    {
        sort($score);
        //debug($score);
        $i = 0;
        // 初期値は最小の値
        $data_range = $score[0];
        //切り捨てを行うために使用。
        $digit = 10;
        $data_range = (floor($data_range / $digit) * $digit);
        //debug($data_range);
        foreach($score as $key => $value):
            if($value < ($data_range+$digit) && ($key+1) != count($score))
            {
                $i++;
                continue;
            }
            else
            {
                if($data_range != $item_sum){
                    $result['count'][$key] = $i;
                    $result['number'][$key] = $data_range;
                    //値が同じだった場合
                    if($data_range == $value)
                    {
                        $value++;
                    }
                    $data_range = (floor($value / $digit) * $digit);
                    if(($key+1) == count($score) && $item_sum == $data_range){
                        $i = 1;
                    }
                }
                if(($key+1) == count($score)){
                    if($score[$key-1] == $score[$key] && $value == $item_sum){
                        $i++;
                        $result['count'][$key+1] = $i;
                        $result['number'][$key+1] = $data_range;
                        break;
                    }
                    if($value == $item_sum){
                        $result['count'][$key+1] = $i;
                        $result['number'][$key+1] = $data_range;
                        break;
                    }
                    $i++;
                    $result['count'][$key] = $i;
                    $result['number'][$key] = $data_range;
                }
                $i = 1;
                continue;
            }
        endforeach;
        $result['count'] = array_merge($result['count']);
        $result['number'] = array_merge($result['number']);
        //debug($result);
        return $result;
    }
    /* 不正解数を返す */
    public function item_incorrect($item_right = array(), $people){
        $result[] = 0;
        foreach($item_right as $key => $value):
            $result[$key] = $people - $value;
        endforeach;
        return $result;
    }

    public function item_grouping($diff, $disc, $caution){
        $i = 0;
        $j = 0;
        foreach($diff as $key => $value):
            if(($value < 0.4 || 0.8 <= $value) && $disc[$key] < 0.3 && 0.5 <= $caution[$key]){
                $bad[$i] = $key + 1;
                if(($value < 0.3 || 0.9 <= $value) && $disc[$key] < 0.2 && 0.75 <= $caution[$key]){
                    $very_bad[$i] = $key + 1;
                }
                $i++;
            }
            if(0.4 <= $value && $value < 0.8 && (0.3 <= $disc[$key] || $disc[$key] < 0.4) && $caution[$key] < 0.5){
                $good[$j] = $key + 1;
                if(0.4 <= $value && $value < 0.8 && 0.4 <= $disc[$key] && $caution[$key] < 0.5){
                    $very_good[$j] = $key + 1;
                }
                $j++;
            }
        endforeach;
        $bad = array_values($bad);
        $very_bad = array_values($very_bad);
        $good = array_values($good);
        $very_good = array_values($very_good);
        return array('bad' => $bad, 'very_bad' => $very_bad,
         'good' => $good, 'very_good' => $very_good);
    }

//項目説明データベース用
    public function description_group($data = array())
    {
        foreach($data[0] as $key => $value):
            if($value < 0.3){
                $result[0][$key] = '1a';
            }else if($value < 0.4){
                $result[0][$key] = '1b';
            }else if(0.8 <= $value){
                $result[0][$key] = '1c';
            }else if(0.9 <= $value){
                $result[0][$key] = '1d';
            }else{
                $result[0][$key] = '1e';
            }
            if($data[1][$key] < 0.2){
                $result[1][$key] = '2a';
            }else if($data[1][$key] < 0.3){
                $result[1][$key] = '2b';
            }else if(0.3 <= $data[1][$key] && $data[1][$key] < 0.4){
                $result[1][$key] = '2c';
            }else{
                $result[1][$key] = '2d';
            }
            if(0.75 <= $data[2][$key]){
                $result[2][$key] = '3a';
            }else if(0.5 <= $data[2][$key] && $data[2][$key] < 0.75){
                $result[2][$key] = '3b';
            }else{
                $result[2][$key] = '3c';
            }
        endforeach;
        return $result;
    }
    public function item_connect($item = array())
    {
        foreach($item[0] as $key => $value):
             $result[$key] = $value.$item[1][$key].$item[2][$key];
        endforeach;
        return $result;
    }
    /*  CSVファイルの場合は0を返す */
    public function file_check($file_name = null)
    {
        /* ファイル名をチェックしている */
        $csv_file = explode(".", $file_name);
        /* countで配列の数を返す */
        $file_count = count($csv_file);
        /* 配列の数を１つ減らしたものが.◯◯以下である */
        if($csv_file[$file_count - 1] == "csv")
        {
            return 0;
        }
            return 1;
    }
}
