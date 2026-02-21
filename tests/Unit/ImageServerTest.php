<?php

declare(strict_types=1);

namespace EveMetis\EveImageServer\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use EveMetis\EveImageServer\Enum\Tenant;
use EveMetis\EveImageServer\ImageServer;

final class ImageServerTest extends TestCase
{
    private ImageServer $imageServer;

    protected function setUp(): void
    {
        $this->imageServer = new ImageServer();
    }

    // -------------------------------------------------------------------------
    // Characters
    // -------------------------------------------------------------------------

    #[Test]
    public function character_portrait_default_size(): void
    {
        $url = $this->imageServer->getCharacterPortrait(1338057886);

        $this->assertSame(
            'https://images.evetech.net/characters/1338057886/portrait?size=128&tenant=tranquility',
            $url
        );
    }

    #[Test]
    public function character_portrait_custom_size(): void
    {
        $url = $this->imageServer->getCharacterPortrait(1338057886, 512);

        $this->assertStringContainsString('size=512', $url);
    }

    #[Test]
    public function character_portrait_invalid_size_falls_back_to_128(): void
    {
        $url = $this->imageServer->getCharacterPortrait(1338057886, 999);

        $this->assertStringContainsString('size=128', $url);
    }

    #[Test]
    public function character_portrait_singularity_tenant(): void
    {
        $url = $this->imageServer->getCharacterPortrait(1338057886, 128, Tenant::SINGULARITY->value);

        $this->assertStringContainsString('tenant=singularity', $url);
    }

    #[Test]
    public function character_portrait_uses_correct_category_and_variation(): void
    {
        $url = $this->imageServer->getCharacterPortrait(1338057886);

        $this->assertStringContainsString('/characters/1338057886/portrait', $url);
    }

    // -------------------------------------------------------------------------
    // Alliances
    // -------------------------------------------------------------------------

    #[Test]
    public function alliance_logo_default(): void
    {
        $url = $this->imageServer->getAllianceLogo(434243723);

        $this->assertSame(
            'https://images.evetech.net/alliances/434243723/logo?size=128&tenant=tranquility',
            $url
        );
    }

    #[Test]
    public function alliance_logo_custom_size(): void
    {
        $url = $this->imageServer->getAllianceLogo(434243723, 64);

        $this->assertStringContainsString('size=64', $url);
    }

    // -------------------------------------------------------------------------
    // Corporations
    // -------------------------------------------------------------------------

    #[Test]
    public function corporation_logo_default(): void
    {
        $url = $this->imageServer->getCorporationLogo(109299958);

        $this->assertSame(
            'https://images.evetech.net/corporations/109299958/logo?size=128&tenant=tranquility',
            $url
        );
    }

    // -------------------------------------------------------------------------
    // NPC Factions (served via corporations category)
    // -------------------------------------------------------------------------

    #[Test]
    public function faction_logo_uses_corporations_category(): void
    {
        // Caldari State: 500001
        $url = $this->imageServer->getFactionLogo(500001);

        $this->assertSame(
            'https://images.evetech.net/corporations/500001/logo?size=128&tenant=tranquility',
            $url
        );
    }

    // -------------------------------------------------------------------------
    // Types — icon
    // -------------------------------------------------------------------------

    #[Test]
    public function type_icon_default(): void
    {
        // Rifter: 587
        $url = $this->imageServer->getTypeIcon(587);

        $this->assertSame(
            'https://images.evetech.net/types/587/icon?size=128&tenant=tranquility',
            $url
        );
    }

    #[Test]
    public function type_icon_invalid_size_falls_back_to_128(): void
    {
        $url = $this->imageServer->getTypeIcon(587, 100);

        $this->assertStringContainsString('size=128', $url);
    }

    // -------------------------------------------------------------------------
    // Types — render
    // -------------------------------------------------------------------------

    #[Test]
    public function type_render(): void
    {
        $url = $this->imageServer->getTypeRender(587);

        $this->assertSame(
            'https://images.evetech.net/types/587/render?size=128&tenant=tranquility',
            $url
        );
    }

    // -------------------------------------------------------------------------
    // Types — blueprints
    // -------------------------------------------------------------------------

    #[Test]
    public function type_blueprint(): void
    {
        // Avatar Blueprint: 11568
        $url = $this->imageServer->getTypeBlueprint(11568);

        $this->assertSame(
            'https://images.evetech.net/types/11568/bp?size=128&tenant=tranquility',
            $url
        );
    }

    #[Test]
    public function type_blueprint_copy(): void
    {
        $url = $this->imageServer->getTypeBlueprintCopy(11568);

        $this->assertSame(
            'https://images.evetech.net/types/11568/bpc?size=128&tenant=tranquility',
            $url
        );
    }

    // -------------------------------------------------------------------------
    // Types — relic
    // -------------------------------------------------------------------------

    #[Test]
    public function type_relic(): void
    {
        // Intact Hull Section: 30752
        $url = $this->imageServer->getTypeRelic(30752);

        $this->assertSame(
            'https://images.evetech.net/types/30752/relic?size=128&tenant=tranquility',
            $url
        );
    }

    // -------------------------------------------------------------------------
    // Size validation
    // -------------------------------------------------------------------------
    #[DataProvider('validSizeProvider')]
    #[Test]
    public function all_valid_sizes_are_accepted(int $size): void
    {
        $url = $this->imageServer->getTypeIcon(587, $size);

        $this->assertStringContainsString("size={$size}", $url);
    }

    /** @return array<string, array{int}> */
    public static function validSizeProvider(): array
    {
        return array_combine(
            array_map(static fn(int $s): string => "size_{$s}", ImageServer::VALID_SIZES),
            array_map(static fn(int $s): array => [$s], ImageServer::VALID_SIZES),
        );
    }

    // -------------------------------------------------------------------------
    // Default/placeholder images (ID = 1)
    // -------------------------------------------------------------------------

    #[Test]
    public function default_character_placeholder(): void
    {
        $url = $this->imageServer->getCharacterPortrait(1);

        $this->assertStringContainsString('/characters/1/portrait', $url);
    }

    #[Test]
    public function default_corporation_placeholder(): void
    {
        $url = $this->imageServer->getCorporationLogo(1);

        $this->assertStringContainsString('/corporations/1/logo', $url);
    }

    #[Test]
    public function default_alliance_placeholder(): void
    {
        $url = $this->imageServer->getAllianceLogo(1);

        $this->assertStringContainsString('/alliances/1/logo', $url);
    }
}
