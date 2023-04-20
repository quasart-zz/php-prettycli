<?php

namespace PrettyCli\Table\Frame;

use PrettyCli\Common\Color;
use PrettyCli\Common\ColorEnum;

abstract class FrameTemplate
{
    public string $styleSeparator = "*";

    public string $styleMainFrameVertical = "*";
    public string $styleMainFrameHorizontal = "*";

    public string $styleUpperLeft = "*";
    public string $styleUpperMid = "*";
    public string $styleUpperRight = "*";

    public string $styleLowerLeft = "*";
    public string $styleLowerMid = "*";
    public string $styleLowerRight = "*";

    public string $styleMiddleLeft = "*";
    public string $styleMiddleMid = "*";
    public string $styleMiddleRight = "*";

    public bool $printFrame = true;
    public bool $printHeaderFrame = true;
    public bool $printBodyFrame = true;
    public bool $printSeparator = true;

    public Color $color;
    public ColorEnum $foreColor = ColorEnum::Default;
    public ColorEnum $backColor = ColorEnum::Default;
    public bool $bright = false;


    public function __construct() {
        $this->color = new Color();
        $this->color->foreColor = $this->foreColor;
        $this->color->backColor = $this->backColor;
        $this->color->bright = $this->bright;
    }
}