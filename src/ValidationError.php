<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

final class ValidationError
{
    /**
     * @var string Message describing why the password could not be validated.
     */
    private $message;

    /**
     * @var Password|string $password Password that could not be validated.
     */
    private $password;

    /**
     * @var Rule Rule that the password is not in compliance with.
     */
    private $rule;

    /**
     * @var int Weight of violated constraint.
     */
    private $weight;

    /**
     * @param string $message Message describing why the password could not be validated.
     * @param Password|string $password Password that could not be validated.
     * @param Rule $rule Rule that the password is not in compliance with.
     * @param int $weight Weight of violated constraint.
     */
    public function __construct(string $message, $password, Rule $rule, int $weight)
    {
        $this->message = $message;
        $this->password = $password;
        $this->rule = $rule;
        $this->weight = $weight;
    }

    /**
     * @return string Message describing why the password could not be validated.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return Password|string $password Password that could not be validated.
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return Rule Rule that the password is not in compliance with.
     */
    public function getRule(): Rule
    {
        return $this->rule;
    }

    /**
     * @return int Weight of violated constraint.
     */
    public function getWeight(): int
    {
        return $this->weight;
    }
}
