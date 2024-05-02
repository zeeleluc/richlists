<?php

class Migration
{
    public function run()
    {
        $auth = new \App\Auth();

        $user = new \App\Models\User();
        $user = $user->fromArray([
            'email' => env('EMAIL_LOREN_HAND_ART'),
            'password' => $auth->hashPassword('lorenhandart' . env('DEFAULT_PASSWORD_AFFIX')),
            'project_name' => 'LorenHandArt',
            'project_slug' => 'lorenhandart',
            'token' => generate_token(),
            'token_expires_at' => \Carbon\Carbon::now()->addYearNoOverflow()->format('Y-m-d H:i:s'),
        ]);
        $user = $user->save();

        $this->createCollection($user, 'Merfolk Chronicles', 'rUf6RZVJLv7F1hi5TniiN1LfhVUVzi5m8L', 36651);
        $this->createCollection($user, 'RippleRock', 'rnuXXecrYw5MX6TojAdjqQ8G5kZpMmR21Q', 2);
    }

    private function createCollection(\App\Models\User $user, string $name, string $issuer, string $taxon = null)
    {
        $config = ['issuer' => $issuer];
        if ($taxon) {
            $config['taxon'] = $taxon;
        }

        $collection = new \App\Models\Collection();
        $collection = $collection->fromArray([
            'user_id' => $user->id,
            'chain' => 'xrpl',
            'name' => $name,
            'config' => json_encode($config),
        ]);
        $collection->save();
    }
}
