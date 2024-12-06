<?php

class Migration
{
    public function run()
    {
        $userId = 15; // Paid WL Habibi
        $this->createCollectionBase($userId, 'Chonks', '0x07152bfde079b5319e5308C43fB1Dbc9C76cb4F9');
        $this->createCollectionBase($userId, 'based punks', '0xcB28749c24AF4797808364D71d71539bc01E76d4');
        $this->createCollectionBase($userId, 'No-Punks', '0x4ed83635E2309A7C067d0F98EfCa47B920bF79B1');
        $this->createCollectionBase($userId, 'Mini Moments', '0x1f72101e0F35EB9dA46aD0238b4A9c1e997AC12e');
        $this->createCollectionBase($userId, 'Based Neiro', '0x1AA8931c79C885f71Bf5DA21628e3F7d5DE56174');
        $this->createCollectionBase($userId, 'Miggles On Base', '0x71cfBEbb61a42d2E5ccFf0831663Cd58d2E442d9');
        $this->createCollectionBase($userId, 'Villains by Nev.', '0xA91B95a1DBA98E3537eAd7aA4A488A8886316be2');
        $this->createCollectionBase($userId, 're:generates', '0x56dFE6ae26bf3043DC8Fdf33bF739B4fF4B3BC4A');
        $this->createCollectionBase($userId, 'Fat Cats', '0x545eEE86fD6a81bb0B229283B55E212814f9C0A2');
        $this->createCollectionBase($userId, 'Bucked Blown', '0x13E09Ef7046442B67dd45A4FA4Ca61feB2eB30Aa');
        $this->createCollectionBase($userId, 'MicroMigos', '0x2a20FbCf095eb347edA8C107Bc3ddFF165E0f22b');
    }

    private function createCollectionBase($userId, string $name, string $contract): void
    {
        $collection = new \App\Models\Collection();
        $collection = $collection->fromArray([
            'user_id' => $userId,
            'chain' => 'base',
            'name' => $name,
            'config' => json_encode(['contract' => $contract]),
        ]);
        $collection->save();
    }
}
