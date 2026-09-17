<?php

namespace App\Concerns;

trait TogglesWishlist
{
    public function isWishlisted(string $slug): bool
    {
        return in_array($slug, session('wishlist', []));
    }

    public function toggleWishlist(string $slug): void
    {
        $wishlist = session('wishlist', []);

        if (in_array($slug, $wishlist)) {
            $wishlist = array_values(array_diff($wishlist, [$slug]));
        } else {
            $wishlist[] = $slug;
        }

        session(['wishlist' => $wishlist]);
    }
}