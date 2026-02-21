# EVE Online Image Server

A lightweight, framework-agnostic PHP 8.3+ client for the [EVE Online Image Server](https://docs.esi.evetech.net/docs/image_server.html).

Generates correctly structured image URLs for characters, corporations, alliances, NPC factions, and all type variations (icons, renders, blueprints, relics).

---

## Requirements

- PHP **8.3** or higher
- No framework dependencies

---

## Installation

```bash
composer require eve-metis/evetech-image-server
```

---

## Quick Start

```php
use EveMetis\EveImageServer\ImageServer;

$imageServer = new ImageServer();

// Character portrait
echo $imageServer->getCharacterPortrait(1338057886);
// https://images.evetech.net/characters/1338057886/portrait?size=128&tenant=tranquility

// Ship render
echo $imageServer->getTypeRender(587, 512);
// https://images.evetech.net/types/587/render?size=512&tenant=tranquility

// Alliance logo
echo $imageServer->getAllianceLogo(434243723, 64);
// https://images.evetech.net/alliances/434243723/logo?size=64&tenant=tranquility
```

---

## Usage

### Characters

```php
$imageServer->getCharacterPortrait(int $characterId, int $size = 128, string $tenant = 'tranquility'): string
```

Returns a JPEG URL. All other image types return PNGs.

```php
$imageServer->getCharacterPortrait(1338057886);        // default 128px
$imageServer->getCharacterPortrait(1338057886, 512);   // 512px
```

---

### Alliances

```php
$imageServer->getAllianceLogo(int $allianceId, int $size = 128, string $tenant = 'tranquility'): string
```

```php
$imageServer->getAllianceLogo(434243723);
$imageServer->getAllianceLogo(434243723, 256);
```

---

### Corporations

```php
$imageServer->getCorporationLogo(int $corporationId, int $size = 128, string $tenant = 'tranquility'): string
```

```php
$imageServer->getCorporationLogo(109299958);
```

---

### NPC Factions

NPC faction logos are served via the `corporations` category using the faction ID.

```php
$imageServer->getFactionLogo(int $factionId, int $size = 128, string $tenant = 'tranquility'): string
```

```php
$imageServer->getFactionLogo(500001); // Caldari State
```

---

### Types

Types support multiple image variations depending on the item category.

| Method | Variation | Use case |
|---|---|---|
| `getTypeIcon()` | `icon` | All types — ships, modules, stations, etc. |
| `getTypeRender()` | `render` | Ships and some structures (3D render) |
| `getTypeBlueprint()` | `bp` | Blueprint originals |
| `getTypeBlueprintCopy()` | `bpc` | Blueprint copies |
| `getTypeRelic()` | `relic` | Sleeper relic/salvage types |

```php
$imageServer->getTypeIcon(int $typeId, int $size = 128, string $tenant = 'tranquility'): string
$imageServer->getTypeRender(int $typeId, int $size = 128, string $tenant = 'tranquility'): string
$imageServer->getTypeBlueprint(int $typeId, int $size = 128, string $tenant = 'tranquility'): string
$imageServer->getTypeBlueprintCopy(int $typeId, int $size = 128, string $tenant = 'tranquility'): string
$imageServer->getTypeRelic(int $typeId, int $size = 128, string $tenant = 'tranquility'): string
```

```php
$imageServer->getTypeIcon(587);            // Rifter icon
$imageServer->getTypeRender(587, 512);     // Rifter render at 512px
$imageServer->getTypeBlueprint(11568);     // Avatar Blueprint original
$imageServer->getTypeBlueprintCopy(11568); // Avatar Blueprint copy
$imageServer->getTypeRelic(30752);         // Intact Hull Section relic
```

> **Note:** Not all types support every variation. The image server will return a `404` if the variation doesn't exist for a given type (e.g., requesting a `render` for a module, or a `relic` for a ship). Handle this appropriately in your application.

---

### Size Parameter

Valid sizes are: `32`, `64`, `128`, `256`, `512`, `1024`.

If an invalid size is provided, it silently falls back to `128`. You can reference the valid set from the constant:

```php
ImageServer::VALID_SIZES; // [32, 64, 128, 256, 512, 1024]
```

---

### Tenant Parameter

The `tenant` parameter selects which server's images are returned. Defaults to `tranquility` (live server). Use `singularity` for the test server.

```php
use EveMetis\EveImageServer\Enum\Tenant;

$imageServer->getCharacterPortrait(1338057886, 128, Tenant::SINGULARITY->value);
// or just pass the string directly:
$imageServer->getCharacterPortrait(1338057886, 128, 'singularity');
```

---

### Default/Placeholder Images

The image server returns a default placeholder for ID `1` across all categories:

```php
$imageServer->getCharacterPortrait(1);  // default portrait
$imageServer->getCorporationLogo(1);    // default corp logo
$imageServer->getAllianceLogo(1);       // default alliance logo
```

---

## Testing

```bash
composer test
```

---

## Static Analysis

```bash
composer phpstan
```

---

## Code Style

Check:
```bash
composer phpcs
```

Fix:
```bash
composer phpcbf
```

---

## Rector (Automated Refactoring)

Dry run:
```bash
composer rector -- --dry-run
```

Apply:
```bash
composer rector
```

---

## Contributing

Contributions are welcome. Please ensure all the following pass before opening a pull request:

```bash
composer test
composer phpstan
composer phpcs
composer rector -- --dry-run
```

---

## Licence

MIT. See [LICENSE](LICENSE) for details.
