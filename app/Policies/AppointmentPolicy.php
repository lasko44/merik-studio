<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

/**
 * Authorization policy for appointments.
 */
class AppointmentPolicy
{
    /**
     * Determine whether the user can view any appointments.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('calendar.view')
            || $user->hasPermissionTo('calendar.manage');
    }

    /**
     * Determine whether the user can view the appointment.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        return $user->hasPermissionTo('calendar.view')
            || $user->hasPermissionTo('calendar.manage')
            || $appointment->user_id === $user->id;
    }

    /**
     * Determine whether the user can create appointments.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('calendar.manage');
    }

    /**
     * Determine whether the user can update the appointment.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        return $user->hasPermissionTo('calendar.manage')
            || $appointment->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the appointment.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->hasPermissionTo('calendar.manage');
    }
}
