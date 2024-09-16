<?php

class Migration
{
    public function run()
    {
        $auth = new \App\Auth();

        $user = new \App\Models\User();
        $user = $user->fromArray([
            'email' => 'test@example.com',
            'password' => $auth->hashPassword('test' . env('DEFAULT_PASSWORD_AFFIX')),
            'project_name' => 'Test',
            'project_slug' => 'test',
            'token' => generate_token(),
            'token_expires_at' => \Carbon\Carbon::now()->addYearNoOverflow()->format('Y-m-d H:i:s'),
        ]);
        $user = $user->save();

        $this->createCollectionBase($user, 'based punks', '0xcB28749c24AF4797808364D71d71539bc01E76d4');
        $this->createCollectionBase($user, 'No-Punks', '0x4ed83635E2309A7C067d0F98EfCa47B920bF79B1');
        $this->createCollectionBase($user, 'Based Nakamigos', '0xB3663f1c1B7d7b3AE45c031abD4c3CB13f8eE984');
        $this->createCollectionBase($user, 'Tiny DinoPunks', '0x20Be3B999421A12F1247C33519874a73FcE88FD1');
        $this->createCollectionBase($user, 'Based Chimpers', '0x4C7A450BDd0AB7b08e79aF7201dC5BEF14aE2A4b');
        $this->createCollectionBase($user, 'HaHa Hyenas', '0xf225D5386422B836f6C79fC65CcE06DE56eb0e99');
        $this->createCollectionBase($user, 'NoUnkes', '0x10aaeE376a1F7C5d70E72778FFF1b82C2E23ABe2');
        $this->createCollectionBase($user, 'Bario Punks', '0xDC1C20Df3F8EDeDF1466399C5d5D17d864bD3F0f');
        $this->createCollectionBase($user, 'Based Birds', '0xA854bAff5Bc500775C151614D648EdEe5cE8D9E7');
        $this->createCollectionBase($user, 'Miggles On Base', '0x71cfBEbb61a42d2E5ccFf0831663Cd58d2E442d9');
        $this->createCollectionBase($user, 'Based PNUKS', '0xB87E6B437887AFc65265Da34Dc7a76aF237d774F');
    }

    private function createCollectionBase(\App\Models\User $user, string $name, string $contract): void
    {
        $collection = new \App\Models\Collection();
        $collection = $collection->fromArray([
            'user_id' => $user->id,
            'chain' => 'base',
            'name' => $name,
            'config' => json_encode(['contract' => $contract]),
        ]);
        $collection->save();
    }
}
