<?php


class RotatorConfigForm extends sfForm
{
    public function configure()
    {

        if (!defined('DATALIFEENGINE')) {
            die("Hacking attempt!");
        }

        $cats  = $this->CategorySelection();

        $this->setWidgets(array(
            'enable' => new sfWidgetFormChoice(
                array(
                    'choices' => array('�������', '��������'),
                )
            ),

            'show_title' => new sfWidgetFormChoice(
                array(

                    'choices' => array('���', '��'),
                )
            ),
            'title_pos' => new sfWidgetFormChoice(
                array(

                    'choices' => array( 'top' =>'��� ���������', 'bottom' => '��� ���������'),
                )
            ),


            'rows' => new sfWidgetFormInput(
                array(),
                array(
                    'style' => 'width: 50px;'
                )
            ),
            'columns' => new sfWidgetFormInput(
                array(),
                array(
                    'style' => 'width: 50px;'
                )),

            'img_width' => new sfWidgetFormInput(
                                array(),
                                array(
                                    'style' => 'width: 100px;'
                                )),
            'img_height' => new sfWidgetFormInput(
                array(),
                array(
                    'style' => 'width: 100px;'
                )),
            'cats' => new sfWidgetFormChoice(
                array(
                    'multiple' => true,
                    'choices' => $cats,
                )
            ),


        ));

        $this->setValidators(array(

            'rows' => new sfValidatorInteger(
                array(
                    'required' => true,
                )
            ),
            'columns' => new sfValidatorInteger(
                array(
                    'required' => true,
                )
            ),

            'img_width' => new sfValidatorInteger(
                array(
                    'required' => true,
                )
            ),

            'img_height' => new sfValidatorInteger(
                array(
                    'required' => true,
                )
            ),

            'enable' => new sfValidatorChoice(
                array(
                    'choices' => array_keys(array('�������', '��������')),
                )
            ),

            'show_title' => new sfValidatorChoice(
                array(
                    'choices' => array_keys(array('�������', '��������')),
                )
            ),

            'title_pos' => new sfValidatorChoice(
                array(
                    'choices' => array_keys(array( 'top' =>'��� ���������', 'bottom' => '��� ���������')),
                )
            ),

            'cats' => new sfValidatorChoice(
                array(
                    'multiple' => true,
                    'choices' => array_keys($cats),
                )
            ),

        ));


        $this->widgetSchema->setLabels(array(
            'enable' => '���/���� ������',
            'rows' => '���������� �����',
            'columns' => '���������� ��������',
            'img_width' => '������ �������� � �����',
            'show_title' => '�������� ����� �������',
            'cats' => '��������� �� ������� ����� �����',
            'img_height' => '������ �������� � �����',
            'title_pos' => '������� ������',
        ));

        $this->widgetSchema->setHelps(array(


            #  'rows' => '������������ ������ ���������� � ���������� ������� �� ������� ����� �� ����, ���� ����������� ������ ��� ����� ������� �������� �� ���� ����� �����������. ���������� ������������ ������� �� ������ "��������". <br /> ���� ������ <b>0</b> �� ���������� �� ������������ ���������',
            #  'columns' => '����� ����� ��������� ������������� �� ���� ����������� �����',

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


        # @file_put_contents(ENGINE_DIR . '/inc/receptonator/template/full.php', $values['static_template']);

        # unset ($values['static_template']);
        #   $values['block_template'] = addslashes($values['block_template']);

        $this->write_ini_file($values, DATA_DIR . '/rotator.config.ini', false);

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


    function CategorySelection()
    {
        global $cat_info, $user_group, $member_id;

        $cats = array();
        if (count($cat_info)) {

            foreach ($cat_info as $k => $c) {
                $cats[$c['id']] = $c['name'];
            }

        }
        return $cats;
    }


}