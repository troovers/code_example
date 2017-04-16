<?php

namespace App\Model\Helpers;

use App\Model\Users\Roles;
use App\Model\Users\User;
use Eloquent;
use Illuminate\Support\Facades\Input;
use Request;

class Html
{

    /**
     * Create an input text element
     *
     * @param string $name The name
     * @param string $value The value
     * @param string $placeholder The placeholder
     * @param string $type The type
     * @param string $class Optional classes
     * @param string $html Optional extra HTML
     * @param string $front_addon Optional front addon
     * @param string $end_addon Optional end addon
     * @param string $width Optional width of the element
     * @param string $disabled Optionally disable the input element
     * @return string
     */
    public static function input($name, $value = '', $placeholder, $type = 'text', $class = '',  $html = '', $front_addon = '', $end_addon = '', $width = '', $disabled = '') {
        $width = !empty($width) ? 'style="width: '.$width.';"' : $width;

        if(!empty($front_addon) || !empty($end_addon)) {
            $input = '<div class="input-group" ' . $width . '>';
            $input .= !empty($front_addon) ? '<div class="input-group-addon">' . $front_addon . '</div>' : '';
            $input .= '<input type="' . $type . '" id="' . $name . '" name="' . $name . '" value="' . $value . '" placeholder="' . $placeholder . '" class="form-control ' . $class . '" ' . $html . ' ' . $disabled . '/>';
            $input .= !empty($end_addon) ? '<div class="input-group-addon">' . $end_addon . '</div>' : '';
            $input .= '</div>';
        } else {
            $input = '<input type="' . $type . '" id="' . $name . '" name="' . $name . '" value="' . $value . '" placeholder="' . $placeholder . '" class="form-control ' . $class . '" ' . $html . ' ' . $width . ' ' . $disabled . ' />';
        }

        return $input;
    }


    /**
     * Generate and return a toolbar
     *
     * @param $base_route
     * @param $buttons
     * @param bool $searchbar
     * @param array $filters
     * @return string
     */
	public static function toolbar($base_route, $buttons, $searchbar = false, $filters = []) {
		$actions = '';

		foreach($buttons as $type => $route) {
			$actions .= self::create_button($type, $route, $base_route) . PHP_EOL;
		}

		$toolbar  = '<div class="row toolbar">' . PHP_EOL;
        //$toolbar .= '<div class="col-sm-4"><h1>' . $page_title . '</h1></div>' . PHP_EOL;
		$toolbar .= '<div class="col-sm-8">' . PHP_EOL;
        $toolbar .= '<form class="form-inline" method="post" action="' . getenv('APP_SUFFIX') . Request::getPathInfo() . '">' . PHP_EOL;

        $toolbar .= csrf_field();

        foreach($filters as $filter) {
            $toolbar .= '<div class="form-group">' . PHP_EOL;
            $toolbar .= $filter . PHP_EOL;
            $toolbar .= '</div>' . PHP_EOL;
        }

        if($searchbar) {
            $toolbar .= '<div class="form-group"><div class="input-group">' . PHP_EOL;
            $toolbar .= '<input type="text" name="search" class="form-control" placeholder="Zoeken" value="' . (!empty(Request::get('search')) ? Request::get('search') : '') . '">' . PHP_EOL;
            $toolbar .= '<span class="input-group-btn"><button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button></span>' . PHP_EOL;
            $toolbar .= '</div></div>' . PHP_EOL;
		}

		$toolbar .= '</form>' . PHP_EOL;
        $toolbar .= '</div>' . PHP_EOL;
        $toolbar .= '<div class="col-sm-4 text-right"><div class="form-group">' . $actions . '</div></div>' . PHP_EOL;
		$toolbar .= '</div>' . PHP_EOL;

		return $toolbar;
	}


    /**
     * Generate a selection dropdown
     *
     * @param string $name Name of the select
     * @param array $options List of options
     * @param string $value Value of the option elements
     * @param string $text Text of the option elements
     * @param string $selected_value Selected value
     * @param string $placeholder Placeholder inside the select
     * @param string $class optional class
     * @param string $html Optional html
     * @return string
     */
    public static function select($name, $options, $value, $text, $selected_value, $placeholder = '', $class = '', $html = '') {
        $select  = '<select name="' . $name . '" id="' . $name . '" class="form-control ' . $class . '" ' . $html . '>' . PHP_EOL;

        if(!empty($placeholder)) {
            $select .= '<option value="0">' . $placeholder . '</option>'. PHP_EOL;
        }

        foreach($options as $option) {
            $selected = isset($option->{$value}) && $option->{$value} == $selected_value ? 'selected' : '';

            $select .= '<option value="' . (isset($option->{$value}) ? $option->{$value} : '') . '" ' . $selected . '>' . (isset($option->{$text}) ? $option->{$text} : '') . '</option>' . PHP_EOL;
        }

        $select .= '</select>' . PHP_EOL;

        return $select;
    }


    /**
     * Create a button for inside the toolbar
     *
     * @param string $type The action we are performing
     * @param string $route The route that needs to pe followed
     * @return string
     */
	public static function create_button($type, $route, $base_route) {
		switch($type) {
			case 'delete':
				$class = 'btn-danger';
				$text  = '<span class="glyphicon glyphicon-trash" aria-hidden"true"></span>';
				$action = Roles::DELETE;
				break;
			case 'add':
				$class = 'btn-info';
				$text  = '<span class="glyphicon glyphicon-plus" aria-hidden"true"></span>';
                $action = Roles::ADD;
                break;
			case 'save':
				$class = 'btn-info';
				$text  = '<span class="glyphicon glyphicon-floppy-disk" aria-hidden"true"></span>';
                $action = Roles::EDIT;
                break;
            case 'edit':
                $class = 'btn-default';
                $text  = '<span class="glyphicon glyphicon-pencil" aria-hidden"true"></span>';
                $action = Roles::EDIT;
                break;
            case 'print':
                $class = 'btn-default';
                $text  = '<span class="glyphicon glyphicon-print" aria-hidden"true"></span>';
                $action = Roles::VIEW;
                break;
            case 'import':
                $class = 'btn-default';
                $text  = '<span class="glyphicon glyphicon-import" aria-hidden"true"></span>';
                $action = Roles::ADD;
                break;
            case 'export':
                $class = 'btn-default';
                $text  = '<span class="glyphicon glyphicon-export" aria-hidden"true"></span>';
                $action = Roles::VIEW;
                break;
            case 'mail':
                $class = 'btn-default';
                $text  = '<span class="glyphicon glyphicon-envelope" aria-hidden"true"></span>';
                $action = Roles::VIEW;
                break;
            case 'preview':
                $class = 'btn-default';
                $text  = '<span class="glyphicon glyphicon-picture" aria-hidden"true"></span>';
                $action = Roles::VIEW;
                break;
            case 'recalculate':
                $class = 'btn-default';
                $text  = '<span class="glyphicon glyphicon-refresh" aria-hidden"true"></span> Opnieuw berekenen';
                $action = Roles::VIEW;
                break;
			default:
				$class = 'btn-default';
				$text  = $type;
                $action = Roles::VIEW;
                break;
		}

		// Don't display buttons if the user can't perform the action
		if(!User::is_allowed_to($action, $base_route) && !User::is_admin()) {
            return '';
        }

		if(empty($route)) {
			$route = '#';
		}

		return "<a href='" . $route . "' name='" . $type . "' class='btn " . $class . "'>" . $text . "</a>";
	}


    /**
     * Check if an item exists inside a recursive array
     *
     * @param $needle
     * @param $haystack
     * @param bool $strict
     * @return bool
     */
    public static function in_recursive_array($needle, $haystack, $strict = false) {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && self::in_recursive_array($needle, $item, $strict))) {
                return true;
            }
        }

        return false;
    }


    /**
     * Return a toggle switch
     *
     * @param string $name Name of the switch, unique
     * @param int $state The state of the switch
     * @param bool $yes_and_no Whether to use Yes and no, or On and off
     * @param string $html
     * @return string The switch
     */
    public static function toggle_switch($name, $state = 0, $yes_and_no = true, $html = '') {
        if($yes_and_no) {
            $label_on  = 'Ja';
            $label_off = 'Nee';
        } else {
            $label_on  = 'Aan';
            $label_off = 'Uit';
        }

        $switch  = '<input type="radio" name="' . $name . '" id="' . $name . '-on" value="1" hidden ' . ($state == 1 ? 'checked' : '') . ' ' . $html . '>' . PHP_EOL;
        $switch .= '<label for="' . $name . '-on" class="switch switch--on">' . $label_on . '</label>' . PHP_EOL;
        $switch .= '<input type="radio" name="' . $name . '" id="' . $name . '-off" value="0" hidden ' . ($state == 0 ? 'checked' : '') . ' ' . $html . '>' . PHP_EOL;
        $switch .= '<label for="' . $name . '-off" class="switch switch--off">' . $label_off . '</label>' . PHP_EOL;

        return $switch;
    }


    /**
     * Filter input values to remove id, token and save route
     *
     * @param $input
     * @param array $filter_keys additional filtering
     */
    public static function filter_input(&$input, $filter_keys = []) {
        unset($input['_token']);
        unset($input['id']);

        foreach($input as $key => $value) {
            if(substr($key, 0, 1) === '/' || in_array($key, $filter_keys)) {
                unset($input[$key]);
            }
        }
    }


    /**
     * Get the row number
     *
     * @param Eloquent $data
     * @param int $key
     * @return mixed
     */
    public static function row_number($data, $key) {
        return (($data->currentPage() - 1) * $data->perPage()) + $key + 1;
    }


    /**
     * Get pagination links
     *
     * @param $data
     * @return mixed
     */
    public static function pagination($data) {
        return $data->appends(Input::get())->links();
    }
}