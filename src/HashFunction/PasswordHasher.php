<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\HashFunction;

use ErrorException;
use RuntimeException;
use Stadly\PasswordPolice\HashFunction;

final class PasswordHasher implements HashFunction
{
    /**
     * @var int Algorithm.
     */
    private $algorithm;

    /**
     * @var array<string, string|int> Options.
     */
    private $options;

    /**
     * See http://php.net/manual/en/function.password-hash.php for details.
     *
     * @param int $algorithm Algorithm.
     * @param array<string, string|int> $options Options.
     */
    public function __construct(int $algorithm = PASSWORD_DEFAULT, array $options = [])
    {
        $this->algorithm = $algorithm;
        $this->options = $options;
    }

    public function hash(string $password): string
    {
        set_error_handler([self::class, 'errorHandler']);
        try {
            $hash = password_hash($password, $this->algorithm, $this->options);
        } catch (ErrorException $exception) {
            throw new RuntimeException(
                'An error occurred while hashing the password: ' . $exception->getMessage(),
                /*code*/0,
                $exception
            );
        } finally {
            restore_error_handler();
        }

        assert($hash !== false);

        return $hash;
    }

    public function compare(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    // phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedMethod
    /**
     * @throws ErrorException Error converted to an exception.
     */
    private static function errorHandler(int $severity, string $message, string $filename, int $line): bool
    {
        throw new ErrorException($message, /*code*/0, $severity, $filename, $line);
    }
    // phpcs:enable
}
