<?php

namespace App\Factory;

use App\DTO\Probe\CreateProbeDto;
use App\Entity\Probe;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Probe>
 */
final class ProbeFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return Probe::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->text(40),
            'enabled' => true,
            'default' => false,
            'token' => bin2hex(random_bytes(32)),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->beforeInstantiate(function (array $attributes): array {
                // Create DTO from the attributes
                $dto = new CreateProbeDto();
                $dto->name = $attributes['name'];
                $dto->enabled = $attributes['enabled'];
                $dto->default = $attributes['default'];

                return [
                    'dto' => $dto,
                    'token' => $attributes['token'],
                ];
            })
            ->afterInstantiate(function (Probe $probe): void {
                $probe->setToken(hash('sha256', $probe->getToken()));
            });
    }
}
