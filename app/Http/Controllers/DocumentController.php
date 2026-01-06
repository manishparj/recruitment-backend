<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function downloadDocument($filename)
    {
        // Check if the user is authenticated and authorized
        if (auth()->check() && $this->userHasAccess($filename)) {
            // Serve the document for download
            return Storage::download('documents/' . $filename);
        }

        abort(403, 'Unauthorized access.');
    }

    private function userHasAccess($filename)
    {
        // Implement custom logic to verify user access, e.g.:
        // Check if the document belongs to the logged-in user
        $userId = auth()->id();
        // Example: Fetch document ownership from DB
        // return \App\Models\Document::where('file_name', $filename)
        //                            ->where('user_id', $userId)
        //                            ->exists();
    }
}
