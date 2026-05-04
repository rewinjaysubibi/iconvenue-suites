<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Venue;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\CarouselImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function admin_can_upload_venue_images()
    {
        $admin = User::factory()->create(['role_id' => 1]);
        $this->actingAs($admin);

        $image1 = UploadedFile::fake()->image('venue1.jpg', 1920, 1080);
        $image2 = UploadedFile::fake()->image('venue2.jpg', 1920, 1080);

        $response = $this->post(route('admin.venues.store'), [
            'name' => 'Test Venue',
            'type' => 'venue',
            'description' => 'Test Description',
            'capacity' => 100,
            'price_per_day' => 5000,
            'price_morning' => 1500,
            'price_afternoon' => 1500,
            'price_evening' => 2000,
            'images' => [$image1, $image2],
            'is_active' => true,
        ]);

        $response->assertRedirect();
        
        $venue = Venue::where('name', 'Test Venue')->first();
        $this->assertNotNull($venue);
        $this->assertCount(2, $venue->images);
        
        // Check if images were stored
        foreach ($venue->images as $imagePath) {
            Storage::disk('public')->assertExists($imagePath);
        }
        
        // Verify it's using the new table
        $this->assertEquals('venues_and_suites', $venue->getTable());
    }

    /** @test */
    public function admin_can_upload_suite_images()
    {
        $admin = User::factory()->create(['role_id' => 1]);
        $this->actingAs($admin);

        $image = UploadedFile::fake()->image('suite.jpg', 1920, 1080);

        $response = $this->post(route('admin.suites.store'), [
            'name' => 'Test Suite',
            'type' => 'suite',
            'description' => 'Test Suite Description',
            'capacity' => 4,
            'price_per_day' => 3000,
            'images' => [$image],
            'is_active' => true,
        ]);

        $response->assertRedirect();
        
        $suite = Venue::where('name', 'Test Suite')->where('type', 'suite')->first();
        $this->assertNotNull($suite);
        $this->assertCount(1, $suite->images);
        
        Storage::disk('public')->assertExists($suite->images[0]);
    }

    /** @test */
    public function admin_can_upload_payment_proof_image()
    {
        $admin = User::factory()->create(['role_id' => 1]);
        $venue = Venue::factory()->create();
        $booking = Booking::factory()->create(['venue_id' => $venue->id]);
        
        $this->actingAs($admin);

        $proofImage = UploadedFile::fake()->image('payment_proof.jpg', 800, 600);

        $response = $this->post(route('admin.payments.store'), [
            'booking_id' => $booking->id,
            'amount' => 1000,
            'payment_method' => 'Bank Transfer',
            'reference_number' => 'REF123456',
            'proof_image' => $proofImage,
            'notes' => 'Test payment',
        ]);

        $response->assertRedirect();
        
        $payment = Payment::where('booking_id', $booking->id)->first();
        $this->assertNotNull($payment);
        $this->assertNotNull($payment->proof_image);
        
        Storage::disk('public')->assertExists($payment->proof_image);
    }

    /** @test */
    public function admin_can_upload_carousel_image()
    {
        $admin = User::factory()->create(['role_id' => 1]);
        $this->actingAs($admin);

        $carouselImage = UploadedFile::fake()->image('carousel.jpg', 1920, 1080);

        $response = $this->post(route('admin.carousel.store'), [
            'image' => $carouselImage,
            'title' => 'Test Carousel Image',
            'order' => 1,
        ]);

        $response->assertRedirect();
        
        $carousel = CarouselImage::where('title', 'Test Carousel Image')->first();
        $this->assertNotNull($carousel);
        $this->assertNotNull($carousel->image_path);
        
        Storage::disk('public')->assertExists($carousel->image_path);
    }

    /** @test */
    public function admin_can_upload_profile_image()
    {
        $admin = User::factory()->create(['role_id' => 1]);
        $this->actingAs($admin);

        $profileImage = UploadedFile::fake()->image('profile.jpg', 400, 400);

        $response = $this->put(route('admin.profile.update'), [
            'name' => $admin->name,
            'email' => $admin->email,
            'profile_image' => $profileImage,
        ]);

        $response->assertRedirect();
        
        $admin->refresh();
        $this->assertNotNull($admin->profile_image);
        
        Storage::disk('public')->assertExists($admin->profile_image);
    }

    /** @test */
    public function image_upload_validates_file_size()
    {
        $admin = User::factory()->create(['role_id' => 1]);
        $this->actingAs($admin);

        // Create a fake large image (over 10MB for venues)
        $largeImage = UploadedFile::fake()->create('large_image.jpg', 15000); // 15MB

        $response = $this->post(route('admin.venues.store'), [
            'name' => 'Test Venue',
            'type' => 'venue',
            'description' => 'Test Description',
            'capacity' => 100,
            'price_per_day' => 5000,
            'images' => [$largeImage],
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('images.0');
    }

    /** @test */
    public function image_upload_validates_file_type()
    {
        $admin = User::factory()->create(['role_id' => 1]);
        $this->actingAs($admin);

        // Create a non-image file
        $textFile = UploadedFile::fake()->create('document.txt', 100);

        $response = $this->post(route('admin.venues.store'), [
            'name' => 'Test Venue',
            'type' => 'venue',
            'description' => 'Test Description',
            'capacity' => 100,
            'price_per_day' => 5000,
            'images' => [$textFile],
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('images.0');
    }

    /** @test */
    public function admin_can_remove_venue_image()
    {
        $admin = User::factory()->create(['role_id' => 1]);
        $this->actingAs($admin);

        // Create venue with image
        $image = UploadedFile::fake()->image('venue.jpg');
        Storage::disk('public')->put('venues/test_image.jpg', $image->getContent());
        
        $venue = Venue::factory()->create([
            'images' => ['venues/test_image.jpg']
        ]);

        $response = $this->post(route('admin.venues.removeImage', $venue), [
            'image' => 'venues/test_image.jpg'
        ]);

        $response->assertJson(['success' => true]);
        
        $venue->refresh();
        $this->assertEmpty($venue->images);
        
        Storage::disk('public')->assertMissing('venues/test_image.jpg');
    }
}