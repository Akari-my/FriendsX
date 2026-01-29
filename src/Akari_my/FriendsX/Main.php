<?php

namespace Akari_my\FriendsX;

use Akari_my\FriendsX\command\FriendsCommand;
use Akari_my\FriendsX\data\DataProvider;
use Akari_my\FriendsX\data\JSONProvider;
use Akari_my\FriendsX\data\YAMLProvider;
use Akari_my\FriendsX\listener\PlayerJoinListener;
use Akari_my\FriendsX\manager\BlockManager;
use Akari_my\FriendsX\manager\FriendsManager;
use Akari_my\FriendsX\manager\LangManager;
use Akari_my\FriendsX\manager\LastSeenManager;
use Akari_my\FriendsX\manager\RequestManager;
use Akari_my\FriendsX\manager\SettingsManager;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase {

    private static Main $instance;

    private DataProvider $provider;
    private FriendsManager $friendsManager;
    private RequestManager $requestManager;
    private SettingsManager $settingsManager;
    private BlockManager $blockManager;
    private LastSeenManager $lastSeenManager;
    private bool $formsEnabled = false;

    public function onEnable(): void {
        self::$instance = $this;

        @mkdir($this->getDataFolder() . "data/");
        @mkdir($this->getDataFolder() . "lang/");

        $this->saveResource("config.yml");
        $this->saveResource("lang/eng.yml");
        $this->saveResource("lang/ita.yml");
        $this->saveResource("lang/deu.yml");
        $this->saveResource("lang/esp.yml");
        $this->saveResource("lang/fra.yml");
        $this->saveResource("lang/pol.yml");
        $this->saveResource("lang/por.yml");
        $this->saveResource("lang/ukr.yml");
        $this->saveResource("lang/rus.yml");
        $this->saveResource("lang/jpn.yml");
        $this->saveResource("lang/kor.yml");
        $this->saveResource("lang/zho.yml");
        $this->saveResource("lang/tur.yml");

        LangManager::init($this);

        $config = $this->getConfig();

        $this->formsEnabled = (bool)$config->getNested("forms.enabled", false);

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
        $this->settingsManager = new SettingsManager($this);
        $this->blockManager = new BlockManager($this);
        $this->lastSeenManager = new LastSeenManager($this);

        $this->getServer()->getPluginManager()->registerEvents(new PlayerJoinListener($this), $this);
        $this->getServer()->getCommandMap()->register("friends", new FriendsCommand($this));
    }

    public function getFriendsManager(): FriendsManager {
        return $this->friendsManager;
    }

    public function getRequestManager(): RequestManager {
        return $this->requestManager;
    }

    public function getSettingsManager(): SettingsManager {
        return $this->settingsManager;
    }

    public function getBlockManager(): BlockManager {
        return $this->blockManager;
    }

    public function getLastSeenManager(): LastSeenManager {
        return $this->lastSeenManager;
    }

    public function areFormsEnabled(): bool {
        return $this->formsEnabled;
    }

    public static function getInstance(): self {
        return self::$instance;
    }
}