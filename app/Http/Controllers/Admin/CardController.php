<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CardController extends Controller
{
    public function index()
    {
        $cards = Card::orderBy('order', 'asc')
            ->orderBy('unlock_cost', 'asc')
            ->get();

        return Inertia::render('Admin/Cards/Index', [
            'cards' => $cards,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'era' => ['required', 'string', 'max:255'],
            'rarity' => ['required', 'in:common,rare,epic,legendary'],
            'unlock_cost' => ['required', 'integer', 'min:1'],
            'bio' => ['required', 'string'],
            'quote' => ['required', 'string'],
            'accent_color' => ['required', 'string', 'max:20'],
            'image_url' => ['nullable', 'string'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'is_active' => ['boolean'],
            'order' => ['nullable', 'integer'],
        ]);

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $uploadPath = public_path('uploads/cards');
            
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $file->move($uploadPath, $filename);
            $validated['image_url'] = '/uploads/cards/' . $filename;
        }

        unset($validated['image_file']);
        Card::create($validated);

        return back()->with('success', 'Scholar Card created successfully.');
    }

    public function update(Request $request, Card $card)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'era' => ['required', 'string', 'max:255'],
            'rarity' => ['required', 'in:common,rare,epic,legendary'],
            'unlock_cost' => ['required', 'integer', 'min:1'],
            'bio' => ['required', 'string'],
            'quote' => ['required', 'string'],
            'accent_color' => ['required', 'string', 'max:20'],
            'image_url' => ['nullable', 'string'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'is_active' => ['boolean'],
            'order' => ['nullable', 'integer'],
        ]);

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $uploadPath = public_path('uploads/cards');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $file->move($uploadPath, $filename);
            $validated['image_url'] = '/uploads/cards/' . $filename;
        }

        unset($validated['image_file']);
        $card->update($validated);

        return back()->with('success', 'Scholar Card updated successfully.');
    }

    public function destroy(Card $card)
    {
        $card->delete();

        return back()->with('success', 'Scholar Card deleted successfully.');
    }
}
