<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhoneNumberValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_registration_rejects_an_alphabetic_phone_number(): void
    {
        $this->postJson('/api/v1/user/register', $this->registrationPayload('letters-only'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('phone_number');
    }

    public function test_merchant_registration_rejects_an_alphabetic_phone_number(): void
    {
        $this->postJson('/api/v1/merchant/register', $this->registrationPayload('0812ABC456'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('phone_number');
    }

    public function test_registration_accepts_local_and_international_phone_formats(): void
    {
        $this->postJson('/api/v1/user/register', $this->registrationPayload('081234567890'))
            ->assertCreated();

        $this->postJson('/api/v1/merchant/register', [
            ...$this->registrationPayload('+6281234567890'),
            'email' => 'merchant-phone@example.com',
        ])->assertCreated();
    }

    private function registrationPayload(string $phoneNumber): array
    {
        return [
            'name' => 'Phone Test',
            'email' => 'phone-test@example.com',
            'phone_number' => $phoneNumber,
            'password' => 'secure-password',
            'confirmed_password' => 'secure-password',
        ];
    }
}
