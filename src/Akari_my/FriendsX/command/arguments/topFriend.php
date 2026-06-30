<?php

declare(strict_types=1);

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\command\CommandSender;

class topFriend implements SubCommand {

    public function execute(CommandSender $sender, array $args): void {
        $plugin = Main::getInstance();
        $data = $plugin->getFriendsManager()->getData();

        $allPlayers = [];

        if ($data instanceof \Akari_my\FriendsX\data\SQLiteProvider) {
            $pdo = new \ReflectionProperty($data, 'pdo');
            $pdo->setAccessible(true);
            $conn = $pdo->getValue($data);
            $rows = $conn->query('SELECT owner, COUNT(friend) as cnt FROM friends GROUP BY owner ORDER BY cnt DESC LIMIT 10');
            foreach ($rows as $row) {
                $allPlayers[] = ["name" => $row["owner"], "count" => (int)$row["cnt"]];
            }
        } elseif ($data instanceof \Akari_my\FriendsX\data\JSONProvider) {
            $refl = new \ReflectionProperty($data, 'data');
            $refl->setAccessible(true);
            $raw = $refl->getValue($data);
            foreach ($raw as $name => $friends) {
                $allPlayers[] = ["name" => $name, "count" => count($friends)];
            }
        } elseif ($data instanceof \Akari_my\FriendsX\data\YAMLProvider) {
            $refl = new \ReflectionProperty($data, 'config');
            $refl->setAccessible(true);
            $raw = $refl->getValue($data)->getAll();
            foreach ($raw as $name => $friends) {
                $allPlayers[] = ["name" => $name, "count" => count($friends)];
            }
        }

        if (!empty($allPlayers)) {
            usort($allPlayers, fn($a, $b) => $b["count"] <=> $a["count"]);
            $allPlayers = array_slice($allPlayers, 0, 10);
        }

        if (empty($allPlayers)) {
            $sender->sendMessage(LangManager::get("top-empty"));
            return;
        }

        $lines = [];
        $i = 1;
        foreach ($allPlayers as $entry) {
            $lines[] = "§7{$i}. §e{$entry["name"]} §7- §f{$entry["count"]} " . LangManager::raw("top-friends-count");
            $i++;
        }

        $sender->sendMessage(LangManager::get("top-header"));
        $sender->sendMessage(implode("\n", $lines));
    }
}
