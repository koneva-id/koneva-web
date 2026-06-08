<?php

namespace App\Services\BrandDna;

/**
 * Koneva Brand DNA Construction Engine
 *
 * Orchestrates the full pipeline:
 *   1. Extract attributes from owner / product / customer text
 *   2. Score each DNA dimension per source
 *   3. Map relationships and compute alignment
 *   4. Classify primary + secondary DNA
 *   5. Return communication strategy template for that DNA
 *
 * AI is NOT involved in any of these steps.
 * AI only receives the result and writes the narrative explanation.
 */
class BrandDnaEngine
{
    public function __construct(
        private AttributeExtractor $extractor,
        private RelationshipMapper $mapper,
    ) {}

    /**
     * Analyze personalization answers and return the complete Brand DNA profile.
     *
     * @param  array $answers personalization_answers from the client record
     * @return array Brand DNA profile (see return shape below)
     */
    public function analyze(array $answers): array
    {
        // ── 1. Owner dimension ───────────────────────────────────────
        $ownerRaw = $this->extractor->extractFromFields([
            $answers['owner_personality']  ?? '',
            $answers['owner_values']        ?? '',
            $answers['owner_habits']        ?? '',
            $answers['owner_comm_style']    ?? '',
            $answers['owner_interests']     ?? '',
            $answers['owner_gender']        ?? '',
        ]);
        $ownerScores = $this->extractor->normalize($ownerRaw);

        // ── 2. Product dimension ─────────────────────────────────────
        $productRaw = $this->extractor->extractFromFields([
            $answers['product_featured']    ?? '',
            $answers['product_usp']         ?? '',
            $answers['product_advantages']  ?? '',
            $answers['production_capacity'] ?? '',
            $answers['description']         ?? '',
            $answers['unique_value']        ?? '',
        ]);
        $productScores = $this->extractor->normalize($productRaw);

        // ── 3. Customer dimension ────────────────────────────────────
        $customerRaw = $this->extractor->extractFromFields([
            $answers['customer_buy_reason']    ?? '',
            $answers['customer_repeat_reason'] ?? '',
            $answers['customer_need_reason']   ?? '',
            $answers['customer_persona']       ?? '',
            $answers['target_audience']        ?? '',
            $answers['customer_testimonials']  ?? '',
        ]);
        $customerScores = $this->extractor->normalize($customerRaw);

        // ── 4. Industry signal injection ─────────────────────────────
        // Add baseline industry signals so cold-start forms still yield meaningful DNA
        $industrySignals = $this->getIndustryBaselineSignals($answers['industry'] ?? '');
        $productScores   = $this->mergeWithBaseline($productScores, $industrySignals, 0.3);

        // ── 5. Relationship mapping ──────────────────────────────────
        $mapping = $this->mapper->map($ownerScores, $productScores, $customerScores);

        $finalScores = $mapping['scores'];
        $sorted      = $finalScores; // already sorted desc by mapper
        arsort($sorted);

        // ── 6. Classify DNA ──────────────────────────────────────────
        $allKeys   = array_keys($sorted);
        $primaryKey   = $allKeys[0] ?? 'craftsmanship';
        $secondaryKey = $allKeys[1] ?? null;

        // Secondary DNA only shown if score is at least 60% of primary
        $primaryScore   = $sorted[$primaryKey] ?? 0;
        $secondaryScore = $secondaryKey ? ($sorted[$secondaryKey] ?? 0) : 0;

        if ($secondaryScore < ($primaryScore * 0.60) || $secondaryScore < 20) {
            $secondaryKey = null;
        }

        // ── 7. Build result ──────────────────────────────────────────
        $primaryDna   = DnaTaxonomy::DNA_TYPES[$primaryKey];
        $primaryStrat = DnaTaxonomy::STRATEGIES[$primaryKey];

        $result = [
            'primary_dna'       => $primaryKey,
            'secondary_dna'     => $secondaryKey,
            'overall_alignment' => $mapping['overall_alignment'],
            'scores'            => $finalScores,
            'dimension_scores'  => [
                'owner'    => $ownerScores,
                'product'  => $productScores,
                'customer' => $customerScores,
            ],
            'alignment_per_dna'  => $mapping['alignment_per_dna'],
            'primary_info'       => $primaryDna,
            'secondary_info'     => $secondaryKey ? DnaTaxonomy::DNA_TYPES[$secondaryKey] : null,
            'strategy'           => $primaryStrat,
            'secondary_strategy' => $secondaryKey ? DnaTaxonomy::STRATEGIES[$secondaryKey] : null,
        ];

        return $result;
    }

    /**
     * Build a compact text summary of the DNA result for injection into AI prompts.
     */
    public function toPromptContext(array $dnaResult): string
    {
        $primary   = $dnaResult['primary_dna'];
        $secondary = $dnaResult['secondary_dna'];
        $score     = $dnaResult['scores'][$primary] ?? 0;
        $alignment = $dnaResult['overall_alignment'];
        $info      = $dnaResult['primary_info'];
        $strat     = $dnaResult['strategy'];

        $mix = implode(', ', array_map(
            fn ($m) => "{$m['pct']}% {$m['label']}",
            $strat['content_mix']
        ));

        $pillars = implode(' | ', $strat['content_pillars']);

        $lines = [
            "=== BRAND DNA (ditentukan oleh Koneva Scoring Engine — JANGAN DIUBAH) ===",
            "DNA Utama    : {$info['name']} — {$info['character']} (Skor: {$score}/100)",
        ];

        if ($secondary) {
            $secInfo  = $dnaResult['secondary_info'];
            $secScore = $dnaResult['scores'][$secondary] ?? 0;
            $lines[]  = "DNA Sekunder : {$secInfo['name']} — {$secInfo['character']} (Skor: {$secScore}/100)";
        }

        $lines[] = "Alignment Score: {$alignment}/100";
        $lines[] = "";
        $lines[] = "Communication Strategy Template (dari Koneva):";
        $lines[] = "Konten Mix   : {$mix}";
        $lines[] = "Pilar Konten : {$pillars}";
        $lines[] = "Tone         : {$strat['posting_tone']}";
        $lines[] = "Hook Style   : {$strat['hook_style']}";
        $lines[] = "=== AKHIR BRAND DNA ===";

        return implode("\n", $lines);
    }

    // ─── Private ────────────────────────────────────────────────────────────────

    /**
     * Industry-specific baseline DNA signals to bootstrap cold-start profiles.
     */
    private function getIndustryBaselineSignals(string $industry): array
    {
        $baselines = [
            'food'       => ['craftsmanship' => 30, 'community' => 20, 'local_pride' => 20],
            'fashion'    => ['lifestyle' => 35, 'craftsmanship' => 20],
            'beauty'     => ['lifestyle' => 30, 'transformation' => 30, 'craftsmanship' => 15],
            'technology' => ['innovation' => 35, 'efficiency' => 25, 'authority' => 15],
            'education'  => ['authority' => 35, 'transformation' => 20, 'community' => 15],
            'health'     => ['transformation' => 30, 'authority' => 25, 'value_driven' => 20],
            'property'   => ['authority' => 25, 'lifestyle' => 20, 'transformation' => 15],
            'retail'     => ['efficiency' => 25, 'value_driven' => 20, 'community' => 15],
            'finance'    => ['authority' => 30, 'efficiency' => 20, 'value_driven' => 20],
            'creative'   => ['innovation' => 30, 'personal_connection' => 25, 'lifestyle' => 20],
        ];

        return $baselines[$industry] ?? [];
    }

    /**
     * Merge industry baseline into product scores with a dilution factor.
     * Only applies to dimensions that haven't received any signal from text.
     */
    private function mergeWithBaseline(array $scores, array $baseline, float $weight): array
    {
        foreach ($baseline as $dna => $baseValue) {
            if (($scores[$dna] ?? 0) < 10) {
                // Only inject baseline when no user signal detected for this dimension
                $scores[$dna] = (int) round($baseValue * $weight);
            }
        }
        return $scores;
    }
}
