<?php

namespace App\Services;

class KNearestNeighborsService
{
    private int $k;

    /** @var list<array{features: array<int, float>, label: string, name: string}> */
    private array $dataset = [];

    /** @var array<int, float> */
    private array $mins = [];

    /** @var array<int, float> */
    private array $maxs = [];

    public function __construct(int $k = 3)
    {
        $this->k = $k;
    }

    /**
     * Train the model with a customer record
     *
     * @param  array<int, float>  $features
     */
    public function train(array $features, string $label, string $name = ''): void
    {
        $this->dataset[] = [
            'features' => $features, // [avg_sweetness, coffee_ratio, avg_spending]
            'label' => $label,
            'name' => $name,
        ];
    }

    /**
     * Calculate min and max for each feature to perform scaling
     */
    private function calculateScalingParams(): void
    {
        if (empty($this->dataset)) {
            return;
        }

        $numFeatures = count($this->dataset[0]['features']);
        $this->mins = array_fill(0, $numFeatures, INF);
        $this->maxs = array_fill(0, $numFeatures, -INF);

        foreach ($this->dataset as $data) {
            foreach ($data['features'] as $i => $val) {
                if ($val < $this->mins[$i]) {
                    $this->mins[$i] = (float) $val;
                }
                if ($val > $this->maxs[$i]) {
                    $this->maxs[$i] = (float) $val;
                }
            }
        }
    }

    /**
     * Normalize a single point's features
     *
     * @param  array<int, float>  $features
     * @return array<int, float>
     */
    public function normalize(array $features): array
    {
        $normalized = [];
        foreach ($features as $i => $val) {
            $min = $this->mins[$i] ?? 0.0;
            $max = $this->maxs[$i] ?? 1.0;

            if ($max - $min == 0.0) {
                $normalized[] = 0.5;
            } else {
                $normalized[] = (float) (($val - $min) / ($max - $min));
            }
        }

        return $normalized;
    }

    /**
     * Get dataset count
     */
    public function getDatasetCount(): int
    {
        return count($this->dataset);
    }

    /**
     * Calculate Euclidean distance between two normalized feature vectors
     *
     * @param  array<int, float>  $p1
     * @param  array<int, float>  $p2
     */
    private function calculateDistance(array $p1, array $p2): float
    {
        $sum = 0.0;
        for ($i = 0; $i < count($p1); $i++) {
            $sum += pow(($p1[$i] - $p2[$i]), 2);
        }

        return (float) sqrt($sum);
    }

    /**
     * Classify a new customer's features
     * Returns an array with the winning label and execution details (for UI transparency)
     *
     * @param  array<int, float>  $newFeatures
     * @return array{
     *     label: string,
     *     neighbors: list<array<string, mixed>>,
     *     all_distances: list<array<string, mixed>>,
     *     normalized_test: array<int, float>,
     *     scaling_mins: array<int, float>,
     *     scaling_maxs: array<int, float>
     * }
     */
    public function classify(array $newFeatures): array
    {
        if (empty($this->dataset)) {
            return [
                'label' => 'Unknown',
                'neighbors' => [],
                'all_distances' => [],
                'normalized_test' => [],
                'scaling_mins' => [],
                'scaling_maxs' => [],
            ];
        }

        // 1. Calculate scaling parameters (min & max of training set)
        $this->calculateScalingParams();

        // 2. Normalize the input test features
        $normalizedNewFeatures = $this->normalize($newFeatures);

        // 3. Compute distances to all training points
        $distances = [];
        foreach ($this->dataset as $data) {
            $normalizedTrainFeatures = $this->normalize($data['features']);
            $dist = $this->calculateDistance($normalizedNewFeatures, $normalizedTrainFeatures);

            $distances[] = [
                'name' => $data['name'],
                'features' => $data['features'],
                'normalized_features' => $normalizedTrainFeatures,
                'distance' => (float) round($dist, 4),
                'label' => $data['label'],
            ];
        }

        // 4. Sort by distance ascending
        usort($distances, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        // 5. Take top K neighbors
        $neighbors = array_slice($distances, 0, $this->k);

        // 6. Majority vote
        $counts = array_count_values(array_column($neighbors, 'label'));
        arsort($counts);
        $winner = array_key_first($counts) ?? 'Unknown';

        return [
            'label' => $winner,
            'neighbors' => $neighbors,
            'all_distances' => $distances, // to display in UI table
            'normalized_test' => $normalizedNewFeatures,
            'scaling_mins' => $this->mins,
            'scaling_maxs' => $this->maxs,
        ];
    }
}
