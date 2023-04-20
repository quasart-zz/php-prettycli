<?php

namespace PrettyCli;

use PrettyCli\Common\Color;
use PrettyCli\Common\TextAlignEnum;
use PrettyCli\Table\Frame;
use PrettyCli\Table\Frame\FrameTemplateEnum;
use PrettyCli\Table\Style;

class Table
{
    private array $data = [];
    private array $dataSizes = [];
    private array $header = [];

    private string $separatorBodyFrame = "";
    private string $bodyFrame = "";
    private string $separatorHeaderFrame = "";
    private string $headerFrame = "";

    private FrameTemplateEnum $defaultFrame = FrameTemplateEnum::DoubleLineSingleSeparator;
    private string $EOL = PHP_EOL;

    private Style $globalStyle;
    private array $rowStyle;
    private array $colStyle;
    private array $cellStyle;
    private $customStyleFunction = null;

    public Frame $frame;
    public bool $printHeader = true;
    public bool $spacingRight = true;
    public string $spacingRightString = " ";
    public bool $spacingLeft = true;
    public string $spacingLeftString = " ";

    public function __construct(
        array $data = []
    ) {
        $this->globalStyle = New Style();
        $this->frame = new Frame($this->defaultFrame);
        $this->data = $data;
    }

    private function getLength($item): int
    {
        if (is_null($item)) {
            return 0;
        }

        return mb_strlen($item);
    }

    private function getSpacingRight(): string
    {
        if ($this->spacingRight) {
            return $this->spacingRightString;
        }
        return "";
    }

    private function getSpacingLeft(): string
    {
        if ($this->spacingLeft) {
            return $this->spacingLeftString;
        }
        return "";
    }

    public function setFrameTemplate(FrameTemplateEnum $frameTemplateEnum): void
    {
        $this->frame->applyFrameTemplate($frameTemplateEnum);
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function setHeader(array $header): void
    {
        $this->header = $header;
    }

    public function setEOL(string $EOL): void
    {
        $this->EOL = $EOL;
    }

    public function setStyle(Style $style): void
    {
        $this->globalStyle = $style;
    }

    public function setRowStyle(Style $style, int $rowIndex): void
    {
        if ($rowIndex >= -1) {
            $this->rowStyle[$rowIndex] = $style;
        }
    }

    public function setColStyle(Style $style, int $colIndex): void
    {
        if ($colIndex >= 0) {
            $this->colStyle[$colIndex] = $style;
        }
    }

    public function setCellStyle(Style $style, int $colIndex, int $rowIndex): void
    {
        if ($colIndex >= 0
            && $rowIndex >= -1
        ){
            $this->cellStyle[$colIndex][$rowIndex] = $style;
        }
    }

    public function setCustomStyleFunction(Callable $function): void
    {
        $this->customStyleFunction = $function;
    }

    private function getStyle(int $colIndex, int $rowIndex, $data): Style
    {
        if ($this->customStyleFunction) {
           $style = call_user_func($this->customStyleFunction, $colIndex, $rowIndex, $data);

           if (!is_null($style)
               && $style instanceof Style
           ){
               return $style;
           }
        }

        if (isset($this->cellStyle[$colIndex][$rowIndex])) {
            return $this->cellStyle[$colIndex][$rowIndex];
        }

        if (isset($this->rowStyle[$rowIndex])) {
            return $this->rowStyle[$rowIndex];
        }

        if (isset($this->colStyle[$colIndex])) {
            return $this->colStyle[$colIndex];
        }

        return $this->globalStyle;
    }

    private function renderFrameLower(): string
    {
        return $this->renderFrame($this->frame->styleLowerLeft, $this->frame->styleLowerMid, $this->frame->styleLowerRight, $this->frame->styleLowerMid);
    }

    private function renderFrameMiddle(): string
    {
        return $this->renderFrame($this->frame->styleMiddleLeft, $this->frame->styleMiddleMid, $this->frame->styleMiddleRight, $this->frame->styleUpperMid);
    }

    private function renderFrameUpper(): string
    {
        return $this->renderFrame($this->frame->styleUpperLeft, $this->frame->styleUpperMid, $this->frame->styleUpperRight);
    }

    private function renderFrame(string $UL, string $UM, string $UR, ?string $alternateMid = null): string
    {
        $frame = "";
        foreach ($this->dataSizes as $index => $columnSize) {
            if ($frame !== ""
                && $this->frame->printSeparator
            ){
                if (isset($this->header[$index]) || !$this->frame->printHeaderFrame) {
                    $frame .=  $UM;
                } else {
                    if (!is_null($alternateMid)) {
                        $frame .= $alternateMid;
                    } else {
                        $frame .= $this->frame->styleMainFrameHorizontal;
                    }
                }
            }

            $size = $columnSize + mb_strlen($this->getSpacingLeft()) + mb_strlen($this->getSpacingRight());

            if ($size > 0) {
                $frame .= str_repeat($this->frame->styleMainFrameHorizontal, $size);
            }
        }

        return $this->frame->color->getColorCode() . $UL . $frame . $UR . Color::resetCode() . $this->EOL;
    }

    private function loadData()
    {
        $this->dataSizes = [];

        $normalizedData = [];

        $maxCols = 0;

        foreach ($this->data as $rowIndex => $row) {
            if (!is_array($row)) {
                $row = [$row];
            }
            $cols = count($row);
            if ($cols > $maxCols) {
                $maxCols = $cols;
            }
            $normalizedData[] = $row;
        }
        $this->data = [];

        $headerCols = count($this->header);
        if ($headerCols > $maxCols) {
            $maxCols = $headerCols;
        }

        $calcHeader = [];
        foreach ($normalizedData as $rowIndex => $row) {

            $cols = count($row);

            if ($cols < $maxCols) {
                for ($cols; $cols < $maxCols; $cols ++) {
                    $row[] = "";
                }
            }

            $index = -1;
            foreach ($row as $fieldKey => $fieldValue) {
                $index ++;

                if (!isset($calcHeader[$index])) {
                    $calcHeader[$index] = $fieldKey;
                }

                $length = $this->getLength($fieldValue);

                if (!isset($this->dataSizes[$index])
                    || $this->dataSizes[$index] < $length
                ){
                    $this->dataSizes[$index] = $length;
                }
            }

            $this->data[$rowIndex] = array_values($row);
        }
        $this->data = array_values($this->data);

        if ($headerCols === 0) {
            $this->header = array_values($calcHeader);
        }

        foreach ($this->header as $index => $headerValue) {
            $length = $this->getLength($headerValue);
            if ($length > $this->dataSizes[$index]) {
                $this->dataSizes[$index] = $length;
            }
        }

        if ($this->frame->printBodyFrame){
            if ($this->frame->printSeparator) {
                $this->separatorBodyFrame = $this->frame->color->getColorCode() . $this->frame->styleSeparator . Color::resetCode();
            }
            $this->bodyFrame = $this->frame->color->getColorCode() . $this->frame->styleMainFrameVertical . Color::resetCode();
        }

        if ($this->frame->printHeaderFrame){
            if ($this->frame->printSeparator) {
                $this->separatorHeaderFrame = $this->frame->color->getColorCode() . $this->frame->styleSeparator . Color::resetCode();
            }
            $this->headerFrame = $this->frame->color->getColorCode() . $this->frame->styleMainFrameVertical . Color::resetCode();
        }

    }

    private function normalizeConfig(): void
    {
        if (!$this->printHeader) {
            $this->frame->printHeaderFrame = false;
        }

        if (!$this->frame->printFrame) {
            $this->frame->printBodyFrame = false;
            $this->frame->printHeaderFrame = false;
        }

        if ($this->frame->printFrame
            && !$this->frame->printBodyFrame
            && !$this->frame->printHeaderFrame
        ){
            $this->frame->printFrame = false;
        }
    }

    private function renderBody(): string
    {
        $bodyRender = "";
        foreach ($this->data as $rowIndex => $rowData) {
            $bodyRender .= $this->renderRow($rowData, $rowIndex);
        }
        return $bodyRender;
    }

    private function renderHeader(): string
    {
        if (!$this->printHeader) {
            return "";
        }

        return $this->renderRow($this->header, -1);
    }

    private function renderRow(array $row, int $rowIndex): string
    {
        $rowRender = "";
        $fieldCount = count($row);

        $separator = $this->separatorBodyFrame;
        $frameSide = $this->bodyFrame;
        if ($rowIndex === -1) {
            $separator = $this->separatorHeaderFrame;
            $frameSide = $this->headerFrame;
        }

        foreach ($row as $fieldIndex => $fieldData) {

            $style = $this->getStyle($fieldIndex, $rowIndex, $fieldData);

            if (!$this->frame->printBodyFrame
                && $this->frame->printHeaderFrame
            ){
                $rowRender .= " ";
            }

            $leftSpaces = 0;
            $leftSpaceBlock = "";
            $rightSpaces = 0;
            $rightSpaceBlock = "";
            $size = $this->dataSizes[$fieldIndex];
            $diff = $size - $this->getLength($fieldData);
            if ($diff > 0) {

                if ($style->align == TextAlignEnum::Center) {
                    $leftSpaces = floor($diff / 2);
                    $rightSpaces = $diff - $leftSpaces;
                } elseif ($style->align == TextAlignEnum::Left) {
                    $rightSpaces = $diff;
                } else {
                    $leftSpaces = $diff;
                }

                if ($leftSpaces > 0) {
                    $leftSpaceBlock = str_repeat(" ", $leftSpaces);
                }

                if ($rightSpaces > 0) {
                    $rightSpaceBlock = str_repeat(" ", $rightSpaces);
                }
            }

            $rowRender .= $this->getSpacingLeft() . $leftSpaceBlock . $style->color->getColorCode() . $fieldData . Color::resetCode() . $rightSpaceBlock . $this->getSpacingRight();

            if ($fieldIndex < ($fieldCount - 1)
                && $separator !== ""
            ){
                $rowRender .= $separator;
            }
        }

        $columsCount = count($this->dataSizes);

        if ($fieldCount < $columsCount) {
            for ($fieldCount; $fieldCount < $columsCount; $fieldCount ++) {
                $size = $this->dataSizes[$fieldCount];
                $rowRender .= $this->getSpacingLeft() . $this->getSpacingRight() .  str_repeat(" ", $size + 1) ;
            }
        }

        return $frameSide . $rowRender . $frameSide . $this->EOL;
    }

    public function render(): string
    {
        $this->loadData();
        $this->normalizeConfig();

        $tableRender = "";

        $header = $this->renderHeader();
        $body = $this->renderBody();

        $frameUpper = "";
        $frameMiddle = "";
        $frameLower = "";
        if ($this->frame->printFrame) {
            $frameUpper = $this->renderFrameUpper();
            $frameMiddle = $this->renderFrameMiddle();
            $frameLower = $this->renderFrameLower();
        }

        $preHeaderRender = "";
        $preBodyRender = "";
        $postBodyRender = "";

        if ($this->frame->printHeaderFrame) {
            $preHeaderRender = $frameUpper;

            if ($this->frame->printBodyFrame){
                $preBodyRender = $frameMiddle;
                $postBodyRender = $frameLower;
            } else {
                $preBodyRender = $frameLower;
            }

        } elseif ($this->frame->printBodyFrame) {
            $preBodyRender = $frameUpper;
            $postBodyRender = $frameLower;
        }

        $tableRender .= $preHeaderRender . $header . $preBodyRender . $body . $postBodyRender;

        return $tableRender;
    }

}