<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

final class Password
{
    /**
     * @var string Password.
     */
    private $password;

    /**
     * @param string $password Password.
     */
    public function __construct(string $password)
    {
        $this->password = $password;
    }

    /**
     * @return string Password.
     */
    public function __toString(): string
    {
        return $this->password;
    }

    /**
     * @return string Password.
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
