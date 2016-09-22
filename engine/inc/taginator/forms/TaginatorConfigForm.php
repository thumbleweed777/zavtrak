<?php


class TaginatorConfigForm extends sfForm
{
    public function configure()
    {

        $accuracy_search = array(
            'full_match' => 'Точно вхождение',
            'all_words' => 'Все слова но в любой последовательности',
            'once_word' => 'Хотя бы одно слово',
            'last_two' => 'Последние два',
            'first_two' => 'Первые два',
            'first_three' => 'Первые 3 - самый руль! )',
            'first_four' => 'Первые 4',
            'first_five' => 'Первые 5',
        );
        $block_sort = array(
            'DESC' => 'Первые новые',
            'ASC' => 'Первые старые',
            'RANDOM' => 'Случайная выборка - супер!',

        );

        $this->setWidgets(array(
            'enable' => new sfWidgetFormChoice(
                array(
                    'choices' => array('Включен', 'Выключен'),
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
                    'choices' => array_keys(array('Включен', 'Выключен')),
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
            'enable' => 'Вкл/Выкл Модуля',
            'admin_list_limit' => 'Количесво ключей отображаемое в админ-панели',
            'popularity_key' => 'Популярность не менее',
            'add_to_tag' => 'Добавлять к тэгу в начало фразу',
            'static_template' => 'Шаблон статической страницы',
            'stop_list' => 'Стоп-слова',
            'search_limit' => 'Лимит поиска по новостям',
            'accuracy_search' => 'Точность поиска',
            'cut_list' => 'Вырезать слова',
            'block_limit' => 'Лимит тэгов в блоке',
            'block_sort' => 'Сортировка тэгов в блоке',
            'block_template' => 'Шаблон вывода тэгов в <b>блоке</b>',

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