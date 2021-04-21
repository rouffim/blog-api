<?php

namespace App\Providers;

use App\Enums\PermissionEnum;
use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        /*
         * ARTICLES
         */

        Gate::define(PermissionEnum::AddArticle, function (User $user) {
            return $user->tokenCan(PermissionEnum::AddArticle)
                ? Response::allow()
                : Response::deny("You don't have the permission to add an article.");
        });

        Gate::define(PermissionEnum::EditOwnArticle, function (User $user, Article $article) {
            return $user->id === $article->users_id || $user->tokenCan(PermissionEnum::EditAllArticle)
                ? Response::allow()
                : Response::deny("You don't have the permission to edit this article.");
        });

        Gate::define(PermissionEnum::RemoveOwnArticle, function (User $user, Article $article) {
            return $user->id === $article->users_id || $user->tokenCan(PermissionEnum::RemoveAllArticle)
                ? Response::allow()
                : Response::deny("You don't have the permission to delete this article.");
        });

        /*
         * USERS
         */

        Gate::define(PermissionEnum::ChangeRoleUser, function (User $user, User $userToChange) {
            return $user->id !== $userToChange->id && $user->tokenCan(PermissionEnum::ChangeRoleUser)
                ? Response::allow()
                : Response::deny("You don't have the permission to change the role of this user.");
        });

        Gate::define(PermissionEnum::RemoveUser, function (User $user, User $userToDelete) {
            return $user->id === $userToDelete->id || $user->tokenCan(PermissionEnum::RemoveUser)
                ? Response::allow()
                : Response::deny("You don't have the permission to delete this user.");
        });
    }
}
