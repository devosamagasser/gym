<?php

namespace App\Trait;

use Illuminate\Database\Eloquent\Model;

trait TranslationTrait
{
    private function fillTranslations(Model $model, array $translations): void
    {
        foreach ($translations as $locale => $fields) {
            $model->translateOrNew($locale)->fill($fields);
        }
    }

}
