<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use InvalidArgumentException;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

final class LengthFilter implements Formatter
{
    use Chaining;

    /**
     * @var int Minimum string length.
     */
    private $minLength;

    /**
     * @var int|null Maximum string length.
     */
    private $maxLength;

    /**
     * @var array<CharTree> Memoization of formatted character trees.
     */
    private static $memoization = [];

    /**
     * @param int $minLength Minimum string length.
     * @param int|null $maxLength Maximum string length.
     */
    public function __construct(int $minLength = 0, ?int $maxLength = null)
    {
        if ($minLength < 0) {
            throw new InvalidArgumentException('Min length must be non-negative.');
        }
        if ($maxLength !== null && $maxLength < $minLength) {
            throw new InvalidArgumentException('Max length cannot be smaller than min length.');
        }

        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @return CharTree Length filtered variant of the character tree.
     */
    protected function applyCurrent(CharTree $charTree): CharTree
    {
        return $this->applyInternal($charTree, $this->minLength, $this->maxLength);
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @param int $minLength Minimum string length.
     * @param int|null $maxLength Maximum string length.
     * @return CharTree Length filtered variant of the character tree.
     */
    private function applyInternal(CharTree $charTree, int $minLength, ?int $maxLength): CharTree
    {
        // When PHP 7.1 is no longer supported, change to using spl_object_id.
        $hash = spl_object_hash($charTree) . ';' . $minLength . ';' . $maxLength;

        if (!isset(self::$memoization[$hash])) {
            self::$memoization[$hash] = $this->format($charTree, $minLength, $maxLength);
        }

        return self::$memoization[$hash];
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @param int $minLength Minimum string length.
     * @param int|null $maxLength Maximum string length.
     * @return CharTree Length filtered variant of the character tree. Memoization is not used.
     */
    private function format(CharTree $charTree, int $minLength, ?int $maxLength): CharTree
    {
        $root = $charTree->getRoot();

        if ($root === null || ($minLength === 0 && $maxLength === null)) {
            return $charTree;
        }

        $branches = $charTree->getBranches();

        if ($maxLength === 0) {
            if ($root === '' && ($branches === [] || isset($branches[null]))) {
                return CharTree::fromString('');
            }
            return CharTree::fromNothing();
        }

        $rootLength = mb_strlen($root);
        $branchMinLength = $minLength <= $rootLength ? 0 : $minLength - $rootLength;
        $branchMaxLength = $maxLength === null ? null : $maxLength - $rootLength;

        $filteredBranches = [];
        foreach ($branches as $branch) {
            if ($branch->getRoot() === null) {
                if ($branchMinLength === 0) {
                    $filteredBranches[] = $branch;
                }
            } elseif ($branchMaxLength === null || 0 < $branchMaxLength) {
                $filteredBranch = $this->applyInternal($branch, $branchMinLength, $branchMaxLength);
                if ($filteredBranch->getRoot() !== null) {
                    $filteredBranches[] = $filteredBranch;
                }
            }
        }

        if ($filteredBranches !== [] || ($minLength <= $rootLength && $branches === [])) {
            return CharTree::fromString($root, $filteredBranches);
        }

        return CharTree::fromNothing();
    }
}
