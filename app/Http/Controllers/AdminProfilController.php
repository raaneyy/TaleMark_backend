<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Halaman Setting Akun (profil admin)
 * View: resources/views/admin/profil.blade.php
 * Memakai DB::table('admin') dan session('admin_id'), sama seperti AdminAuthController.
 *
 * Kolom tabel `admin` yang dipakai:
 *   id_admin, nama, username, email, password, foto
 */
class AdminProfilController extends Controller
{
    /**
     * Ambil data admin yang sedang login.
     */
    private function adminLogin(Request $request)
    {
        return DB::table('admin')
            ->where('id_admin', $request->session()->get('admin_id'))
            ->first();
    }

    /**
     * Tampilkan halaman Setting Akun.
     */
    public function show(Request $request)
    {
        $admin = $this->adminLogin($request);

        if (!$admin) {
            return redirect()->route('admin.login')
                ->with('error', 'Silakan masuk terlebih dahulu.');
        }

        return view('admin.profil', compact('admin'));
    }

    /**
     * Simpan perubahan profil (nama, username, email, foto, password).
     */
    public function update(Request $request)
    {
        $admin = $this->adminLogin($request);

        if (!$admin) {
            return redirect()->route('admin.login')
                ->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $data = $request->validate([
            'nama'          => ['required', 'string', 'max:100'],
            'username'      => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9_.-]+$/',
                                Rule::unique('admin', 'username')->ignore($admin->id_admin, 'id_admin')],
            'email'         => ['required', 'email', 'max:150',
                                Rule::unique('admin', 'email')->ignore($admin->id_admin, 'id_admin')],
            'foto'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'password_lama' => ['nullable', 'required_with:password', 'string'],
            'password'      => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'nama.required'               => 'Nama wajib diisi.',
            'username.required'           => 'Nama pengguna wajib diisi.',
            'username.regex'              => 'Hanya huruf, angka, titik, garis bawah, dan tanda hubung.',
            'username.unique'             => 'Nama pengguna sudah dipakai.',
            'email.required'              => 'Email wajib diisi.',
            'email.email'                 => 'Format email tidak valid.',
            'email.unique'                => 'Email sudah dipakai akun lain.',
            'foto.image'                  => 'File harus berupa gambar.',
            'foto.mimes'                  => 'Foto harus berformat JPG, PNG, atau WEBP.',
            'foto.max'                    => 'Ukuran foto maksimal 2 MB.',
            'password_lama.required_with' => 'Masukkan password saat ini.',
            'password.min'                => 'Password baru minimal 8 karakter.',
            'password.confirmed'          => 'Konfirmasi password tidak sama.',
        ]);

        $perubahan = [
            'nama'     => $data['nama'],
            'username' => $data['username'],
            'email'    => $data['email'],
        ];

        // Ganti password (hanya jika diisi)
        if (!empty($data['password'])) {
            if (!Hash::check($data['password_lama'], $admin->password)) {
                return back()
                    ->withErrors(['password_lama' => 'Password saat ini salah.'])
                    ->withInput($request->except('password_lama', 'password', 'password_confirmation'));
            }
            $perubahan['password'] = Hash::make($data['password']);
        }

        // Ganti foto profil (disimpan di storage/app/public/admin)
        if ($request->hasFile('foto')) {
            if (!empty($admin->foto)) {
                Storage::disk('public')->delete($admin->foto);
            }
            $perubahan['foto'] = $request->file('foto')->store('admin', 'public');
        }

        DB::table('admin')
            ->where('id_admin', $admin->id_admin)
            ->update($perubahan);

        return redirect()->route('admin.profil')
            ->with('success', 'Profil berhasil disimpan.');
    }
}