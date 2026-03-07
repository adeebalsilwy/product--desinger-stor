<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\TitleToFolderName;
use App\Http\Controllers\Controller;
use App\Models\ShirtImage;
use App\Models\Tshirt;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TshirtsController extends Controller
{
    public function index()
    {
        $tshirts = Tshirt::with('images')
            ->orderBy('created_at', 'desc')
            ->paginate(9)
            ->through(function ($tshirt) {
                return [
                    'id' => $tshirt->id,
                    'title' => $tshirt->title,
                    'slug' => $tshirt->slug,
                    'description' => $tshirt->description,
                    'price' => $tshirt->price,
                    'listed' => $tshirt->listed,
                    'mainImage' => $tshirt->getMainImage(),
                    'otherImages' => $tshirt->getOtherImages(),
                    'imagesFolderName' => $tshirt->getImagesFolderName(),
                    'totalSells' => $tshirt->getTotalSales(),
                    'totalRevenue' => $tshirt->getTotalRevenue()
                ];
            });

        // test empty state without deleting t-shirts
        // $tshirts = [
        //     'data' => [],
        // ];

        return inertia('Admin/Tshirts', [
            'tshirts' => $tshirts,
            'orders_count' => \App\Models\Order::count(),
            'customers_count' => \App\Models\Customer::count(),
            'tshirts_count' => \App\Models\Product::count(),
            'revenue' => \App\Models\Order::sum('total_amount'),
        ]);
    }


    public function store(Request $request)
    {
        if (! Gate::allows('super_admin')) {
            return back()->withErrors([
                'authorization' => config('messages.guest_admin_restriction'),
            ]);
        }
        // Validate request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'mainImage' => 'required|image|max:1024',
            'secondImage' => 'nullable|image|max:1024',
            'thirdImage' => 'nullable|image|max:1024',
            'forthImage' => 'nullable|image|max:1024',
            'fifthImage' => 'nullable|image|max:1024',
        ]);

        // Generate folder name from the first 3 words of the title
        $folderName = TitleToFolderName::convert($validatedData['title']);

        // Create a new Tshirt instance and save it
        $tshirt = new Tshirt();
        $tshirt->title = $validatedData['title'];
        $tshirt->slug = Str::slug($validatedData['title'], '-');
        $tshirt->price = $validatedData['price'];
        $tshirt->description = $validatedData['description'];
        $tshirt->images_folder_name = $folderName;
        $tshirt->save();



        // Array of images with their corresponding order
        $images = [
            'mainImage' => 1,
            'secondImage' => 2,
            'thirdImage' => 3,
            'forthImage' => 4,
            'fifthImage' => 5,
        ];

        // Loop through each image input
        foreach ($images as $imageKey => $order) {
            // Check if the image exists in the request
            if ($request->hasFile($imageKey)) {
                $file = $request->file($imageKey);
                $extension = $file->getClientOriginalExtension(); // Get original extension
                $filename = "{$imageKey}.{$extension}";

                // Store the image in the public disk under the specified folder
                $path = $file->storeAs("tshirts/{$folderName}", $filename, 'public');

                // Create a new TshirtImage instance
                $tshirtImage = new ShirtImage();
                $tshirtImage->order = $order;
                $tshirtImage->tshirt_id = $tshirt->id;
                // $tshirtImage->url = Storage::url($path); // Get URL for public access
                $tshirtImage->url = '/storage/' . $path; // Get URL for public access
                $tshirtImage->save();
            }
        }

        // Redirect to the t-shirts route
        return redirect()->route('t-shirts');
    }

    public function update(Request $request, Tshirt $tshirt)
    {

        if (! Gate::allows('super_admin')) {
            return back()->withErrors([
                'authorization' => config('messages.guest_admin_restriction'),
            ]);
        }

        // Validate request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'listed' => 'required|boolean',
            'mainImage' => 'nullable|max:1024',
            'secondImage' => 'nullable|max:1024',
            'thirdImage' => 'nullable|max:1024',
            'forthImage' => 'nullable|max:1024',
            'fifthImage' => 'nullable|max:1024',
        ]);

        // Tshirt folder name
        $folderName = $tshirt->getImagesFolderName();

        // Only update text fields if they have changed
        $changes = [];
        if ($tshirt->title !== $validatedData['title']) {
            $changes['title'] = $validatedData['title'];
            $changes['slug'] = Str::slug($validatedData['title'], '-');
        }
        if ($tshirt->price !== (float) $validatedData['price']) {
            $changes['price'] = (float) $validatedData['price'];
        }
        if ($tshirt->description !== $validatedData['description']) {
            $changes['description'] = $validatedData['description'];
        }
        if ($tshirt->listed !== $validatedData['listed']) {
            $changes['listed'] = $validatedData['listed'];
        }
        if (!empty($changes)) {
            $tshirt->update($changes);
        }

        // Array of images with their corresponding order and field names
        $images = [
            'mainImage' => 1,
            'secondImage' => 2,
            'thirdImage' => 3,
            'forthImage' => 4,
            'fifthImage' => 5,
        ];

        // Loop through each image input
        foreach ($images as $imageKey => $order) {
            // Find the current image path in the database
            $currentImage = ShirtImage::where('tshirt_id', $tshirt->id)->where('order', $order)->first();
            $currentImagePath = $currentImage ? $currentImage->url : null;

            // Check if a new file is uploaded for this image
            if ($request->hasFile($imageKey)) {
                // Remove the old image from storage if it exists
                if ($currentImagePath) {
                    Storage::delete($currentImagePath);
                }

                // Store the new image
                $file = $request->file($imageKey);
                $extension = $file->getClientOriginalExtension();
                $filename = "{$imageKey}.{$extension}";
                $newImagePath = $file->storeAs("tshirts/{$folderName}", $filename, 'public');

                // Update or create the database record for this image
                ShirtImage::updateOrCreate(
                    ['tshirt_id' => $tshirt->id, 'order' => $order],
                    ['url' => '/storage/' . $newImagePath]
                );
            } elseif (
                $request->input($imageKey) === null
            ) {
                // If the field is null, remove from storage and database
                if ($currentImagePath) {
                    Storage::delete($currentImagePath);
                    ShirtImage::where('tshirt_id', $tshirt->id)->where('order', $order)->delete();
                }
            }
            // If "originalImage" is set, we leave the existing image unchanged
        }


        // Redirect to the t-shirts route
        return redirect()->back();
    }

    public function destroy(Tshirt $tshirt)
    {
        if (! Gate::allows('super_admin')) {
            return back()->withErrors([
                'authorization' => config('messages.guest_admin_restriction'),
            ]);
        }
        // Delete the images
        $tshirt->images()->delete();

        // delete the images folder
        $folderPath = "tshirts/{$tshirt->getImagesFolderName()}";
        Storage::disk('public')->deleteDirectory($folderPath);

        // delete the tshirt
        $tshirt->delete();

        return redirect()->route('t-shirts');
    }
}
