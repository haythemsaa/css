<?php

namespace Tests\Feature;

use App\Models\Match;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\TicketPurchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $homeTeam = Team::factory()->create(['name' => 'CSS']);
        $awayTeam = Team::factory()->create(['name' => 'EST']);

        $this->match = Match::create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'competition' => 'Ligue 1',
            'season' => '2024/2025',
            'venue' => 'Stade Taïeb Mhiri',
            'match_date' => now()->addWeek(),
            'status' => 'scheduled',
        ]);

        $this->ticket = Ticket::create([
            'match_id' => $this->match->id,
            'category' => 'tribune',
            'section' => 'Tribune Centrale',
            'price' => 50.00,
            'total_quantity' => 100,
            'available_quantity' => 100,
            'is_available' => true,
            'sale_starts_at' => now(),
            'sale_ends_at' => $this->match->match_date->subHours(2),
        ]);
    }

    public function test_can_list_tickets_for_match(): void
    {
        $response = $this->getJson("/api/v1/matches/{$this->match->id}/tickets");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'category', 'price', 'available_quantity']
                ]
            ]);
    }

    public function test_can_filter_tickets_by_category(): void
    {
        Ticket::create([
            'match_id' => $this->match->id,
            'category' => 'vip',
            'section' => 'Loges VIP',
            'price' => 150.00,
            'total_quantity' => 50,
            'available_quantity' => 50,
            'is_available' => true,
            'sale_starts_at' => now(),
            'sale_ends_at' => $this->match->match_date,
        ]);

        $response = $this->getJson("/api/v1/matches/{$this->match->id}/tickets?category=vip");

        $response->assertStatus(200);
        $data = $response->json('data');

        foreach ($data as $ticket) {
            $this->assertEquals('vip', $ticket['category']);
        }
    }

    public function test_authenticated_user_can_purchase_tickets(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/tickets/purchase', [
                'ticket_id' => $this->ticket->id,
                'quantity' => 2,
                'payment_method' => 'd17',
            ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Billets réservés avec succès']);

        $this->assertDatabaseHas('ticket_purchases', [
            'user_id' => $this->user->id,
            'ticket_id' => $this->ticket->id,
            'quantity' => 2,
            'status' => 'pending',
        ]);

        // Check stock decremented
        $this->ticket->refresh();
        $this->assertEquals(98, $this->ticket->available_quantity);
        $this->assertEquals(2, $this->ticket->sold_quantity);
    }

    public function test_cannot_purchase_more_tickets_than_available(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/tickets/purchase', [
                'ticket_id' => $this->ticket->id,
                'quantity' => 150,
                'payment_method' => 'd17',
            ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Quantité insuffisante disponible']);
    }

    public function test_ticket_purchase_generates_unique_ticket_number(): void
    {
        $purchase1 = TicketPurchase::create([
            'ticket_number' => TicketPurchase::generateTicketNumber(),
            'user_id' => $this->user->id,
            'ticket_id' => $this->ticket->id,
            'match_id' => $this->match->id,
            'quantity' => 1,
            'unit_price' => 50.00,
            'total_price' => 50.00,
            'status' => 'pending',
            'payment_method' => 'd17',
            'payment_status' => 'pending',
        ]);

        $purchase2 = TicketPurchase::create([
            'ticket_number' => TicketPurchase::generateTicketNumber(),
            'user_id' => $this->user->id,
            'ticket_id' => $this->ticket->id,
            'match_id' => $this->match->id,
            'quantity' => 1,
            'unit_price' => 50.00,
            'total_price' => 50.00,
            'status' => 'pending',
            'payment_method' => 'd17',
            'payment_status' => 'pending',
        ]);

        $this->assertNotEquals($purchase1->ticket_number, $purchase2->ticket_number);
    }

    public function test_marking_purchase_as_paid_generates_qr_code(): void
    {
        $purchase = TicketPurchase::create([
            'ticket_number' => TicketPurchase::generateTicketNumber(),
            'user_id' => $this->user->id,
            'ticket_id' => $this->ticket->id,
            'match_id' => $this->match->id,
            'quantity' => 1,
            'unit_price' => 50.00,
            'total_price' => 50.00,
            'status' => 'pending',
            'payment_method' => 'd17',
            'payment_status' => 'pending',
        ]);

        $purchase->markAsPaid('TXN123456');

        $this->assertEquals('paid', $purchase->payment_status);
        $this->assertEquals('confirmed', $purchase->status);
        $this->assertNotNull($purchase->qr_code);
        $this->assertNotNull($purchase->paid_at);
    }

    public function test_can_cancel_pending_ticket_purchase(): void
    {
        $purchase = TicketPurchase::create([
            'ticket_number' => TicketPurchase::generateTicketNumber(),
            'user_id' => $this->user->id,
            'ticket_id' => $this->ticket->id,
            'match_id' => $this->match->id,
            'quantity' => 3,
            'unit_price' => 50.00,
            'total_price' => 150.00,
            'status' => 'confirmed',
            'payment_method' => 'd17',
            'payment_status' => 'paid',
        ]);

        $this->ticket->update(['available_quantity' => 97, 'sold_quantity' => 3]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/tickets/purchases/{$purchase->id}/cancel");

        $response->assertStatus(200);

        $purchase->refresh();
        $this->assertEquals('cancelled', $purchase->status);

        // Check stock restored
        $this->ticket->refresh();
        $this->assertEquals(100, $this->ticket->available_quantity);
        $this->assertEquals(0, $this->ticket->sold_quantity);
    }

    public function test_qr_code_verification_works(): void
    {
        $purchase = TicketPurchase::create([
            'ticket_number' => TicketPurchase::generateTicketNumber(),
            'user_id' => $this->user->id,
            'ticket_id' => $this->ticket->id,
            'match_id' => $this->match->id,
            'quantity' => 1,
            'unit_price' => 50.00,
            'total_price' => 50.00,
            'status' => 'confirmed',
            'payment_method' => 'd17',
            'payment_status' => 'paid',
            'qr_code' => 'QR-TEST123456',
        ]);

        $response = $this->postJson('/api/v1/tickets/verify', [
            'qr_code' => 'QR-TEST123456',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'valid' => true,
                'message' => 'Billet valide',
            ]);
    }

    public function test_cannot_use_same_ticket_twice(): void
    {
        $purchase = TicketPurchase::create([
            'ticket_number' => TicketPurchase::generateTicketNumber(),
            'user_id' => $this->user->id,
            'ticket_id' => $this->ticket->id,
            'match_id' => $this->match->id,
            'quantity' => 1,
            'unit_price' => 50.00,
            'total_price' => 50.00,
            'status' => 'used',
            'payment_method' => 'd17',
            'payment_status' => 'paid',
            'qr_code' => 'QR-USED123',
            'used_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/tickets/verify', [
            'qr_code' => 'QR-USED123',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'valid' => false,
                'message' => 'Ce billet a déjà été utilisé',
            ]);
    }

    public function test_ticket_occupancy_rate_is_calculated(): void
    {
        $this->ticket->update([
            'total_quantity' => 100,
            'sold_quantity' => 75,
        ]);

        $this->assertEquals(75.0, $this->ticket->occupancy_rate);
    }
}
