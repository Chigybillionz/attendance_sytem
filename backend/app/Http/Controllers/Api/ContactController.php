<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact; // ADD THIS LINE
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function submitContactForm(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'subject' => 'nullable|string|max:255', // ADD THIS
        'message' => 'required|string|max:2000',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $data = $validator->validated();
        
        // Log the submission
        \Log::info('Contact form submission:', $data);
        
        // Save to database
        $contact = Contact::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => $data['subject'] ?? 'other', // ADD THIS
            'message' => $data['message'],
            'status' => 'new'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Thank you for your message! We\'ll get back to you soon.'
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Contact form error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while sending your message. Please try again later.'
        ], 500);
    }
}
}