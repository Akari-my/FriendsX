<?php

namespace Akari_my\FriendsX;

use Akari_my\FriendsX\command\FriendsCommand;
use Akari_my\FriendsX\data\DataProvider;
use Akari_my\FriendsX\data\JSONProvider;
use Akari_my\FriendsX\data\YAMLProvider;
use Akari_my\FriendsX\listener\PlayerJoinListener;
use Akari_my\FriendsX\manager\FriendsManager;
use Akari_my\FriendsX\manager\LangManager;
use Akari_my\FriendsX\manager\RequestManager;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase {

    private static Main $instance;

    private DataProvider $provider;
    private FriendsManager $friendsManager;
    private RequestManager $requestManager;

    public function onEnable(): void {
        self::$instance = $this;

        @mkdir($this->getDataFolder() . "data/");
        @mkdir($this->getDataFolder() . "lang/");
        @mkdir($this->getDataFolder() . "sql/");

        $this->saveResource("config.yml");
        $this->saveResource("lang/eng.yml");
        $this->saveResource("lang/ita.yml");

        LangManager::init($this);

        $config = $this->getConfig();
        $storageType = strtolower($config->get("storage", "yaml"));

        switch ($storageType) {
            case "json":
                $this->provider = new JSONProvider($this);
                break;

            case "yaml":
                $this->provider = new YAMLProvider($this);
                break;
            default:
                throw new \InvalidArgumentException("Invalid storage type: $storageType");
        }

        $this->friendsManager = new FriendsManager($this->provider);
        $this->requestManager = new RequestManager($this);

        $this->getServer()->getPluginManager()->registerEvents(new PlayerJoinListener($this), $this);
        $this->getServer()->getCommandMap()->register("friend", new FriendsCommand($this));
    }

    public function getFriendsManager(): FriendsManager {
        return $this->friendsManager;
    }

    public function getRequestManager(): RequestManager {
        return $this->requestManager;
    }

    public static function getInstance(): self {
        return self::$instance;
    }
}