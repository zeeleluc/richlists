<?php
namespace App\RichList;

class Config {

    const PIXEL_ASTROS = 'pixelastros';
    const ASTRO_NAUGHTIES = 'astronaughties';
    const RIPPLE_PUNKS = 'ripplepunks';
    const HAS_MINTS = 'hasmints';


    private array $richLists = [
        self::PIXEL_ASTROS => [
            [
                'name' => 'PixelAstros',
                'issuer' => 'rLULtFuV1agdSQdVmSd7AYx2cfEiN6noxY',
                'taxon' => 34637,
            ],
        ],
        self::ASTRO_NAUGHTIES => [
            [
                'name' => 'AstroNaughties',
                'issuer' => 'rGNuFE4e2c5NwEp2HnuiJqaSVdaNYRQ7PV',
                'taxon' => 53,
            ],
            [
                'name' => 'Older Collections',
                'issuer' => 'rMQw4pe2eXvs6b5hKLNM2MCgVqHEHwXBkJ',
            ],
        ],
        self::RIPPLE_PUNKS => [
            [
                'name' => 'RipplePunks',
                'issuer' => 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS',
                'taxon' => 604,
            ],
            [
                'name' => 'RipplePunks Rewind',
                'issuer' => 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS',
                'taxon' => 48919,
            ],
            [
                'name' => 'RipplePunks Quartet',
                'issuer' => 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS',
                'taxon' => 23578,
            ],
            [
                'name' => 'RipplePunks On Demand',
                'issuer' => 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS',
                'taxon' => 49619,
            ],
        ],
        self::HAS_MINTS => [
            [
                'name' => 'RipplePunks',
                'issuer' => 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS',
                'taxon' => 604,
            ],
            [
                'name' => 'RipplePunks Rewind',
                'issuer' => 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS',
                'taxon' => 48919,
            ],
            [
                'name' => 'RipplePunks Quartet',
                'issuer' => 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS',
                'taxon' => 23578,
            ],
            [
                'name' => 'RipplePunks On Demand',
                'issuer' => 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS',
                'taxon' => 49619,
            ],
            [
                'name' => 'xLooney Luca',
                'issuer' => 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS',
                'taxon' => 6929464,
            ],
            [
                'name' => 'XRPunks',
                'issuer' => 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS',
                'taxon' => 6669233,
            ],
        ],
    ];

    public function getProjectsIssuerTaxon(string $projectName = null):? array
    {
        if ($projectName) {
            if (array_key_exists($projectName, $this->richLists)) {
                return $this->richLists[$projectName];
            }

            return null;
        }

        return $this->richLists;
    }

    public static function mapProjectNameSlug(string $projectSlug):? string
    {
        $mapper = [
            self::PIXEL_ASTROS => 'PixelAstros',
            self::ASTRO_NAUGHTIES => 'AstroNaughties',
            self::RIPPLE_PUNKS => 'RipplePunks',
            self::HAS_MINTS => 'HasMints',
        ];

        if (!array_key_exists($projectSlug, $mapper)) {
            return $projectSlug;
        }

        return $mapper[$projectSlug];
    }
}
