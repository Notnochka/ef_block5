<?php

namespace App;

interface UserRepository {
    public function findUserByEmail(string $email): ?User;
}