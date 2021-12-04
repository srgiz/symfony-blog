<?php
declare(strict_types=1);

namespace App\Nav;

use App\Dto\Nav\NavCollection;
use App\Dto\Nav\NavItem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class SiteNavBuilder implements SiteNavBuilderInterface
{
    public function __construct(
        private TagAwareCacheInterface $cache,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    public function public(): NavCollection
    {
        $collection = new NavCollection();

        $collection->add(new NavItem('/', 'Home'));

        return $collection;
    }

    public function backend(): NavCollection
    {
        return $this->cache->get('app_nav_backend', function (ItemInterface $item) {
            $collection = new NavCollection();

            $collection->add(new NavItem($this->urlGenerator->generate('backend_dashboard'), 'Dashboard'));
            $collection->add(new NavItem($this->urlGenerator->generate('backend_dashboard'), 'Users'));

            //$item->tag(['app1']);
            $item->expiresAfter(60);

            return $collection;
        });
    }
}
