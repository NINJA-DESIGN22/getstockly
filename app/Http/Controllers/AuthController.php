<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // ===== VIEW AUTH =====
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    // ===== REGISTER (POST) -> simpan ke tabel 'user' =====
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'username'     => 'required|alpha_dash|min:3|max:50|unique:user,username',
            'email'        => 'nullable|email|max:100|unique:user,email',
            'password'     => 'required|min:6|confirmed',
        ]);

        User::create([
            'nama_lengkap'   => $validated['nama_lengkap'],
            'username'       => $validated['username'],
            'email'          => $validated['email'] ?? null,
            'password'       => Hash::make($validated['password']), // <-- HASH!
            'level'          => 'user',
            'foto'           => null,
            'status_aktif'   => 1,
            'tanggal_daftar' => now(),
        ]);

        return redirect('/login')->with('status', 'Registrasi berhasil. Silakan login.');
    }

    // ===== LOGIN (POST) =====
    public function login(Request $request)
{
    $credentials = $request->validate([
        'username' => ['required','string'], // diisi username atau email
        'password' => ['required','string'],
    ]);

    $remember = $request->boolean('remember');

    // cek apakah input username sebenarnya email
    $field = filter_var($credentials['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    if (Auth::attempt([
        $field    => $credentials['username'],
        'password'=> $credentials['password']
    ], $remember)) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    return back()
        ->withErrors(['username' => 'Username atau password salah.'])
        ->onlyInput('username');
}


    // ===== LOGOUT (POST) =====
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('status', 'Anda sudah logout.');
    }

    // ===== UPDATE FOTO PROFIL (POST) =====
    // Dipanggil dari sidebar: route('profile.photo.update')
public function updatePhoto(Request $request)
{
    $request->validate([
        'foto' => ['required','image','mimes:jpg,jpeg,png,webp','max:4096'], // naikkan ke 4MB
    ]);

    $user = $request->user();

    // hapus lama kalau ada
    if ($user->foto && Storage::disk('public')->exists('avatars/'.$user->foto)) {
        Storage::disk('public')->delete('avatars/'.$user->foto);
    }

    // simpan baru
    $file = $request->file('foto');
    $name = 'u'.$user->id.'_'.time().'.'.$file->getClientOriginalExtension();
    $file->storeAs('avatars', $name, 'public');

    $user->foto = $name;
    $user->save();
    $user->refresh(); // refresh data user di session

    // cek benar-benar tersimpan
    if (!Storage::disk('public')->exists('avatars/'.$name)) {
        return back()->withErrors(['foto' => 'File gagal tersimpan di storage/avatars.']);
    }

    return back()->with('status', 'Foto profil berhasil diperbarui.');
}
}
