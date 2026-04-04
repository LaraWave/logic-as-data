<?php

namespace LaraWave\LogicAsData\Http\Controllers;

use LaraWave\LogicAsData\LogicRegistry;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;

class AdminController extends Controller
{
    /**
     * Bootstrap the Single Page Application.
     */
    public function __invoke(): View
    {
        // \LaraWave\LogicAsData\Facades\LogicEngine::passes('cart.checkout', ['user' => (new \Illuminate\Foundation\Auth\User)->forceFill(['email' => 'vip_customer@larawave.com','email_verified_at' => now(),'timezone' => 'Asia/Kolkata'])], [(new \Illuminate\Foundation\Auth\User)->forceFill(['email' => 'vip_customer@larawave.com','email_verified_at' => now(),'timezone' => 'Asia/Kolkata', 'id' => 1010]), ['promo_code' => 'TBK-999']]);
        $registry = app(LogicRegistry::class);

        $operators = Arr::map($registry->operators(), fn ($class, $alias) => [
            'class' => $class, 
            'metadata' => $class::metadata($alias)
        ]);

        $extractors = Arr::map($registry->extractors(), fn ($class, $alias) => [
            'class' => $class, 
            'metadata' => $class::metadata($alias)
        ]);

        $resolvers = Arr::map($registry->resolvers(), fn ($class, $alias) => [
            'class' => $class, 
            'metadata' => $class::metadata($alias)
        ]);

        $actions = Arr::map($registry->actions(), fn ($class, $alias) => [
            'class' => $class, 
            'metadata' => $class::metadata($alias)
        ]);

        $config = [
            'routePrefix' => config('logic-as-data.route.prefix'),
            'baseUrl' => url(config('logic-as-data.route.prefix')),
            'extractors' => $extractors,
            'operators'  => $operators,
            'resolvers' => $resolvers,
            'actions'    => $actions,
        ];

        return view('logic-as-data::admin', compact('config'));
    }
}
