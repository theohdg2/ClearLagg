<?php

namespace theohdg2\ClearLagg;

use pocketmine\entity\object\ExperienceOrb;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class ClearLagg extends PluginBase
{

    /**
     * @var Task
     */
    private static $task;
    private static ClearLagg $instance;

    protected function onEnable(): void
    {
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
        $this->getServer()->getCommandMap()->register("clearlagg",new ClearLaggCommand());

        $this->getScheduler()->scheduleRepeatingTask($task = (new class($this) extends Task{

            /**
             * @var bool|mixed
             */
            private mixed $annonce;
            /**
             * @var bool|mixed
             */
            private mixed $arrayPrev;
            /**
             * @var bool|mixed
             */
            private mixed $restant;
            private ClearLagg $Main;
            /**
             * @var bool|mixed
             */
            private mixed $messagePrev;

            public function __construct(ClearLagg $clearLagg)
            {
                $this->Main = $clearLagg;
                $this->getDefaultConfig();
            }

            /**
             * @return bool|mixed
             */
            public function getRestant(): mixed
            {
                return $this->restant;
            }

            /**
             * @param bool|mixed $restant
             */
            public function setRestant(mixed $restant): void
            {
                $this->restant = $restant;
            }
            public function onRun(): void
            {
                if (in_array($this->restant,$this->arrayPrev)){
                    Server::getInstance()->broadcastMessage(str_replace("{sec}",$this->restant,$this->messagePrev));
                }
                if($this->restant <= 0){
                    $count = 0;
                    foreach (Server::getInstance()->getWorldManager()->getWorlds() as $world) {
                        foreach ($world->getEntities() as $entity){
                            if($entity instanceof ItemEntity || $entity instanceof ExperienceOrb){
                                $entity->close();
                                $count++;
                            }
                        }
                    }
                    Server::getInstance()->broadcastMessage(str_replace("{count}",$count,$this->annonce));
                    $this->getDefaultConfig();
                }
                $this->restant--;
            }

            private function getDefaultConfig():void{
                $this->restant = $this->Main->getConfig()->get("delay-inter-clear",1800);
                $this->arrayPrev = $this->Main->getConfig()->get("delay-prevention",[]);
                $this->messagePrev = $this->Main->getConfig()->get("message-prevention");
                $this->annonce = $this->Main->getConfig()->get("annonce-entity-clear");
                $this->Main->getConfig()->reload();
            }

        }),20);
        self::$instance = $this;
        self::$task = $task;
    }

    /**
     * @return ClearLagg
     */
    public static function getInstance(): ClearLagg
    {
        return self::$instance;
    }

    /**
     * @return Task
     */
    public static function getTask(): Task
    {
        return self::$task;
    }
}