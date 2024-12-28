<?php

namespace App\Console\Commands\Ypareo;

use App\Models\Absence;
use App\Models\Classroom;
use App\Models\Role;
use App\Models\Subject;
use App\Models\Training;
use App\Models\User;
use App\Services\Ypareo;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ypareo:sync:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Down sync all Ypareo data (Users, Classrooms, Subjects, Trainings, Absences, …)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Ypareo $ypareo)
    {
        logger()->debug('Syncing all data from Ypareo', [
            'arguments' => $this->arguments(),
            'options' => $this->options(),
        ]);

        Artisan::call('ypareo:sync:users');
        $this->newLine(2);

        Artisan::call('ypareo:sync:classrooms');
        $this->newLine(2);

        Artisan::call('ypareo:sync:subjects');
        $this->newLine(2);

        // Student's training
        $this->info('- Students:');
        DB::transaction(function () use ($ypareo) {
            $this->withProgressBar(Classroom::all(), function ($c) use ($ypareo) {
                User::whereIn(
                    'ypareo_id',
                    $ypareo->getClassroomsStudents($c->ypareo_id)->pluck('codeApprenant')
                )->update(['training_id' => $c->training_id]);
            });
        });
        // /Student's classroom

        $this->newLine(2);

        // Trainer's trainings
        $this->info('- Trainers:');
        DB::transaction(function () use ($ypareo) {
            $this->withProgressBar(User::where('is_trainer', true)->get(), function ($u) use ($ypareo) {
                $yClassrooms = $ypareo->getClassrooms($u['ypareo_id']);
                $u->trainings()->sync(
                    Classroom::whereIn('ypareo_id', $yClassrooms->pluck('codeGroupe'))
                             ->pluck('training_id')
                );
            });
        });
        // /Trainer's classroom

        $this->newLine(2);

        // Absences
        $this->info('- Absences:');
        DB::transaction(function () use ($ypareo) {
            $absences = $ypareo->getAllAbsences();
            Absence::whereNotNull('ypareo_id')->delete();

            $this->withProgressBar($absences, function ($abs) {
                $dbAbsence = Absence::firstOrNew(['ypareo_id' => $abs['codeAbsence']])
                                    ->forceFill([
                                        'label' => $abs['motifAbsence']['nomMotifAbsence'],
                                        'is_delay' => $abs['isRetard'],
                                        'is_justified' => $abs['isJustifie'],
                                        'started_at' => Carbon::createFromFormat('d/m/Y', $abs['dateDeb'])
                                                              ->addMinutes($abs['heureDeb']),
                                        'ended_at' => Carbon::createFromFormat('d/m/Y', $abs['dateFin'])
                                                            ->addMinutes($abs['heureFin']),
                                        'duration' => $abs['duree'],
                                    ]);

                try {
                    $training = Training::whereRelation('classrooms', 'ypareo_id', $abs['codeGroupe'])
                                        ->first();
                    $user = User::where('ypareo_id', $abs['codeApprenant'])
                                ->first();

                    if (is_null($training) || is_null($user)) {
                        return;
                    }

                    $dbAbsence->student()->associate($user);
                    $dbAbsence->training()->associate($training);

                    $dbAbsence->save();
                } catch (QueryException $e) {
                    //
                }
            });
        });
        // /Absences

        return 0;
    }
}
