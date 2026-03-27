<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class CnpjValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Extrai apenas os números da string (ignora pontos, barras e traços)
        $cnpj = preg_replace('/[^0-9]/', '', (string) $value);

        // Verifica se tem 14 dígitos
        if (strlen($cnpj) != 14) {
            $fail('O :attribute informado não é um CNPJ válido.');
            return;
        }

        // Verifica se todos os dígitos são iguais (ex: 11.111.111/1111-11)
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            $fail('O :attribute informado não é um CNPJ válido.');
            return;
        }

        // Validação do primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;
        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
            $fail('O :attribute informado não é um CNPJ válido.');
            return;
        }

        // Validação do segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;
        if ($cnpj[13] != ($resto < 2 ? 0 : 11 - $resto)) {
            $fail('O :attribute informado não é um CNPJ válido.');
            return;
        }
    }
}
