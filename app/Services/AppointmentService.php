<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Handles appointment/calendar business logic.
 */
class AppointmentService
{
    /**
     * Get appointments for a date range.
     */
    public function getAppointmentsForRange(Carbon $start, Carbon $end): Collection
    {
        return Appointment::with('user')
            ->inRange($start, $end)
            ->orderBy('starts_at')
            ->get();
    }

    /**
     * Get upcoming appointments.
     */
    public function getUpcoming(int $limit = 10): Collection
    {
        return Appointment::with('user')
            ->upcoming()
            ->limit($limit)
            ->get();
    }

    /**
     * Get today's appointments.
     */
    public function getTodayAppointments(): Collection
    {
        return Appointment::with('user')
            ->whereDate('starts_at', today())
            ->orderBy('starts_at')
            ->get();
    }

    /**
     * Create a new appointment.
     */
    public function create(array $data): Appointment
    {
        return Appointment::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'starts_at' => Carbon::parse($data['starts_at']),
            'ends_at' => Carbon::parse($data['ends_at']),
            'status' => $data['status'] ?? Appointment::STATUS_PENDING,
            'client_name' => $data['client_name'],
            'client_email' => $data['client_email'] ?? null,
            'client_phone' => $data['client_phone'] ?? null,
            'notes' => $data['notes'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'service_type' => $data['service_type'] ?? null,
        ]);
    }

    /**
     * Update an appointment.
     */
    public function update(Appointment $appointment, array $data): Appointment
    {
        $appointment->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'starts_at' => Carbon::parse($data['starts_at']),
            'ends_at' => Carbon::parse($data['ends_at']),
            'status' => $data['status'] ?? $appointment->status,
            'client_name' => $data['client_name'],
            'client_email' => $data['client_email'] ?? null,
            'client_phone' => $data['client_phone'] ?? null,
            'notes' => $data['notes'] ?? null,
            'user_id' => $data['user_id'] ?? $appointment->user_id,
            'service_type' => $data['service_type'] ?? null,
        ]);

        return $appointment->fresh();
    }

    /**
     * Delete an appointment.
     */
    public function delete(Appointment $appointment): bool
    {
        return $appointment->delete();
    }

    /**
     * Confirm an appointment.
     */
    public function confirm(Appointment $appointment): Appointment
    {
        $appointment->confirm();
        return $appointment->fresh();
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(Appointment $appointment): Appointment
    {
        $appointment->cancel();
        return $appointment->fresh();
    }

    /**
     * Get appointment statistics.
     */
    public function getStatistics(): array
    {
        $today = today();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        return [
            'today_count' => Appointment::whereDate('starts_at', $today)->count(),
            'upcoming_count' => Appointment::upcoming()->count(),
            'month_count' => Appointment::inRange($startOfMonth, $endOfMonth)->count(),
            'pending_count' => Appointment::withStatus(Appointment::STATUS_PENDING)->count(),
            'confirmed_count' => Appointment::withStatus(Appointment::STATUS_CONFIRMED)->count(),
        ];
    }

    /**
     * Get available staff members.
     */
    public function getStaffMembers(): Collection
    {
        return User::permission('calendar.manage')
            ->orWhereHas('roles', function ($query) {
                $query->whereIn('name', ['Super Admin', 'Admin']);
            })
            ->select(['id', 'name', 'email'])
            ->get();
    }

    /**
     * Get available appointment statuses for selection.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function getStatuses(): array
    {
        return [
            ['value' => Appointment::STATUS_PENDING, 'label' => 'Pending'],
            ['value' => Appointment::STATUS_CONFIRMED, 'label' => 'Confirmed'],
            ['value' => Appointment::STATUS_CANCELLED, 'label' => 'Cancelled'],
            ['value' => Appointment::STATUS_COMPLETED, 'label' => 'Completed'],
            ['value' => Appointment::STATUS_NO_SHOW, 'label' => 'No Show'],
        ];
    }
}
