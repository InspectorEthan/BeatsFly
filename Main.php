<?php
namespace ItsDucky\DuckyFly;

use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;

class Main extends PluginBase implements Listener {

     /* @var players */
     public $players = [];

     public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TextFormat::GREEN . "BeatsFly by Epicrafter60 enabled!");
     }

     public function onDisable() {
        $this->getLogger()->info(TextFormat::RED . "BeatsFly by Epicrafter60 disabled!");
     }
   
     public function onEntityDamage(EntityDamageEvent $event) {
        if($event instanceof EntityDamageByEntityEvent) {
        $damager = $event->getDamager();
        $player = $event->getEntity();
           if($damager instanceof Player and $this->isPlayer($damager)) {
              $damager->sendMessage(TextFormat::RED . "§l§bBeats§cFly§r§7> §cYou cannot damage players while in fly mode!");
              $event->setCancelled(true);
           }elseif($player instanceof Player and $this->isPlayer($player)){
               $this->removePlayer($player);
               $player->setAllowFlight(false);
               $this->sendPacket($player);
               $player->sendMessage(TextFormat::RED . "You've been crashed!");
           }
        }
     }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if(strtolower($command->getName()) == "fly") {
            if($sender instanceof Player) {
                if($this->isPlayer($sender)) {
                   $this->removePlayer($player);
               $player->setAllowFlight(false);
               $this->sendPacket($player);
                    $sender->sendMessage(TextFormat::RED . "§b§lBeats§cFly§r§7> §cYou have disabled fly mode!");
                    return true;
                }
                else{
                    $this->addPlayer($sender);
                    $sender->setAllowFlight(true);
                    $sender->sendMessage(TextFormat::GREEN . "§b§lBeats§cFly§r§7> §aYou have enabled fly mode!");
                    return true;
                }
            }
            else{
                $sender->sendMessage(TextFormat::RED . "§b§lBeats§cFly§r§7> Please use this command in-game.");
                return true;
            }
        }
    }
    public function addPlayer(Player $player) {
        $this->players[$player->getName()] = $player->getName();
    }
    public function isPlayer(Player $player) {
        return in_array($player->getName(), $this->players);
    }
    public function removePlayer(Player $player) {
        unset($this->players[$player->getName()]);
    }
    public function sendPacket(Player $player, Int $gm = 0){
        $pk = new SetPlayerGameTypePacket();
        $pk->gamemode = $gm;
        $player->dataPacket($pk);
        if($gm == 0) $player->setMotion($player->getMotion()->add(0, -5)); // Pull player down
    }
}
