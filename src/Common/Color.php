<?php

namespace PrettyCli\Common;

class Color
{
    public ColorEnum $foreColor = ColorEnum::Default;
    public ColorEnum $backColor = ColorEnum::Default;
    public bool $bright = false;

    private string $colorCode = "";

    public function getColorCode(): string
    {
        if ($this->colorCode != "") {
            return $this->colorCode;
        }

        $code = "\033[";

        if ($this->bright) {
            $code .= "1;";
        }

        if ($this->foreColor != ColorEnum::Default) {
            $code .= $this->foreColor->value . ";";
        }

        if ($this->backColor != ColorEnum::Default) {
            $code .= ($this->backColor->value + 10) . ";";
        }
        $code .= "m";

        $this->colorCode = $code;

        return $code;
    }

    public static function resetCode(): string
    {
        return "\033[0m";
    }
}