<?php

class Migration
{
    public function run()
    {
        $auth = new \App\Auth();

        $user = new \App\Models\User();
        $user = $user->fromArray([
            'email' => 'x',
            'password' => $auth->hashPassword('nobased' . env('DEFAULT_PASSWORD_AFFIX')),
            'project_name' => 'No-Based',
            'project_slug' => 'nobased',
            'token' => generate_token(),
            'token_expires_at' => \Carbon\Carbon::now()->addYearNoOverflow()->format('Y-m-d H:i:s'),
        ]);
        $user = $user->save();

        $this->createCollectionBase($user, 'No-Based', '0xCE829656E70b2f4784580538233eB03f8a068B70');
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
