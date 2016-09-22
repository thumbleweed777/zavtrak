<?php


class VideonatorConfigForm extends sfForm
{
    public function configure()
    {


        $this->setWidgets(array(
            'enable' => new sfWidgetFormChoice(
                array(
                    'choices' => array('Включен', 'Выключен'),
                )
            ),

            'locale' => new sfWidgetFormChoice(
                array(
                    'choices' => array('All' ,'RU', 'EN' ),
                )
            ),
            'before_title' => new sfWidgetFormInput(),
            'after_title' => new sfWidgetFormInput(),
            #  'static_template' => new sfWidgetFormTextarea(),
            'stop_list' => new sfWidgetFormTextarea(),


            'publish_date' => new sfWidgetFormInput(),


        ));

        $this->setValidators(array(

            'before_title' => new sfValidatorString(
                array(
                    'required' => false,
                )
            ),
            'after_title' => new sfValidatorString(
                array(
                    'required' => false,
                )
            ),

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
                    'choices' => array_keys(array('Включен', 'Выключен')),
                )
            ),


            'locale' => new sfValidatorChoice(
                array(
                    'choices' => array_keys (array('All' ,'RU', 'EN' )),
                )
            ),

        ));


        $this->widgetSchema->setLabels(array(
            'enable' => 'Вкл/Выкл Модуля',
            'before_title' => 'Добавлять до тайтла',
            'after_title' => 'Добавлять после тайтла',
            'stop_list' => 'Стоп-слова',
            'comments' => 'Парсить комментарии',
            'description' => 'Парсить описание',
            'num_comments' => 'Лимит комментариев',
            'publish_date' => 'Растягивать публикацию на (мин)',
            'locale' => 'Локаль'

        ));

        $this->widgetSchema->setHelps(array(

            'admin_list_limit' => 'Количесво ключей отображаемое в админ-панели от 10 до 1000',
            'popularity_key' => 'Популярность ключей измеряется в количестве вхождей по данному ключу на сайт, если популяность больше или равна данному значению то ключ будет учитываться. Статистика популярности берется из модуля "Переходы". <br /> Если указан <b>0</b> то фильтрация по популярности отключена',
            'add_to_tag' => 'Фраза будет добавлена автоматически ко всем создаваемым тэгам',
            'stop_list' => 'Список стоп слов, при обнаружении которых модуль не будет учитывать переходы, слова разделять <b>запятой</b>',
            'accuracy_search' => 'Точность поиска по новостям',
            'cut_list' => 'Модуль будет вырезать слова из ключей которые будут вписаны в это поле, слова разделять <b>запятой</b>',
            'block_template' => 'Допустимы переменные: <br /> <b>{title}</b> - Название тэга <br /> <b>{link}</b> - ссылка на статическую страницу с тэгом <br /> <b>{img}</b> - путь к изображению тэга',
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


        $this->write_ini_file($values, DATA_DIR . '/receptonator.config.ini', false);

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