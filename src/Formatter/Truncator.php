<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use InvalidArgumentException;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

final class Truncator implements Formatter
{
    use Chaining;

    /**
     * @var int Maximum string length.
     */
    private $length;

    /**
     * @var CharTree[] Memoization of formatted character trees.
     */
    private static $memoization = [];

    /**
     * @param int $length Maximum string length.
     */
    public function __construct(int $length)
    {
        if ($length < 0) {
            throw new InvalidArgumentException('Length must be non-negative.');
        }

        $this->length = $length;
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @return CharTree Truncated variant of the character tree.
     */
    protected function applyCurrent(CharTree $charTree): CharTree
    {
        return $this->applyInternal($charTree, $this->length);
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @param int $length Maximum string length.
     * @return CharTree Truncated variant of the character tree.
     */
    private function applyInternal(CharTree $charTree, int $length): CharTree
    {
        // When PHP 7.1 is no longer supported, change to using spl_object_id.
        $hash = spl_object_hash($charTree).';'.$length;

        if (!isset(self::$memoization[$hash])) {
            self::$memoization[$hash] = $this->truncate($charTree, $length);
        }

        return self::$memoization[$hash];
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @param int $length Maximum string length.
     * @return CharTree Truncated variant of the character tree. Memoization is not used.
     */
    private function truncate(CharTree $charTree, int $length): CharTree
    {
        $root = $charTree->getRoot();

        if ($root === null) {
            return $charTree;
        }

        $rootLength = mb_strlen($root);
        $branchLength = $length-$rootLength;

        if ($branchLength < 0) {
            return CharTree::fromString('');
        }

        $truncatedBranches = [];
        foreach ($charTree->getBranches() as $branch) {
            $truncatedBranches[] = $this->applyInternal($branch, $branchLength);
        }

        return CharTree::fromString($root, $truncatedBranches);
    }
}
