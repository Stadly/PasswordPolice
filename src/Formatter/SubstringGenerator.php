<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use ArrayObject;
use InvalidArgumentException;
use SplObjectStorage;
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
     * @var SplObjectStorage Memoization for already filtered character trees.
     */
    private $substringMemoization;

    /**
     * @var SplObjectStorage Memoization for already filtered character trees.
     */
    private $startsWithMemoization;

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
        $this->substringMemoization = new SplObjectStorage();
        $this->startsWithMemoization = new SplObjectStorage();
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
        if (!isset($this->substringMemoization[$charTree])) {
            $this->substringMemoization[$charTree] = new ArrayObject();
        }

        $memoization1 = $this->substringMemoization[$charTree];
        if (!isset($memoization1[$minLength])) {
            $memoization1[$minLength] = new ArrayObject();
        }

        $memoization2 = $memoization1[$minLength];
        if (!isset($memoization2[$maxLength ?? ''])) {
            $memoization2[$maxLength ?? ''] = $this->generate($charTree, $minLength, $maxLength);
        }

        return $memoization2[$maxLength ?? ''];
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @param int $minLength Minimum substring length.
     * @param int|null $maxLength Maximum substring length.
     * @return CharTree Substrings of the character tree. Memoization is not used.
     */
    private function generate(CharTree $charTree, int $minLength, ?int $maxLength): CharTree
    {
        $charTrees = [];

        $containsCharTree = $this->generateContains($charTree, $minLength, $maxLength);
        if ($containsCharTree->getRoot() !== null) {
            $charTrees[] = $containsCharTree;
        }

        $startsWithCharTree = $this->applyInternalStartsWith($charTree, $minLength, $maxLength);
        if ($startsWithCharTree->getRoot() !== null) {
            $charTrees[] = $startsWithCharTree;
        }

        return CharTree::fromArray($charTrees);
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @param int $minLength Minimum substring length.
     * @param int|null $maxLength Maximum substring length.
     * @return CharTree Substrings of the character tree not starting with root.
     */
    private function generateContains(CharTree $charTree, int $minLength, ?int $maxLength): CharTree
    {
        $root = $charTree->getRoot();

        if ($root === null) {
            return $charTree;
        }

        $branches = $charTree->getBranches();
        $substringBranches = [];
        if (0 < $maxLength || $maxLength === null) {
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
    private function applyInternalStartsWith(CharTree $charTree, int $minLength, ?int $maxLength): CharTree
    {
        if (!isset($this->startsWithMemoization[$charTree])) {
            $this->startsWithMemoization[$charTree] = new ArrayObject();
        }

        $memoization1 = $this->startsWithMemoization[$charTree];
        if (!isset($memoization1[$minLength])) {
            $memoization1[$minLength] = new ArrayObject();
        }

        $memoization2 = $memoization1[$minLength];
        if (!isset($memoization2[$maxLength ?? ''])) {
            $memoization2[$maxLength ?? ''] = $this->generateStartsWith($charTree, $minLength, $maxLength);
        }

        return $memoization2[$maxLength ?? ''];
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @param int $minLength Minimum substring length.
     * @param int|null $maxLength Maximum substring length.
     * @return CharTree Substrings of the character tree starting with root. Memoization is not used.
     */
    private function generateStartsWith(CharTree $charTree, int $minLength, ?int $maxLength): CharTree
    {
        $root = $charTree->getRoot();

        if ($root === null) {
            return $charTree;
        }

        $rootLength = mb_strlen($root);
        if ($rootLength <= $maxLength || $maxLength === null) {
            $branches = $charTree->getBranches();
            $branchMinLength = $minLength <= $rootLength ? 0 : $minLength-$rootLength;
            $branchMaxLength = $maxLength === null ? null : $maxLength-$rootLength;

            $substringBranches = [];
            foreach ($branches as $branch) {
                $substringBranch = $this->applyInternalStartsWith($branch, $branchMinLength, $branchMaxLength);
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
