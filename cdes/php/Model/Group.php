<?php

/**
 * Clays Project
 * 
 * @package Clays
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @license Apache 2
 */

namespace Apiles\Clays\ClaysDescriptor\Model;

class Group implements Node
{
    /**
     * Name
     *
     * @var string
     */
    public $name = '';

    /**
     * Servers
     *
     * @var Server[]
     */
    public $servers = [];

    /**
     * Comment
     *
     * @var string
     */
    public $comment = '';
}
