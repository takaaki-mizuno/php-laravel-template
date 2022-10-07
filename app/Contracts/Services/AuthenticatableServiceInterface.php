<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Models\AuthenticatableBase;

interface AuthenticatableServiceInterface extends BaseServiceInterface
{
    /**
     * @return ?AuthenticatableBase
     */
    public function signInById(int $id): ?AuthenticatableBase;

    /**
     * @param array $input
     */
    public function signIn($input): AuthenticatableBase;

    /**
     * @param array $input
     *
     * @return \App\Models\AuthenticatableBase
     */
    public function signUp($input);

    /**
     * @param string $email
     *
     * @return bool
     */
    public function sendPasswordReset($email);

    /**
     * @return bool
     */
    public function signOut();

    /**
     * @return bool
     */
    public function resignation();

    /**
     * @param \App\Models\AuthenticatableBase $user
     */
    public function setUser($user);

    /**
     * @return \App\Models\AuthenticatableBase
     */
    public function getUser();

    /**
     * @param string $email
     */
    public function sendPasswordResetEmail($email);

    /**
     * @param string $email
     * @param string $password
     * @param string $token
     *
     * @return bool
     */
    public function resetPassword($email, $password, $token);

    /**
     * @return bool
     */
    public function isSignedIn();

    /**
     * @return string
     */
    public function getGuardName();

    /**
     * @param array  $input
     * @param string $imageUrl
     *
     * @return \App\Models\AuthenticatableBase
     */
    public function createWithImageUrl($input, $imageUrl);
}
