<?php

namespace App\Services\BrandDna;

/**
 * Maps relationships between owner, product, and customer attribute scores.
 *
 * Core of the Brand DNA engine: determines whether the three dimensions
 * are aligned, and boosts DNA scores where alignment is strong.
 *
 * Weights: Product 40% + Owner 35% + Customer 25%
 * Alignment bonus: up to +20 when all three dimensions score ≥ 50
 */
class RelationshipMapper
{
    private const WEIGHT_PRODUCT  = 0.40;
    private const WEIGHT_OWNER    = 0.35;
    private const WEIGHT_CUSTOMER = 0.25;

    private const ALIGNMENT_THRESHOLD = 50; // minimum score per dimension to get bonus
    private const ALIGNMENT_BONUS     = 20; // bonus points when all three align

    /**
     * Compute final DNA scores by combining all three dimensions.
     *
     * @param  array $ownerScores    normalized 0-100 per DNA
     * @param  array $productScores  normalized 0-100 per DNA
     * @param  array $customerScores normalized 0-100 per DNA
     * @return array{scores: array, alignment_per_dna: array, overall_alignment: int}
     */
    public function map(array $ownerScores, array $productScores, array $customerScores): array
    {
        $dnaKeys  = array_keys(DnaTaxonomy::DNA_TYPES);
        $finalScores    = [];
        $alignmentPerDna = [];

        foreach ($dnaKeys as $dna) {
            $o = $ownerScores[$dna]    ?? 0;
            $p = $productScores[$dna]  ?? 0;
            $c = $customerScores[$dna] ?? 0;

            // Weighted base score
            $base = ($p * self::WEIGHT_PRODUCT)
                  + ($o * self::WEIGHT_OWNER)
                  + ($c * self::WEIGHT_CUSTOMER);

            // Alignment bonus: all three exceed threshold → strong DNA signal
            $aligned = $o >= self::ALIGNMENT_THRESHOLD
                    && $p >= self::ALIGNMENT_THRESHOLD
                    && $c >= self::ALIGNMENT_THRESHOLD;

            $alignmentBonus = $aligned ? self::ALIGNMENT_BONUS : 0;

            // Partial alignment: two of three exceed threshold → smaller bonus
            $partialCount = ($o >= self::ALIGNMENT_THRESHOLD ? 1 : 0)
                          + ($p >= self::ALIGNMENT_THRESHOLD ? 1 : 0)
                          + ($c >= self::ALIGNMENT_THRESHOLD ? 1 : 0);

            if (! $aligned && $partialCount === 2) {
                $alignmentBonus = 8;
            }

            $finalScores[$dna]     = (int) min(100, round($base + $alignmentBonus));
            $alignmentPerDna[$dna] = $aligned ? 'full' : ($partialCount === 2 ? 'partial' : 'weak');
        }

        // Sort descending
        arsort($finalScores);

        // Overall alignment: average of top-3 DNA alignment quality
        $topDna    = array_slice(array_keys($finalScores), 0, 3);
        $fullCount = array_reduce($topDna, fn ($c, $d) => $c + ($alignmentPerDna[$d] === 'full' ? 1 : 0), 0);
        $partCount = array_reduce($topDna, fn ($c, $d) => $c + ($alignmentPerDna[$d] === 'partial' ? 1 : 0), 0);

        $overallAlignment = (int) min(100, round(
            ($fullCount * 33) + ($partCount * 18) + ($finalScores[array_key_first($finalScores)] * 0.3)
        ));

        return [
            'scores'           => $finalScores,
            'alignment_per_dna' => $alignmentPerDna,
            'overall_alignment' => $overallAlignment,
        ];
    }
}
