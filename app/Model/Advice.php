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
        $people = count($array_file);
        debug($array_file);
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

        /* 項目の得点合計や平均点のところ
        ここは項目の困難度とクロンバックで使う項目分散のところ
        各項目の分散値を表す式
        1問2点の問題があったとすると
        100人中98人正解したとすると
        196点となる。また、2人が不正解で不正解者は0点となっている
        ここでの平均点は合計点の200 / 100は2つまり配点を定める必要がでてくる。1.98点となる
        ただし、1点を基準としてやる問題であれば 1 で問題はない。
        */
        $item_dispersion = $this->item_dispersion($people, $item_sum, 1);
        $cronbach = $this->cronbach(count($item_sum), $item_dispersion, $score_dispersion);
        $student_top = $this->student_top($score, $people);
        //$student_under = $this->student_under($score);
        debug($student_top);
        $student_top_ =
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
    public function student_top($score = array(), $people = null)
    {
        arsort($score);
        debug($score);
        $basic_value = ceil($people * 0.27);
        echo $basic_value;
        // iはループカウンタ
        $i = 0;
        foreach($item_sum as $key => $value):
            if($i == $basic_value){
                break;
            }
            $hairetu[$i] = $key;
            $i++;
        endforeach;
        return $hairetu;
    }
    public function student_under()
    {

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
