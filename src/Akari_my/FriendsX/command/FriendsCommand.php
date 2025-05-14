<?php

namespace Akari_my\FriendsX\command;

use Akari_my\FriendsX\command\arguments\acceptFriend;
use Akari_my\FriendsX\command\arguments\addFriend;
use Akari_my\FriendsX\command\arguments\denyFriend;
use Akari_my\FriendsX\command\arguments\listFriend;
use Akari_my\FriendsX\command\arguments\removeFriend;
use Akari_my\FriendsX\Main;
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

    public function __construct(Main $plugin) {
        parent::__construct("friend", "Friends management", "/friend <add|list|remove>");

        $this->plugin = $plugin;

        $this->addFriend = new addFriend();
        $this->listFriend = new listFriend();
        $this->removeFriend = new removeFriend();
        $this->acceptFriend = new acceptFriend();
        $this->denyFriend = new denyFriend();

        $this->setPermission("friendsx.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§c✖ Only players can use this command!");
            return true;
        }

        if (!isset($args[0])) {
            $sender->sendMessage("§cUse: /friend <add|list|remove|accept|deny>");
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
            default:
                $sender->sendMessage("§cUnknown subcommand. Use: /friend <add|list|remove|accept|deny>");
                break;
        }

        return true;
    }

    public function getOwningPlugin(): Plugin{
        return $this->plugin;
    }
}