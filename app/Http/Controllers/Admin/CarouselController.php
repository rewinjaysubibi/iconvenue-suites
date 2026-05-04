<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarouselImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarouselController extends Controller
{
    public function index()
    {
        $images = CarouselImage::orderBy('order')->get();
        return view('admin.carousel.index', compact('images'));
    }

    public function create()
    {
        return view('admin.carousel.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'title' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0'
        ]);

        $imagePath = $request->file('image')->store('carousel', 'public');
        $this->copyToPublicStorage($imagePath);

        CarouselImage::create([
            'image_path' => $imagePath,
            'title' => $request->title,
            'order' => $request->order ?? 0,
            'is_active' => true
        ]);

        return redirect()->route('admin.carousel.index')
            ->with('success', 'Carousel image added successfully!');
    }

    public function edit(CarouselImage $carousel)
    {
        return view('admin.carousel.edit', compact('carousel'));
    }

    public function update(Request $request, CarouselImage $carousel)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'title' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0'
        ]);

        $data = [
            'title' => $request->title,
            'order' => $request->order ?? 0,
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            if ($carousel->image_path) {
                Storage::disk('public')->delete($carousel->image_path);
            }
            $data['image_path'] = $request->file('image')->store('carousel', 'public');
            $this->copyToPublicStorage($data['image_path']);
        }

        $carousel->update($data);

        return redirect()->route('admin.carousel.index')
            ->with('success', 'Carousel image updated successfully!');
    }

    public function destroy(CarouselImage $carousel)
    {
        if ($carousel->image_path) {
            Storage::disk('public')->delete($carousel->image_path);
        }
        
        $carousel->delete();

        return redirect()->route('admin.carousel.index')
            ->with('success', 'Carousel image deleted successfully!');
    }

    public function toggleActive(CarouselImage $carousel)
    {
        $carousel->update(['is_active' => !$carousel->is_active]);

        return redirect()->route('admin.carousel.index')
            ->with('success', 'Carousel image status updated!');
    }

    private function copyToPublicStorage(string $storedPath): void
    {
        $sourcePath = storage_path('app/public/' . $storedPath);
        $publicPath = public_path('storage/' . $storedPath);
        $publicDir  = dirname($publicPath);
        if (!is_dir($publicDir)) mkdir($publicDir, 0755, true);
        if (file_exists($sourcePath) && !file_exists($publicPath)) copy($sourcePath, $publicPath);
    }
}
