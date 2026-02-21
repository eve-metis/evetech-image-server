<?php

declare(strict_types=1);

namespace EveMetis\EveImageServer;

use EveMetis\EveImageServer\Enum\Tenant;

/**
 * Client for the EVE Online Image Server.
 *
 * URL format: https://images.evetech.net/{category}/{id}/{variation}?size={size}&tenant={tenant}
 *
 * Categories: alliances, corporations, characters, types
 * Variations:
 *   alliances: logo
 *   corporations: logo (also covers NPC factions via faction ID)
 *   characters: portrait
 *   types: icon, render, bp, bpc, relic
 *
 * @see https://docs.esi.evetech.net/docs/image_server.html
 */
final readonly class ImageServer
{
    /** @var int[] */
    public const array VALID_SIZES = [32, 64, 128, 256, 512, 1024];

    private const string BASE_URL = 'https://images.evetech.net';
    private const int DEFAULT_SIZE = 128;

    // -------------------------------------------------------------------------
    // Characters
    // -------------------------------------------------------------------------

    /**
     * Get the portrait URL for a character.
     * Note: character portraits are returned as JPEGs; all other types are PNGs.
     */
    public function getCharacterPortrait(
        int $characterId,
        int $size = self::DEFAULT_SIZE,
        string $tenant = Tenant::TRANQUILITY->value,
    ): string {
        return $this->buildUrl('characters', $characterId, 'portrait', $size, $tenant);
    }

    // -------------------------------------------------------------------------
    // Alliances
    // -------------------------------------------------------------------------

    public function getAllianceLogo(
        int $allianceId,
        int $size = self::DEFAULT_SIZE,
        string $tenant = Tenant::TRANQUILITY->value,
    ): string {
        return $this->buildUrl('alliances', $allianceId, 'logo', $size, $tenant);
    }

    // -------------------------------------------------------------------------
    // Corporations & NPC Factions
    // -------------------------------------------------------------------------

    public function getCorporationLogo(
        int $corporationId,
        int $size = self::DEFAULT_SIZE,
        string $tenant = Tenant::TRANQUILITY->value,
    ): string {
        return $this->buildUrl('corporations', $corporationId, 'logo', $size, $tenant);
    }

    /**
     * NPC faction logos are served via the corporations category using the faction ID.
     */
    public function getFactionLogo(
        int $factionId,
        int $size = self::DEFAULT_SIZE,
        string $tenant = Tenant::TRANQUILITY->value,
    ): string {
        return $this->buildUrl('corporations', $factionId, 'logo', $size, $tenant);
    }

    // -------------------------------------------------------------------------
    // Types
    // -------------------------------------------------------------------------

    public function getTypeIcon(
        int $typeId,
        int $size = self::DEFAULT_SIZE,
        string $tenant = Tenant::TRANQUILITY->value,
    ): string {
        return $this->buildUrl('types', $typeId, 'icon', $size, $tenant);
    }

    /**
     * Available for ships and some structures.
     */
    public function getTypeRender(
        int $typeId,
        int $size = self::DEFAULT_SIZE,
        string $tenant = Tenant::TRANQUILITY->value,
    ): string {
        return $this->buildUrl('types', $typeId, 'render', $size, $tenant);
    }

    public function getTypeBlueprint(
        int $typeId,
        int $size = self::DEFAULT_SIZE,
        string $tenant = Tenant::TRANQUILITY->value,
    ): string {
        return $this->buildUrl('types', $typeId, 'bp', $size, $tenant);
    }

    public function getTypeBlueprintCopy(
        int $typeId,
        int $size = self::DEFAULT_SIZE,
        string $tenant = Tenant::TRANQUILITY->value,
    ): string {
        return $this->buildUrl('types', $typeId, 'bpc', $size, $tenant);
    }

    /**
     * Used for Sleeper relic/salvage types.
     */
    public function getTypeRelic(
        int $typeId,
        int $size = self::DEFAULT_SIZE,
        string $tenant = Tenant::TRANQUILITY->value,
    ): string {
        return $this->buildUrl('types', $typeId, 'relic', $size, $tenant);
    }

    // -------------------------------------------------------------------------
    // Internal
    // -------------------------------------------------------------------------

    private function buildUrl(
        string $category,
        int $id,
        string $variation,
        int $size,
        string $tenant,
    ): string {
        $resolvedSize = $this->isValidSize($size) ? $size : self::DEFAULT_SIZE;

        $params = http_build_query([
            'size' => $resolvedSize,
            'tenant' => $tenant,
        ]);

        return sprintf('%s/%s/%d/%s?%s', self::BASE_URL, $category, $id, $variation, $params);
    }

    private function isValidSize(int $size): bool
    {
        return in_array($size, self::VALID_SIZES, strict: true);
    }
}
