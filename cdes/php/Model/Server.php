<?php

/**
 * Clays Project
 * 
 * @package Clays
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @license Apache 2
 */

namespace Apiles\Clays\ClaysDescriptor\Model;

class Server
{
    /**
     * Name
     *
     * @var string
     */
    public $name = '';

    /**
     * Address
     *
     * @var string
     */
    public $address = '';

    /**
     * Properties
     *
     * @var string[]
     */
    public $properties = [];

    /**
     * Comment
     *
     * @var string
     */
    public $comment = '';
}
