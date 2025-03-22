<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class MaxImageCount implements ValidationRule
{
    protected int $maxImageCount;

    public function __construct(int $maxImageCount = 1)
    {
        $this->maxImageCount = $maxImageCount;
    }

    /**
     * Реализуем метод validate, который вызывается при валидации.
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        // Используем регулярное выражение для поиска всех тегов <img>
        preg_match_all('/<img[^>]*>/i', $value, $matches);
        $imageCount = count($matches[0]);

        // Если количество изображений превышает заданное в конструкторе
        if ($imageCount > $this->maxImageCount) {
            $fail("The {$attribute} field must contain no more than {$this->maxImageCount} images.");
        }
    }
}
