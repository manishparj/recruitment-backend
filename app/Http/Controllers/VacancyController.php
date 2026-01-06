<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVacancyDocsRequest;
use App\Http\Requests\StoreVacancyRequest;
use App\Http\Requests\UpdateVacancyDataRequest;
use App\Http\Requests\UpdateVacancyRequest;
use App\Models\Post;
use App\Models\Vacancy;
use App\Models\VacancyDoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VacancyController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load the posts relationship
        $vacancies = Vacancy::with('posts')->with('vacancyDocs') ->orderBy('id', 'desc')->get();


        if(!$vacancies){
            return $this->errorResponse("Vacancies data not found!!");
        }

        return $this->successResponse($vacancies, "List of vacancies", 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVacancyRequest $request)
    {
        $validated = $request->validated();
        // $vacancy = Vacancy::create($request->validated());
        // Use a transaction to ensure data integrity
        DB::transaction(function () use ($validated) {
            // Create the user
            $vacancy = Vacancy::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);

            // Create the posts
            foreach ($validated['posts'] as $postData) {
                $vacancy->posts()->create($postData);
            }

            // Create the vacancy docs
            foreach ($validated['vacancy_docs'] as $vacancyDocsData) {
                $path = null;
                if (!is_string($vacancyDocsData['document'])) {
                    $path = $vacancyDocsData['document']->store('documents', 'public');
                }


                $vacancy->vacancyDocs()->create([
                    'vacancy_id' => $vacancy->id,
                    'title' => $vacancyDocsData['title'],
                    'type' => $vacancyDocsData['type'],
                    'doc_path' => $path ?? $vacancyDocsData['document'],
                ]);
            }

        });

        
        return $this->successResponse(null, "New vacancy created!!");

    }

    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $vacancy = vacancy::with('posts')->with('vacancyDocs')->whereId($id)->first();

        if(!$vacancy){
            return $this->errorResponse("Vacancy data not found!!");
        }

        return $this->successResponse($vacancy, "Vacancy displayed..");
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVacancyRequest $request, $id)
    {
        $vacancy = Vacancy::whereId($id)->first();
        if(!$vacancy){
            return $this->errorResponse("Vacancy data not found!!");
        }
        $vacancy->update($request->validated());

        if(!$vacancy){
            return $this->errorResponse("Vacancy updation failed!!");
        }

        return $this->successResponse($vacancy, "Vacancy updated..");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $vacancy = Vacancy::whereId($id)->first();
        if(!$vacancy){
            return $this->errorResponse("Vacancy data not found!!");
        }

        $vacancy->delete();
        return $this->successResponse(null, "Vacancy deleted!!");
    }

    public function vacancies_data()
    {
        // Eager load the posts relationship
        // $vacancies = Vacancy::with('posts')->with('vacancyDocs')->with('users')->get();
        // $query = Vacancy::with(['posts.users', 'vacancyDocs']);
        // dd($query->toSql());
        // $vacancies=$query->get();
        $vacancies = Vacancy::with(['posts.users', 'vacancyDocs'])
            ->orderBy('id', 'desc')
            ->get();
        
        
        //add registration number in query result
        // adding user registration number after retrieving the data
        $vacancies->each(function ($vacancy) {
            $vacancy->posts->each(function ($post) {
                $post->users->each(function ($user) {
                    $user->registration_number = $user->id + 12453289;
                    $user->user_details = $user->userDetails;
                    $user->user_experience_details = $user->userExperienceDetails;
                    $user->user_payment_details = $user->userPaymentDetails;
                    $user->user_education_details = $user->userEducationDetails;
                    $user->user_documents = $user->userDocuments;
                });
            });
        });
        if(!$vacancies){
            return $this->errorResponse("Vacancies data not found!!");
        }

        return $this->successResponse($vacancies, "Vacancies Data", 200);
    }

    

    public function vacancy_data_update(UpdateVacancyDataRequest $request, $vacancy_id)
    {
        //print_r($request->all());
        
        //$validated = $request->validated();
        $validated = $request;
    
        $vacancy = Vacancy::find($validated['vacancy_id']);
        if (!$vacancy) {
            return $this->errorResponse("Vacancy data not found!!");
        }
    
        // Begin the transaction
        DB::beginTransaction();
        try {
            // Update vacancy data
            $vacancy->update([
                'code' => $validated['vacancy_code'],
                'name' => $validated['vacancy_name'],
                'start_date' => $validated['vacancy_start_date'],
                'end_date' => $validated['vacancy_end_date'],
            ]);
    
            // Update or insert posts
            foreach ($validated['posts'] as $post) {
                if ($post['id'] != 0) {
                    // Update the post or delete if marked for deletion
                    if ($post['is_deleted']) {
                        Post::where('id', $post['id'])
                            ->where('vacancy_id', $validated['vacancy_id'])
                            ->delete();
                    } else {
                        Post::where('id', $post['id'])
                            ->update([
                                'code' => $post['code'],
                                'name' => $post['name'],
                            ]);
                    }
                } else {
                    // Insert new post
                    Post::create([
                        'code' => $post['code'],
                        'name' => $post['name'],
                        'vacancy_id' => $validated['vacancy_id'],
                    ]);
                }
            }
    
            // Update or insert vacancy_docs
            foreach ($validated['vacancy_docs'] as $vacancyDoc) {
                $path = null;
                if (!is_string($vacancyDoc['document'])) {
                    $path = $vacancyDoc['document']->store('documents', 'public');
                }
    
                if ($vacancyDoc['id'] != 0) {
                    // Update the vacancy_doc or delete if marked for deletion
                    if ($vacancyDoc['is_deleted']) {
                        VacancyDoc::where('id', $vacancyDoc['id'])
                            ->where('vacancy_id', $validated['vacancy_id'])
                            ->delete();
                    } else {
                        VacancyDoc::where('id', $vacancyDoc['id'])
                            ->update([
                                'title' => $vacancyDoc['title'],
                                'type' => $vacancyDoc['type'],
                                'doc_path' => $path ?? $vacancyDoc['document'],
                            ]);
                    }
                } else {
                    // Insert new vacancy_doc
                    VacancyDoc::create([
                        'title' => $vacancyDoc['title'],
                        'type' => $vacancyDoc['type'],
                        'vacancy_id' => $validated['vacancy_id'],
                        'doc_path' => $path ?? $vacancyDoc['document'],
                    ]);
                }
            }
    
            // Commit the transaction
            DB::commit();
    
            return $this->successResponse(null, "Vacancy Data updated successfully", 200);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            // Log::error($e->getMessage());        
            return $this->errorResponse("Vacancy data update failed!");
        }
        
    }
    public function vacancy_data_destroy($vacancy_id)
    {
         // Find the vacancy by ID
        $vacancy = Vacancy::with(['posts', 'vacancyDocs'])->find($vacancy_id);

        // Check if the vacancy exists
        if (!$vacancy) {
            return $this->errorResponse("Vacancy data not found!!");
        }

        // Check for associated users in the users table
        $associatedUsers = DB::table('users')->where('vacancy_id', $vacancy_id)->exists();

        if ($associatedUsers) {
            return $this->errorResponse("Cannot delete vacancy with associated users.");
        }

        // Begin a transaction
        DB::beginTransaction();

        try {
            // Delete associated posts and documents
            $vacancy->posts()->delete();
            $vacancy->vacancyDocs()->delete();

            // Delete the vacancy itself
            $vacancy->delete();

            // Commit the transaction
            DB::commit();

            return $this->successResponse(null, "Vacancy and its associated data deleted successfully.");
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();
            return $this->errorResponse("Failed to delete vacancy data.");
        }

    }

    
}
