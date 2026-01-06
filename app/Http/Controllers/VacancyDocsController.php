<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVacancyDocsRequest;
use App\Models\VacancyDoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VacancyDocsController extends ApiController
{
    /*
    * Store documents releted to vacancy
    */
    public function store(StoreVacancyDocsRequest $request)
    {
        $validated = $request->validated();
        DB::transaction(function () use ($validated) {
            $vacancyDocs = VacancyDoc::create([
                'vacancy_id' => $validated['vacancy_id'],
                'title' => $validated['title'],
                'type' => $validated['type'],
                'doc_path' => $validated['doc_path'],
            ]);

        });

        
        return $this->successResponse(null, "New vacancy docs created!!");

    }
}
