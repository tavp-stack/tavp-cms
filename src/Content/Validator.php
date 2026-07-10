<?php

declare(strict_types=1);

namespace Tavp\Cms\Content;

/**
 * Validates content data against a content type's field rules.
 *
 * Rules syntax per field:
 *   required     — field must be present and non-empty
 *   min:N        — minimum length (strings)
 *   max:N        — maximum length (strings)
 *   email        — must be a valid e-mail address
 *   url          — must be a valid URL
 *   unique       — slug uniqueness (checked by the caller / BreadManager)
 *   numeric      — must be numeric
 *   in:a,b,c     — value must be in the list
 *   regex:/pattern/ — value must match
 */
class Validator
{
    /**
     * Validate data against a content type's fields.
     *
     * @param array<string,mixed> $data
     * @return array<string,string[]> field => errors (empty if valid)
     */
    public function validate(ContentType $type, array $data): array
    {
        $errors = [];

        foreach ($type->fields as $field) {
            $value = $data[$field->name] ?? null;
            $fieldErrors = $this->validateField($field, $value, $type, $data);

            if ($fieldErrors !== []) {
                $errors[$field->name] = $fieldErrors;
            }
        }

        return $errors;
    }

    /**
     * @param mixed $value
     * @return string[]
     */
    private function validateField(Field $field, mixed $value, ContentType $type, array $allData): array
    {
        $rules = $field->rules;
        $errors = [];

        foreach ($rules as $rule) {
            $error = $this->applyRule($rule, $field, $value);

            if ($error !== null) {
                $errors[] = $error;
            }
        }

        return $errors;
    }

    /**
     * @param mixed $value
     */
    private function applyRule(string $rule, Field $field, mixed $value): ?string
    {
        $label = $field->label();

        return match (true) {
            $rule === 'required' && ($value === null || $value === '' || $value === []) => "{$label} wajib diisi.",
            $rule === 'required' => null,

            str_starts_with($rule, 'min:') && !$this->checkMin($rule, $value) =>
                "{$label} minimal " . $this->extractNumber($rule) . " karakter.",

            str_starts_with($rule, 'max:') && !$this->checkMax($rule, $value) =>
                "{$label} maksimal " . $this->extractNumber($rule) . " karakter.",

            $rule === 'email' && is_string($value) && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL) =>
                "{$label} harus berupa alamat email yang valid.",

            $rule === 'url' && is_string($value) && $value !== '' && !filter_var($value, FILTER_VALIDATE_URL) =>
                "{$label} harus berupa URL yang valid.",

            $rule === 'numeric' && $value !== null && $value !== '' && !is_numeric($value) =>
                "{$label} harus berupa angka.",

            str_starts_with($rule, 'in:') && !$this->checkIn($rule, $value) =>
                "{$label} harus salah satu dari: " . substr($rule, 3) . ".",

            str_starts_with($rule, 'regex:') && !$this->checkRegex($rule, $value) =>
                "{$label} format tidak valid.",

            default => null,
        };
    }

    private function checkMin(string $rule, mixed $value): bool
    {
        if (!is_string($value)) {
            return true;
        }

        return mb_strlen($value) >= $this->extractNumber($rule);
    }

    private function checkMax(string $rule, mixed $value): bool
    {
        if (!is_string($value)) {
            return true;
        }

        return mb_strlen($value) <= $this->extractNumber($rule);
    }

    private function checkIn(string $rule, mixed $value): bool
    {
        $options = array_map('trim', explode(',', substr($rule, 3)));

        return in_array((string) $value, $options, true);
    }

    private function checkRegex(string $rule, mixed $value): bool
    {
        if (!is_string($value)) {
            return true;
        }

        $pattern = substr($rule, 6);

        return (bool) preg_match($pattern, $value);
    }

    private function extractNumber(string $rule): int
    {
        return (int) substr($rule, strpos($rule, ':') + 1);
    }
}
