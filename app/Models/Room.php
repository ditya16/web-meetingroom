<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'nama_ruangan',
        'penanggung_jawab',
        'kapasitas',
        'fasilitas',
        'status'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'ruangan_id');
    }

    public function roleAccess()
    {
        return $this->hasMany(RoleAccess::class, 'ruangan_id');
    }

    public function isAvailable($date, $startTime, $endTime, $excludeBookingId = null)
    {
        $query = Booking::where('ruangan_id', $this->id)
                       ->where('tanggal', $date)
                       ->whereIn('status', ['Diterima', 'Menunggu'])
                       ->where(function ($q) use ($startTime, $endTime) {
                           $q->where(function ($q2) use ($startTime, $endTime) {
                               $q2->where('waktu_mulai', '<', $endTime)
                                  ->where('waktu_selesai', '>', $startTime);
                           })->orWhere(function ($q2) use ($startTime, $endTime) {
                               $q2->where('waktu_mulai', '<', $endTime)
                                  ->where('waktu_selesai', '>', $startTime);
                           })->orWhere(function ($q2) use ($startTime, $endTime) {
                               $q2->where('waktu_mulai', '>=', $startTime)
                                  ->where('waktu_selesai', '<=', $endTime);
                           });
                       });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return !$query->exists();
    }

    public function getUserAccessibleRooms($userRole)
    {
        return self::whereHas('roleAccess', function ($query) use ($userRole) {
            $query->where('role', $userRole)->where('can_book', true);
        })->where('status', 'Aktif')->get();
    }
}
