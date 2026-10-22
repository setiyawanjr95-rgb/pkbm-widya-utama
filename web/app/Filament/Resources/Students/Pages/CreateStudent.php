<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\Student;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

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
