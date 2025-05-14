<?php

namespace Akari_my\FriendsX\manager;

use Akari_my\FriendsX\Main;
use pocketmine\scheduler\ClosureTask;

class RequestManager {

    /** @var array<string, array<string, int>> */
    private array $requests = [];

    private string $file;
    private int $cooldown;

    public function __construct(private Main $plugin) {
        $this->file = $plugin->getDataFolder() . "data/requests.json";
        $this->cooldown = $plugin->getConfig()->get("friend-request-cooldown", 120);
        $this->load();
    }

    public function load(): void {
        if (file_exists($this->file)) {
            $raw = json_decode(file_get_contents($this->file), true) ?? [];
            $this->requests = [];

            foreach ($raw as $target => $senders) {
                foreach ($senders as $sender => $time) {
                    if (time() - $time <= $this->cooldown) {
                        $this->requests[$target][$sender] = $time;
                    }
                }
            }
        }
    }

    public function save(): void {
        $plugin = $this->plugin;
        $now = time();
        foreach ($this->requests as $target => &$senders) {
            foreach ($senders as $sender => $time) {
                if ($now - $time > $this->cooldown) {
                    unset($senders[$sender]);
                }
            }
            if (empty($senders)) {
                unset($this->requests[$target]);
            }
        }

        if (empty($this->requests)) {
            if (file_exists($this->file)) {
                @unlink($this->file);
            }
            return;
        }

        $json = json_encode($this->requests, JSON_PRETTY_PRINT);
        $plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($json) {
            file_put_contents($this->file, $json);
        }), 1);
    }

    public function sendRequest(string $sender, string $target): bool {
        $time = time();
        $sender = strtolower($sender);
        $target = strtolower($target);

        $this->requests[$target] ??= [];

        if (isset($this->requests[$target][$sender])) {
            if ($time - $this->requests[$target][$sender] < $this->cooldown) {
                return false;
            } else {
                unset($this->requests[$target][$sender]);
            }
        }

        $this->requests[$target][$sender] = $time;
        $this->save();
        return true;
    }

    public function getRequests(string $player): array {
        $player = strtolower($player);
        if (!isset($this->requests[$player])) return [];

        $result = [];
        $now = time();
        foreach ($this->requests[$player] as $sender => $timestamp) {
            if ($now - $timestamp <= $this->cooldown) {
                $result[] = $sender;
            }
        }
        return $result;
    }

    public function hasRequest(string $target, string $sender): bool {
        $target = strtolower($target);
        $sender = strtolower($sender);
        return isset($this->requests[$target][$sender]) &&
            (time() - $this->requests[$target][$sender] <= $this->cooldown);
    }

    public function removeRequest(string $target, string $sender): void {
        $target = strtolower($target);
        $sender = strtolower($sender);

        if (isset($this->requests[$target][$sender])) {
            unset($this->requests[$target][$sender]);
            if (empty($this->requests[$target])) {
                unset($this->requests[$target]);
            }

            $this->save();
        }
    }

    public function getRemainingCooldown(string $target, string $sender): int {
        $target = strtolower($target);
        $sender = strtolower($sender);
        if (isset($this->requests[$target][$sender])) {
            $timeLeft = $this->cooldown - (time() - $this->requests[$target][$sender]);
            return max(0, $timeLeft);
        }
        return 0;
    }
}