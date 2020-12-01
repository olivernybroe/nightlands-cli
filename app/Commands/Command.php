<?php

namespace App\Commands;

use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use LaravelZero\Framework\Commands\Command as BaseCommand;

abstract class Command extends BaseCommand
{
    protected function selectUsers(Collection $users = null, string $question = 'Select user'): Collection
    {
        if ($this->hasOption('user') && ($selected = $this->option('user')) !== []) {
            return User::query()->whereKey($selected)->get();
        }

        $users = $users ?? User::all();

        $choices = $users->collect()
            ->keyBy->getEmail()
            ->map->getDisplayName()
            ->prepend('All users', 'all')
            ->all();

        $selected = $this->choice(
            $question,
            $choices,
            'all',
            1,
            true,
        );

        if (in_array('all', $selected, true)) {
            return $users;
        }

        return $users->filter(fn(User $user) => in_array($user->getEmail(), $selected, true));
    }

    protected function getFirstToken(): string
    {
        return User::query()->whereNotNull('last_issued_token')
            ->pluck('last_issued_token')
            ->first();
    }

    protected function notify(string $description): void
    {
        $this->__call('notify', [
            "Nightlands CLI",
            $description,
            resource_path('icon.png')
        ]);
    }

    protected function userNotify(User $user, string $description): void
    {
        Log::info("User notification send! [$description]");
        $this->notify("{$user->getDisplayName()}: \n{$description}");
    }

    protected function userInfo(User $user, string $description): void
    {
        $this->info("[{$user->getDisplayName()}] {$description}");
    }
}
