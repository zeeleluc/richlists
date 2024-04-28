<?php
namespace App\RichList;

class Config {

    const RIPPLE_PUNKS = 'ripplepunks';

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
        ];

        if (!array_key_exists($projectSlug, $mapper)) {
            return null;
        }

        return $mapper[$projectSlug];
    }
}
