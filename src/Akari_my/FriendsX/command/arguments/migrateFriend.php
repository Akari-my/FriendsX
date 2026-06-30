<?php

declare(strict_types=1);

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\migration\MigrationManager;
use pocketmine\command\CommandSender;

class migrateFriend implements SubCommand {

    public function execute(CommandSender $sender, array $args): void {
        $plugin = Main::getInstance();

        if (!$sender->hasPermission("friendsx.migrate")) {
            $sender->sendMessage("§cYou don't have permission to run migration.");
            return;
        }

        $sender->sendMessage("§eStarting friends migration... Backup will be created.");
        MigrationManager::migrate($plugin);
        $sender->sendMessage("§aMigration completed. Check console for details.");

        // If config.storage is sqlite, attempt to import the produced friends_v2.json into SQLite now
        $config = $plugin->getConfig();
        if (strtolower((string)$config->get('storage', 'yaml')) === 'sqlite') {
            $provider = $plugin->getFriendsManager()->getData();
            if (method_exists($provider, 'importFromV2')) {
                $v2 = $plugin->getDataFolder() . "data/friends_v2.json";
                if (is_readable($v2)) {
                    try {
                        $count = $provider->importFromV2($v2);
                        $sender->sendMessage("§aImported {$count} friend entries into SQLite.");
                    } catch (\Throwable $e) {
                        $sender->sendMessage("§cImport failed: " . $e->getMessage());
                    }
                } else {
                    $sender->sendMessage("§eNo friends_v2.json file found to import.");
                }
            } else {
                $sender->sendMessage("§eCurrent provider does not support importFromV2.");
            }
        }
    }
}
