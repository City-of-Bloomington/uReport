<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Templates\Helpers;

use Blossom\Classes\Helper;
use Blossom\Classes\View;

class Field extends Helper
{
    /**
     * Parameters:
     *
     * label string
     * name  string
     * id    string
     * value mixed
     * type  string   HTML5 input tag type (text, email, date, etc.)
     * required     Boolean
     * attr         Additional attributes to include inside the input tag
     *
     * @param array $params
     */
    public function field(array $params)
    {
        $required = '';
        $classes  = '';
        if (!empty($params['required']) && $params['required']) {
            $required = 'required="true"';
            $class[]  = 'required';
        }

        if (isset(  $params['type'])) {
            switch ($params['type']) {
                case 'date':
                    $params['value'] = !empty($params['value']) ? date(DATE_FORMAT, $params['value']) : '';
                    $params['attr']['placeholder'] = View::translateDateString(DATE_FORMAT);
                    $renderInput = 'input';
                break;

                case 'select':
                case 'textarea':
                case 'radio':
                case 'checkbox':
                case 'person':
                    $class[]     = $params['type'];
                    $renderInput = $params['type'];
                break;

                default:
                    $renderInput = 'input';
            }
        }
        else {
            $renderInput = 'input';
        }

        if (!empty($class)) { $classes = ' class="'.implode(' ', $class).'"'; }

        $attr = '';
        if (!empty(  $params['attr'])) {
            foreach ($params['attr'] as $k=>$v) { $attr.= "$k=\"$v\""; }
        }

        $input = $this->$renderInput($params, $required, $attr);
        $for   = !empty($params['id'   ]) ? " for=\"$params[id]\""                       : '';
        $label = !empty($params['label']) ? "<dt><label$for>$params[label]</label></dt>" : '';

        return "
        <dl$classes>
            $label
            <dd>$input</dd>
        </dl>
        ";
    }

    /**
     * Parameters:
     *
     * label string
     * name  string
     * id    string
     * value string
     * type  string   HTML5 input tag type (text, email, date, etc.)
     *
     * @param array  $params
     * @param string $required  The string for the attribute 'required="true"'
     * @param string $attr      The string for any and all additional attributes
     */
    public function input(array $params, $required=null, $attr=null)
    {
        $value = !empty($params['value']) ? $params['value'] : '';

        $type = '';
        if (!empty($params['type'])) {
            $type = "type=\"$params[type]\"";

        }

        return "<input name=\"$params[name]\" id=\"$params[id]\" $type value=\"$value\" $required  $attr />";
    }

    /**
     * Parameters:
     *
     * label string
     * name  string
     * id    string
     * value string
     * type  string   HTML5 input tag type (text, email, date, etc.)
     *
     * @param array  $params
     * @param string $required  The string for the attribute 'required="true"'
     * @param string $attr      The string for any and all additional attributes
     */
    public function select(array $params, $required=null, $attr=null)
    {
        if ($params['type'] !== 'select') { throw new \Exception('incorrectType'); }

        $value = !empty($params['value']) ? $params['value'] : '';

        $select = "<select name=\"$params[name]\" id=\"$params[id]\" $required $attr>";
        if (!empty(  $params['options'])) {
            foreach ($params['options'] as $o) {
                $label    = !empty($o['label'])   ? $o['label']       : $o['value'];
                $selected = $value == $o['value'] ? 'selected="true"' : '';
                $select.= "<option value=\"$o[value]\" $selected>$label</option>";
            }
        }
        $select.= "</select>";
        return $select;
    }

    /**
     * Parameters:
     *
     * label string
     * name  string
     * id    string
     * value string
     * type  string   HTML5 input tag type (text, email, date, etc.)
     *
     * @param array  $params
     * @param string $required  The string for the attribute 'required="true"'
     * @param string $attr      The string for any and all additional attributes
     */
    public function radio(array $params, $required=null, $attr=null)
    {
        if ($params['type'] !== 'radio') { throw new \Exception('incorrectType'); }

        $value = !empty($params['value']) ? $params['value'] : '';

        $radioButtons = '';
        if (!empty(  $params['options'])) {
            foreach ($params['options'] as $o) {
                $label   = !empty($o['label'])   ? $o['label']      : $o['value'];
                $checked = $value == $o['value'] ? 'checked="true"' : '';

                $radioButtons.= "<label><input name=\"$params[name]\" type=\"radio\" value=\"$o[value]\" $checked/> $label</label>";
            }
        }
        return $radioButtons;
    }

    /**
     * Parameters:
     *
     * label string
     * name  string
     * id    string
     * value array
     * type  string   HTML5 input tag type (text, email, date, etc.)
     *
     * @param array  $params
     * @param string $required  The string for the attribute 'required="true"'
     * @param string $attr      The string for any and all additional attributes
     */
    public function checkbox(array $params, $required=null, $attr=null)
    {
        if ($params['type'] !== 'checkbox') { throw new \Exception('incorrectType'); }

        $values = !empty($params['value']) ? $params['value'] : [];

        $inputs = '';
        if (!empty(  $params['options'])) {
            foreach ($params['options'] as $o) {
                $label   = !empty($o['label'])            ? $o['label']      : $o['value'];
                $checked = in_array($o['value'], $values) ? 'checked="true"' : '';

                $name   = $params['name'].'['.$o['value'].']';
                $inputs.= "<label><input name=\"$name\" type=\"checkbox\" value=\"$o[value]\" $checked/> $label</label>";
            }
        }
        return $inputs;
    }

    /**
     * Parameters:
     *
     * label string
     * name  string
     * id    string
     * value string
     * type  string   HTML5 input tag type (text, email, date, etc.)
     *
     * @param array  $params
     * @param string $required  The string for the attribute 'required="true"'
     * @param string $attr      The string for any and all additional attributes
     */
    public function textarea(array $params, $required=null, $attr=null)
    {
        if ($params['type'] !== 'textarea') { throw new \Exception('incorrectType'); }

        $value = !empty($params['value']) ? $params['value'] : '';

        return "<textarea name=\"$params[name]\" id=\"$params[id]\" $required $attr>$value</textarea>";
    }

    /**
     * Parameters:
     *
     * label string
     * name  string
     * id    string
     * value string
     * type  string   HTML5 input tag type (text, email, date, etc.)
     *
     * @param array  $params
     * @param string $required  The string for the attribute 'required="true"'
     * @param string $attr      The string for any and all additional attributes
     */
    public function person(array $params, $required=null, $attr=null)
    {
        $h = $this->template->getHelper('personChooser');
        return $h->personChooser($params['name'], $params['value']);
    }
}