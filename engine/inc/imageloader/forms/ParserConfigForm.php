<?php


class ParserConfigForm extends sfForm
{
    public function configure()
    {

        $pos = array(
            'top' => '������',
            'bottom' => '�����',
            'right' => '������',
            'left' => '�����',
        );


        $this->setWidgets(array(
            'enable' => new sfWidgetFormChoice(
                array(
                    'choices' => array('�������', '��������'),
                )
            ),
            'width' => new sfWidgetFormInput(
                array(
                    'label' => '������'
                )
            ),
            'position' => new sfWidgetFormChoice(
                array(
                    'choices' => $pos,
                )
            ),

        ));

        $this->setValidators(array(

            'enable' => new sfValidatorChoice(
                array(
                    'choices' => array_keys(array('�������', '��������')),
                )
            ),
            'width' => new sfValidatorString(
                array(

                )
            ),


            'position' => new sfValidatorChoice(
                array(
                    'choices' => array_keys($pos),
                )
            ),

        ));


        $this->widgetSchema->setLabels(array(
            'enable' => '���/���� ������',
            'before_title' => '��������� �� ������',
            'after_title' => '��������� ����� ������',
            'stop_list' => '����-�����',
            'comments' => '������� �����������',
            'description' => '������� ��������',
            'num_comments' => '����� ������������',
            'publish_date' => '����������� ���������� �� (���)',
            'on_home_page' => '�� �������',
            'site' => '���� ��� ��������',
        ));



        $block_template_d = stripslashes($this->getDefault('block_template'));

        $this->setDefault('block_template', $block_template_d);

        $this->widgetSchema->setNameFormat('setting[%s]');

        $this->widgetSchema->setDefaultFormFormatterName('Admin');

        $this->addCSRFProtection('spataco');

        $this->widgetSchema->setCharset('cp1251');
    }

    public function bind(array $taintedValues = null, array $taintedFiles = null)
    {
        #  $taintedValues['block_template'] = stripslashes($taintedValues['block_template']);
        parent::bind($taintedValues, $taintedFiles);
    }

    public function setDefaults($defaults)
    {
        return parent::setDefaults($defaults);
    }


    public function save()
    {
        $values = $this->getValues();


        $this->write_ini_file($values, DATA_DIR . '/' . getModuleName() . '.config.ini', false);

    }

    public function write_ini_file($assoc_arr, $path, $has_sections = FALSE)
    {
        $content = "";
        if ($has_sections) {
            foreach ($assoc_arr as $key => $elem) {
                $content .= "[" . $key . "]\n";
                foreach ($elem as $key2 => $elem2) {
                    if (is_array($elem2)) {
                        for ($i = 0; $i < count($elem2); $i++) {
                            $content .= $key2 . "[] = \"" . $elem2[$i] . "\"\n";
                        }
                    }
                    else if ($elem2 == "") $content .= $key2 . " = \n";
                    else $content .= $key2 . " = \"" . $elem2 . "\"\n";
                }
            }
        }
        else {
            foreach ($assoc_arr as $key => $elem) {
                if (is_array($elem)) {
                    for ($i = 0; $i < count($elem); $i++) {
                        $content .= $key . "[] = \"" . $elem[$i] . "\"\n";
                    }
                }
                else if ($elem == "") $content .= $key . " = \n";
                else $content .= $key . " = \"" . $elem . "\"\n";
            }
        }

        if (!$handle = fopen($path, 'w')) {
            return false;
        }
        if (!fwrite($handle, $content)) {
            return false;
        }
        fclose($handle);
        return true;
    }

}