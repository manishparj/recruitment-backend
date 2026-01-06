<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserDetailsRequest;
use App\Mail\UserFormSubmitted;
use App\Mail\UserPaymentSuccessful;
use App\Models\Post;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserDocument;
use App\Models\UserEducationalDetail;
use App\Models\UserExperienceDetail;
use App\Models\UserPayment;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class UserDetailsController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = UserDetail::get();


        if(!$users){
            return $this->errorResponse("User data not found!!");
        }

        return $this->successResponse($users, "List of users", 200);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserDetailsRequest $request)
    {
        $user = UserDetail::create($request->validated());
        if(!$user){
            return $this->errorResponse("Failed to store user data!!");
        }
        return $this->successResponse(null, "New user created!!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $step)
    {
        // $user = $request->user();
        $user = Auth::user()->role === 'admin' ? $request->user() : Auth::user();

        $id = $user->id;
        // dd($id);
        switch ($step) {
            case 1:
                // Validation rules for Step 1: Personal Information
                $userDetails = UserDetail::where('user_id', $id)->first();
                if(!$userDetails){
                    return $this->errorResponse("User details data not found!!");
                }
                return $this->successResponse($userDetails, "User personal details displayed..");

            case 2:
                // Validation rules for Step 2: Educational Information
                $educationalDetails = UserEducationalDetail::join('user_documents', function($join) {
                    $join->on('user_educational_details.user_id', '=', 'user_documents.user_id')
                         ->on('user_educational_details.exam_name', '=', 'user_documents.document_type');
                })
                ->where('user_educational_details.user_id', $id)
                ->get(['user_educational_details.*', 'user_documents.*', 'user_documents.document_path as document']);
                
                if(!$educationalDetails){
                    return $this->errorResponse("User educational details data not found!!");
                }

                return $this->successResponse($educationalDetails, "User educational details displayed..");

            case 3:
                // Validation rules for Step 3: Experience Information
                $experienceDetails = UserExperienceDetail::select('user_experience_details.*', 'user_experience_details.document_path as document')
                ->where('user_id', $id)
                ->get();
                
                if(!$experienceDetails){
                    return $this->errorResponse("User experience details data not found!!");
                }
                return $this->successResponse($experienceDetails, "User experience details displayed..");

            case 4:
                // Validation rules for Step 4: Docs Information
                $docsDetails = UserDocument::select(['user_documents.*', 'user_documents.document_path as document'])->where('user_id', $id)->whereIn('document_type', ['photo', 'signature', 'category'])->get();
                if(!$docsDetails){
                    return $this->errorResponse("User documents data not found!!");
                }
                return $this->successResponse($docsDetails, "User docs details displayed..");

            case 5:
                // show final submit information
                
                return $this->successResponse(null, "User final submit details displayed..");
    
            case 6:
                // show payment information
                $docsDetails = UserDocument::select(['user_documents.*', 'user_documents.document_path as document'])->where('user_id', $id)->whereIn('document_type', ['payment'])->get();
                if(!$docsDetails){
                    return $this->errorResponse("User documents data not found!!");
                }
                return $this->successResponse($docsDetails, "User docs details displayed..");

            default:
                return $this->errorResponse("Invalid step!!");
        }


        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreUserDetailsRequest $request)
    {
        // $user = $request->user();
        $user = Auth::user()->role === 'admin' ? $request->user() : Auth::user();

        $validatedData = $request->validated();
        
        $step = $request->input('step');

        switch ($step) {
             case 1:
                // Update Personal Information
                 $userDetails = UserDetail::where('user_id', $user->id)->first();
                // dd($userDetails);
                if(!$userDetails){
                    return $this->errorResponse("User details data not found!!");
                }
                
                $userDetails->update($request->only(
                    'first_name',
                    'middle_name',
                    'last_name',
                    'gender',
                    'marital_status',
                    'spouse_name',
                    'phone',
                    'aadhaar_number',
                    'date_of_birth',
                    'father_name',
                    'mother_name',
                    'user_category',
                    'applied_category',
                    'pwd_category',
                    'pwd_category_option',
                    'ex_service_man',
                    'istype_speed_req',
                    'correspondence_address',
                    'permanent_address',
                    'addresses_are_same'
                ));
                return $this->successResponse(null, "User details updated successfully!!");

            case 2:
                DB::beginTransaction();
                try { 
                    //delete previous user educational data
                    UserEducationalDetail::where('user_id', $user->id)->delete();
                    //delete previous user educational documents
                    //'identity', 'payment', 'education', 'experience'
                    UserDocument::where('user_id', $user->id)->where('document_group', 'education')->delete();
                    // Update Educational Information
                    foreach ($validatedData['exams'] as $examData) {
                        UserEducationalDetail::updateOrCreate(
                            [
                                'user_id' => $user->id, // criteria to check for existence
                                'exam_name' => $examData['exam_name'], // additional criteria if necessary
                            ], 
                            [
                                'subject_names' => $examData['subject_names'],
                                'institute_name' => $examData['institute_name'],
                                'roll_number' => $examData['roll_number'],
                                'result_type' => $examData['result_type'],
                                'result' => $examData['result'],
                                'year_of_passed' => $examData['year_of_passed'],
                                'remarks' => $examData['remarks'] ?? NULL,
                            ]
                        );
                        if (!is_string($examData['document'])) {
                            $path = $examData['document']->store('documents', 'public');
                            // $path = $examData['document']->store('documents');
                        }
                        UserDocument::Create(
                            [
                                'user_id' => $user->id, // Assuming the user is authenticated
                                'document_type' => $examData['document_type'],
                                'document_group' => 'education',
                                'document_path' => $path ?? $examData['document'],
                                'document_verified' => 0
                            ]
                        );

                    }
                    DB::commit();
                    return $this->successResponse(null, "User educational details saved successfully!!");
                } catch (\Exception $e) {
                    // Rollback the transaction on error
                    DB::rollBack();
                    // Log::error($e->getMessage());
                    return $this->errorResponse("User educational details saved failed!!");
                }
            case 3:
                // Update Experience Settings
                UserExperienceDetail::where('user_id', $user->id)->delete();
                // Begin the transaction
                if(!isset($validatedData['experience'])){
                    //experience data is not available
                    return $this->successResponse(null, "User experience details empty filled successfully!!");
                }
                DB::beginTransaction();
                // print_r($validatedData['experience']);
                try {                
                    foreach ($validatedData['experience'] as $experienceData) {
                        if (!is_string($experienceData['document'])) {
                            $path = $experienceData['document']->store('documents', 'public');
                            // $path = $experienceData['document']->store('documents'); // Default private storage
                        }

                        UserExperienceDetail::Create(
                            [
                                'user_id' => $user->id, // criteria to check for existence
                                'experience_type' => $experienceData['experience_type'],
                                'signatury_designation' => $experienceData['signatury_designation'],
                                'employer' => $experienceData['employer'],
                                'designation' => $experienceData['designation'],
                                'department' => $experienceData['department'],
                                'nature_of_duties' => $experienceData['nature_of_duties'],
                                'from_date' => $experienceData['from_date'],
                                'to_date' => $experienceData['to_date'] ?? NULL,
                                'presently_working' => in_array($experienceData['presently_working'] ?? false, [true, 'true', 1, '1', ], true),
                                'year_of_experience' => $experienceData['year_of_experience'] ?? NULL,
                                'remarks' => $experienceData['remarks'] ?? NULL,
                                'document_path' => $path ?? $experienceData['document'],
                                'document_verified' => 0
                            ]
                        );

                    }
                    // Commit the transaction
                    DB::commit();
                    return $this->successResponse(null, "User experience details saved successfully!!");
                } catch (\Exception $e) {
                    // Rollback the transaction on error
                    DB::rollBack();
                    // Log::error($e->getMessage());
                    return $this->errorResponse("User experience details saved failed!!".$e);
                }
            case 4:
                // Begin the transaction
                DB::beginTransaction();
                try {
                    foreach ($validatedData['docs'] as $docs) {
                        $path = null;
                        if (!isset($docs['document'])) {
                            return $this->errorResponse("User's document upload failed!!");
                        }
                        if (!is_string($docs['document'])) {
                            $path = $docs['document']->store('documents', 'public');
                            // $path = $docs['document']->store('documents'); // Default private storage
                        }
                        // $fullPath = Storage::url($path);
                        // UserDocument::updateOrCreate(
                        //     [
                        //         'user_id' => $user->id, // Assuming the user is authenticated
                        //         'document_type' => $docs['document_type'],
                        //         'document_group' => 'identity',
                        //     ],
                        //     [
                        //         'document_path' => $path ?? $docs['document'],
                        //         'document_verified' => 0
                        //     ]
                        // );
                        $document = UserDocument::where('user_id', $user->id)
                            ->where('document_type', $docs['document_type'])
                            ->where('document_group', 'identity')
                            ->first();
                        if ($document) {
                            // Update the existing document
                            $document->update([
                                'document_path' => $path ?? $docs['document'],
                                'document_verified' => 0,
                            ]);
                        } else {
                            // Create a new document if no match found
                            UserDocument::create([
                                'user_id' => $user->id,
                                'document_type' => $docs['document_type'],
                                'document_group' => 'identity',
                                'document_path' => $path ?? $docs['document'],
                                'document_verified' => 0,
                            ]);
                        }


                    }
                    // Commit the transaction
                    DB::commit();
                    return $this->successResponse(null, "User's document(s) uploaded successfully!!");
                } catch (\Exception $e) {
                    // Rollback the transaction on error
                    DB::rollBack();
                    // Log::error($e->getMessage());        
                    return $this->errorResponse("User's documents upload failed!!");
                }
            case 5:
                // change status to submitted
                $userEducationDetailsCount = UserEducationalDetail::where('user_id', $user->id)->get()->count();
                //'identity', 'payment', 'education', 'experience'
                $userDocumentsCount = UserDocument::where('user_id', $user->id)->where('document_group', 'identity')->get()->count();
                if($userEducationDetailsCount >= 1 && $userDocumentsCount >= 2){
                    $rowsAffected = User::where('id', $user->id)->update(['registration_status' => 'submitted']);
                    if($rowsAffected == 1){
                        $data['submit_time'] = time();
                        //send email notification for successfully submitted form
                        //email data
                        $data['email'] = $user->email;
                        $data['user_id'] = $user->id+12453289;
                        $data['vacancy'] = Vacancy::whereId($user->vacancy_id)->first();
                        $data['post'] = Post::whereId($user->post_id)->where('vacancy_id', $user->vacancy_id)->first();
                        $data['registration_status'] = 'submitted';

                        Mail::to($user->email)->send(new UserFormSubmitted($data));
                    }
                    return $this->successResponse($data, "User's form uploaded successfully and email sent!!");
                }
                return $this->errorResponse("User's form submittion failed!!");

            case 6:
                //updating only if user form submitted status is draft
                if($user->registration_status != 'submitted'){
                    return $this->errorResponse("Form submittion pending so payment not allowed!!");
                }

                // payment information
                // change status to submitted
                $paymentStatus = 'unverified';
                if($validatedData['amount'] > 0){
                    if (!isset($validatedData['document'])) {
                        return $this->errorResponse("User's payment document upload failed!!");
                    }
                    if (!is_string($validatedData['document'])) {
                        $path = $validatedData['document']->store('documents', 'public');
                        // $path = $validatedData['document']->store('documents');
                    }
                }else{
                    $path = 'documents/xxxx.jpg';//documents/J65uThe6z9pK6EaF7zszZWgrUd2kDLZ4BbH0stiO.pdf
                    $validatedData['document_type'] = 'payment';
                    $paymentStatus = 'verified';
                }

                $user = User::whereId($user->id)->first();
                if($user->registration_status == 'submitted'){
                    $rowsAffected = User::where('id', $user->id)->update(['registration_status' => 'final']);
                    UserDocument::updateOrCreate(
                        [
                            'user_id' => $user->id, // Assuming the user is authenticated
                            'document_type' => $validatedData['document_type'],
                            'document_group' => 'payment',
                        ],
                        [
                            'document_path' => $path ?? $validatedData['document'],
                            'document_verified' => 0
                        ]
                    );
                    UserPayment::create([
                        'user_id' => $user->id,
                        'status' => $paymentStatus,
                        'reference_number' => $validatedData['reference_number'],
                        'amount' => $validatedData['amount'],
                        'remarks' => $validatedData['remarks'],
                    ]);
                    if($rowsAffected == 1){
                        $data['payment_time'] = time();
                        //send email notification for successfully finalization form
                        //email data
                        $data['email'] = $user->email;
                        $data['user_id'] = $user->id+12453289;
                        $data['vacancy'] = Vacancy::whereId($user->vacancy_id)->first();
                        $data['post'] = Post::whereId($user->post_id)->where('vacancy_id', $user->vacancy_id)->first();
                        $data['registration_status'] = 'final';
                        $data['payment_status'] = $paymentStatus;
                        $data['reference_number'] = $validatedData['reference_number'];
                        $data['amount'] = $validatedData['amount'];

                        Mail::to($user->email)->send(new UserPaymentSuccessful($data));
                    }
                    return $this->successResponse($data, "User's form finalization successfully and email sent!!");
                }
                return $this->errorResponse("User's form finalization failed!!");
                    
            default:
                // Handle invalid step
                return $this->errorResponse("Invalid step!!");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = UserDetail::whereId($id)->first();
        if(!$user){
            return $this->errorResponse("User data not found!!");
        }

        $user->delete();
        return $this->successResponse(null, "User deleted!!");
    }
}
