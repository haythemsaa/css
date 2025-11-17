<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Partner;
use App\Models\PartnerOffer;
use App\Models\ReductionCode;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReductionCodeControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Premium user can generate a reduction code
     */
    public function test_premium_user_can_generate_code(): void
    {
        $premiumUser = User::factory()->create(['user_type' => 'premium']);
        $partner = Partner::factory()->create();
        $offer = PartnerOffer::factory()->create([
            'partner_id' => $partner->id,
            'stock_available' => 10,
            'status' => 'active',
        ]);

        $token = $premiumUser->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/v1/codes/generate/{$offer->slug}", [
            'type' => 'qr',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'code',
                         'type',
                         'offer',
                         'valid_until',
                     ],
                 ]);

        $this->assertDatabaseHas('reduction_codes', [
            'user_id' => $premiumUser->id,
            'offer_id' => $offer->id,
            'code_type' => 'qr',
            'status' => 'active',
        ]);
    }

    /**
     * Test Free user cannot generate code
     */
    public function test_free_user_cannot_generate_code(): void
    {
        $freeUser = User::factory()->create(['user_type' => 'free']);
        $partner = Partner::factory()->create();
        $offer = PartnerOffer::factory()->create([
            'partner_id' => $partner->id,
            'stock_available' => 10,
        ]);

        $token = $freeUser->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/v1/codes/generate/{$offer->slug}", [
            'type' => 'qr',
        ]);

        $response->assertStatus(403)
                 ->assertJson([
                     'success' => false,
                 ]);
    }

    /**
     * Test cannot generate code when offer is out of stock
     */
    public function test_cannot_generate_code_when_offer_out_of_stock(): void
    {
        $premiumUser = User::factory()->create(['user_type' => 'premium']);
        $partner = Partner::factory()->create();
        $offer = PartnerOffer::factory()->create([
            'partner_id' => $partner->id,
            'stock_available' => 0,
        ]);

        $token = $premiumUser->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/v1/codes/generate/{$offer->slug}", [
            'type' => 'qr',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                 ]);
    }

    /**
     * Test can validate a valid reduction code
     */
    public function test_can_validate_valid_code(): void
    {
        $user = User::factory()->create(['user_type' => 'premium']);
        $partner = Partner::factory()->create();
        $offer = PartnerOffer::factory()->create(['partner_id' => $partner->id]);

        $code = ReductionCode::factory()->create([
            'user_id' => $user->id,
            'partner_id' => $partner->id,
            'offer_id' => $offer->id,
            'code' => 'TEST-CODE-123',
            'status' => 'active',
            'expires_at' => now()->addDays(30),
        ]);

        $response = $this->postJson('/api/v1/codes/validate', [
            'code' => 'TEST-CODE-123',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'valid' => true,
                         'code' => 'TEST-CODE-123',
                     ],
                 ]);
    }

    /**
     * Test cannot validate expired code
     */
    public function test_cannot_validate_expired_code(): void
    {
        $user = User::factory()->create(['user_type' => 'premium']);
        $partner = Partner::factory()->create();
        $offer = PartnerOffer::factory()->create(['partner_id' => $partner->id]);

        $code = ReductionCode::factory()->create([
            'user_id' => $user->id,
            'partner_id' => $partner->id,
            'offer_id' => $offer->id,
            'code' => 'EXPIRED-CODE',
            'status' => 'active',
            'expires_at' => now()->subDays(1),
        ]);

        $response = $this->postJson('/api/v1/codes/validate', [
            'code' => 'EXPIRED-CODE',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                     'data' => [
                         'valid' => false,
                     ],
                 ]);
    }

    /**
     * Test can use a valid code
     */
    public function test_can_use_valid_code(): void
    {
        $user = User::factory()->create([
            'user_type' => 'premium',
            'loyalty_points' => 0,
        ]);
        $partner = Partner::factory()->create();
        $offer = PartnerOffer::factory()->create([
            'partner_id' => $partner->id,
            'reduction_value' => 20,
        ]);

        $code = ReductionCode::factory()->create([
            'user_id' => $user->id,
            'partner_id' => $partner->id,
            'offer_id' => $offer->id,
            'code' => 'VALID-CODE',
            'status' => 'active',
            'expires_at' => now()->addDays(30),
        ]);

        $response = $this->postJson("/api/v1/codes/{$code->code}/use", [
            'amount' => 100.00,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('reduction_codes', [
            'code' => 'VALID-CODE',
            'status' => 'used',
        ]);

        // Verify loyalty points were awarded (10% of amount)
        $user->refresh();
        $this->assertEquals(10, $user->loyalty_points);
    }

    /**
     * Test user can get their generated codes
     */
    public function test_user_can_get_their_codes(): void
    {
        $user = User::factory()->create(['user_type' => 'premium']);
        $partner = Partner::factory()->create();
        $offer = PartnerOffer::factory()->create(['partner_id' => $partner->id]);

        ReductionCode::factory()->count(3)->create([
            'user_id' => $user->id,
            'partner_id' => $partner->id,
            'offer_id' => $offer->id,
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/codes/my-codes');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }
}
