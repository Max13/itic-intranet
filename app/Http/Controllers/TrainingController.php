<?php

namespace App\Http\Controllers;

use App\Models\Training;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (
               $request->user()->roles->contains('name', 'Admin')
            || $request->user()->roles->contains('name', 'HeadTeacher')
        ) {
            $trainings = Training::query();
        } else {
            $trainings = $request->user()->trainings();
        }

        $trainings = $trainings->with('students')
                               ->orderBy('nth_year')
                               ->orderBy('shortname')
                               ->get();

        return view('trainings.index', [
            'trainings' => $trainings,
            'students' => $trainings->pluck('students')
                                    ->flatten(1)
                                    ->map(function ($s) use ($trainings) {
                                         return [
                                             'id' => $s->id,
                                             'fullname' => $s->fullname.' - '.$trainings->where('id', $s->training_id)->first()->name,
                                         ];
                                     }),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function show(Training $training)
    {
        return view('trainings.show', [
            'training' => $training->load([
                'students' => function ($query) {
                    $query->withSum('absences as total_absences', 'duration')
                          ->withSum('points as total_points', 'points');
                },
            ]),
        ]);
    }

    /**
     * Show training's ranking
     *
     * @param  \App\Models\Training     $training
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function ranking(Training $training, Request $request)
    {
        $training->load([
            'students' => function ($query) {
                $query->withSum('points as total_points', 'points');
            },
        ]);

        return view('trainings.ranking', [
            'training' => $training,
        ]);
    }
}
