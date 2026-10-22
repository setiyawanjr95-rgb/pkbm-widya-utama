<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\Registration;
use App\Models\Student;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    /** Isi otomatis dari data pendaftaran online (?pendaftar=id). */
    protected function afterFill(): void
    {
        $registration = Registration::with('program')->find(request()->query('pendaftar'));
        if (! $registration) {
            return;
        }
        $paket = collect(array_keys(Student::PAKET))
            ->first(fn ($p) => str_contains(strtolower($registration->program?->name ?? ''), strtolower($p)));

        $this->form->fill([
            'name' => $registration->name,
            'phone' => $registration->phone,
            'email' => $registration->email,
            'address' => $registration->address,
            'paket' => $paket,
            'active' => true,
        ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'] ?: null,
                'email' => $data['email'] ?: null,
                'password' => $data['password'],
                'role' => 'student',
            ]);

            return Student::create([...collect($data)->except(['name', 'username', 'email', 'password'])->all(), 'user_id' => $user->id]);
        });
    }
}
