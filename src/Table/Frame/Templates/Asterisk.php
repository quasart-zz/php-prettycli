<?php

namespace PrettyCli\Table\Frame\Templates;

use PrettyCli\Common\Color;
use PrettyCli\Common\ColorEnum;
use PrettyCli\Table\Frame\FrameTemplate;

class Asterisk extends FrameTemplate
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
    public bool $printSeparator = false;

    public ColorEnum $foreColor = ColorEnum::Default;
    public ColorEnum $backColor = ColorEnum::Default;
    public bool $bright = false;
}