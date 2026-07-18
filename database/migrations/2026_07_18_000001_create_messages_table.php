<?php

declare(strict_types=1);

use Tavp\Core\Database\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $this->db->execute("
            CREATE TABLE IF NOT EXISTS messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(191) NOT NULL,
                email VARCHAR(191) NOT NULL,
                subject VARCHAR(255) DEFAULT '',
                body TEXT NOT NULL,
                status VARCHAR(32) NOT NULL DEFAULT 'unread',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT NULL,
                INDEX idx_status (status),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    public function down(): void
    {
        $this->db->execute('DROP TABLE IF EXISTS messages');
    }
};
