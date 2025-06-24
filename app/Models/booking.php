<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Room;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'booking';

    protected $fillable = [
        'id',
        'nama_event', 
        'kode_booking', 
        'nama_organisasi', 
        'tanggal', 
        'waktu_mulai', 
        'waktu_selesai', 
        'nama_pic',
        'status',
        'bidang',
        'kegiatan',
        'komersial/non',
        'lantai',  
        'ruangan',
        'jumlah_peserta',
        'sdgs_data' 
    ];

   protected $casts = ['sdgs_data' => 'array'];

    public function getWaktuAttribute()
    {
        return Carbon::parse($this->waktu_mulai)->format('H:i') . ' - ' . Carbon::parse($this->waktu_selesai)->format('H:i');
    }

    public function absen()
    {
        return $this->hasMany(Absen::class, 'id_booking', 'kode_booking');
    }

    public function peminjaman()
    {
        return $this->hasMany(PeminjamanBarang::class, 'kode_booking', 'kode_booking');
    }

    public function getAbsenStatusAttribute()
    {
        return $this->absen()->latest()->value('status');
    }
    
    public function ruangan()
    {
        return $this->belongsTo(Room::class); 
    }
}
