<?php

namespace App\Services;

use App\Models\DocumentNumberingConfig;
use Illuminate\Support\Facades\DB;

class DocumentNumberingService
{
    /**
     * Generate next number for entity type
     */
    public static function generateNextNumber(string $entityType): string
    {
        $config = DocumentNumberingConfig::getActiveConfig($entityType);
        
        if (!$config) {
            throw new \Exception("No active numbering configuration found for entity type: {$entityType}");
        }

        // Check if number should be reset
        if ($config->shouldResetNumber()) {
            $config->resetNumber();
        }

        // Generate the number
        $number = $config->generateNextNumber();
        
        // Increment the current number
        $config->incrementNumber();
        
        return $number;
    }

    /**
     * Generate next number with transaction
     */
    public static function generateNextNumberWithTransaction(string $entityType): string
    {
        return DB::transaction(function () use ($entityType) {
            return self::generateNextNumber($entityType);
        });
    }

    /**
     * Get preview of next number without incrementing
     */
    public static function getPreview(string $entityType): string
    {
        $config = DocumentNumberingConfig::getActiveConfig($entityType);
        
        if (!$config) {
            throw new \Exception("No active numbering configuration found for entity type: {$entityType}");
        }

        return $config->getPreview();
    }

    /**
     * Generate multiple numbers at once
     */
    public static function generateMultipleNumbers(string $entityType, int $count): array
    {
        $numbers = [];
        
        for ($i = 0; $i < $count; $i++) {
            $numbers[] = self::generateNextNumber($entityType);
        }
        
        return $numbers;
    }

    /**
     * Check if entity type has active configuration
     */
    public static function hasActiveConfig(string $entityType): bool
    {
        return DocumentNumberingConfig::getActiveConfig($entityType) !== null;
    }

    /**
     * Get all active configurations
     */
    public static function getActiveConfigs(): \Illuminate\Database\Eloquent\Collection
    {
        return DocumentNumberingConfig::where('is_active', true)->get();
    }

    /**
     * Get configuration for entity type
     */
    public static function getConfig(string $entityType): ?DocumentNumberingConfig
    {
        return DocumentNumberingConfig::getActiveConfig($entityType);
    }

    /**
     * Reset number for entity type
     */
    public static function resetNumber(string $entityType): bool
    {
        $config = DocumentNumberingConfig::getActiveConfig($entityType);
        
        if (!$config) {
            return false;
        }

        $config->resetNumber();
        return true;
    }

    /**
     * Get current number for entity type
     */
    public static function getCurrentNumber(string $entityType): int
    {
        $config = DocumentNumberingConfig::getActiveConfig($entityType);
        
        if (!$config) {
            return 0;
        }

        return $config->current_number;
    }

    /**
     * Generate number with custom format (for testing)
     */
    public static function generateWithCustomFormat(string $entityType, array $customFormat = []): string
    {
        $config = DocumentNumberingConfig::getActiveConfig($entityType);
        
        if (!$config) {
            throw new \Exception("No active numbering configuration found for entity type: {$entityType}");
        }

        // Temporarily update config with custom format
        $originalConfig = $config->toArray();
        
        foreach ($customFormat as $key => $value) {
            if (isset($config->$key)) {
                $config->$key = $value;
            }
        }

        $number = $config->generateNextNumber();
        
        // Restore original config
        foreach ($originalConfig as $key => $value) {
            if (isset($config->$key)) {
                $config->$key = $value;
            }
        }

        return $number;
    }
}
