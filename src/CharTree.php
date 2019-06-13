<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use IteratorAggregate;
use Traversable;

final class CharTree implements IteratorAggregate
{
    /**
     * @var string|null Root of the character tree. No more than 1 character long.
     */
    private $root;

    /**
     * @var array<self> Branches of the character tree.
     */
    private $branches = [];

    /**
     * @var array<bool> Memoization of startsWith().
     */
    private $startsWithMemoization = [];

    /**
     * @var array<bool> Memoization of contains().
     */
    private $containsMemoization = [];

    /**
     * @var array<CharTree> Memoization of constructed character trees.
     */
    private static $constructMemoization = [];

    /**
     * @param string|null $root Root of the character tree. No more than 1 character long.
     * @param array<self> $branches Branches of the character tree.
     */
    private function __construct(?string $root, array $branches)
    {
        assert($root !== null || $branches === [], 'Empty tree cannot have branches.');
        assert($root === null || mb_strlen($root) <= 1, 'Root must contain at most one character.');

        $this->root = $root;
        $this->branches = $branches;
    }

    /**
     * @param string|null $root Root of the character tree. No more than 1 character long.
     * @param array<self> $branches Branches of the character tree.
     */
    private static function construct(?string $root, array $branches): self
    {
        if ($root === null) {
            assert($branches === [], 'Empty tree cannot have branches.');
            if (!isset(self::$constructMemoization['null'])) {
                self::$constructMemoization['null'] = new self($root, $branches);
            }
            return self::$constructMemoization['null'];
        }

        assert(mb_strlen($root) <= 1, 'Root must contain at most one character.');

        $hash = $root;
        ksort($branches, SORT_STRING);
        foreach ($branches as $branch) {
            // When PHP 7.1 is no longer supported, change to using spl_object_id.
            $branchHash = spl_object_hash($branch);
            $hash .= ';' . $branchHash;
        }

        if (!isset(self::$constructMemoization[$hash])) {
            self::$constructMemoization[$hash] = new self($root, $branches);
        }

        return self::$constructMemoization[$hash];
    }

    /**
     * @param string $string Root of the character tree.
     * @param array<self> $branches Branches of the character tree.
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
            $branch = reset($branches);
            if ($branch->root === null) {
                // If only one branch and its root is null, throw it away.
                $branches = [];
            } elseif ($string === '') {
                // If root is empty string and only one branch, use it.
                return $branch;
            }
        }

        return self::construct($string, $branches);
    }

    /**
     * @param array<self> $charTrees Character trees to combine.
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
        return self::construct(null, []);
    }

    /**
     * @param array<self> $charTrees Character trees to normalize.
     * @return array<self> Character trees where branches of trees with empty string roots have been promoted to trees.
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
     * @param array<self> $charTrees Character trees to normalize.
     * @return array<self> Character trees where branches of trees with the same root have been combined.
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
     * @return array<self> Branches of the character tree.
     */
    public function getBranches(): array
    {
        return $this->branches;
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
                        yield $this->root . $string;
                    }
                }
            }

            if ($this->branches === []) {
                yield $this->root;
            }
        }
    }
}
