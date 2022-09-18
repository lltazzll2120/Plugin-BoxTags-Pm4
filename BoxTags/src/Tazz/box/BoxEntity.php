<?php

namespace Tazz\box;

use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;

class BoxEntity extends Human {

    public function getName() :string {

        return "BoxBlue";

    }

    public function __construct(Location $level,$skin, $nbt) {
        parent::__construct($level, $skin, $nbt);
        $patch = LootBox::getInstance()->getDataFolder() . "wood_box_blue.png";
        $data = LootBox::getInstance()->PNGtoBYTES($patch);
        $cape = "";
        $patch = LootBox::getInstance()->getDataFolder() . "wood_box.geo.json";
        $goemetry = file_get_contents($patch);
        $skin = new Skin("BoxBlue", $data, $cape, "geometry.unknown", $goemetry);
        $this->setSkin($skin);

    }

}