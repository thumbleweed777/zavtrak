<?php


class ParserConfigForm extends sfForm
{
    public function configure()
    {


        $sites = array(
            'gotovim-sami.ru' => 'gotovim-sami.ru',
            'cook-note.ru' => 'cook-note.ru',
            'menagere.ru' => 'menagere.ru',
            'gotovo-doma.ru' => 'gotovo-doma.ru',
            'mapcook.ru' => 'mapcook.ru',
        );

        $synonims = array();
        foreach (array_map('basename', glob(PARSER_DIR . '/synonyms/*')) as $k => $v)
            $synonims[$v] = $v;

        $this->setWidgets(array(
            'enable' => new sfWidgetFormChoice(
                array(
                    'choices' => array('�������', '��������'),
                )
            ),


            'stop_list' => new sfWidgetFormTextarea(),


            'publish_date' => new sfWidgetFormInput(),
            'on_home_page' => new sfWidgetFormInputCheckbox(array('value_attribute_value' => 'yes')),


            'synonims' => new sfWidgetFormChoice(
                array(
                    'choices' => $synonims,
                )
            ),

        ));

        $this->setValidators(array(


            'stop_list' => new sfValidatorString(
                array(
                    'required' => false,
                )
            ),

            'publish_date' => new sfValidatorInteger(
                array(
                    'required' => false,
                )
            ),
            'enable' => new sfValidatorChoice(
                array(
                    'choices' => array_keys(array('�������', '��������')),
                )
            ),


            'on_home_page' => new sfValidatorBoolean(
                array(
                    'required' => false,
                )
            ),

            'synonims' => new sfValidatorChoice(
                array(
                    'choices' => array_keys($synonims),
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

        $this->widgetSchema->setHelps(array(

            'admin_list_limit' => '��������� ������ ������������ � �����-������ �� 10 �� 1000',
            'popularity_key' => '������������ ������ ���������� � ���������� ������� �� ������� ����� �� ����, ���� ����������� ������ ��� ����� ������� �������� �� ���� ����� �����������. ���������� ������������ ������� �� ������ "��������". <br /> ���� ������ <b>0</b> �� ���������� �� ������������ ���������',
            'add_to_tag' => '����� ����� ��������� ������������� �� ���� ����������� �����',
            'stop_list' => '������ ���� ����, ��� ����������� ������� ������ �� ����� ��������� ��������, ����� ��������� <b>�������</b>',
            'accuracy_search' => '�������� ������ �� ��������',
            'cut_list' => '������ ����� �������� ����� �� ������ ������� ����� ������� � ��� ����, ����� ��������� <b>�������</b>',
            'block_template' => '��������� ����������: <br /> <b>{title}</b> - �������� ���� <br /> <b>{link}</b> - ������ �� ����������� �������� � ����� <br /> <b>{img}</b> - ���� � ����������� ����',
            'on_home_page' => '���������� �� �������',
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