<?php

namespace Pondra\PhpApiStarterKit\Repositories;

use PDO;
use Pondra\PhpApiStarterKit\Models\EmailQueue;

class EmailQueueRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getAllEmailQueue(array $filters = [], int $limit = 0): ?array
    {
        $sql = 'SELECT id, name, email, email_type, token, status FROM email_queue';
        $params = [];
        $paramCounter = 1;

        $conditions = [];
        if (!empty($filters)) {
            foreach ($filters as $column => $value) {
                if ($column === 'status' && is_array($value)) {
                    $placeholders = implode(',', array_fill(0, count($value), '?'));
                    $conditions[] = "`{$column}` IN ({$placeholders})";

                    foreach ($value as $statusValue) {
                        $params[$paramCounter++] = $statusValue;
                    }
                } else {
                    $conditions[] = "`{$column}` = ?";
                    $params[$paramCounter++] = $value;
                }
            }
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $bindLimit = false;
        if (!empty($limit)) {
            $sql .= ' LIMIT ?';
            $bindLimit = true;

            $params[$paramCounter] = $limit;
            
        }

        $statement = $this->connection->prepare($sql);

        // Bind parameters using bindValue for type safety
        foreach ($params as $index => $value) {
            // Determine the PDO type. Default to string, but ensure int for LIMIT
            $pdoType = PDO::PARAM_STR;
            if ($bindLimit && $index === $paramCounter) { // If it's the limit parameter
                $pdoType = PDO::PARAM_INT;
            }
            // You might want more sophisticated type guessing based on $value, e.g., is_int($value)
            $statement->bindValue($index, $value, $pdoType);
        }

        $statement->execute();

        try {
            if ($rows = $statement->fetchAll()) {
                $emailQueue = [];

                foreach ($rows as $row) {
                    $eQ = new EmailQueue();
                    $eQ->id = $row['id'];
                    $eQ->name = $row['name'];
                    $eQ->email = $row['email'];
                    $eQ->email_type = $row['email_type'];
                    $eQ->token = $row['token'];
                    $eQ->status = $row['status'];

                    $emailQueue[] = $eQ;
                }

                return $emailQueue;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function save(EmailQueue $emailQueue): EmailQueue
    {
        $statement = $this->connection->prepare('INSERT INTO email_queue(id, name, email, email_type, token, created_at) VALUES (?, ?, ?, ?, ?, ?)');
        $statement->execute([
            $emailQueue->id,
            $emailQueue->name,
            $emailQueue->email,
            $emailQueue->email_type,
            $emailQueue->token,
            $emailQueue->createdAt->format('Y-m-d H:i:s')
        ]);

        return $emailQueue;
    }
}