<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function create(Request $request, Rental $rental): View
    {
        $this->reviewableRental($request, $rental);
        $rental->load('vehicle');

        return view('user.review', compact('rental'));
    }

    public function store(Request $request, Rental $rental): RedirectResponse
    {
        $this->reviewableRental($request, $rental);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $rental->review()->create($data);

        return redirect()
            ->route('user.rentals.show', $rental)
            ->with('success', 'Your review has been submitted.');
    }

    private function reviewableRental(Request $request, Rental $rental): Rental
    {
        abort_unless($rental->customer_id === $request->user()->id, 404);
        abort_unless($rental->status === 'completed', 422, 'Only completed rentals can be reviewed.');
        abort_if($rental->review()->exists(), 409, 'This rental has already been reviewed.');

        return $rental;
    }
}
