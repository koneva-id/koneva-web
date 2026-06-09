<?php

namespace App\Services\BrandDna;

/**
 * Extracts DNA attribute scores from free-text input.
 *
 * Scans text for keyword signals and accumulates weighted scores
 * per DNA dimension. Does NOT use AI — pure deterministic keyword matching.
 */
class AttributeExtractor
{
    /**
     * Extract DNA dimension scores from a text string.
     *
     * @return array<string, int> DNA key → raw score (uncapped)
     */
    public function extract(string $text): array
    {
        $scores = array_fill_keys(array_keys(DnaTaxonomy::DNA_TYPES), 0);

        if (empty(trim($text))) {
            return $scores;
        }

        $normalized = mb_strtolower($text);

        foreach (DnaTaxonomy::KEYWORD_MAP as $keyword => $contributions) {
            if (str_contains($normalized, $keyword)) {
                // Count occurrences, capped at 3 — prevents gaming with repetition
                $count = min(substr_count($normalized, $keyword), 3);
                foreach ($contributions as $dna => $weight) {
                    $scores[$dna] += $weight * $count;
                }
            }
        }

        return $scores;
    }

    /**
     * Extract from multiple text fields combined.
     */
    public function extractFromFields(array $fields): array
    {
        $combined = implode(' ', array_filter($fields));
        return $this->extract($combined);
    }

    /**
     * Normalize raw scores to 0–100 scale.
     * Uses a soft cap: score of 100 = saturated signal (~4-5 strong keywords).
     */
    public function normalize(array $rawScores): array
    {
        $cap = 80; // raw score above which we consider fully saturated → 100

        $normalized = [];
        foreach ($rawScores as $dna => $score) {
            $normalized[$dna] = (int) min(100, round(($score / $cap) * 100));
        }

        return $normalized;
    }
}
