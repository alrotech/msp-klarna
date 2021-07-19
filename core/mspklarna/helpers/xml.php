<?php
/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

namespace alroniks\mspoplati\helpers\xml;

use JsonException;
use XMLWriter;

/**
 * @param string $content XML string
 *
 * @return array
 * @throws JsonException
 */
function xmlToArray(string $content): array
{
    return json_decode(
        json_encode(simplexml_load_string($content), JSON_THROW_ON_ERROR),
        true,
        512,
        JSON_THROW_ON_ERROR
    );
}

/**
 * @param array          $data
 *
 * @param XmlWriter|null $writer
 * @param int            $nestingLevel Nesting level of recursive function
 *
 * @return XmlWriter
 */
function arrayToXml(array $data, XmlWriter $writer = null, int $nestingLevel = 0): XmlWriter
{
    if (!$writer) {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->startDocument('1.0', 'utf-8');
    }
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $writer->startElement($key);
            arrayToXml($value, $writer, $nestingLevel++);
            $writer->endElement();
            continue;
        }
        $writer->writeElement($key, $value);
    }

    return $writer;
}
