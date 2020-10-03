<?php

/**
 * Clays Project
 * 
 * @package Clays
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @license Apache 2
 */

namespace Apiles\Clays\ClaysDescriptor\Model;

class Cluster implements Node
{
    /**
     * Name
     *
     * @var string
     */
    public $name = '';

    /**
     * Groups
     *
     * @var Group[]
     */
    public $groups = [];

    /**
     * Comment
     *
     * @var string
     */
    public $comment = '';
}
