<?php

/**
 * Clays Project
 * 
 * @package Clays
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @license Apache 2
 */

namespace Apiles\Clays\ClaysDescriptor;

use Apiles\Clays\ClaysDescriptor\Model\Node;
use Apiles\Clays\ClaysDescriptor\Model\Group;
use Apiles\Clays\ClaysDescriptor\Model\Server;
use Apiles\Clays\ClaysDescriptor\Model\Cluster;
use Apiles\Clays\ClaysDescriptor\Utils\BlankCount;
use Apiles\Clays\ClaysDescriptor\Utils\SafeExplode;
use Apiles\Clays\ClaysDescriptor\Utils\SyntaxErrorException;
use Apiles\Clays\ClaysDescriptor\Utils\UnmatchedBracketsException;
use Apiles\Clays\ClaysDescriptor\Utils\UnsupportedVersionException;

/**
 * Clays Descriptor Format (*.cdes) Parser
 */
class Parser
{
    /**
     * Blank Counter
     *
     * @var BlankCount
     */
    protected $counter;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        $this->counter = new BlankCount;
    }

    /**
     * Parse the code
     *
     * @param string $code
     * @return Node[]
     */
    public function parse(string $code): array
    {
        $this->counter->initialize();
        /**
         * @var Cluster[]
         */
        $rslt = [];

        $lines = explode("\n", $code);
        $currCluster = '';
        $currGroup = '';
        $currServer = '';

        foreach ($lines as $n => $line) {
            $num = $n + 1;
            if (SafeExplode::isEmptyLine($line)) continue;
            try {
                $level = $this->counter->feed($line);
                $tokenized = SafeExplode::explode('', $line);
            } catch (UnmatchedBracketsException $e) {
                throw new UnmatchedBracketsException($e . " on line $num");
            }
            if ($level != 4) {
                foreach ($tokenized as &$v) {
                    if (substr(trim($v), 0, 1) == '"') {
                        $v = json_decode($v);
                    }
                }
            }
            // DEBUG
            // echo "[$num]\t (level=$level) \t $line\r\n";
            switch ($level) {
                case 1:
                    if ($tokenized[0] == 'clays.descriptor.version') {
                        if (!isset($tokenized[1])) {
                            throw new SyntaxErrorException("Syntax error, expected 1 argument on line $num");
                        }
                        $version = (int) $tokenized[1];
                        if ($version !== CDES_FORMAT_VERSION) {
                            throw new UnsupportedVersionException("Unsupported version $version on line $num");
                        }
                    } elseif ($tokenized[0] == 'cluster') {
                        if (!isset($tokenized[1])) {
                            throw new SyntaxErrorException("Syntax error, expected name on line $num");
                        }
                        $currCluster = $tokenized[1];
                        $currGroup = '';
                        $currServer = '';
                        $rslt[$currCluster] = new Cluster();
                        $rslt[$currCluster]->name = $tokenized[1];
                    } else {
                        throw new SyntaxErrorException("Unrecognized command $tokenized[0] on line $num");
                    }
                    break;
                case 2:
                    if ($currCluster == '') {
                        throw new SyntaxErrorException("Syntax error, unexpected line on line $num");
                    }
                    if ($tokenized[0] == '.comment') {
                        $rslt[$currCluster]->comment .= trim(substr(trim($line), 8));
                    } elseif ($tokenized[0] == 'group') {
                        if (!isset($tokenized[1])) {
                            throw new SyntaxErrorException("Syntax error, expected 1 argument on line $num");
                        }
                        $currGroup = $tokenized[1];
                        $currServer = '';
                        $rslt[$currCluster]->groups[$currGroup] = new Group;
                        $rslt[$currCluster]->groups[$currGroup]->name = $currGroup;
                    } else {
                        throw new SyntaxErrorException("Unrecognized command $tokenized[0] on line $num");
                    }
                    break;
                case 3:
                    if ($currGroup == '') {
                        throw new SyntaxErrorException("Syntax error, unexpected line on line $num");
                    }
                    if ($tokenized[0] == '.comment') {
                        $rslt[$currCluster]->groups[$currGroup]->comment .= trim(substr(trim($line), 8));
                    } elseif ($tokenized[0] == 'server') {
                        if (!isset($tokenized[1]) || !isset($tokenized[2])) {
                            throw new SyntaxErrorException("Syntax error, expected 2 argument on line $num");
                        }
                        $currServer = $tokenized[1];
                        $rslt[$currCluster]->groups[$currGroup]->servers[$currServer] = new Server;
                        $rslt[$currCluster]->groups[$currGroup]->servers[$currServer]->name = $currServer;
                        $rslt[$currCluster]->groups[$currGroup]->servers[$currServer]->address = trim($tokenized[2]);
                    } else {
                        throw new SyntaxErrorException("Unrecognized command $tokenized[0] on line $num");
                    }
                    break;
                case 4:
                    if ($currServer == '') {
                        throw new SyntaxErrorException("Syntax error, unexpected line on line $num");
                    }
                    if ($tokenized[0] == '.comment') {
                        $rslt[$currCluster]->groups[$currGroup]->servers[$currServer]->comment .= trim(substr(trim($line), 8));
                    } else {
                        $val = trim(substr(trim($line), strlen($tokenized[0])));
                        if (!is_numeric($val) && !($val === 'true') && !($val === 'false') && substr($val, 0, 1) != '[' && substr($val, 0, 1) != '{' && substr($val, 0, 1) != '"') {
                            $val = json_encode($val);
                        }
                        $rslt[$currCluster]->groups[$currGroup]->servers[$currServer]->properties[$tokenized[0]] = json_decode($val, true);
                        if ($rslt[$currCluster]->groups[$currGroup]->servers[$currServer]->properties[$tokenized[0]] === null) {
                            throw new SyntaxErrorException("JSON Syntax Error on line $num");
                        }
                    }
                    break;
                default:
                    throw new SyntaxErrorException("Unexpected line on line $num");
            }
        }

        return $rslt;
    }

    /**
     * Parse a file
     *
     * @param string $filename
     * @return Node[]
     */
    public function parseFile(string $filename): array
    {
        return $this->parse(file_get_contents($filename));
    }
}
