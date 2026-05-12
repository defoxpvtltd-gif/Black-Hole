<?php
declare(strict_types=1);

namespace App\Auth;

use App\Http\Redirect;
use App\Support\Flash;
use App\Support\Url;

final class AuthService
{
    private const SESSION_USER_ID = '__auth_user_id';

    public function __construct(private readonly UserRepository $users = new UserRepository())
    {
    }

    public function user(): ?array
    {
        $userId = $_SESSION[self::SESSION_USER_ID] ?? null;
        if (!is_int($userId) && !ctype_digit((string) $userId)) {
            return null;
        }

        return $this->users->findById((int) $userId);
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function attempt(string $email, string $password): bool
    {
        $user = $this->users->findByEmail($email);
        if (!$user || !isset($user['password_hash'])) {
            return false;
        }

        if (!password_verify($password, (string) $user['password_hash'])) {
            return false;
        }

        $_SESSION[self::SESSION_USER_ID] = (int) $user['id'];
        session_regenerate_id(true);
        return true;
    }

    public function register(string $name, string $email, string $password): array
    {
        return $this->users->create($name, $email, $password);
    }

    public function loginUser(array $user): void
    {
        $_SESSION[self::SESSION_USER_ID] = (int) ($user['id'] ?? 0);
        session_regenerate_id(true);
    }

    public function logout(): void
    {
        unset($_SESSION[self::SESSION_USER_ID], $_SESSION['chat_history']);
        session_regenerate_id(true);
    }

    public function requireGuest(): void
    {
        if ($this->check()) {
            Flash::set('info', 'You are already signed in.');
            Redirect::to(Url::to('/chat.php'));
        }
    }

    public function requireAuth(): void
    {
        if (!$this->check()) {
            Flash::set('error', 'Please login to continue.');
            Redirect::to(Url::to('/login.php'));
        }
    }
}
