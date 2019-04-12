<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use ArrayObject;
use InvalidArgumentException;
use SplObjectStorage;
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
     * @var SplObjectStorage Memoization for already filtered character trees.
     */
    private $filterMemoization;

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
        $this->filterMemoization = new SplObjectStorage();
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
        if (!isset($this->filterMemoization[$charTree])) {
            $this->filterMemoization[$charTree] = new ArrayObject();
        }

        $memoization1 = $this->filterMemoization[$charTree];
        if (!isset($memoization1[$minLength])) {
            $memoization1[$minLength] = new ArrayObject();
        }

        $memoization2 = $memoization1[$minLength];
        if (!isset($memoization2[$maxLength ?? ''])) {
            $memoization2[$maxLength ?? ''] = $this->filter($charTree, $minLength, $maxLength);
        }

        return $memoization2[$maxLength ?? ''];
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @param int $minLength Minimum string length.
     * @param int|null $maxLength Maximum string length.
     * @return CharTree Length filtered variant of the character tree. Memoization is not used.
     */
    private function filter(CharTree $charTree, int $minLength, ?int $maxLength): CharTree
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
        $branchMinLength = $minLength <= $rootLength ? 0 : $minLength-$rootLength;
        $branchMaxLength = $maxLength === null ? null : $maxLength-$rootLength;

        $filteredBranches = [];
        foreach ($branches as $branch) {
            if ($branch->getRoot() === null) {
                if ($minLength <= $rootLength) {
                    $filteredBranches[] = $branch;
                }
            } elseif ($rootLength < $maxLength) {
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
