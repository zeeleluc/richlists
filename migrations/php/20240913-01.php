<?php

class Migration
{
    public function run()
    {
        $auth = new \App\Auth();

        $user = new \App\Models\User();
        $user = $user->fromArray([
            'email' => env('EMAIL_WEEPING_PLEBS'),
            'password' => $auth->hashPassword('weeping' . env('DEFAULT_PASSWORD_AFFIX')),
            'project_name' => 'Weeeping Plebs',
            'project_slug' => 'weepingplebs',
            'token' => generate_token(),
            'token_expires_at' => \Carbon\Carbon::now()->addYearNoOverflow()->format('Y-m-d H:i:s'),
        ]);
        $user = $user->save();

        $this->createCollectionBase($user, 'Weeping Plebs', '0x20b96003cE0cB506C30a21e8912d6733A992bc4f');
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
