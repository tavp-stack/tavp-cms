<?php

declare(strict_types=1);

namespace Tavp\Cms\Content;

/**
 * Thrown when BREAD validation fails.
 */
class ValidationException extends \RuntimeException
{
    /** @var array<string,string[]> field => error messages */
    private array $errors;

    /**
     * @param array<string,string[]> $errors
     */
    public function __construct(array $errors)
    {
        parent::__throwable($this);
        $this->errors = $errors;
    }

    /**
     * @return array<string,string[]>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Flat array of all error messages.
     *
     * @return string[]
     */
    public function all(): array
    {
        return array_merge(...array_values($this->errors));
    }
}
