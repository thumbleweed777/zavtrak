<?php

class sfWidgetFormSchemaFormatterAdmin extends sfWidgetFormSchemaFormatter
{

    protected
    $rowFormat = "<tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=2></td></tr> <tr  >\n  <td style=\"vertical-align: top; padding:4px\" class=\"option\" ><b>%label%</b>
<span class=small>%help%</span> </td>\n  <td width=494 align=left  >%error%%field%%hidden_fields%</td>\n</tr>\n",
    $errorRowFormat = "<tr><td colspan=\"2\">\n%errors%</td></tr>\n",
    $helpFormat = '<br />%help%',
    $decoratorFormat = "<table>\n  %content%</table>";

    public function formatRow($label, $field, $errors = array(), $help = '', $hiddenFields = null)
    {


        return strtr(parent::formatRow($label, $field, $errors, $help, $hiddenFields), array(
                                                                                            '%is_error%' => (count($errors) > 0) ? ' field_error'
                                                                                                    : '',
                                                                                            '%id_form%' => $this->getIdForm(),
                                                                                            //'%is_required%' => $field,
                                                                                       ));
    }

    public function generateLabel($name, $attributes = array())
    {

        $this->setIdForm($name);


        return parent::generateLabel($name, $attributes);
    }

    public function setIdForm($name)
    {

        $this->id_form = $name;
    }

    public function getIdForm()
    {

        return $this->id_form;
    }

}

