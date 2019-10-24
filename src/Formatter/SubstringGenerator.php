<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use InvalidArgumentException;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

final class SubstringGenerator implements Formatter
{
    use Chaining;

    /**
     * @var int Minimum substring length.
     */
    private $minLength;

    /**
     * @var int|null Maximum substring length.
     */
    private $maxLength;

    /**
     * @var array<CharTree> Memoization of formatted character trees.
     */
    private static $memoization = [];

    /**
     * @var array<CharTree> Memoization of intermediate results of formatted character trees.
     */
    private static $startsWithMemoization = [];

    /**
     * @param int $minLength Ignore substrings shorter than this.
     * @param int|null $maxLength Ignore substrings longer than this.
     */
    public function __construct(int $minLength = 3, ?int $maxLength = 25)
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
     * @return CharTree Substrings of the character tree.
     */
    protected function applyCurrent(CharTree $charTree): CharTree
    {
        return $this->applyInternal($charTree, $this->minLength, $this->maxLength);
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @param int $minLength Minimum substring length.
     * @param int|null $maxLength Maximum substring length.
     * @return CharTree Substrings of the character tree.
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
     * @param int $minLength Minimum substring length.
     * @param int|null $maxLength Maximum substring length.
     * @return CharTree Substrings of the character tree. Memoization is not used.
     */
    private function format(CharTree $charTree, int $minLength, ?int $maxLength): CharTree
    {
        $charTrees = [];

        $notStartingWithRoot = $this->generateNotStartingWithRoot($charTree, $minLength, $maxLength);
        if ($notStartingWithRoot->getRoot() !== null) {
            $charTrees[] = $notStartingWithRoot;
        }

        $startingWithRoot = $this->applyInternalStartingWithRoot($charTree, $minLength, $maxLength);
        if ($startingWithRoot->getRoot() !== null) {
            $charTrees[] = $startingWithRoot;
        }

        return CharTree::fromArray($charTrees);
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @param int $minLength Minimum substring length.
     * @param int|null $maxLength Maximum substring length.
     * @return CharTree Substrings of the character tree not starting with root.
     */
    private function generateNotStartingWithRoot(CharTree $charTree, int $minLength, ?int $maxLength): CharTree
    {
        $root = $charTree->getRoot();

        if ($root === null) {
            return $charTree;
        }

        $branches = $charTree->getBranches();
        $substringBranches = [];
        if ($maxLength > 0 || $maxLength === null) {
            foreach ($branches as $branch) {
                $substringBranch = $this->applyInternal($branch, $minLength, $maxLength);
                if ($substringBranch->getRoot() !== null) {
                    $substringBranches[] = $substringBranch;
                }
            }
        }

        if ($substringBranches !== [] || $minLength === 0) {
            if ($substringBranches !== [] && $minLength === 0) {
                $substringBranches[] = CharTree::fromNothing();
            }
            return CharTree::fromString('', $substringBranches);
        }

        return CharTree::fromNothing();
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @param int $minLength Minimum substring length.
     * @param int|null $maxLength Maximum substring length.
     * @return CharTree Substrings of the character tree starting with root.
     */
    private function applyInternalStartingWithRoot(CharTree $charTree, int $minLength, ?int $maxLength): CharTree
    {
        // When PHP 7.1 is no longer supported, change to using spl_object_id.
        $hash = spl_object_hash($charTree) . ';' . $minLength . ';' . $maxLength;

        if (!isset(self::$startsWithMemoization[$hash])) {
            self::$startsWithMemoization[$hash] = $this->generateStartingWithRoot($charTree, $minLength, $maxLength);
        }

        return self::$startsWithMemoization[$hash];
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @param int $minLength Minimum substring length.
     * @param int|null $maxLength Maximum substring length.
     * @return CharTree Substrings of the character tree starting with root. Memoization is not used.
     */
    private function generateStartingWithRoot(CharTree $charTree, int $minLength, ?int $maxLength): CharTree
    {
        $root = $charTree->getRoot();

        if ($root === null) {
            return $charTree;
        }

        $rootLength = mb_strlen($root);
        if ($rootLength <= $maxLength || $maxLength === null) {
            $branches = $charTree->getBranches();
            $branchMinLength = $minLength <= $rootLength ? 0 : $minLength - $rootLength;
            $branchMaxLength = $maxLength === null ? null : $maxLength - $rootLength;

            $substringBranches = [];
            foreach ($branches as $branch) {
                $substringBranch = $this->applyInternalStartingWithRoot($branch, $branchMinLength, $branchMaxLength);
                if ($substringBranch->getRoot() !== null) {
                    $substringBranches[] = $substringBranch;
                }
            }

            if ($substringBranches !== [] || $minLength <= $rootLength) {
                if ($substringBranches !== [] && $minLength <= $rootLength) {
                    $substringBranches[] = CharTree::fromNothing();
                }
                return CharTree::fromString($root, $substringBranches);
            }
        }

        return CharTree::fromNothing();
    }
}
