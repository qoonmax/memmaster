<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class ContentLength implements ValidationRule
{
    protected int $maxLength;

    public function __construct(int $maxLength)
    {
        $this->maxLength = $maxLength;
    }

    /**
     * Реализуем метод validate, который вызывается при валидации.
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        // Убираем все теги изображений из контента
        $textWithoutImages = preg_replace('/<img[^>]+>/i', '', $value);

        // Оставляем только текст
        $textOnly = strip_tags($textWithoutImages);

        // Удаляем дополнительные пробелы, перевод строки, табуляции и прочие невидимые символы
        $textOnly = preg_replace('/\s+/', ' ', $textOnly);

        // Тримим текст для удаления лишних пробелов в начале и в конце
        $textOnly = trim($textOnly);

        // Проверяем длину оставшегося текста
        if (mb_strlen($textOnly) > $this->maxLength) {
            $fail("The {$attribute} field must not exceed {$this->maxLength} characters, excluding images.");
        }
    }
}
