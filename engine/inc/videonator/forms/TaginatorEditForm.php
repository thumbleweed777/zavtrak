<?php


class TaginatorForm extends sfForm
{
    public function configure()
    {

        $accuracy_search = array(
            'tag' => 'Точно совпадение',
            'slug' => 'Хотябы одно слово',
            'image' => 'Точно совпадение',
            'post_ids' => 'Последние два',
        );

        $this->setWidgets(array(
                               'id' => new sfWidgetFormInputHidden(),
                               'tag' => new sfWidgetFormInput(),
                               'slug' => new sfWidgetFormInput(),
                               'image' => new sfWidgetFormInputFileEditable(
                                   array(
                                        'label' => 'Изображение',
                                        'file_src' => '/uploads/taginator/' . $this->getDefault('image'),
                                        'is_image' => true,
                                        'edit_mode' => true,
                                        'template' => '<div class="edit_image">%file%<br />%input%<br /></div>',
                                   )
                               ),
                               'post_ids' => new sfWidgetFormInput(),
                          ));

        $this->setValidators(array(
                                  'id' => new sfValidatorInteger(),
                                  'tag' => new sfValidatorString(),
                                  'slug' => new sfValidatorRegex(
                                      array(
                                           'trim' => true,
                                           'pattern' => '#^[0-9a-z\-_]+$#i',
                                      ),
                                      array(
                                           'invalid' => 'Только лат. символы',
                                      )
                                  ),
                                  'image' => new sfValidatorFile(
                                      array(
                                           'required' => false,
                                           'path' => false,
                                           'mime_types' => 'web_images',
                                           'max_size' => 112000,
                                      )
                                  ),
                                  'post_ids' => new sfValidatorRegex(
                                      array(
                                           'pattern' => '#^(?:-?[0-9]+)(?:\,(?:-?[0-9]+))*$$#i',
                                      ),
                                      array(
                                           'invalid' => 'Только цифры через запятую',
                                      )
                                  ),
                             ));


        $this->widgetSchema->setLabels(array(
                                            'tag' => 'Тэг(ключ)',
                                            'slug' => 'ЧПУ',
                                            'image' => 'Изображение',
                                            'post_ids' => 'ID привязанных новостей',
                                       ));

        $this->widgetSchema->setHelps(array(
                                           'tag' => 'Тэг(ключ)',
                                           'slug' => 'только латинские символы и цифры (также _ - )',
                                           'image' => 'Изображение для тэга',
                                           'post_ids' => 'ID привязанных новостей, разделять запятой',
                                      ));

        $this->widgetSchema->setNameFormat('tag[%s]');

        $this->widgetSchema->setDefaultFormFormatterName('Admin');

        $this->addCSRFProtection('spataco');

        $this->widgetSchema->setCharset('cp1251');
    }


    public function save()
    {
        global $db;
        $id = $this->getValue('id');
        $values = $this->getValues();

        if (is_object($values['image'])) {
           $values['image'] = $this->save_image($values['image']);
        }else {
           unset($values['image']);
        }


        $sql_values = array();
        unset($values['id']);
        foreach ($values as $key => $value) {
            $sql_values [] = $key . '="' . $db->safesql($value) . '"';
        }
        $sql_value = implode(', ', $sql_values);
        $query = "UPDATE " . PREFIX . "_taginator SET {$sql_value} WHERE id='{$id}'";
        $db->query($query);
    }

    /**
     * @param sfValidatedFile $image
     * @return void
     */
    public function  save_image($image)
    {
        if (is_object($image)) {
            move_uploaded_file($image->getTempName(), UPLOAD_DIR . '/taginator/' . $this->getValue('slug') . $image->getOriginalExtension());
            return $this->getValue('slug') . $image->getOriginalExtension();
        }

        return $image;
    }

}
