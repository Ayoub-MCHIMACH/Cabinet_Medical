<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\AppointmentsWithPatientResource;
use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentsController extends Controller
{

    public function index()
    {
        $appointments = Appointment::with('patient')->get();
        $appointmentsWithPatient = AppointmentsWithPatientResource::collection($appointments);

        return $appointmentsWithPatient;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function getAppointmentStatistics()
    {
        $year = Carbon::now()->year;
        $appointments = Appointment::select(
            DB::raw('MONTH(start_time) as month'),
            DB::raw('COUNT(*) as appointment_count')
        )
            ->whereYear('start_time', $year)
            ->groupBy(DB::raw('MONTH(start_time)'))
            ->get();

        $statistics = [];
        foreach ($appointments as $appointment) {
            $monthName = Carbon::create()->month($appointment->month)->format('F');
            $statistics[$monthName] = $appointment->appointment_count;
        }

        return response()->json($statistics);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreAppointmentRequest $request)
    {
        $request->validated($request->all());
        $patient = Patient::where('CIN', $request->patient_CIN)->first();

        $availableSlots = Appointment::where(function($query) use ($request) {
            $query->whereBetween('start_time', [$request['start_time'], $request['end_time']])
                ->orWhereBetween('end_time', [$request['start_time'], $request['end_time']])
                ->orWhere(function($query) use ($request) {
                    $query->where('start_time', '<', $request['start_time'])
                        ->where('end_time', '>', $request['end_time']);
                });
        })->count();

        if ($availableSlots > 0) {
            return response()->json(['error' => 'There is already an appointment scheduled during this time.'], 409);
        }

        $appointment = Appointment::create([
            'patient_id'=>$patient->id,
            'start_time'=>$request->start_time,
            'end_time'=>$request->end_time,
            'reason'=>$request->reason,
        ]);
        return new AppointmentResource($appointment);
    }


    public function show(Appointment $appointment)
    {
        return new AppointmentResource($appointment);
    }


    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, Appointment $appointment)
    {
        $appointment->update($request->all());

        return new AppointmentResource($appointment);
    }
    public function approve(Appointment $appointment)
    {
        $appointment->update(['status' => 'approved']);

        return new AppointmentResource($appointment);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return response(null,204);
    }
}
