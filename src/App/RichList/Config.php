<?php
namespace App\RichList;

class Config {

    const RIPPLE_PUNKS = 'ripplepunks';

    const HAS_MINTS = 'hasmints';

    const ASTRO_NAUGHTIES = 'astronaughties';

    private array $richLists = [
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
            self::RIPPLE_PUNKS => 'RipplePunks',
            self::HAS_MINTS => 'HasMints',
            self::ASTRO_NAUGHTIES => 'AstroNaughties',
        ];

        if (!array_key_exists($projectSlug, $mapper)) {
            return $projectSlug;
        }

        return $mapper[$projectSlug];
    }
}
