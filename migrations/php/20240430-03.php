<?php

class Migration
{
    public function run()
    {
        $user = (new \App\Query\UserQuery())->getUserByEmail(env('ADMIN_EMAIL'));

        $this->createCollectionEthereum($user, 'LoadingPunks', '0x1be440b9d0a6595290c201f640ccd815cbc55168');
        $this->createCollectionEthereum($user, 'Looney Luca', '0x333a0d54c0f30454391c89570bfaf463d5345219');
        $this->createCollectionEthereum($user, 'ShapedPunks', '0xfb2513c14a47fd058edd5d9a12da4358a37a1e16');
        $this->createCollectionEthereum($user, 'PipingPunks', '0x912ce67c2d7741a2292b21fd7c8a22dc7a8d6efa');
        $this->createCollectionEthereum($user, 'OpepePunks', '0xfb560beab5811b8c99bad50ec60dee0157f96269');
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
