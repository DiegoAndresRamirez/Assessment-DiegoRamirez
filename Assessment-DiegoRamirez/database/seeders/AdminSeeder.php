<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Faker\Factory as Faker;
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Crear el rol 'admin' si no existe
        $role = Role::firstOrCreate(['name' => 'admin']);

        // Crear 3 usuarios con el rol 'admin'
        for ($i = 0; $i < 3; $i++) {
            $user = User::create([
                'name' => $faker->firstName,
                'lastname' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'), // Contraseña genérica
                'speciality' => 'Admin',
                'age' => $faker->numberBetween(25, 50),
            ]);

            // Asignar el rol 'admin' al usuario
            $user->assignRole($role);
        }

        $this->command->info('Se han creado 3 administradores y se les ha asignado el rol "admin".');
    }
}
