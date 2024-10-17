<?php

class Migration
{
    public function run()
    {
        $auth = new \App\Auth();

        $user = new \App\Models\User();
        $user = $user->fromArray([
            'email' => 'hustlr@example.com',
            'password' => $auth->hashPassword('test' . env('DEFAULT_PASSWORD_AFFIX')),
            'project_name' => 'Hustlr',
            'project_slug' => 'hustlr',
            'token' => generate_token(),
            'token_expires_at' => \Carbon\Carbon::now()->addYearNoOverflow()->format('Y-m-d H:i:s'),
        ]);
        $user = $user->save();

        $this->createCollectionBase($user, 'No-Based', '0xCE829656E70b2f4784580538233eB03f8a068B70');
        $this->createCollectionBase($user, 'Based Fellas', '0x217Ec1aC929a17481446A76Ff9B95B9a64F298cF');
        $this->createCollectionBase($user, 'Primitives', '0x424d781E0163B5A42ca2F27d036c2d5C561022C3');
        $this->createCollectionBase($user, 'Br8tties', '0x0Fa0D0Ca5Ef191b339fc213c9c84F5b670713CCD');
        $this->createCollectionBase($user, 'RektDogs', '0xAce8187B113a38F83Bd9C896C6878B175c234dCc');
        $this->createCollectionBase($user, 'ChiliBangs', '0x83fD365803Fd926e5a35B0d449524Ea9C978a40D');
        $this->createCollectionBase($user, 'Based Lines', '0x0582d0f7696dd7e335F4e70e0cb48e860cC5E606');
        $this->createCollectionBase($user, 'PNUKS', '0x3d8683Bbf9CaE7ad0441b65ddCadEC3850d1256E');
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
