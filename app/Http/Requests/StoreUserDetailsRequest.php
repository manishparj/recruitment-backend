<?php
namespace App\Http\Requests;

use App\Rules\BooleanTrueFalse;
use App\Rules\DocumentOrPath;
use App\Rules\EndDateAfterStartDate;
use App\Rules\RequiredIfAmountGreaterThanZero;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserDetailsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // dd($this->all());
        $step = $this->input('step');

        $rules = [];

        switch ($step) {
            case 1:
                // Validation rules for Step 1: Personal Information
                $rules = [
                    'first_name' => 'required|string|max:80',
                    'middle_name' => 'nullable|string|max:80',
                    'last_name' => 'nullable|string|max:80',
                    'gender' => 'nullable|string|in:male,female,other',
                    'marital_status' => 'nullable|string|in:single,married,widowed,divorced',
                    'spouse_name' => 'nullable|string|max:100|required_if:marital_status,married',
                    //'email' => 'required|email|unique:users,email,' . $this->user()->id,
                    'phone' => 'nullable|string|max:15',
                    'aadhaar_number' => 'nullable|string|min:12|max:12',
                    'date_of_birth' => 'nullable|date_format:Y-m-d',
                    'father_name' => 'nullable|string|max:100',
                    'mother_name' => 'nullable|string|max:100',
                    'user_category' => 'nullable|string|in:general,sc,st,obc,sbc,ews',
                    'applied_category' => 'nullable|string|in:general,sc,st,obc,sbc,ews',
                    'pwd_category' => 'nullable|boolean',
                    'pwd_category_option' => 'nullable|string|in:pwd_category_a,pwd_category_b,pwd_category_c,pwd_category_d,pwd_category_e|required_if:pwd_category,true',
                    'ex_serviceman' => 'nullable|boolean',
                    'istype_speed_req' => 'nullable|boolean',
                    'addresses_are_same' => 'nullable|boolean',
                    'correspondence_address' => 'nullable|string|max:255',
                    'permanent_address' => 'nullable|string|max:255|required_if:addresses_are_same,false',
                ];
                 
                break;

            case 2:
                // Validation rules for Step 2: Educational Information
                $rules = [
                    'exams' => 'required|array',
                    'exams.*.exam_name' => 'required|string|in:secondary,sr_secondary,graduation,post_graduation,phd,other,iti',
                    'exams.*.subject_names' => 'required|string|max:200',
                    'exams.*.institute_name' => 'required|string|max:100',
                    'exams.*.roll_number' => 'required|string|max:30',
                    'exams.*.result_type' => 'required|string|in:percentage,cgpa,grade',
                    'exams.*.result' => 'required|string|max:20',
                    'exams.*.year_of_passed' => 'required|string',
                    'exams.*.remarks' => 'nullable|string|max:255',
                    // 'exams.*.document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    'exams.*.document' => ['required', new DocumentOrPath],
                    'exams.*.document_type' => 'required|string|in:secondary,sr_secondary,graduation,post_graduation,phd,other,iti',
                ];
                break;

            case 3:
                // Validation rules for Step 3: Experience Information
                $rules = [
                    'experience' => 'nullable|array',
                    'experience.*.experience_type' => 'nullable|string|in:govt,private,temporary,deputation,ad_hoc,full_time,other',
                    'experience.*.signatury_designation' => 'nullable|string|max:200',
                    'experience.*.employer' => 'nullable|string|max:255',
                    'experience.*.designation' => 'nullable|string|max:100',
                    'experience.*.department' => 'nullable|string|max:200',
                    'experience.*.nature_of_duties' => 'nullable|string|max:255',
                    'experience.*.from_date' => 'nullable|string|date_format:Y-m-d',
                    'experience.*.to_date' => ['nullable','string','date_format:Y-m-d', new EndDateAfterStartDate($this->from_date)],
                    'experience.*.presently_working' => ['nullable', new BooleanTrueFalse],
                    'experience.*.year_of_experience' => ['nullable','string'],
                    'experience.*.remarks' => 'nullable|string|max:255',
                        'experience.*.document' => ['required', new DocumentOrPath],
                ]; 
                break;
            case 4:
                // Validation rules for Step 3: Documents
                // $rules = [
                //     'docs' => 'required|array',
                //     'docs.*.document' => ['required', new DocumentOrPath],
                //     'docs.*.document_type' => 'required|string|in:photo,signature,category',
                // ];
                $rules = [
                    'docs' => 'required|array',
                    'docs.*.document_type' => 'required|string|in:photo,signature,category',
                    'docs.*.document' => [
                        'sometimes', 
                        'required_if:docs.*.document_type,photo,signature', 
                        new DocumentOrPath
                    ],
                ];
                break;
            case 5:
                // Form submit after preview
                $rules = [
                ];
                break;
            case 6:
                //Payment receipt after preview
                $rules = [
                    // 'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    // 'document' => ['required', new DocumentOrPath],
                    'document' => [new RequiredIfAmountGreaterThanZero, new DocumentOrPath],
                    // 'document' => ['required_if:amount,>,0'],
                    'document_type' => 'required|string|in:payment', // document_type is always required
                    'amount' => 'required|integer|min:0',
                    'reference_number' => 'required|string|min:8', // Syntax issue resolved
                    'remarks' => 'nullable|string',
                ];
                break;
            default:
                // Default rules (optional)
                break;
        }

        return $rules;
    }
}
