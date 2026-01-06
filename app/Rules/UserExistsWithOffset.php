<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserExistsWithOffset implements Rule
{
    protected $offset;

    public function __construct($offset = 12453289)
    {
        $this->offset = $offset;
    }

    public function passes($attribute, $value)
    {
        if(!is_numeric($this->offset)){
            return false;
        }
        $adjustedId = $value - $this->offset;

        return DB::table('users')->where('id', $adjustedId)->exists();
    }

    public function message()
    {
        return 'The selected user ID is invalid.';
    }
}
