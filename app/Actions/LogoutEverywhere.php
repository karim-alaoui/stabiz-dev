<?php


namespace App\Actions;


use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;

/**
 * Completely logout an user or staff
 * It will delete all the access tokens and refresh tokens
 * Class Logout
 * @package App\Actions
 */
class LogoutEverywhere
{
    /**
     * @param User|Staff $user
     */
    public static function execute(User|Staff $user)
    {
        $tokens = $user->tokens->pluck('id');
        DB::transaction(function () use ($tokens) {
            if (count($tokens)) {
                Token::whereIn('id', $tokens)
                    ->update(['revoked' => 1]);

                RefreshToken::whereIn('access_token_id', $tokens)
                    ->update(['revoked' => 1]);
            }
        });
    }
}
