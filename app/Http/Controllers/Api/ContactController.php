<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min($request->get('per_page', 10), 50);

        $contacts = Contact::orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => $contacts->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'email' => $contact->email,
                    'phone' => $contact->phone,
                    'birthdate' => $contact->birthdate?->format('Y-m-d'),
                    'gravatar_url' => $contact->gravatar_url,
                    'created_at' => $contact->created_at,
                ];
            }),
            'meta' => [
                'current_page' => $contacts->currentPage(),
                'per_page' => $contacts->perPage(),
                'total' => $contacts->total(),
                'last_page' => $contacts->lastPage(),
            ],
        ]);
    }
}
