<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointments\StoreAppointmentRequest;
use App\Http\Requests\Appointments\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles appointment/calendar CRUD operations.
 */
class AppointmentController extends Controller
{
    /**
     * Display calendar view with appointments.
     */
    public function index(Request $request, AppointmentService $service): Response
    {
        $this->authorize('viewAny', Appointment::class);

        $start = Carbon::parse($request->get('start', now()->startOfMonth()));
        $end = Carbon::parse($request->get('end', now()->endOfMonth()));

        return Inertia::render('Admin/Calendar/Index', [
            'appointments' => $service->getAppointmentsForRange($start, $end),
            'statistics' => $service->getStatistics(),
            'staff' => $service->getStaffMembers(),
            'currentStart' => $start->toDateString(),
            'currentEnd' => $end->toDateString(),
        ]);
    }

    /**
     * Show create appointment form.
     */
    public function create(AppointmentService $service): Response
    {
        $this->authorize('create', Appointment::class);

        return Inertia::render('Admin/Calendar/Create', [
            'staff' => $service->getStaffMembers(),
            'statuses' => $service->getStatuses(),
        ]);
    }

    /**
     * Store a new appointment.
     */
    public function store(StoreAppointmentRequest $request, AppointmentService $service): RedirectResponse
    {
        $appointment = $service->create($request->validated());

        return redirect()
            ->route('admin.calendar.index')
            ->with('success', 'Appointment created successfully.');
    }

    /**
     * Show appointment details.
     */
    public function show(Appointment $appointment): Response
    {
        $this->authorize('view', $appointment);

        return Inertia::render('Admin/Calendar/Show', [
            'appointment' => $appointment->load('user'),
        ]);
    }

    /**
     * Show edit appointment form.
     */
    public function edit(Appointment $appointment, AppointmentService $service): Response
    {
        $this->authorize('update', $appointment);

        return Inertia::render('Admin/Calendar/Edit', [
            'appointment' => $appointment->load('user'),
            'staff' => $service->getStaffMembers(),
            'statuses' => $service->getStatuses(),
        ]);
    }

    /**
     * Update an appointment.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment, AppointmentService $service): RedirectResponse
    {
        $service->update($appointment, $request->validated());

        return redirect()
            ->route('admin.calendar.index')
            ->with('success', 'Appointment updated successfully.');
    }

    /**
     * Delete an appointment.
     */
    public function destroy(Appointment $appointment, AppointmentService $service): RedirectResponse
    {
        $this->authorize('delete', $appointment);

        $service->delete($appointment);

        return redirect()
            ->route('admin.calendar.index')
            ->with('success', 'Appointment deleted successfully.');
    }
}
