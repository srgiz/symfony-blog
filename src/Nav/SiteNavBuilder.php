<?php
declare(strict_types=1);

namespace App\Nav;

use App\Dto\Nav\NavCollection;
use App\Dto\Nav\NavItem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SiteNavBuilder implements SiteNavBuilderInterface
{
    public function __construct(
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
        $collection = new NavCollection();

        $collection->add(new NavItem($this->urlGenerator->generate('backend_dashboard'), 'Dashboard'));
        $collection->add(new NavItem($this->urlGenerator->generate('backend_dashboard'), 'Users'));

        return $collection;
    }
}
