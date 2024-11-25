<?php

namespace App\Http\Controllers\Marking;

use App\Http\Controllers\Controller;
use App\Models\Marking\Criterion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CriterionController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Criterion::class, 'criterion');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Criterion::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:marking_criteria,name',
            'min_points' => 'required|integer',
            'max_points' => 'required|integer',
        ]);

        $criterion = $request->user()->criteria()->create($data);

        return redirect()->route('marking.criteria.show', $criterion);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Marking\Criterion  $criterion
     * @return \Illuminate\Http\Response
     */
    public function show(Criterion $criterion)
    {
        return $criterion;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marking\Criterion  $criterion
     * @return \Illuminate\Http\Response
     */
    public function edit(Criterion $criterion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Marking\Criterion  $criterion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Criterion $criterion)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique((new Criterion)->getTable())
                    ->ignore($criterion->id),
            ],
            'min_points' => 'required|integer',
            'max_points' => 'required|integer',
        ]);

        $criterion->fill($data);

        return redirect()->route('marking.criteria.show', $criterion);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marking\Criterion  $criterion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Criterion $criterion)
    {
        //
    }
}
