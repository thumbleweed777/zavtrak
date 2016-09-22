<?php


class TaginatorConfigForm extends sfForm
{
    public function configure()
    {

        $accuracy_search = array(
            'full_match' => '����� ���������',
            'all_words' => '��� ����� �� � ����� ������������������',
            'once_word' => '���� �� ���� �����',
            'last_two' => '��������� ���',
            'first_two' => '������ ���',
            'first_three' => '������ 3 - ����� ����! )',
            'first_four' => '������ 4',
            'first_five' => '������ 5',
        );
        $block_sort = array(
            'DESC' => '������ �����',
            'ASC' => '������ ������',
            'RANDOM' => '��������� ������� - �����!',

        );

        $this->setWidgets(array(
            'enable' => new sfWidgetFormChoice(
                array(
                    'choices' => array('�������', '��������'),
                )
            ),
            'admin_list_limit' => new sfWidgetFormInput(),
            'popularity_key' => new sfWidgetFormInput(),
            'add_to_tag' => new sfWidgetFormInput(),
            'static_template' => new sfWidgetFormTextarea(),
            'stop_list' => new sfWidgetFormTextarea(),
            'cut_list' => new sfWidgetFormTextarea(),
            'search_limit' => new sfWidgetFormInput(),
            'accuracy_search' => new sfWidgetFormChoice(
                array(
                    'choices' => $accuracy_search,
                )
            ),
            'block_limit' => new sfWidgetFormInput(),
            'block_template' => new sfWidgetFormTextarea(),
            'block_sort' => new sfWidgetFormChoice(
                array(
                    'choices' => $block_sort,
                )
            ),
        ));

        $this->setValidators(array(
            'admin_list_limit' => new sfValidatorInteger(
                array(
                    'max' => 1000,
                    'min' => 10,
                )
            ),
            'popularity_key' => new sfValidatorInteger(
                array(
                    'max' => 1000,
                    'min' => 0,
                )
            ),
            'add_to_tag' => new sfValidatorString(
                array(
                    'required' => false,
                )
            ),

            'static_template' => new sfValidatorString(
                array(
                    'required' => true,
                )
            ),
            'stop_list' => new sfValidatorString(
                array(
                    'required' => false,
                )
            ),
            'cut_list' => new sfValidatorString(
                array(
                    'required' => false,
                )
            ),
            'search_limit' => new sfValidatorInteger(
                array(
                    'max' => 100,
                    'min' => 1,
                )
            ),
            'accuracy_search' => new sfValidatorChoice(
                array(
                    'choices' => array_keys($accuracy_search),
                )
            ),
            'enable' => new sfValidatorChoice(
                array(
                    'choices' => array_keys(array('�������', '��������')),
                )
            ),
            'block_limit' => new sfValidatorInteger(
                array(
                    'max' => 100,
                    'min' => 1,
                )
            ),
            'block_template' => new sfValidatorString(
                array(
                    'required' => true,
                )
            ),
            'block_sort' => new sfValidatorChoice(
                array(
                    'choices' => array_keys($block_sort),
                )
            ),
        ));


        $this->widgetSchema->setLabels(array(
            'enable' => '���/���� ������',
            'admin_list_limit' => '��������� ������ ������������ � �����-������',
            'popularity_key' => '������������ �� �����',
            'add_to_tag' => '��������� � ���� � ������ �����',
            'static_template' => '������ ����������� ��������',
            'stop_list' => '����-�����',
            'search_limit' => '����� ������ �� ��������',
            'accuracy_search' => '�������� ������',
            'cut_list' => '�������� �����',
            'block_limit' => '����� ����� � �����',
            'block_sort' => '���������� ����� � �����',
            'block_template' => '������ ������ ����� � <b>�����</b>',

        ));

        $this->widgetSchema->setHelps(array(

            'admin_list_limit' => '��������� ������ ������������ � �����-������ �� 10 �� 1000',
            'popularity_key' => '������������ ������ ���������� � ���������� ������� �� ������� ����� �� ����, ���� ����������� ������ ��� ����� ������� �������� �� ���� ����� �����������. ���������� ������������ ������� �� ������ "��������". <br /> ���� ������ <b>0</b> �� ���������� �� ������������ ���������',
            'add_to_tag' => '����� ����� ��������� ������������� �� ���� ����������� �����',
            'stop_list' => '������ ���� ����, ��� ����������� ������� ������ �� ����� ��������� ��������, ����� ��������� <b>�������</b>',
            'accuracy_search' => '�������� ������ �� ��������',
            'cut_list' => '������ ����� �������� ����� �� ������ ������� ����� ������� � ��� ����, ����� ��������� <b>�������</b>',
            'block_template' => '��������� ����������: <br /> <b>{title}</b> - �������� ���� <br /> <b>{link}</b> - ������ �� ����������� �������� � ����� <br /> <b>{img}</b> - ���� � ����������� ����',
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


        @file_put_contents(ENGINE_DIR.'/modules/taginator/template/static.php', $values['static_template']);

        unset ($values['static_template']);
        #   $values['block_template'] = addslashes($values['block_template']);


        $this->write_ini_file($values, DATA_DIR . '/taginator.config.ini', false);

    }

    public function write_ini_file($assoc_arr, $path, $has_sections = FALSE)
    {
        $content = "";
        if ($has_sections) {
            foreach ($assoc_arr as $key => $elem) {
                $content .= "[" . $key . "]\n";
                foreach ($elem as $key2 => $elem2) {
                    if (is_array($elem2)) {
                        for ($i = 0; $i < count($elem2); $i++)
                        {
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
                    for ($i = 0; $i < count($elem); $i++)
                    {
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