<?php

namespace PrettyCli\Table;

use PrettyCli\Common\Color;
use PrettyCli\Common\TextAlignEnum;

class Style
{
    public TextAlignEnum $align = TextAlignEnum::Left;
    public Color $color;

    public function __construct() {
        $this->color = new Color();
    }
}