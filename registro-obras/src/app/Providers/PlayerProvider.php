<?php

namespace App\Providers;

use Illuminate\Support\Str;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use App\Models\SADAIC\Interprete;

class PlayerProvider extends EloquentUserProvider
{
    public function retrieveById($identifier)
    {
        return $this->retrieveByCredentials([
            'player->id' => $identifier
        ]);
    }

    public function retrieveByCredentials(array $fields)
    {
        if (empty($fields)) {
            return;
        }

        // Consultamos sobre la BBDD vieja
        $query = Player::query();

        if (array_key_exists('player_id', $fields)) {
            $query->where('interpreteid', $fields['player_id']);
        }

        if (array_key_exists('email', $fields)) {
            $query->where('email', $fields['email']);
        }

        $interprete = $query->first();

        if ($interprete) {
            $player = Player::firstOrCreate(
                ['player_id' => $interprete->interpreteid],
                ['email' => $interprete->email]
            );

            return $player;
        }
    }

    public function validateCredentials(Authenticatable $player, array $credentials)
    {
        // Recibimos un objeto del tipo Player pero lo validamos contra
        // Interprete
        $interprete = Interprete::where('interpreteid', $player->player_id)
            ->first(['interpreteid', 'clave']);

        if ($interprete->interpreteid != $credentials['player_id']) {
            return false;
        }

        $passwordHash = hash('sha512', $credentials['password'] . env('SADAIC_HASH'));

        if ($passwordHash != $interprete->clave) {
            return false;
        }

        return true;
    }
}