<?php

use RestAPI\Models\Model;

namespace RestAPI\Models;

class User extends Model
{
    public function getAllUsers($page, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        $countQuery = $this->execute("SELECT COUNT(id) FROM users")->fetch_row();

        return [
            'data' => $this->execute('SELECT id AS iduser, name, email FROM users LIMIT ?, ?', ['i', 'i'], [$offset, $perPage])->get_result()->fetch_all(MYSQLI_ASSOC),
            'totalPages' => $this->getTotalPages($countQuery[0], $perPage)
        ];
    }

    public function getUserById(int $id)
    {
        if (!$id) {
            return false;
        }

        $query = $this->execute('SELECT id AS iduser, name, email, password FROM users WHERE id = ?', ['i'], [$id]);

        return $query->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserByEmail(string $email)
    {
        if (!$email) {
            return false;
        }

        $query = $this->execute('SELECT id AS iduser, name, email, password FROM users WHERE email = ?', ['s'], [$email]);

        return $query->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserByToken(string $token)
    {
        $query = $this->execute(
            'SELECT *
                                FROM users usr
                                INNER JOIN access_tokens act
                                   ON act.user_id = usr.id
                                WHERE act.token = ?',
            ['s'],
            [$token]
        );

        return $query->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function createUser(array $data)
    {
        $query = $this->execute(
            "INSERT INTO users
                (name, email, password)
            VALUES (?, ?, ?)",
            ['s', 's', 's'],
            [$data['name'], $data['email'], password_hash($data['password'], PASSWORD_BCRYPT)]
        );

        return $this->getUserById($query->insert_id);
    }

    public function updateUser(int $id, array $data)
    {
        $query = $this->execute(
            "UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?",
            ['s', 's', 's', 'i'],
            [$data['name'], $data['email'], password_hash($data['password'], PASSWORD_BCRYPT), $id]
        );

        return $this->getUserById($id);
    }

    public function destroyUser(int $id)
    {
        $query = $this->execute('DELETE FROM users WHERE id = ?', ['i'], [$id]);

        if ($query->affected_rows) {
            return true;
        }

        return false;
    }

    public function authenticateUser(int $user_id, string $token)
    {
        // Delete previous token
        $deleteQuery = $this->execute('DELETE FROM access_tokens WHERE user_id = ?', ['i'], [$user_id]);

        // Create a new token
        $query = $this->execute('INSERT INTO access_tokens (user_id, token) VALUES (?, ?)', ['i', 's'], [$user_id, $token]);

        if ($query->affected_rows) {
            return $token;
        }

        return false;
    }

    public function validateToken(string $token)
    {
        $query = $this->execute(
            'SELECT token
                                    FROM access_tokens
                                    WHERE token = ?
                                        AND TIMESTAMPDIFF(HOUR, created_at, NOW()) < 24',
            ['s'],
            [$token]
        )
            ->get_result()->fetch_all(MYSQLI_ASSOC);

        if (count($query)) {
            return true;
        }

        return false;
    }

    public function drinkWater(int $id, int $drinkMl)
    {
        $query = $this->execute('INSERT INTO user_drink (user_id, drink_ml) VALUES (?, ?)', ['i', 'i'], [$id, $drinkMl]);

        if ($query->affected_rows) {
            return $this->getDrinkWater($id);
        } else {    
            return false;
        }
    }
    
    public function getDrinkWater(int $id)
    {
        $sum = $this->execute('SELECT SUM(drink_ml) AS total FROM user_drink WHERE user_id = ?', ['i'], [$id])->get_result()->fetch_assoc();
        
        return (int) $sum['total'] ?? 0;
    }
}
