<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\HashFunction;

use ErrorException;
use RuntimeException;
use Stadly\PasswordPolice\HashFunction;

final class PasswordHash implements HashFunction
{
    /**
     * @var int Algorithm.
     */
    private $algorithm;

    /**
     * @var array Options.
     */
    private $options;

    /**
     * See http://php.net/manual/en/function.password-hash.php for details.
     *
     * @param int $algorithm Algorithm.
     * @param array $options Options.
     */
    public function __construct(int $algorithm = PASSWORD_DEFAULT, array $options = [])
    {
        $this->algorithm = $algorithm;
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function hash(string $password): string
    {
        set_error_handler([self::class, 'errorHandler']);
        try {
            $hash = password_hash($password, $this->algorithm, $this->options);
        } catch (ErrorException $exception) {
            throw new RuntimeException(
                'An error occurred while hashing the password: '.$exception->getMessage(),
                /*code*/0,
                $exception
            );
        } finally {
            restore_error_handler();
        }

        assert($hash !== false);

        return $hash;
    }

    /**
     * {@inheritDoc}
     */
    public function compare(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * @throws ErrorException Error converted to an exception.
     */
    private static function errorHandler(int $severity, string $message, string $filename, int $line): void
    {
        throw new ErrorException($message, /*code*/0, $severity, $filename, $line);
    }
}
