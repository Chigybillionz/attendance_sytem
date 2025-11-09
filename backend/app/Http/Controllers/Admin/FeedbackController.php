<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Contact::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'feedbacks' => $feedbacks
        ]);
    }

    public function destroy($id)
    {
        $feedback = Contact::findOrFail($id);
        $feedback->delete();
        
        return response()->json([
            'message' => 'Feedback deleted successfully'
        ]);
    }
}