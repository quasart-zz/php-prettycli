<?php

namespace PrettyCli\Table\Frame;

use PrettyCli\Table\Frame\Templates\Asterisk;
use PrettyCli\Table\Frame\Templates\Basic;
use PrettyCli\Table\Frame\Templates\DoubleLine;
use PrettyCli\Table\Frame\Templates\DoubleLineSingleSeparator;
use PrettyCli\Table\Frame\Templates\SingleLine;

enum FrameTemplateEnum: int
{
    case SingleLine = 1;
    case DoubleLine = 2;
    case DoubleLineSingleSeparator = 3;
    case Basic = 4;
    case Asterisk = 5;

    public function frameTemplate(): FrameTemplate
    {
        return match($this)
        {
            self::SingleLine => new SingleLine(),
            self::DoubleLine => new DoubleLine(),
            self::DoubleLineSingleSeparator => new DoubleLineSingleSeparator(),
            self::Basic => new Basic(),
            self::Asterisk => new Asterisk(),
        };
    }
}