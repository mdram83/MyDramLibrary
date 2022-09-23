<?php

namespace MyDramLibrary\View;

use Exception;
use MyDramLibrary\Configuration\DirectoryConfiguration;

class HTMLTemplate implements MVCTemplate
{
    /*
    1. dedicated exceptions and exceptions handling
    2. if with some logics (e.g. staus == 1 sth, or status == 0 sth else)
    */

    protected string $content;
    protected array $data;
    protected ?HTMLTemplate $parent;
    protected ?HTMLTemplate $master;
    protected ?int $index;

    protected HTMLTemplateCollection $childSections; // czy potrzebne?

    protected ?array $markers = null;
    protected ?array $startMarkers = null;
    protected ?array $endMarkers = null;

    protected ?int $startMarkerPosition = null;
    protected ?int $sectionStartPosition = null;
    protected ?string $startMarkerType = null;
    protected ?string $sectionContext = null;
    protected ?int $sectionEndPosition = null;
    protected ?string $sectionContent = null;

    public function __construct(
        string $content,
        array $data,
        ?HTMLTemplate $parent = null,
        ?HTMLTemplate $master = null,
        ?int $index = null
    ) {
        $this->content = $content;
        $this->data = $data;
        $this->parent = $parent ?? null;
        $this->master = $master ?? null;
        $this->index = $index ?? null;
        $this->childSections = new HTMLTemplateCollection(); // czy potrzebne?
    }

    public function process(): string
    {
        $this->content = $this->processSections($this->content);
        $this->parse();
        return $this->content;
    }

    protected function processSections(string $content): string
    {
        if ($this->setSectionMarkers($content)) {
            $this->setSectionOpeningParams();
            for ($markerIndex = 1, $depth = 1; $markerIndex < count($this->markers[0]); $markerIndex++) {
                ($this->startMarkers[$markerIndex][1] === -1) ? --$depth : ++$depth;
                if ($depth == 0) {
                    if ($this->endMarkers[$markerIndex]['type'] != $this->startMarkerType) {
                        throw new Exception('Incorrect Section setup: wrong section closure');
                    }
                    $this->setSectionClosingParams($content, $markerIndex);
                    break;
                }
            }
            if ($depth > 0) {
                throw new Exception('Incorrect Section setup: Section not closed');
            }
            return
                substr($content, 0, $this->startMarkerPosition)
                . $this->processChildSection()
                . $this->processSections(
                    substr(
                        $content,
                        $this->sectionEndPosition + strlen(
                            HTMLTemplateConfig::TEMPLATE_SECTION_END_OPENING
                            . $this->startMarkerType
                            . HTMLTemplateConfig::TEMPLATE_TAG_CLOSE_BRACKET
                        )
                    )
                );
        }
        return $content;
    }

    protected function setSectionMarkers(string $content): bool
    {
        if (
            preg_match_all(
                '/(' . HTMLTemplateConfig::TEMPLATE_EREG_PATTERN_LOOP_START
                . ')|(' . HTMLTemplateConfig::TEMPLATE_EREG_PATTERN_LOOP_END
                . ')|(' . HTMLTemplateConfig::TEMPLATE_EREG_PATTERN_IF_START
                . ')|(' . HTMLTemplateConfig::TEMPLATE_EREG_PATTERN_IF_END
                . ')|(' . HTMLTemplateConfig::TEMPLATE_EREG_PATTERN_ROLL_START
                . ')|(' . HTMLTemplateConfig::TEMPLATE_EREG_PATTERN_ROLL_END
                . ')|(' . HTMLTemplateConfig::TEMPLATE_EREG_PATTERN_IFNOT_START
                . ')|(' . HTMLTemplateConfig::TEMPLATE_EREG_PATTERN_IFNOT_END
                . ')/',
                $content,
                $this->markers,
                PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE
            )
        ) {
            $this->startMarkers = array();
            $this->endMarkers = array();

            for ($i = 0; $i < count($this->markers[0]); $i++) {
                $this->startMarkers[$i] =
                    (($this->markers[1][$i][1] !== -1) ? $this->markers[1][$i] :
                        (($this->markers[3][$i][1] !== -1) ? $this->markers[3][$i] :
                            ($this->markers[5][$i][1] !== -1 ? $this->markers[5][$i] : $this->markers[7][$i])));
                $this->startMarkers[$i]['type'] =
                    ($this->startMarkers[$i][1] !== -1)
                    ? substr(
                        $this->startMarkers[$i][0],
                        1,
                        strpos($this->startMarkers[$i][0], HTMLTemplateConfig::TEMPLATE_SEPARATOR) - 1
                    )
                    : null;
                $this->endMarkers[$i] =
                    (($this->markers[2][$i][1] !== -1) ? $this->markers[2][$i] :
                        (($this->markers[4][$i][1] !== -1) ? $this->markers[4][$i] :
                            ($this->markers[6][$i][1] !== -1 ? $this->markers[6][$i] : $this->markers[8][$i])));
                $this->endMarkers[$i]['type'] =
                    ($this->endMarkers[$i][1] !== -1)
                    ? substr(
                        $this->endMarkers[$i][0],
                        strlen(HTMLTemplateConfig::TEMPLATE_SECTION_END_OPENING),
                        - strlen(HTMLTemplateConfig::TEMPLATE_TAG_CLOSE_BRACKET)
                    )
                    : null;
            }
            return true;
        }
        return false;
    }

    protected function setSectionOpeningParams(): void
    {
        $this->startMarkerPosition = $this->getStartMarkerPosition();
        $this->sectionStartPosition = $this->startMarkerPosition + strlen($this->startMarkers[0][0]);
        $this->startMarkerType = $this->startMarkers[0]['type'];

        $this->sectionContext = substr(
            $this->startMarkers[0][0],
            strlen(
                HTMLTemplateConfig::TEMPLATE_TAG_OPEN_BRACKET
                . $this->startMarkerType
                . HTMLTemplateConfig::TEMPLATE_SEPARATOR
            ),
            - strlen(HTMLTemplateConfig::TEMPLATE_TAG_CLOSE_BRACKET)
        );
    }

    protected function getStartMarkerPosition(): int
    {
        if ($this->startMarkers[0][1] === -1) {
            throw new Exception('Incorrect Section setup: Section closed before it was opened');
            // inny exception in jak go obslugiwac z perskeptywy usera?
        }
        return $this->startMarkers[0][1];
    }

    protected function setSectionClosingParams(string $content, int $markerIndex): void
    {
        $this->sectionEndPosition = $this->endMarkers[$markerIndex][1];
        $this->sectionContent = substr(
            $content,
            $this->sectionStartPosition,
            $this->sectionEndPosition - $this->sectionStartPosition
        );
    }

    protected function processChildSection(): ?string
    {
        if ($this->startMarkerType == HTMLTemplateConfig::TEMPLATE_SECTION_LOOP_TAG) {
            return $this->processLoopSection();
        }
        if ($this->startMarkerType == HTMLTemplateConfig::TEMPLATE_SECTION_IF_TAG) {
            return $this->processIfSection();
        }
        if ($this->startMarkerType == HTMLTemplateConfig::TEMPLATE_SECTION_IFNOT_TAG) {
            return $this->processIfnotSection();
        }
        if ($this->startMarkerType == HTMLTemplateConfig::TEMPLATE_SECTION_ROLL_TAG) {
            return $this->processRollSection();
        }
        return null;
    }

    protected function processLoopSection(): ?string
    {
        if (isset($this->data[$this->sectionContext]) && is_array($this->data[$this->sectionContext])) {
            $processedSectionContent = null;
            $loopIndex = 0;
            foreach ($this->data[$this->sectionContext] as $loopKey => $loopData) {
                if (!is_array($loopData)) {
                    $loopData = [$this->sectionContext => $loopData];
                }
                $childSection = new HTMLTemplate(
                    $this->sectionContent,
                    $loopData,
                    $this,
                    $this->master ?? $this,
                    $loopIndex
                );
                $this->childSections->addItem($childSection); // czy potrzebne?
                $processedSectionContent .= $childSection->process();
                $loopIndex++;
            }
            return $processedSectionContent;
        }
        return null;
    }

    protected function processIfSection(): ?string
    {
        if (!$this->isVarEmpty($this->getVarName($this->sectionContext, '', ''))) {
            $childSection = new HTMLTemplate(
                $this->sectionContent,
                $this->data,
                $this,
                $this->master ?? $this,
                $this->index ?? null
            );
            $this->childSections->addItem($childSection); // czy potrzebne?
            return $childSection->process();
        }
        return null;
    }

    protected function processIfnotSection(): ?string
    {
        if ($this->isVarEmpty($this->getVarName($this->sectionContext, '', ''))) {
            $childSection = new HTMLTemplate(
                $this->sectionContent,
                $this->data,
                $this,
                $this->master ?? $this,
                $this->index ?? null
            );
            $this->childSections->addItem($childSection); // czy potrzebne?
            return $childSection->process();
        }
        return null;
    }

    protected function processRollSection(): ?string
    {
        $rollContext = explode(HTMLTemplateConfig::TEMPLATE_SECTION_ROLL_SEPARATOR, $this->sectionContext);
        $rollStart = (isset($rollContext[1])) ? $rollContext[0] : 1;
        $rollEnd = $rollContext[1] ?? $rollContext[0];
        if ((int)$rollStart != $rollStart || (int)$rollEnd != $rollEnd) {
            throw new Exception('Incorrect section setup: Invalid rolling parameter');
        }
        $processedSectionContent = null;
        for (
            $loopIndex = $rollStart;
            ($rollEnd >= $rollStart) ? $loopIndex <= $rollEnd : $loopIndex >= $rollEnd;
            ($rollEnd >= $rollStart) ? $loopIndex++ : $loopIndex--
        ) {
            $childSection = new HTMLTemplate(
                $this->sectionContent,
                $this->data,
                $this,
                $this->master ?? $this,
                $loopIndex
            );
            $this->childSections->addItem($childSection); // czy potrzebne?
            $processedSectionContent .= $childSection->process();
        }
        return $processedSectionContent;
    }

    protected function parse(): void
    {
        $this->content = $this->parseFiles();
        $this->content = $this->parseIndex();
        $this->content = $this->parseVars();
    }

    protected function parseVars(): string
    {
        return preg_replace_callback(
            '/(' . HTMLTemplateConfig::TEMPLATE_EREG_PATTERN_VARIABLE . ')/',
            function ($matches) {
                return $this->getContextValue(
                    $matches[0],
                    HTMLTemplateConfig::TEMPLATE_VARIABLE_OPENING,
                    HTMLTemplateConfig::TEMPLATE_TAG_CLOSE_BRACKET
                );
            },
            $this->content
        );
    }

    protected function parseFiles(): string
    {
        return preg_replace_callback(
            '/(' . HTMLTemplateConfig::TEMPLATE_EREG_PATTERN_FILE_PATH . ')/',
            function ($matches) {
                $filename = $this->getVarName(
                    $matches[0],
                    HTMLTemplateConfig::TEMPLATE_FILE_OPENING,
                    HTMLTemplateConfig::TEMPLATE_TAG_CLOSE_BRACKET
                );
                $filePath = DirectoryConfiguration::templatesPath() . $filename . '.php';
                if (file_exists($filePath)) {
                    ob_start();
                    $data = $this->data;
                    require $filePath;
                    $childSection = new HTMLTemplate(ob_get_clean(), $this->data, $this, $this->master ?? $this);
                    $this->childSections->addItem($childSection); // czy potrzebne?
                    return $childSection->process();
                } else {
                    throw new Exception('Template included file missing');
                    // TODO: better exception handling
                }
            },
            $this->content
        );
    }

    protected function parseIndex(): ?string
    {
        return preg_replace_callback(
            '/(' . HTMLTemplateConfig::TEMPLATE_EREG_PATTERN_INDEX . ')/',
            function ($matches) {
                return $this->index;
            },
            $this->content
        );
    }

    protected function getContextValue(string $context, string $openTag = '', string $closeTag = ''): ?string
    {
        $variableName = $this->getVarName($context, $openTag, $closeTag);
        if ($this->hasEncodeFunction($context)) {
            return $this->encode($this->getVarValue($variableName));
        }
        return $this->getVarValue($variableName);
    }

    protected function getVarName(string $tag, string $openTag, string $closeTag): ?string
    {
        $tag = substr($tag, strlen($openTag), ($closeTag == '' ? null : -strlen($closeTag)));
        $tag =
            ($this->hasIndexFunction($tag))
            ? str_replace(
                HTMLTemplateConfig::TEMPLATE_FUNCTION_SEPARATOR . HTMLTemplateConfig::TEMPLATE_FUNCTION_INDEX_TAG,
                '',
                $tag
            ) . ".$this->index"
            : $tag;
        $tag =
            ($this->hasEncodeFunction($tag))
            ? str_replace(
                HTMLTemplateConfig::TEMPLATE_FUNCTION_SEPARATOR . HTMLTemplateConfig::TEMPLATE_FUNCTION_ENCODE_TAG,
                '',
                $tag
            )
            : $tag;
        return $tag;
    }

    protected function getVarValue(string $variableName): ?string
    {
        $variableNames = explode('.', $variableName);
        if (count($variableNames) > 1) {
            $data = $this->data;
            for ($i = 0; $i < count($variableNames); $i++) {
                if (isset($data[$variableNames[$i]])) {
                    $data = $data[$variableNames[$i]];
                } else {
                    return null;
                }
            }
            return (!is_array($data) ? $data : null);
        }
        return
            (isset($this->data[$variableName]) && !is_array($this->data[$variableName]))
            ? $this->data[$variableName]
            : null;
    }

    protected function isVarEmpty(string $variableName): bool
    {
        $variableNames = explode('.', $variableName);
        if (count($variableNames) > 1) {
            $data = $this->data;
            for ($i = 0; $i < count($variableNames); $i++) {
                if (isset($data[$variableNames[$i]])) {
                    $data = $data[$variableNames[$i]];
                } else {
                    return true;
                }
            }
            if (is_array($data) && count($data) > 0) {
                return false;
            }
            return ($data == '') ? true : false;
        }
        return
            (
                !isset($this->data[$variableName])
                || ($this->data[$variableName] == '')
                || (is_array($this->data[$variableName]) && count($this->data[$variableName]) == 0)
            )
            ? true
            : false;
    }

    protected function encode(?string $content): ?string
    {
        return (isset($content)) ? htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5) : null;
    }

    protected function hasIndexFunction(string $context): bool
    {
        return
            (strpos(
                $context,
                HTMLTemplateConfig::TEMPLATE_FUNCTION_SEPARATOR . HTMLTemplateConfig::TEMPLATE_FUNCTION_INDEX_TAG
            ) !== false)
            ? true
            : false;
    }

    protected function hasEncodeFunction(string $context): bool
    {
        return
            (strpos(
                $context,
                HTMLTemplateConfig::TEMPLATE_FUNCTION_SEPARATOR . HTMLTemplateConfig::TEMPLATE_FUNCTION_ENCODE_TAG
            ) !== false)
            ? true
            : false;
    }
}
