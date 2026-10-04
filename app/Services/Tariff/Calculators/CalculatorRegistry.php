<?php

namespace App\Services\Tariff\Calculators;

class CalculatorRegistry
{
    /** @var array<string, CalculatorInterface> */
    private array $calculators = [];

    public function __construct()
    {
        foreach ($this->defaults() as $c) {
            $this->register($c);
        }
    }

    public function register(CalculatorInterface $c): void
    {
        $this->calculators[$c->key()] = $c;
    }

    public function has(string $key): bool
    {
        return isset($this->calculators[$key]);
    }

    public function get(string $key): ?CalculatorInterface
    {
        return $this->calculators[$key] ?? null;
    }

    /** @return array<string, CalculatorInterface> */
    public function all(): array
    {
        return $this->calculators;
    }

    /** @return CalculatorInterface[] */
    private function defaults(): array
    {
        return [
            new AnnualPropertyLevyCalculator(),
            new SubBuildingCalculator(),
            new FencePermitCalculator(),
            new TreeCuttingCalculator(),
            new BusinessesCalculator(),
            new PermitRenewalCalculator(),
            new MachineryHourlyCalculator(),
            new ExcavationCalculator(),
            new WasteCalculator(),
            new Article99PenaltyCalculator(),
        ];
    }
}
