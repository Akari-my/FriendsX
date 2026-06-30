<?php

declare(strict_types=1);

namespace Akari_my\FriendsX\data;

use Akari_my\FriendsX\Main;
use PDO;

class SQLiteProvider implements DataProvider {

    private PDO $pdo;
    private string $file;

    public function __construct(private Main $plugin) {
        $this->file = $plugin->getDataFolder() . "data/friends.db";
        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        $this->pdo = new PDO('sqlite:' . $this->file);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->initSchema();
    }

    private function initSchema(): void {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS friends (
            owner TEXT NOT NULL,
            friend TEXT NOT NULL,
            PRIMARY KEY(owner, friend)
        );");
    }

    public function load(): void {
        // SQLite is ready on construct; nothing to load into memory
    }

    public function save(): void {
        // Changes are applied immediately with transactions
    }

    public function getFriends(string $player): array {
        $playerKey = strtolower(trim($player));
        $stmt = $this->pdo->prepare('SELECT friend FROM friends WHERE owner = :owner');
        $stmt->execute([':owner' => $playerKey]);
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $rows ?: [];
    }

    public function setFriends(string $player, array $friends): void {
        $playerKey = strtolower(trim($player));
        $this->pdo->beginTransaction();
        $del = $this->pdo->prepare('DELETE FROM friends WHERE owner = :owner');
        $del->execute([':owner' => $playerKey]);

        $ins = $this->pdo->prepare('INSERT OR IGNORE INTO friends (owner, friend) VALUES (:owner, :friend)');
        foreach ($friends as $f) {
            $ins->execute([':owner' => $playerKey, ':friend' => strtolower(trim((string)$f))]);
        }

        $this->pdo->commit();
    }

    /**
     * Import data from friends_v2.json produced by MigrationManager.
     * Returns the number of friend entries imported.
     *
     * @param string $file
     * @return int
     * @throws \RuntimeException
     */
    public function importFromV2(string $file): int {
        if (!is_readable($file)) {
            throw new \RuntimeException("File not readable: {$file}");
        }

        $contents = file_get_contents($file);
        $data = json_decode($contents, true);
        if (!is_array($data)) {
            throw new \RuntimeException("Invalid or empty JSON in: {$file}");
        }

        $total = 0;
        foreach ($data as $owner => $entries) {
            if (!is_array($entries)) continue;
            $friends = [];
            foreach ($entries as $entry) {
                if (!is_array($entry) || !isset($entry['id'])) continue;
                $friends[] = strtolower(trim((string)$entry['id']));
                $total++;
            }
            $friends = array_values(array_unique($friends));
            $this->setFriends($owner, $friends);
        }

        return $total;
    }
}
