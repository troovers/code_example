<?php

namespace App\Model\Helpers;

class Ordering
{

    /**
     * Create a table column sorting link
     *
     * @param string $label Label of the column header
     * @param string $column Identifier of the table column
     * @param array $filters Array of filters
     *
     * @return string
     */
	public static function create_label($label, $column, $filters) {
	    $url = $filters->path . '?page=' . $filters->page . '&column=' . $column . '&dir=' . ($filters->column == $column ? ($filters->direction == 'asc' ? 'desc' : 'asc') : 'asc');

	    echo '<a class="column-sortable" href="' . $url . '">' . $label . ' <span class="glyphicon glyphicon-triangle-'. ($filters->column == $column ? ($filters->direction == 'asc' ? 'top' : 'bottom') : 'top') . '"></span></a>';
    }
}