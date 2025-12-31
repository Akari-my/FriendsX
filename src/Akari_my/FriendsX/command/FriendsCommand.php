<?php

namespace Akari_my\FriendsX\command;

use Akari_my\FriendsX\command\arguments\acceptFriend;
use Akari_my\FriendsX\command\arguments\addFriend;
use Akari_my\FriendsX\command\arguments\blockFriend;
use Akari_my\FriendsX\command\arguments\blockedFriend;
use Akari_my\FriendsX\command\arguments\denyFriend;
use Akari_my\FriendsX\command\arguments\listFriend;
use Akari_my\FriendsX\command\arguments\removeFriend;
use Akari_my\FriendsX\command\arguments\requestsFriend;
use Akari_my\FriendsX\command\arguments\settingsFriend;
use Akari_my\FriendsX\command\arguments\unblockFriend;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

class FriendsCommand extends Command implements PluginOwned {

    private Main $plugin;

    private addFriend $addFriend;
    private listFriend $listFriend;
    private removeFriend $removeFriend;
    private acceptFriend $acceptFriend;
    private denyFriend $denyFriend;
    private requestsFriend $requestsFriend;
    private settingsFriend $settingsFriend;
    private blockFriend $blockFriend;
    private unblockFriend $unblockFriend;
    private blockedFriend $blockedFriend;

    public function __construct(Main $plugin) {
        parent::__construct("friend", "Friends management", "/friend <add, list, remove, accept, deny, requests, settings, block, unblock, blocked>");

        $this->plugin = $plugin;

        $this->addFriend = new addFriend();
        $this->listFriend = new listFriend();
        $this->removeFriend = new removeFriend();
        $this->acceptFriend = new acceptFriend();
        $this->denyFriend = new denyFriend();
        $this->requestsFriend = new requestsFriend();
        $this->settingsFriend = new settingsFriend();
        $this->blockFriend = new blockFriend();
        $this->unblockFriend = new unblockFriend();
        $this->blockedFriend = new blockedFriend();

        $this->setPermission("friendsx.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage(LangManager::get("only-player-command"));
            return true;
        }

        if (!isset($args[0])) {
            $sender->sendMessage(LangManager::get("friend-usage"));
            return true;
        }

        $subCommand = strtolower(array_shift($args));

        switch ($subCommand) {
            case "add":
                $this->addFriend->execute($sender, $args);
                break;
            case "list":
                $this->listFriend->execute($sender, $args);
                break;
            case "remove":
                $this->removeFriend->execute($sender, $args);
                break;
            case "accept":
                $this->acceptFriend->execute($sender, $args);
                break;
            case "deny":
                $this->denyFriend->execute($sender, $args);
                break;
            case "requests":
                $this->requestsFriend->execute($sender, $args);
                break;
            case "settings":
                $this->settingsFriend->execute($sender, $args);
                break;
            case "block":
                $this->blockFriend->execute($sender, $args);
                break;
            case "unblock":
                $this->unblockFriend->execute($sender, $args);
                break;
            case "blocked":
                $this->blockedFriend->execute($sender, $args);
                break;
            default:
                $sender->sendMessage(LangManager::get("unknown-subcommand"));
                break;
        }

        return true;
    }

    public function getOwningPlugin(): Main {
        return $this->plugin;
    }
}