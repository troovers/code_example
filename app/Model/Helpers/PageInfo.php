<?php

namespace App\Model\Helpers;

use Illuminate\Http\Request;
use stdClass;

class PageInfo
{
    /** @var string The path of the current page */
    private $path = '';

    /** @var string Column identifier for ordering */
    private $column = '';

    /** @var string Direction in which we want to order */
    private $direction = '';

    /** @var int Page number we are on  */
    private $page = 0;


    public function __construct(Request $request, $default_column)
    {
        $this->path      = $request->get('path_info');
        $this->column    = $request->get('column', $default_column);
        $this->direction = $request->get('dir', 'asc');
        $this->page      = $request->get('page', '1');
    }


    /**
     * Get the filters for the current page
     *
     * @return stdClass
     */
    public function get_filters() {
        $filters = new stdClass();

        $filters->path = $this->path;
        $filters->column = $this->column;
        $filters->direction = $this->direction;
        $filters->page = $this->page;

        return $filters;
    }
}