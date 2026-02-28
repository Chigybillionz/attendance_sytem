<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
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
            'subject' => 'required|string|in:bug,feature,feedback,support,other', // Validate subject values
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
            
            // Log the submission to see what's being saved
            \Log::info('Contact form submission:', [
                'name' => $data['name'],
                'email' => $data['email'],
                'subject' => $data['subject'], // Should be: bug, feature, feedback, support, or other
                'message' => $data['message']
            ]);
            
            // Save to database with subject
            $contact = Contact::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'subject' => $data['subject'], // Store lowercase value
                'message' => $data['message'],
                'status' => 'new'
            ]);
            
            \Log::info('Contact saved with ID: ' . $contact->id . ' and subject: ' . $contact->subject);
            
            return response()->json([
                'success' => true,
                'message' => 'Thank you for your message! We\'ll get back to you soon.'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Contact form error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending your message. Please try again later.'
            ], 500);
        }
    }
}