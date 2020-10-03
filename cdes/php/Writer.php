<?php

/**
 * Clays Project
 * 
 * @package Clays
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @license Apache 2
 */

namespace Apiles\Clays\ClaysDescriptor;

use Apiles\Clays\ClaysDescriptor\Model\Cluster;
use Apiles\Clays\ClaysDescriptor\Utils\SafeExplode;

class Writer
{
    /**
     * Tabs to use
     *
     * @var integer
     */
    protected $tabs = 4;

    /**
     * Constructor
     *
     * @param integer $tabs
     */
    public function __construct($tabs = 4)
    {
        $this->tabs = $tabs;
    }

    /**
     * Writer
     *
     * @param Cluster[] $root
     * @return string
     */
    public function write(array $root): string
    {
        $tabstr = str_repeat(' ', $this->tabs);
        $rslt = "clays.descriptor.version 1\r\n\r\n";
        foreach ($root as $cluster) {
            $rslt .= "cluster {$cluster->name}\r\n";
            if ($cluster->comment != '') {
                $rslt .= "{$tabstr}.comment {$cluster->comment}\r\n";
            }
            foreach ($cluster->groups as $group) {
                $rslt .= "{$tabstr}group {$group->name}\r\n";
                if ($group->comment != '') {
                    $rslt .= "{$tabstr}{$tabstr}.comment {$group->comment}\r\n";
                }
                foreach ($group->servers as $server) {
                    $rslt .= "{$tabstr}{$tabstr}server {$server->name} {$server->address}\r\n";
                    if ($server->comment != '') {
                        $rslt .= "{$tabstr}{$tabstr}{$tabstr}.comment {$server->comment}\r\n";
                    }
                    foreach ($server->properties as $k => $v) {
                        $value = '';
                        if (!is_array($v) && SafeExplode::isIdentify($v) && $v !== 'true' && $v !== 'false') {
                            $value = $v;
                        } else {
                            $value = json_encode($v);
                        }
                        $rslt .= "{$tabstr}{$tabstr}{$tabstr}$k $value\r\n";
                    }
                }
            }
            $rslt .= "\r\n";
        }
        $rslt = substr($rslt, 0, -2);
        return $rslt;
    }

    /**
     * Write to file
     *
     * @param string $filename
     * @param array $root
     * @return self
     */
    public function writeFile(string $filename, array $root): self
    {
        file_put_contents($filename, $this->write($root));
        return $this;
    }
}
