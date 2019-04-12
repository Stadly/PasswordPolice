<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

final class CharTree implements IteratorAggregate
{
    /**
     * @var string|null
     */
    private $root;

    /**
     * @var self[]
     */
    private $branches = [];

    /**
     * @var bool[]
     */
    private $startsWithMemoization = [];

    /**
     * @var bool[]
     */
    private $containsMemoization = [];

    /**
     * @param string|null $root Root of the character tree. No more than 1 character long.
     * @param self[] $branches Branches of the character tree.
     */
    private function __construct(?string $root, array $branches)
    {
        assert($root === null || mb_strlen($root) <= 1, 'Root must contain at most one character.');

        $this->root = $root;
        $this->branches = $branches;
    }

    /**
     * @param string $string Root of the character tree.
     * @param self[] $branches Branches of the character tree.
     */
    public static function fromString(string $string, array $branches = []): self
    {
        if (1 < mb_strlen($string)) {
            // Maximum one character in root.
            $branches = [self::fromString(mb_substr($string, 1), $branches)];
            $string = mb_substr($string, 0, 1);
        }

        $branches = self::promoteEmptyRoots($branches);
        $branches = self::combineDuplicateRoots($branches);

        if (count($branches) === 1) {
            if ($string === '') {
                // If root is empty and only one branch, use it.
                return reset($branches);
            }
            if (reset($branches)->root === null) {
                // If only one branch and its root is null, throw it away.
                $branches = [];
            }
        }

        return new self($string, $branches);
    }

    /**
     * @param self[] $charTrees Character trees to combine.
     */
    public static function fromArray(array $charTrees): self
    {
        foreach ($charTrees as $charTree) {
            if ($charTree->root !== null) {
                return self::fromString('', $charTrees);
            }
        }

        return self::fromNothing();
    }

    /**
     * Construct empty character tree.
     */
    public static function fromNothing(): self
    {
        return new self(null, []);
    }

    /**
     * @param self[] $charTrees Character trees to normalize.
     * @return self[] Character trees where branches of trees with empty string roots have been promoted to trees.
     */
    private static function promoteEmptyRoots(array $charTrees): array
    {
        $normalized = [];
        foreach ($charTrees as $charTree) {
            if ($charTree->root === '') {
                if ($charTree->branches !== []) {
                    $normalized = array_merge($normalized, array_values($charTree->branches));
                } else {
                    $normalized[] = self::fromNothing();
                }
            } else {
                $normalized[] = $charTree;
            }
        }

        return $normalized;
    }

    /**
     * @param self[] $charTrees Character trees to normalize.
     * @return self[] Character trees where branches of trees with the same root have been combined.
     */
    private static function combineDuplicateRoots(array $charTrees): array
    {
        $normalized = [];
        foreach ($charTrees as $charTree) {
            if (isset($normalized[$charTree->root]) && $charTree->root !== null) {
                $normalizedCharTree = $normalized[$charTree->root];
                $normalizedBranches = array_values($normalizedCharTree->branches);
                if ($normalizedBranches === []) {
                    $normalizedBranches[] = self::fromNothing();
                }
                $branches = array_values($charTree->branches);
                if ($branches === []) {
                    $branches[] = self::fromNothing();
                }
                $charTree = self::fromString(
                    $charTree->root,
                    array_merge($normalizedBranches, $branches)
                );
            }
            $normalized[$charTree->root] = $charTree;
        }

        return $normalized;
    }

    /**
     * @return string|null Root of the character tree.
     */
    public function getRoot(): ?string
    {
        return $this->root;
    }

    /**
     * @return self[] Branches of the character tree.
     */
    public function getBranches(): array
    {
        return $this->branches;
    }

    /**
     * @param int $length Length of each string in the character tree.
     * @return self Character tree containing the $length first characters of this character tree.
     */
    public function getTreeTrimmedToLength(int $length): self
    {
        if ($length < 0) {
            throw new InvalidArgumentException('Length must be non-negative.');
        }

        if ($this->root === null) {
            return $this;
        }

        if ($length === 0) {
            return self::fromString('');
        }

        $branches = [];
        $rootLength = mb_strlen($this->root);
        if ($rootLength < $length) {
            $branchLength = $length-$rootLength;
            foreach ($this->branches as $branch) {
                $branchTree = $branch->getTreeTrimmedToLength($branchLength);
                if ($branchTree->root !== null) {
                    $branches[] = $branchTree;
                }
            }

            if ($branches === []) {
                return self::fromNothing();
            }
        }

        return self::fromString($this->root, $branches);
    }

    /**
     * @param string $root Root that comes before the brances.
     * @return self Character tree containing the branches of this character tree that come after $root.
     */
    public function getBranchesAfterRoot(string $root): self
    {
        if ($this->root === null || $root === '') {
            return $this;
        }

        if ($this->root === $root) {
            return self::fromString('', $this->branches);
        }

        if ($this->root !== mb_substr($root, 0, mb_strlen($this->root))) {
            return self::fromNothing();
        }

        $rootTail = mb_substr($root, mb_strlen($this->root));
        $rootHead = mb_substr($rootTail, 0, 1);

        if (!isset($this->branches[$rootHead])) {
            return self::fromNothing();
        }

        return $this->branches[$rootHead]->getBranchesAfterRoot($rootTail);
    }

    /**
     * @param string $string String to check.
     * @return bool Whether the character tree contains the string.
     */
    public function contains(string $string): bool
    {
        if (!isset($this->containsMemoization[$string])) {
            $this->containsMemoization[$string] = $this->calculateContains($string);
        }

        return $this->containsMemoization[$string];
    }

    /**
     * @param string $string String to check.
     * @return bool Whether the character tree starts with the string.
     */
    public function startsWith(string $string): bool
    {
        if (!isset($this->startsWithMemoization[$string])) {
            $this->startsWithMemoization[$string] = $this->calculateStartsWith($string);
        }

        return $this->startsWithMemoization[$string];
    }

    /**
     * @param string $string String to check.
     * @return bool Whether the character tree contains the string. Memoization is not used.
     */
    private function calculateContains(string $string): bool
    {
        if ($this->startsWith($string)) {
            return true;
        }

        foreach ($this->branches as $branch) {
            if ($branch->contains($string)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $string String to check.
     * @return bool Whether the character tree starts with the string. Memoization is not used.
     */
    private function calculateStartsWith(string $string): bool
    {
        if ($this->root !== null && $this->root === mb_substr($string, 0, mb_strlen($this->root))) {
            $stringTail = mb_substr($string, mb_strlen($this->root));
            if ($stringTail === '') {
                return true;
            } else {
                foreach ($this->branches as $branch) {
                    if ($branch->startsWith($stringTail)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @return Traversable<string> All strings in the character tree.
     */
    public function getIterator(): Traversable
    {
        if ($this->root !== null) {
            foreach ($this->branches as $branch) {
                if ($branch->root === null) {
                    yield $this->root;
                } else {
                    foreach ($branch as $string) {
                        yield $this->root.$string;
                    }
                }
            }

            if ($this->branches === []) {
                yield $this->root;
            }
        }
    }
}
