<?php

/**
 * Clays Project
 * 
 * @package Clays
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @license Apache 2
 */

namespace Apiles\Clays\ClaysDescriptor\Utils;

use SplStack;

class BlankCount
{
    /**
     * Count stack
     *
     * @var SplStack
     */
    protected $stack;

    /**
     * Current level
     *
     * @var integer
     */
    protected $level = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Initialize the instance
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->stack = new SplStack;
        $this->stack->push(0);
        $this->level = 1;
    }

    /**
     * Feed a line
     *
     * @param string $line
     * @return integer current level
     */
    public function feed(string $line): int
    {
        $cnt = self::countString($line);

        if ($cnt > $this->stack->top()) {
            $this->stack->push($cnt);
            $this->level++;
        } elseif ($cnt < $this->stack->top()) {
            $this->stack->pop();
            $curr = $this->stack->pop();
            $this->level--;
            while ($cnt != $curr) {
                if ($cnt > $curr) {
                    throw new UnmatchedBracketsException("Blanks not matched");
                }
                $this->level--;
                $curr = $this->stack->pop();
            }
            $this->stack->push($curr);
        }

        return $this->getLevel();
    }

    /**
     * Get current level
     *
     * @return integer
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * Get current blank count
     *
     * @return integer
     */
    public function getCurrentCount(): int
    {
        return $this->stack->top();
    }

    /**
     * Count a string
     *
     * @param string $str
     * @return integer
     */
    public static function countString(string $str): int
    {
        $arr = str_split($str);
        $k = 0;
        $len = count($arr);
        $cnt = 0;
        while ($arr[$k] && $k < $len) {
            if (!SafeExplode::isBlankCharacter($arr[$k])) break;
            $cnt++;
            ++$k;
        }
        return $cnt;
    }
}
