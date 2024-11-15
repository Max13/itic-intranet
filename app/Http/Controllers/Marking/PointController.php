<?php

namespace App\Http\Controllers\Marking;

use App\Http\Controllers\Controller;
use App\Models\Marking\Criterion;
use App\Models\Marking\Point;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\User $student
     * @return \Illuminate\Http\Response
     */
    public function index(User $student)
    {
        return $student->points();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\User $student
     * @return \Illuminate\Http\Response
     */
    public function create(User $student)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User          $student
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $student)
    {
        $data = $request->validate([
            'criterion_id' => [
                'required',
                Rule::exists((new Criterion)->getTable()),
            ],
            'points' => 'required|between:'.($criterion = Criterion::find($request->criterion_id))->min_points.','.$criterion->max_points,
        ]);

        $point = new Point($data);
        $point->criterion()->associate($criterion);
        $point->staff()->associate($request->user());
        $point->student()->associate($student);
        $point->save();

        return $point;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marking\Point $point
     * @return \Illuminate\Http\Response
     */
    public function edit(Point $point)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Marking\Point $point
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Point $point)
    {
        $data = $request->validate([
            'points' => 'required|between:'.$point->criterion->min_points.','.$point->criterion->max_points,
        ]);

        $point->points = $data['points'];
        $point->save();

        return $point;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marking\Point $point
     * @return \Illuminate\Http\Response
     */
    public function destroy(Point $point)
    {
        $point->delete();

        return redirect()->route('students.points.index', $point->student_id);
    }
}
