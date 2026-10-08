<?php

namespace App\Services\Tariff\Calculators;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use Illuminate\Support\Facades\Log;

class CalculatorRegistry
{
    /**
     * @var array<string, string|TariffCalculator>
     */
    protected array $calculators = [];

    /**
     * نقشه‌ی نوع تعرفه — برای زمانی که Calculator متد type() نداره
     */
    protected array $typeMap = [
        // عمرانی / عوارض محلی (levy)
        'annual_property'         => 'levy',
        'sub_building'            => 'levy',
        'fence_permit'            => 'levy',
        'tree_cutting'            => 'levy',
        'value_added'             => 'levy',
        'value_added_zoning'      => 'levy',
        'value_added_development' => 'levy',
        'article_99_penalty'      => 'levy',

        // بهای خدمات (service_fee)
        'businesses'              => 'service_fee',
        'permit_renewal'          => 'service_fee',
        'machinery_hourly'        => 'service_fee',
        'excavation'              => 'service_fee',
        'waste_residential'       => 'service_fee',
        'waste_commercial'        => 'service_fee',
        'waste_industrial'        => 'service_fee',
    ];

    /**
     * ثبت ماشین‌حساب
     * حالت اصلی: register('key', ClassName::class)
     * حالت instance: register($instance)
     */
    public function register(string|TariffCalculator $key, ?string $class = null): self
    {
        if ($key instanceof TariffCalculator) {
            // instance mode
            $this->calculators[$key->key()] = $key;
        } elseif (is_string($key) && $class) {
            // string mode
            $this->calculators[$key] = $class;
        } else {
            throw new InvalidArgumentException(
                'register() needs either an instance or (string $key, string $class)'
            );
        }
        return $this;
    }

    public function has(string $key): bool
    {
        return isset($this->calculators[$key]);
    }

    public function get(string $key): TariffCalculator
    {
        if (!$this->has($key)) {
            throw new InvalidArgumentException("Calculator [{$key}] not found.");
        }

        $value = $this->calculators[$key];

        if (is_string($value)) {
            $value = app($value);
            $this->calculators[$key] = $value;
        }

        if (!$value instanceof TariffCalculator) {
            throw new RuntimeException(
                "Calculator [{$key}] must implement TariffCalculator"
            );
        }

        return $value;
    }

    public function all(): Collection
    {
        $result = [];
        foreach (array_keys($this->calculators) as $key) {
            try {
                $result[$key] = $this->get($key);
            } catch (\Throwable $e) {
                Log::warning("Calculator [{$key}] failed to load: " . $e->getMessage());
            }
        }
        return collect($result);
    }

    /**
     * فیلتر بر اساس نوع تعرفه
     */
    public function ofType(string $type): Collection
    {
        $all = $this->all();

        return $all->filter(function ($c, $key) use ($type) {
            // اگر Calculator متد type() داره، استفاده کن
            if (method_exists($c, 'type')) {
                try {
                    return $c->type() === $type;
                } catch (\Throwable $e) {
                    // ادامه بده
                }
            }
            // وگرنه از مپ استفاده کن
            return ($this->typeMap[$key] ?? 'levy') === $type;
        });
    }

    /**
     * لیست همه‌ی typeMap — برای دیباگ
     */
    public function typeMap(): array
    {
        return $this->typeMap;
    }
}
