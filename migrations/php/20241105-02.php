<?php

class Migration
{
    public function run()
    {
        $auth = new \App\Auth();

        $user = new \App\Models\User();
        $user = $user->fromArray([
            'email' => 'testing1234@testing1234.de',
            'password' => $auth->hashPassword('testing1234' . env('DEFAULT_PASSWORD_AFFIX')),
            'project_name' => 'testing1234',
            'project_slug' => 'testing1234',
            'token' => generate_token(),
            'token_expires_at' => \Carbon\Carbon::now()->addYearNoOverflow()->format('Y-m-d H:i:s'),
        ]);
        $user = $user->save();

        $this->createCollectionBase($user, 'No-Based', '0xCE829656E70b2f4784580538233eB03f8a068B70');
        $this->createCollectionBase($user, 'No-Punks', '0x4ed83635E2309A7C067d0F98EfCa47B920bF79B1');
        $this->createCollectionBase($user, 're:generates', '0x56dFE6ae26bf3043DC8Fdf33bF739B4fF4B3BC4A');
        $this->createCollectionBase($user, 'based punks', '0xcB28749c24AF4797808364D71d71539bc01E76d4');
        $this->createCollectionEthereum($user, 'CryptoPunks', '0xb47e3cd837dDF8e4c57F05d70Ab865de6e193BBB');
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

    private function createCollectionEthereum(\App\Models\User $user, string $name, string $contract): void
    {
        $collection = new \App\Models\Collection();
        $collection = $collection->fromArray([
            'user_id' => $user->id,
            'chain' => 'ethereum',
            'name' => $name,
            'config' => json_encode(['contract' => $contract]),
        ]);
        $collection->save();
    }
}
