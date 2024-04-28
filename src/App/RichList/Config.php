<?php
namespace App\RichList;

class Config {

    private array $richLists = [
        'ripplepunks' => [
            [
                // RipplePunks
                'issuer' => 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS',
                'taxon' => 604,
            ],
            [
                // RipplePunks Rewind
                'issuer' => 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS',
                'taxon' => 48919,
            ],
            [
                // RipplePunks Quartet
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

}
