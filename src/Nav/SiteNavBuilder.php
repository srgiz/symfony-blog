<?php
declare(strict_types=1);

namespace App\Nav;

use App\Nav\Dto\NavItem;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

readonly class SiteNavBuilder implements SiteNavBuilderInterface
{
    public function __construct(
        private TagAwareCacheInterface $cache,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    /**
     * @return array<NavItem>
     */
    public function public(): array
    {
        return [];
    }

    /**
     * @return array<NavItem>
     * @throws InvalidArgumentException
     */
    public function backend(): array
    {
        return $this->cache->get('app_nav_backend', function (ItemInterface $item) {
            $list = [];
            $list[] = $this->genItem('index', 'Главная');
            $list[] = $this->genItem('backend_dashboard', 'Пользователи');

            //$item->tag(['app1']);
            $item->expiresAfter(60);
            return $list;
        });
    }

    private function genItem(string $route, string $title): NavItem
    {
        return new NavItem($this->urlGenerator->generate($route), $title);
    }
}
