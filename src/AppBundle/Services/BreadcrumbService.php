<?php
/**
 * Created by PhpStorm.
 * User: ovsiichuk
 * Date: 28.12.18
 * Time: 18:04
 */

namespace AppBundle\Services;

/**
 * Class BreadcrumbService
 */
class BreadcrumbService
{
    /**
     * @var array $breadcrumb
     */
    private $breadcrumb;

    /**
     * BreadcrumbService constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param array $array
     *
     * @return $this
     */
    public function addBreadcrumb($array)
    {
        $this->breadcrumb[] = $array;

        return $this;
    }

    /**
     * @return array
     */
    public function getBreadcrumb()
    {
        return $this->breadcrumb;
    }
}