<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    // Cache durations in minutes
    const DASHBOARD_TTL = 5;
    const TREE_TTL = 15;
    const STATS_TTL = 10;
    const MEMBER_COUNT_TTL = 10;

    /**
     * Get or set dashboard statistics
     */
    public function getDashboardStats(int $clanId, callable $callback): array
    {
        $key = "dashboard_stats_{$clanId}";
        
        return Cache::remember($key, self::DASHBOARD_TTL * 60, $callback);
    }

    /**
     * Get or set family tree
     */
    public function getTreeData(int $id, string $type, callable$callback): array
    {
        $key = "tree_{$type}_{$id}";
        
        return Cache::remember($key, self::TREE_TTL * 60, $callback);
    }

    /**
     * Invalidate dashboard cache
     */
    public function invalidateDashboard(int $clanId): void
    {
        Cache::forget("dashboard_stats_{$clanId}");
    }

    /**
     * Invalidate tree cache
     */
    public function invalidateTree(int $id, string $type): void
    {
        Cache::forget("tree_{$type}_{$id}");
    }

    /**
     * Invalidate all caches for a clan
     */
    public function invalidateClan(int $clanId): void
    {
        Cache::tags(["clan_{$clanId}"])->flush();
        $this->invalidateDashboard($clanId);
    }

    /**
     * Clear all family tree caches
     */
    public function clearAll(): void
    {
        Cache::tags(['family_tree'])->flush();
    }
}
