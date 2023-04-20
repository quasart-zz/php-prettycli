<?php

namespace PrettyCli\Table;

use PrettyCli\Common\Color;
use PrettyCli\Table\Frame\FrameTemplateEnum;

class Frame
{
    public string $styleSeparator = "";
    public string $styleMainFrameVertical = "";
    public string $styleMainFrameHorizontal = "";

    public string $styleUpperLeft = "";
    public string $styleUpperMid = "";
    public string $styleUpperRight = "";
    public string $styleLowerLeft = "";
    public string $styleLowerMid = "";
    public string $styleLowerRight = "";
    public string $styleMiddleLeft = "";
    public string $styleMiddleMid = "";
    public string $styleMiddleRight = "";

    public bool $printFrame = true;
    public bool $printHeaderFrame = true;
    public bool $printBodyFrame = true;
    public bool $printSeparator = true;

    public Color $color;

    public function __construct(
        FrameTemplateEnum $frameTypeEnum
    ) {
        $this->color = new Color();
        $this->applyFrameTemplate($frameTypeEnum);
    }

    public function applyFrameTemplate(FrameTemplateEnum $frameTypeEnum): void
    {
        $frameTemplate = $frameTypeEnum->frameTemplate();

        $this->styleSeparator = $frameTemplate->styleSeparator;

        $this->styleMainFrameHorizontal = $frameTemplate->styleMainFrameHorizontal;
        $this->styleMainFrameVertical = $frameTemplate->styleMainFrameVertical;

        $this->styleUpperLeft = $frameTemplate->styleUpperLeft;
        $this->styleUpperMid = $frameTemplate->styleUpperMid;
        $this->styleUpperRight = $frameTemplate->styleUpperRight;
        $this->styleLowerLeft = $frameTemplate->styleLowerLeft;
        $this->styleLowerMid = $frameTemplate->styleLowerMid;
        $this->styleLowerRight = $frameTemplate->styleLowerRight;
        $this->styleMiddleLeft = $frameTemplate->styleMiddleLeft;
        $this->styleMiddleMid = $frameTemplate->styleMiddleMid;
        $this->styleMiddleRight = $frameTemplate->styleMiddleRight;

        $this->printFrame = $frameTemplate->printFrame;
        $this->printHeaderFrame = $frameTemplate->printHeaderFrame;
        $this->printBodyFrame = $frameTemplate->printBodyFrame;
        $this->printSeparator = $frameTemplate->printSeparator;

        $this->color = $frameTemplate->color;
    }
}