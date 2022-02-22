<?php

namespace theohdg2\ClearLagg;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\Server;

class ClearLaggCommand extends Command
{
    public function __construct(string $name ="clearlag", Translatable|string $description = "permet de voir le temp restant le clearlag", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(Server::getInstance()->isOp($sender->getName())) {
            ClearLagg::getTask()->setRestant("1");
        }
    }
}