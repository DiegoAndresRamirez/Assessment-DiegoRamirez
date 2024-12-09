<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Faker\Factory as Faker;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Crear el rol 'patient' si no existe
        $role = Role::firstOrCreate(['name' => 'patient']);

        // Crear 10 usuarios de tipo 'patient'
        for ($i = 0; $i < 10; $i++) {
            $user = User::create([
                'name' => $faker->firstName,
                'lastname' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'), // Puedes cambiar la contraseña si es necesario
                'speciality' => $faker->randomElement(['General', 'Pediatrics', 'Dermatology']),
                'age' => $faker->numberBetween(18, 80),
            ]);

            // Asignar el rol 'patient' al usuario
            $user->assignRole($role);
        }

        $this->command->info('Se han creado 10 pacientes y se les ha asignado el rol "patient".');
    }
}
