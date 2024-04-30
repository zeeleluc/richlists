<?php

class Migration
{
    public function run()
    {
        $auth = new \App\Auth();

        $user = new \App\Models\User();
        $user = $user->fromArray([
            'email' => env('TEMP_EMAIL_CLIENT_3'),
            'password' => $auth->hashPassword('ripplepunks' . env('DEFAULT_PASSWORD_AFFIX')),
            'project_name' => 'ripplepunks',
            'token' => generate_token(),
            'token_expires_at' => \Carbon\Carbon::now()->addYearNoOverflow()->format('Y-m-d H:i:s'),
        ]);
        $user->save();

        $this->createCollectionsForRipplePunks();
        $this->createCollectionsForHasMints();
        $this->createCollectionsForPixelAstros();
        $this->createCollectionsForAstroNaughties();
    }

    private function createCollectionsForRipplePunks()
    {
        $user = (new \App\Models\User())->getByEmail(env('TEMP_EMAIL_CLIENT_3'));

        $this->createCollection($user, 'RipplePunks', 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS', 604);
        $this->createCollection($user, 'RipplePunks Rewind', 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS', 48919);
        $this->createCollection($user, 'RipplePunks Quartet', 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS', 23578);
        $this->createCollection($user, 'RipplePunks on Demand', 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS', 49619);
    }

    private function createCollectionsForHasMints()
    {
        $user = (new \App\Models\User())->getByEmail(env('ADMIN_EMAIL'));

        $this->createCollection($user, 'RipplePunks', 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS', 604);
        $this->createCollection($user, 'RipplePunks Rewind', 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS', 48919);
        $this->createCollection($user, 'RipplePunks Quartet', 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS', 23578);
        $this->createCollection($user, 'RipplePunks on Demand', 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS', 49619);
        $this->createCollection($user, 'xLooney Luca', 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS', 6929464);
        $this->createCollection($user, 'XRPunks', 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS', 6669233);
    }

    private function createCollectionsForPixelAstros()
    {
        $user = (new \App\Models\User())->getByEmail(env('TEMP_EMAIL_CLIENT_2'));

        $this->createCollection($user, 'PixelAstros', 'rLULtFuV1agdSQdVmSd7AYx2cfEiN6noxY', 34637);
    }

    private function createCollectionsForAstroNaughties()
    {
        $user = (new \App\Models\User())->getByEmail(env('TEMP_EMAIL_CLIENT_2'));

        $this->createCollection($user, 'AstroNaughties', 'rGNuFE4e2c5NwEp2HnuiJqaSVdaNYRQ7PV', 53);
        $this->createCollection($user, 'AstroNaughties OG\'s', 'rMQw4pe2eXvs6b5hKLNM2MCgVqHEHwXBkJ');
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
