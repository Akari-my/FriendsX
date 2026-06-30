<?php

declare(strict_types=1);

namespace Akari_my\FriendsX\migration;

use Akari_my\FriendsX\Main;
use pocketmine\utils\Config;

class MigrationManager {

    public static function migrate(Main $plugin): void {
        $dataDir = $plugin->getDataFolder() . "data/";
        if (!is_dir($dataDir)) {
            @mkdir($dataDir, 0777, true);
        }

        $timestamp = date('Ymd_His');
        $backupDir = $dataDir . 'backup_' . $timestamp . '/';
        @mkdir($backupDir, 0777, true);

        $files = ['friends.json', 'friends.yml', 'blocked.yml', 'last_seen.yml', 'requests.json'];
        foreach ($files as $f) {
            $path = $dataDir . $f;
            if (file_exists($path)) {
                @copy($path, $backupDir . $f);
            }
        }

        // Load legacy friends data (prefer JSON, fallback to YAML)
        $raw = [];
        $jsonFile = $dataDir . 'friends.json';
        $ymlFile = $dataDir . 'friends.yml';

        if (is_readable($jsonFile)) {
            $contents = file_get_contents($jsonFile);
            $raw = json_decode($contents, true) ?? [];
        } elseif (is_readable($ymlFile)) {
            $cfg = new Config($ymlFile, Config::YAML);
            $raw = $cfg->getAll();
        }

        // Normalize and convert to new structure: players => [{id, display, type}, ...]
        $converted = [];
        foreach ($raw as $player => $friends) {
            $playerKey = strtolower((string)$player);
            $converted[$playerKey] = [];
            if (!is_array($friends)) continue;
            foreach ($friends as $f) {
                $display = (string)$f;
                $converted[$playerKey][] = [
                    'id' => strtolower(trim($display)),
                    'display' => $display,
                    'type' => 'name'
                ];
            }
        }

        $outFile = $dataDir . 'friends_v2.json';
        file_put_contents($outFile, json_encode($converted, JSON_PRETTY_PRINT));

        $plugin->getLogger()->info("Friends migration completed. Backup created at: {$backupDir}. New file: {$outFile}");
    }
}
