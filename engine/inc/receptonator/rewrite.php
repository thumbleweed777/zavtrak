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


// ������ ��������� �� txt
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


// �������� �����
    protected function rewrite_st_lozhki($text)
    {
        preg_match_all('#\s*(((\d+(,|\.|/|-))?)\s*(\d+))\s*��\.\s*�((����|����|����|�)?)((\.)?)([^�-�])#xmsi', $text, $array, PREG_SET_ORDER);
        //var_dump($array);


        foreach ($array as $val) {

           # if (isset($val[3])) $val[1] = $val[3];
            if (preg_match('#\.|,|/#i', $val[1])) $repleace = $val[1].' �������� �����';
            if ($val[1] == '1') $repleace = ' ���� �������� �����';
            elseif ($val[1] == '2') $repleace = ' ��� �������� �����';
            elseif ($val[1] == '3') $repleace = ' ��� �������� �����';
            elseif ($val[1] == '4') $repleace = ' ������ �������� �����';
            elseif ($val[1] == '5') $repleace = ' ���� �������� �����';
            elseif ($val[1] > '5') $repleace = str_ireplace("��.", "��������", $val[0]);
            else  $repleace = $val[1] . " �������� �����";


            $replace_text = '#' . preg_quote($val[0]) . '#mi';

            $text = preg_replace($replace_text, ' '.$repleace.' ', $text);
            //var_dump($replace_text);
        }

        return $text;

    }

// �������
    protected function rewrite_stakany($text)
    {
        preg_match_all('#\s*(((\d+(,|\.|/|-))?)\s*(\d+))\s*��\.#xmsi', $text, $array, PREG_SET_ORDER);
        //var_dump($array);


        foreach ($array as $val) {

           # if (isset($val[3])) $val[1] = $val[3];
            if (preg_match('#\.|,|/|\\#i', $val[1])) $repleace = $val[1].' �������';
            if ($val[1] == '1') $repleace = ' ���� ������';
            elseif ($val[1] == '2') $repleace = ' ��� �������';
            elseif ($val[1] == '3') $repleace = ' ��� �������';
            elseif ($val[1] == '4') $repleace = ' ������ �������';
            elseif ($val[1] == '5') $repleace = ' ���� ��������';
            else  $repleace = $val[1] . " ��������";


            $replace_text = '#' . preg_quote($val[0]) . '#mi';

            $text = preg_replace($replace_text, ' '.$repleace.' ', $text);
            //var_dump($replace_text);
        }

        return $text;

    }




//������ �����
    protected function rewrite_lozhki($text)
    {
        preg_match_all('#\s*(((\d+(,|\.|/|-))?)\s*(\d+))\s*�((�|��)?)\.\s*�((����|����|�����|�)?)((\.)?)([^�-�])#xmsi', $text, $array, PREG_SET_ORDER);

        #  print_r($array);

        foreach ($array as $val) {

           # if (isset($val[3])) $val[1] = $val[3];

            if (preg_match('#\.|,|/#i', $val[1])) $repleace = $val[1].' ������ �����';
            elseif ($val[1] == '1') $repleace = ' ���� ������ ����� ';
            elseif ($val[1] == '2') $repleace = ' ��� ������ �����';
            elseif ($val[1] == '3') $repleace = ' ��� ������ �����';
            elseif ($val[1] == '4') $repleace = ' ������ ������ �����';
            elseif ($val[1] == '5') $repleace = ' ���� ������ �����';
            elseif ($val[1] > '5') $repleace = str_ireplace("�.", "������", $val[0]);
            else  $repleace = $val[1] . " ������ �����";

            $text = str_ireplace($val[0], ' '.$repleace, $text);
            // var_dump($replace_text);
        }

        return $text;

    }


//������ "��"
    protected function rewrite_sht($text)
    {
        preg_match_all('#(((\d+(,|\.|/|-))?)\s*(\d+))\s*��((\.)?)([^�-�])#xmsi', $text, $array, PREG_SET_ORDER);

      #   print_r($array);


        foreach ($array as $val) {

           # if (isset($val[2])) $val[1] = $val[2];

            if ($val[1] == '1') $repleace = ' ���� ����� ';
            elseif ($val[1] == '2') $repleace = ' ��� ����� ';
            elseif ($val[1] == '3') $repleace = ' ��� ����� ';
            elseif ($val[1] == '4') $repleace = ' ������ ����� ';
            elseif ($val[1] == '5') $repleace = ' ���� ���� ';
            elseif ($val[1] == '6') $repleace = ' ����� ���� ';
            elseif ($val[1] == '7') $repleace = ' ���� ���� ';
            elseif ($val[1] == '8') $repleace = ' ������ ���� ';
            elseif ($val[1] == '9') $repleace = ' ������ ���� ';
            elseif ($val[1] == '10') $repleace =' ������ ���� ';
            elseif ($val[1] > '10') $repleace = " {$val[1]} ���� ";


            $replace_text = '#' . preg_quote($val[0]) . '#mi';

            $text = preg_replace($replace_text, $repleace, $text);
            // var_dump($replace_text);
        }

        return $text;

    }


    protected function rewrite_grams($text)
    {
        preg_match_all('#(((\d+(,|\.|-))?)\s*(\d+))\s*�((�)?)((\.)?)([^�-�])#msi', $text, $array, PREG_SET_ORDER);

        // print_r($array);


        foreach ($array as $val) {

            str_ireplace("\n", "", $val[0]);

            $repleace = " {$val[1]} ����� ";

            if (preg_match('#�����#i', $val[0])) break;

            $replace_text = $val[0];


            //var_dump($replace_text);

            $text = str_ireplace($replace_text, $repleace, $text);
            // var_dump($replace_text);
        }

        return $text;

    }


    protected function rewrite_mililitry($text)
    {
        preg_match_all('#(((\d+(,|\.|-))?)\s*(\d+))\s*��((\.)?)([^�-�])#msi', $text, $array, PREG_SET_ORDER);

        // print_r($array);


        foreach ($array as $val) {

            str_ireplace("\n", "", $val[0]);

            $repleace = " {$val[1]} ����������� ";

            $replace_text = $val[0];

            //var_dump($replace_text);

            $text = str_ireplace($replace_text, $repleace, $text);
            // var_dump($replace_text);
        }

        return $text;

    }


    protected function rewrite_kg($text)
    {
        preg_match_all('#(((\d+(,|\.|-))?)\s*(\d+))\s*��((\.)?)([^�-�])#xmsi', $text, $array, PREG_SET_ORDER);

        // print_r($array);


        foreach ($array as $val) {

            if (isset($val[2])) {
                $repleace = str_ireplace("��", "���������", $val[0]);
            }
            elseif ($val[1] == '1') $repleace = ' ���� ���������';
            elseif ($val[1] == '2') $repleace = ' ��� ����������';
            elseif ($val[1] == '3') $repleace = ' ��� ����������';
            elseif ($val[1] == '4') $repleace = ' ������ ����������';
            elseif ($val[1] == '5') $repleace = ' ���� ���������';
            elseif ($val[1] > '5') $repleace = " {$val[1]} ���������";

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