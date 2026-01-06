<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class DocumentOrPath implements Rule
{
    protected $errorType;
    protected $triedPath = '';


    public function passes($attribute, $value)
    {
        // Check if a file is uploaded
        if (request()->hasFile($attribute)) {
            $file = request()->file($attribute);
            $isValid = $file->isValid() && in_array($file->getClientOriginalExtension(), ['pdf', 'jpg', 'jpeg', 'png']) && $file->getSize() <= 5120 * 1024;
            if (!$isValid) {
                $this->errorType = 'file';
            }
            
            return $isValid;
        }

        // Check if a document path is provided and if the file exists in the 'documents' folder
        if (is_string($value) && !empty($value)) {
            // return Storage::disk('public')->exists('documents/' . $value);
            // Assuming the provided path includes the 'documents/' prefix
            // $this->triedPath = $value;
            $this->triedPath = Storage::disk('public')->path($value);
            $exists = Storage::disk('public')->exists($value);
            if (!$exists) {
                $this->errorType = 'path';
            }

            return $exists;
        }
         // Check if a document path is provided and if the file exists in the private 'documents' folder
        //  if (is_string($value) && !empty($value)) {
        //     // Assuming the provided path includes the 'documents/' prefix
        //     $this->triedPath = Storage::disk('local')->path($value);  // Using 'local' for private storage
        //     $exists = Storage::disk('local')->exists('documents/' . $value);
        //     if (!$exists) {
        //         $this->errorType = 'path';
        //     }

        //     return $exists;
        // }
        $this->errorType = 'file'; // Default to file error if neither is provided
        return false;
    }

    public function message()
    {
        if ($this->errorType === 'file') {
            return 'The :attribute must be a valid file of type: pdf, jpg, jpeg, png and not exceed 5MB.';
        } elseif ($this->errorType === 'path') {
            return "The :attribute must be a valid document path that exists in the documents folder. Tried path: {$this->triedPath}";
        }
        // if ($this->errorType === 'file') {
        //     return 'The :attribute must be a valid file of type: pdf, jpg, jpeg, png and not exceed 2MB.';
        // } elseif ($this->errorType === 'path') {
        //     // Adjusted to represent the actual storage path as it would appear on the public URL
        //     $displayPath = str_replace('public/', '/storage/', $this->triedPath);
        //     return "The :attribute must be a valid document path that exists in the documents folder. Tried path: {$displayPath}";
        // }
        
        return 'The :attribute must be a valid file or a valid document path.';
    }
}
