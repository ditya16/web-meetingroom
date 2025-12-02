<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'ruangan_id',
        'pemesan_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'keperluan_rapat',
        'status',
        'catatan'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'ruangan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'pemesan_id');
    }

    public function hasConflict($roomId, $date, $startTime, $endTime, $excludeBookingId = null)
    {
        $query = self::where('ruangan_id', $roomId)
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

        return $query->exists();
    }
}
