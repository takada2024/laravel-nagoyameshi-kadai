<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'テストTest',
            'postal_code' => '1234567',
            'address' => 'テストTest',
            'representative' => 'テストTest',
            'establishment_date' => 'テストTest',
            'capital' => 'テストTest',
            'business' => 'テストTest',
            'number_of_employees' => 'テストTest',
        ];
    }
}
