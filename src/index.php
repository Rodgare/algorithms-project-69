<?php

namespace App;

function tokenize(string $text): array
{
    return array_unique(preg_split('/\W+/', strtolower($text), -1, PREG_SPLIT_NO_EMPTY));
}

function buildHash(array $docs): array
{
    $hash = [];
    foreach ($docs as $doc) {
        $docTokens = tokenize($doc['text']);
        foreach ($docTokens as $token) {
            if (!isset($hash[$token])) {
                $hash[$token] = [];
            }
            $hash[$token][] = $doc['id'];
        }
    }
    return $hash;
}

function search(array $docs, string $query): array
{
    $queryTokens = tokenize($query);
    $hash = buildHash($docs);
    $docScores = [];

    foreach ($queryTokens as $token) {
        if (isset($hash[$token])) {
            foreach ($hash[$token] as $docId) {
                if (!isset($docScores[$docId])) {
                    $docScores[$docId] = 0;
                }
                $docScores[$docId] += 1;
            }
        }
    }
    arsort($docScores);

    return array_keys($docScores);
}
