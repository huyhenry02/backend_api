<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\PasswordResetToken;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\Interfaces\UserInterface;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class UserRepository extends BaseRepository implements UserInterface
{

    /**
     * getModel
     *
     * @return string
     */
    public function getModel(): string
    {
        return User::class;
    }

    /**
     * @param User|Authenticatable $user
     * @param string $password
     * @return bool
     */
    public function changePassword(User|Authenticatable $user, string $password): bool
    {
        return $user->update([
            'password' => $password,
        ]);

    }

    /**
     * @param string $email
     * @return array|null
     */
    public function findUserAndSendMail(string $email): ?array
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return null;
        }
        $passwordReset = PasswordResetToken::updateOrCreate(
            ['email' => $email],
            ['token' => Str::random(60)]
        );
        return [
            'user' => $user,
            'token' => $passwordReset['token'],
        ];
    }

    /**
     * @param string $token
     * @param string $newPassword
     * @return array
     */
    public function resetPasswordWithToken(string $token, string $newPassword): array
    {
        $resetToken = PasswordResetToken::where('token', $token)->firstOrFail();
        if (Carbon::parse($resetToken->created_at)->addMinutes(60)->isPast()) {
            $resetToken->delete();
        }
        $email = $resetToken->email;
        $user = User::where('email', $email)->first();
        $user->update([
            'password' => $newPassword,
        ]);
        return [
            'resetToken' => $resetToken,
        ];
    }

}
