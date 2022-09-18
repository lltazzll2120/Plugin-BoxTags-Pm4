<?php

namespace Tazz\box;

use Tazz\FormAPI\SimpleForm;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\permission\PermissionAttachment;
use pocketmine\permission\PermissionManager;
use _64FF00\PurePerms\Commands\SetUserPerm;
use pocketmine\permission\DefaultPermissions;
use pocketmine\lang\Language;
use pocketmine\Server;

class LootBox extends PluginBase implements Listener
{

    private static $main;

    public static function getInstance(): LootBox
    {

        return self::$main;

    }

    public function OnEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        if (!file_exists($this->getDataFolder() . "wood_box.geo.json")) {

            $this->saveResource("wood_box.geo.json");

        }
        if (!file_exists($this->getDataFolder() . "wood_box_blue.png")) {

            $this->saveResource("wood_box_blue.png");

        }
        EntityFactory::getInstance()->register(BoxEntity::class, function (World $world, CompoundTag $nbt): BoxEntity {
            return new BoxEntity(EntityDataHelper::parseLocation($nbt, $world), BoxEntity::parseSkinNBT($nbt), $nbt);
        }, ['box']);


        self::$main = $this;

    }

    public function PNGtoBYTES($path): string
    {
        $img = @imagecreatefrompng($path);
        $bytes = "";
        $L = (int)@getimagesize($path)[0];
        $l = (int)@getimagesize($path)[1];
        for ($y = 0; $y < $l; $y++) {
            for ($x = 0; $x < $L; $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        return $bytes;
    }

    public function getSkinTag(): CompoundTag
    {
        $skin = str_repeat("\x00", 8192);
        $compoundTag = new CompoundTag();
        $compoundTag->setString('name', "Standard_Custom");
        $compoundTag->setByteArray('Data', $skin);
        return $compoundTag;
    }

    public function interact(PlayerInteractEvent $event){
        $sender = $event->getPlayer();
        if ($sender->getInventory()->getItemInHand()->getId() === VanillaItems::BONE()->getId()){
            if ($sender->hasPermission("box.spawn")){
                $entity = new BoxEntity($sender->getLocation(), $sender->getSkin(),clone $sender->saveNBT());
                $entity->spawnToAll();
                $sender->sendMessage("§aVous avez bien fais spawn la box.");
                $item = VanillaItems::STICK();
                $item->setCount(1);
                $sender->getInventory()->removeItem($item);
            }else{
                $sender->sendMessage("Vous n'avez pas la permission");
            }
        }
    }

    public function onDamage(EntityDamageByEntityEvent $event)
    {

        $player = $event->getDamager();
        $victim = $event->getEntity();

        if ($player instanceof Player && $victim instanceof BoxEntity) {

            if ($player->getInventory()->getItemInHand()->getId() === VanillaItems::BONE()->getId()) {

                $victim->flagForDespawn();
            } else {
                $event->cancel();
                $this->BoxUi($player);

            }
        }
    }

    public function BoxUi($sender)
    {
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = new SimpleForm(function (Player $sender, $data){
            $item = ItemFactory::getInstance()->get(VanillaItems::RABBIT_FOOT()->getId(), 0, 1);
            if ($data === null) {
                return null;
            }
            switch ($data) {
                case 0:
                    if ($sender->getInventory()->contains($item)) {
                        $lots = mt_rand(1, 101);
                        if ($lots >= 1 && $lots < 6) {
                            $cmd = 'setuperm ';
                            $sender->sendMessage("§6§l»§r§f Vous avez reçu le tag #§920CPS§f !");
							$this->getServer()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), new Language("eng")), 'setuperm ' . $sender->getName() . ' tags.20cps');
                            $sender->getInventory()->removeItem(ItemFactory::getInstance()->get(VanillaItems::RABBIT_FOOT()->getId(), 0, 1));
                        }
                        if ($lots >= 6 && $lots < 11) {
                            $sender->sendMessage("§6§l»§r§f Vous avez reçu le tag #§5Free§1Kill§f !");
                            $this->getServer()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), new Language("eng")), 'setuperm ' . $sender->getName() . ' tags.freekill');
                            $sender->getInventory()->removeItem(ItemFactory::getInstance()->get(VanillaItems::RABBIT_FOOT()->getId(), 0, 1));
                        }
                        if ($lots >= 11 && $lots < 21) {
                            $sender->sendMessage("§6§l»§r§f Vous avez reçu le tag #§8Cheateur§f !");
							$this->getServer()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), new Language("eng")), 'setuperm ' . $sender->getName() . ' tags.cheateur');
                            $sender->getInventory()->removeItem(ItemFactory::getInstance()->get(VanillaItems::RABBIT_FOOT()->getId(), 0, 1));
                        }
                        if ($lots >= 21 && $lots < 31) {
                            $sender->sendMessage("§6§l»§r§f Vous avez reçu le tag #§2Bedo§3Land§f !");
							$this->getServer()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), new Language("eng")), 'setuperm ' . $sender->getName() . ' tags.bedoland');
                            $sender->getInventory()->removeItem(ItemFactory::getInstance()->get(VanillaItems::RABBIT_FOOT()->getId(), 0, 1));
                        }
                        if ($lots >= 31 && $lots < 41) {
                            $sender->sendMessage("§6§l»§r§f Vous avez reçu le tag #§6NO§aLIFE§f !");
							$this->getServer()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), new Language("eng")), 'setuperm ' . $sender->getName() . ' tags.nolife');
                            $sender->getInventory()->removeItem(ItemFactory::getInstance()->get(VanillaItems::RABBIT_FOOT()->getId(), 0, 1));
                        }
                        if ($lots >= 41 && $lots < 51) {
                            $sender->sendMessage("§6§l»§r§f Vous avez reçu le tag #§aP§7V§aP§f !");
							$this->getServer()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), new Language("eng")), 'setuperm ' . $sender->getName() . ' tags.pvp');
                            $sender->getInventory()->removeItem(ItemFactory::getInstance()->get(VanillaItems::RABBIT_FOOT()->getId(), 0, 1));
                        }
                        if ($lots >= 51 && $lots < 61) {
                            $sender->sendMessage("§6§l»§r§f Vous avez reçu le tag #§3Fr§fan§4ce§f !");
							$this->getServer()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), new Language("eng")), 'setuperm ' . $sender->getName() . ' tags.france');
                            $sender->getInventory()->removeItem(ItemFactory::getInstance()->get(VanillaItems::RABBIT_FOOT()->getId(), 0, 1));
                        }
                        if ($lots >= 61 && $lots < 71) {
                            $sender->sendMessage("§6§l»§r§f Vous avez reçu le tag #§6N§cOO§6B§f !");
							$this->getServer()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), new Language("eng")), 'setuperm ' . $sender->getName() . ' tags.noob');
                            $sender->getInventory()->removeItem(ItemFactory::getInstance()->get(VanillaItems::RABBIT_FOOT()->getId(), 0, 1));
                        }
                        if ($lots >= 71 && $lots < 81) {
                            $sender->sendMessage("§6§l»§r§f Vous avez reçu le tag #§9EZ§f !");
							$this->getServer()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), new Language("eng")), 'setuperm ' . $sender->getName() . ' tags.ez');
                            $sender->getInventory()->removeItem(ItemFactory::getInstance()->get(VanillaItems::RABBIT_FOOT()->getId(), 0, 1));
                        }
                        if ($lots >= 81 && $lots < 91) {
                            $sender->sendMessage("§6§l»§r§f Vous avez reçu le tag #§4Karma§f !");
							$this->getServer()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), new Language("eng")), 'setuperm ' . $sender->getName() . ' tags.karma');
                            $sender->getInventory()->removeItem(ItemFactory::getInstance()->get(VanillaItems::RABBIT_FOOT()->getId(), 0, 1));
                        }
                        if ($lots >= 91 && $lots < 101) {
                            $sender->sendMessage("§6§l»§r§f Vous avez reçu le tag #§6TRY§eHARD§f !");
							$this->getServer()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), new Language("eng")), 'setuperm ' . $sender->getName() . ' tags.tryhard');
                            $sender->getInventory()->removeItem(ItemFactory::getInstance()->get(VanillaItems::RABBIT_FOOT()->getId(), 0, 1));
                        }
                    } else {
                        $sender->sendMessage("§e§l»§r§f Vous n'avez pas de §6Clé Tags§f !");
                        $this->BoxUi($sender);
                    }
                    break;
                case 1:
                    break;
            }
        });
        $c = 0;
        $id = 414;
        $meta = 0;
        foreach ($sender->getInventory()->getContents() as $item) {
            if ($item instanceof Item && $item->getId() == $id && $item->getMeta() == $meta) $c += $item->getCount();
        }
        $name = $sender->getName();
        $form->setTitle("§e- §fBox Tags §e-");
        $form->setContent("§6§l»§r§f Vous avez §6$c clé(s) tag§f dans votre inventaire !\n\nVoici les différents tags :\n§r- #§920CPS\n§r- #§5Free§1Kill\n§r- #§8Cheateur\n§r- #§2Bedo§3Land\n§r- #§4NO§2LIFE\n§r- #§aP§7V§aP\n§r- #§3Fr§fan§4ce\n§r- #§6N§cOO§6B\n§r- #§9EZ\n§r- #§4Karma\n§r- #§6TRY§eHARD §f\n\nVoulez vous ouvrir une box tag ?");
        $form->addButton("§aOuvrir");
        $form->addButton("§cFermer");
        $form->sendToPlayer($sender);
    }
}