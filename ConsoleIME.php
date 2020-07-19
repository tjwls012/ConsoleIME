<?php

/**
 * @name ConsoleIME
 * @main tjwls012\consoleime\ConsoleIME
 * @author ["tjwls012"]
 * @version 0.1
 * @api 3.14.0
 * @description License : LGPL 3.0
 */
 
namespace tjwls012\consoleime;
 
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

use pocketmine\Player;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\command\ConsoleCommandSender;

use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;

class ConsoleIME extends PluginBase implements Listener{

  public static $instance;
  
  public static function getInstance(){
  
    return self::$instance;
  }
  public function onLoad(){
  
    self::$instance = $this;
  }
  public function onEnable(){
  
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    
    $a = new PluginCommand("console", $this);
    $a->setPermission("op");
    $a->setUsage("/console");
    $a->setDescription("use console command in game");
    $this->getServer()->getCommandMap()->register($this->getDescription()->getName(), $a);
    
    $a = new PluginCommand("콘솔", $this);
    $a->setPermission("op");
    $a->setUsage("/콘솔");
    $a->setDescription("인게임에서 콘솔 명령를 사용합니다");
    $this->getServer()->getCommandMap()->register($this->getDescription()->getName(), $a);
  }
  public function onCommand(CommandSender $sender, Command $command, string $label, array $array) : bool{
  
    if(!$sender instanceof Player) return true;
    
    $this->sendUI($sender);
    
    return true;
  }
  public function onDataReceive(DataPacketReceiveEvent $e){
  
    $packet = $e->getPacket();
    
    if($packet instanceof ModalFormResponsePacket){
    
      $form_id = $packet->formId;
      
      $button = json_decode($packet->formData, true);
      
      if($form_id === 1300){
      
        if($button == null) return true;
        
        if(!isset($button[0])) return true;
        
        if($button[0] == null) return true;
        
        $console = new ConsoleCommandSender();
        
        $this->getServer()->dispatchCommand($console, $button[0]);
      }
    }
  }
  public function sendUI($player){
    
    $encode = json_encode([
    
      "type" => "custom_form",
      "title" => "Console IME",
      "content" => [
             [
                 "type" => "input",
                 "text" => "command",
                 "default" => "",
                 "placeholder" => "say hello world!"
             ]
         ]
    ]);
     
    $packet = new ModalFormRequestPacket();
    $packet->formId = 1300;
    $packet->formData = $encode;
    $player->dataPacket($packet);
  }
}
