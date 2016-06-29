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
     * label
     * name
     * id
     * value
     * type         HTML5 input tag type (text, email, date, etc.)
     * required     Boolean
     * attr         Additional attributes to include inside the input tag
     *
     * @param array $params
     */
    public function field(array $params)
    {
        return "
        <dl><dt><label for=\"$params[id]\">$params[label]</label></dt>
            <dd>{$this->input($params)}</dd>
        </dl>
        ";
    }

    /**
     * Parameters:
     *
     * label
     * name
     * id
     * value
     * type         HTML5 input tag type (text, email, date, etc.)
     * required     Boolean
     * attr         Additional attributes to include inside the input tag
     *
     * @param array $params
     */
    public function input(array $params)
    {
        if (isset(  $params['type'])) {
            switch ($params['type']) {
                case 'person':
                    $h = $this->template->getHelper('personChooser');
                    return $h->personChooser($params['name'], $params['id'], $params['value']);
                break;
            }
        }

        $required = '';
        if (!empty($params['required']) && $params['required']) {
            $required = 'required="true"';
            $class[]  = 'required';
        }

        $value = !empty($params['value']) ? $params['value'] : '';

        $type = '';
        if (!empty($params['type'])) {
            $type = "type=\"$params[type]\"";

            if ($params['type'] === 'date') {
                if ($value) { $value = date(DATE_FORMAT, $value); }
                $params['attr']['placeholder'] = View::translateDateString(DATE_FORMAT);
            }
        }

        $attr = '';
        if (!empty(  $params['attr'])) {
            foreach ($params['attr'] as $k=>$v) { $attr.= "$k=\"$v\""; }
        }

        return "<input name=\"$params[name]\" id=\"$params[id]\" $type value=\"$value\" $required  $attr />";
    }
}