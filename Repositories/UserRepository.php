<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use App\Traits\Pageable;
use Hash;
use Illuminate\Database\Eloquent\Builder;

class UserRepository
{
    use Pageable;

    /**
     * create new user in db.
     *
     * @param  array  $input
     * @param  bool  $createCompany
     * @param  bool  $createProject
     * @return \App\Models\User|\Illuminate\Database\Eloquent\Model
     */
    public function create(array $input, $createCompany = false, $createProject = false)
    {
        $email = $input['email'] ?? null;
        $password = $input['password'] ?? false;
        $user = User::create([
            'email'       => $email,
            'name'        => $input['name'] ?? null,
            'lang'        => $input['lang'] ?? \Languages::getDefaultLanguage(),
            'referral_id' => $input['referral_id'] ?? null,
            'activated'   => $input['activated'] ?? true,
            'password'    => $password ? Hash::make($password) : null,
        ]);

        return $user;
    }

    /**
     * edit user info in db.
     *
     * @param  \App\Models\User  $user
     * @param  array  $input
     * @return User
     */
    public function update(User $user, array $input)
    {
        $user->fill($input);
        if (array_key_exists('password', $input)) {
            $user->forceFill([
                'password' => Hash::make($input['password']),
            ]);
        }
        $user->save();

        return $user;
    }

    public function syncRoles(User $user, array $rolesName)
    {
        $roles = Role::whereIn('name', $rolesName)->get();
        $user->syncRoles($roles);

        return true;
    }

    /**
     * get user by field.
     * @param $field
     * @param $value
     * @return User|Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getUserBy($field, $value)
    {
        return User::where($field, $value)->first();
    }

    /**
     * @param  \App\Models\User  $user
     * @param $companyName
     * @return bool
     */
    public function editCompany(User $user, $companyName)
    {
        $company = $user->company;
        if (! $company) {
            $company = Company::create(['name' => $companyName]);
            $user->company()->associate($company);
            $user->save();
        } else {
            $company->name = $companyName;
            $company->save();
        }

        return true;
    }

    /**
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function delete(User $user)
    {
        try {
            $company = $user->company;
            $user->delete();

            if ($company && $company->users()->count() === 0) {
                $company->delete();
            }
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return false;
        }

        return true;
    }
}
