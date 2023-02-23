<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use Behat\Gherkin\Keywords\ArrayKeywords;
use Behat\Gherkin\Lexer;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Parser;

$keywords = new ArrayKeywords([
    'en' => [
        'feature'          => 'Feature',
        'background'       => 'Background',
        'scenario'         => 'Scenario',
        'scenario_outline' => 'Scenario Outline|Scenario Template',
        'examples'         => 'Examples|Scenarios',
        'given'            => 'Given',
        'when'             => 'When',
        'then'             => 'Then',
        'and'              => 'And',
        'but'              => 'But'
    ],
]);

$lexer  = new Lexer($keywords);
$parser = new Parser($lexer);
$outputFile = __DIR__ . '/docs/behat-phrases.md';
$featuresFiles = __DIR__ . '/features/*.feature';
$output = '';
$separator = PHP_EOL . PHP_EOL;
$mockserverPayloadPhrases = [
    'mockserver should receive the following expectation only:' => 'Payload sent to mockserver endpoint `PUT /expectation`:',
    'mockserver should receive the following verification only:' => 'Payload sent to mockserver endpoint `PUT /verify`:',
    'mockserver should have been reset' => 'Mockserver is reset.',
];

$output .= '# Behat phrases' . $separator;

foreach (glob($featuresFiles) as $featureFile) {
    $feature = $parser->parse(file_get_contents($featureFile));

    $output .= '## ' . $feature->getTitle() . $separator;

    if ($feature->getDescription()) {
        $output .= preg_replace('/^ +/m', '', trim($feature->getDescription())) . $separator;
    }

    foreach ($feature->getScenarios() as $scenario) {
        $lines = explode(PHP_EOL, $scenario->getTitle());
        $title = array_shift($lines);
        $description = join(PHP_EOL, array_map(fn (string $line) => trim($line), $lines));

        $output .= '### ' . $title . $separator;

        if ($description) {
            $output .= $description . $separator;
        }

        $codeblock = '';
        $mockserverPayload = null;
        $mockserverPayloadInfo = null;

        foreach ($scenario->getSteps() as $step) {
            if (array_key_exists($step->getText(), $mockserverPayloadPhrases)) {
                $pyStringNode = $step->getArguments()[0] ?? null;
                $mockserverPayloadInfo = $mockserverPayloadPhrases[$step->getText()];

                if ($pyStringNode instanceof PyStringNode) {
                    $mockserverPayload = $pyStringNode->getRaw();
                }

                continue;
            }

            $codeblock .= $step->getKeyword() . ' ' . $step->getText() . PHP_EOL;

            foreach ($step->getArguments() as $argument) {
                if ($argument instanceof PyStringNode) {
                    $codeblock .= '"""' . PHP_EOL . $argument->getRaw() . PHP_EOL . '"""' . PHP_EOL;
                }
            }
        }

        $output .= '``` cucumber' . PHP_EOL . $codeblock . '```' . $separator;

        if ($mockserverPayloadInfo) {
            $output .= $mockserverPayloadInfo . $separator;
        }

        if ($mockserverPayload) {
            $output .= '``` json' . PHP_EOL . $mockserverPayload . PHP_EOL . '```' . $separator;
        }
    }
}


if (file_exists($outputFile)) {
    unlink($outputFile);
}

touch($outputFile);
file_put_contents($outputFile, $output);
