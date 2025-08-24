<?php

// app/Models/User.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    protected $table = 'user'; // pastikan sesuai tabel kamu
    public $timestamps = false; // kalau memang tidak pakai created_at/updated_at

    protected $fillable = [
        'nama_lengkap','username','email','password',
        'level','foto','status_aktif','tanggal_daftar',
    ];

    // Auto-hash password jika belum di-hash
    public function setPasswordAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['password'] = $value;
            return;
        }

        // Jika value sudah hash yang dikenali Laravel, biarkan;
        // kalau belum, hash pakai bcrypt.
        if (Hash::needsRehash($value)) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }
}
