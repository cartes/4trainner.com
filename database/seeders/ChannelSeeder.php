<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Channel;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $professors = User::role('profesor')->get();

        // Si no hay profesores, toma los primeros 3 usuarios cualquiera como fallback
        if ($professors->isEmpty()) {
            $professors = User::take(3)->get();
        }

        foreach ($professors as $professor) {
            // Crea 1 o 2 canales por profesor
            Channel::factory(rand(1, 2))->create([
                'user_id' => $professor->id,
                'name' => 'Canal de Entrenamientos ' . $professor->name,
            ]);
        }
    }
}
