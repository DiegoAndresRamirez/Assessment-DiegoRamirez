<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Faker\Factory as Faker;


class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Crear el rol 'doctor' si no existe
        $role = Role::firstOrCreate(['name' => 'doctor']);

        // Crear 10 usuarios de tipo 'doctor'
        for ($i = 0; $i < 10; $i++) {
            $user = User::create([
                'name' => $faker->firstName,
                'lastname' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'), // Contraseña genérica
                'speciality' => $faker->randomElement(['Cardiology', 'Neurology', 'Orthopedics', 'Pediatrics']),
                'age' => $faker->numberBetween(30, 65),
            ]);

            // Asignar el rol 'doctor' al usuario
            $user->assignRole($role);
        }

        $this->command->info('Se han creado 10 doctores y se les ha asignado el rol "doctor".');
    }
}
