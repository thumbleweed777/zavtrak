<?php


class TaginatorAddForm extends sfForm
{


    public function configure()
    {


        $this->setWidgets(array(

            'tag_list' => new sfWidgetFormTextarea(
                array(),
                array(
                    'style' => 'width: 600px; height:400px'

                )
            ),

        ));

        $this->setValidators(array(

            'tag_list' => new sfValidatorString(),

        ));


        $this->widgetSchema->setLabels(array(
            'tag_list' => 'Список ключей',

        ));

        $this->widgetSchema->setHelps(array(
            'tag_list' => 'Введите список ключей, тагинатор автоматически обработает его',

        ));

        $this->widgetSchema->setNameFormat('tag[%s]');

        $this->widgetSchema->setDefaultFormFormatterName('Admin');

        $this->addCSRFProtection('spataco');

        $this->widgetSchema->setCharset('cp1251');

        $this->widgetSchema->setDefault('tag_list', $this->getFromTxt());
    }


    public function save()
    {
        /*        require_once  ENGINE_DIR . '/modules/taginator.php';

 $tags = explode("\n", $this->getValue('tag_list'));

 foreach ($tags as $tag) {
     taginator_execute(1000, trim($tag));
 }
 header('Location: ' . $_SERVER['PHP_SELF'] . '?mod=taginator&action=list', true, 301);*/

        $this->saveToTxt($this->getValue('tag_list'));
    }


    public function saveToTxt($text)
    {
        $path = ENGINE_DIR . '/data/taginator.db.txt';

        file_put_contents($path, $text);
    }

    public function getFromTxt()
    {
        $path = ENGINE_DIR . '/data/taginator.db.txt';

        if (!is_writable($path)) {
            file_put_contents($path, '');
        }

        $value = trim( file_get_contents($path));

        return $value;
    }
}
