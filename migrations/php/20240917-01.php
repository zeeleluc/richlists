<?php

class Migration
{
    public function run()
    {
        $auth = new \App\Auth();

        $user = new \App\Models\User();
        $user = $user->fromArray([
            'email' => 'test2@example.com',
            'password' => $auth->hashPassword('test2' . env('DEFAULT_PASSWORD_AFFIX')),
            'project_name' => 'Test2',
            'project_slug' => 'test2',
            'token' => generate_token(),
            'token_expires_at' => \Carbon\Carbon::now()->addYearNoOverflow()->format('Y-m-d H:i:s'),
        ]);
        $user = $user->save();

        $this->createCollectionEthereum($user, 'CryptoDickbutts', '0x42069ABFE407C60cf4ae4112bEDEaD391dBa1cdB');
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
