<?php

class Rewrite
{

  protected $s_arr;


    public function __construct(){

        $file = file_get_contents(MODULE_DIR . "/synonyms.txt");
        preg_match_all('#\[w\]([^\[]+)\[/w\]\[s\]([^\[]+)\[/s\]#xmsi', $file, $s_arr, PREG_SET_ORDER);
        $this->s_arr = $s_arr;

    }


    public function rewrite($text)
    {

        $text = $this->rewrite_synonims($text);
        $text = $this->rewrite_st_lozhki($text);
        $text = $this->rewrite_lozhki($text);
        $text = $this->rewrite_grams($text);
        $text = $this->rewrite_kg($text);
        $text = $this->rewrite_sht($text);
        $text = $this->rewrite_br($text);
        $text = $this->rewrite_stakany($text);
        $text = $this->rewrite_mililitry($text);


        return $text;
    }


// замена синонимов из txt
    protected function rewrite_synonims($text)
    {

        global $conter;


        foreach ($this->s_arr as $val) {

            $first_let = mb_substr($val[1], 0, 1, 'CP-1251');
            $up_first = mb_strtoupper($first_let, 'CP-1251');
            if ($up_first <> $first_let)
                $b_synonym = $up_first . mb_substr($val[1], 1, mb_strlen($val[1], 'CP-1251'), 'CP-1251');

            $first_let = mb_substr($val[2], 0, 1, 'CP-1251');
            $up_first = mb_strtoupper($first_let, 'CP-1251');
            if ($up_first <> $first_let)
                $b_r_synonym = $up_first . mb_substr($val[2], 1, mb_strlen($val[2], 'CP-1251'), 'CP-1251');

            $lit_word = preg_quote($val[1]);
            $big_word = preg_quote($b_synonym);

            $text = preg_replace("#([\.\,\! \?>^]+|^){$lit_word}([<\.\,\! \?]?)#xi", "$1" . $val[2] . "$2", $text);
            $text = preg_replace("#([\.\,\! \?>^]+|^){$big_word}([<\.\,\! \?]?)#xi", "$1" . $b_r_synonym . "$2", $text);


        }


        return $text;

    }


// столовые ложки
    protected function rewrite_st_lozhki($text)
    {
        preg_match_all('#\s*(((\d+(,|\.|/|-))?)\s*(\d+))\s*ст\.\s*л((ожка|ожек|ожки|ж)?)((\.)?)([^а-я])#xmsi', $text, $array, PREG_SET_ORDER);
        //var_dump($array);


        foreach ($array as $val) {

           # if (isset($val[3])) $val[1] = $val[3];
            if (preg_match('#\.|,|/#i', $val[1])) $repleace = $val[1].' столовой ложки';
            if ($val[1] == '1') $repleace = ' одна столовая ложка';
            elseif ($val[1] == '2') $repleace = ' две столовых ложки';
            elseif ($val[1] == '3') $repleace = ' три столовых ложки';
            elseif ($val[1] == '4') $repleace = ' четыре столовых ложки';
            elseif ($val[1] == '5') $repleace = ' пять столовых ложек';
            elseif ($val[1] > '5') $repleace = str_ireplace("ст.", "столовых", $val[0]);
            else  $repleace = $val[1] . " столовых ложки";


            $replace_text = '#' . preg_quote($val[0]) . '#mi';

            $text = preg_replace($replace_text, ' '.$repleace.' ', $text);
            //var_dump($replace_text);
        }

        return $text;

    }

// стаканы
    protected function rewrite_stakany($text)
    {
        preg_match_all('#\s*(((\d+(,|\.|/|-))?)\s*(\d+))\s*ст\.#xmsi', $text, $array, PREG_SET_ORDER);
        //var_dump($array);


        foreach ($array as $val) {

           # if (isset($val[3])) $val[1] = $val[3];
            if (preg_match('#\.|,|/|\\#i', $val[1])) $repleace = $val[1].' стакана';
            if ($val[1] == '1') $repleace = ' один стакан';
            elseif ($val[1] == '2') $repleace = ' два стакана';
            elseif ($val[1] == '3') $repleace = ' три стакана';
            elseif ($val[1] == '4') $repleace = ' четыре стакана';
            elseif ($val[1] == '5') $repleace = ' пять стаканов';
            else  $repleace = $val[1] . " стаканов";


            $replace_text = '#' . preg_quote($val[0]) . '#mi';

            $text = preg_replace($replace_text, ' '.$repleace.' ', $text);
            //var_dump($replace_text);
        }

        return $text;

    }




//чайные ложки
    protected function rewrite_lozhki($text)
    {
        preg_match_all('#\s*(((\d+(,|\.|/|-))?)\s*(\d+))\s*ч((а|ай)?)\.\s*л((ожка|ожек|ложки|ж)?)((\.)?)([^а-я])#xmsi', $text, $array, PREG_SET_ORDER);

        #  print_r($array);

        foreach ($array as $val) {

           # if (isset($val[3])) $val[1] = $val[3];

            if (preg_match('#\.|,|/#i', $val[1])) $repleace = $val[1].' чайные ложки';
            elseif ($val[1] == '1') $repleace = ' одна чайная ложка ';
            elseif ($val[1] == '2') $repleace = ' две чайных ложки';
            elseif ($val[1] == '3') $repleace = ' три чайных ложки';
            elseif ($val[1] == '4') $repleace = ' четыре чайных ложки';
            elseif ($val[1] == '5') $repleace = ' пять чайных ложек';
            elseif ($val[1] > '5') $repleace = str_ireplace("ч.", "чайных", $val[0]);
            else  $repleace = $val[1] . " чайные ложки";

            $text = str_ireplace($val[0], ' '.$repleace, $text);
            // var_dump($replace_text);
        }

        return $text;

    }


//Замена "шт"
    protected function rewrite_sht($text)
    {
        preg_match_all('#(((\d+(,|\.|/|-))?)\s*(\d+))\s*шт((\.)?)([^а-я])#xmsi', $text, $array, PREG_SET_ORDER);

      #   print_r($array);


        foreach ($array as $val) {

           # if (isset($val[2])) $val[1] = $val[2];

            if ($val[1] == '1') $repleace = ' одна штука ';
            elseif ($val[1] == '2') $repleace = ' две штуки ';
            elseif ($val[1] == '3') $repleace = ' три штуки ';
            elseif ($val[1] == '4') $repleace = ' четыре штуки ';
            elseif ($val[1] == '5') $repleace = ' пять штук ';
            elseif ($val[1] == '6') $repleace = ' шесть штук ';
            elseif ($val[1] == '7') $repleace = ' семь штук ';
            elseif ($val[1] == '8') $repleace = ' восемь штук ';
            elseif ($val[1] == '9') $repleace = ' девять штук ';
            elseif ($val[1] == '10') $repleace =' десять штук ';
            elseif ($val[1] > '10') $repleace = " {$val[1]} штук ";


            $replace_text = '#' . preg_quote($val[0]) . '#mi';

            $text = preg_replace($replace_text, $repleace, $text);
            // var_dump($replace_text);
        }

        return $text;

    }


    protected function rewrite_grams($text)
    {
        preg_match_all('#(((\d+(,|\.|-))?)\s*(\d+))\s*г((р)?)((\.)?)([^а-я])#msi', $text, $array, PREG_SET_ORDER);

        // print_r($array);


        foreach ($array as $val) {

            str_ireplace("\n", "", $val[0]);

            $repleace = " {$val[1]} грамм ";

            if (preg_match('#грамм#i', $val[0])) break;

            $replace_text = $val[0];


            //var_dump($replace_text);

            $text = str_ireplace($replace_text, $repleace, $text);
            // var_dump($replace_text);
        }

        return $text;

    }


    protected function rewrite_mililitry($text)
    {
        preg_match_all('#(((\d+(,|\.|-))?)\s*(\d+))\s*мл((\.)?)([^а-я])#msi', $text, $array, PREG_SET_ORDER);

        // print_r($array);


        foreach ($array as $val) {

            str_ireplace("\n", "", $val[0]);

            $repleace = " {$val[1]} миллилитров ";

            $replace_text = $val[0];

            //var_dump($replace_text);

            $text = str_ireplace($replace_text, $repleace, $text);
            // var_dump($replace_text);
        }

        return $text;

    }


    protected function rewrite_kg($text)
    {
        preg_match_all('#(((\d+(,|\.|-))?)\s*(\d+))\s*кг((\.)?)([^а-я])#xmsi', $text, $array, PREG_SET_ORDER);

        // print_r($array);


        foreach ($array as $val) {

            if (isset($val[2])) {
                $repleace = str_ireplace("кг", "килограмм", $val[0]);
            }
            elseif ($val[1] == '1') $repleace = ' один килограмм';
            elseif ($val[1] == '2') $repleace = ' два килограмма';
            elseif ($val[1] == '3') $repleace = ' три килограмма';
            elseif ($val[1] == '4') $repleace = ' четыре килограмма';
            elseif ($val[1] == '5') $repleace = ' пять килограмм';
            elseif ($val[1] > '5') $repleace = " {$val[1]} килограмм";

            $replace_text = '#' . preg_quote($val[0]) . '#mi';

            $text = preg_replace($replace_text, $repleace, $text);
            // var_dump($replace_text);
        }

        return $text;

    }

    protected function rewrite_br($text)
    {

        $text = preg_replace("#\n#iUs", "<br />\n", $text) ;


        return $text;

    }
}