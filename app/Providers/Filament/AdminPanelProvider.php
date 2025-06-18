<?php

namespace App\Providers\Filament;

use App\Filament\Pages\LaporanTransaksi;
use POSPage;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\BarangTerlaris;
use Filament\Navigation\NavigationGroup;
use App\Filament\Resources\BarangResource;
use App\Filament\Resources\SatuanResource;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Pages\POSPage as PagesPOSPage;
use App\Filament\Resources\JenisBarangResource;
use App\Filament\Resources\TransaksiResource;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\Widgets\GrafikPenjualanMingguan;
use App\Filament\Widgets\StokRawan;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                StatsOverview::class,
                GrafikPenjualanMingguan::class,
                StokRawan::class,
            ])
            ->font('Poppins')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])

            ->navigation()
            ->brandName('UD Rizky')
            ->sidebarCollapsibleOnDesktop()
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function navigation(): array
    {
        return [
            NavigationGroup::make('Manajemen Barang')
                ->items([
                    BarangResource::getNavigationItems()[0],
                    JenisBarangResource::getNavigationItems()[0],
                    SatuanResource::getNavigationItems()[0],
                ]),
            NavigationGroup::make('Transaksi Dan Laporan')
                ->items([
                    TransaksiResource::getActiveNavigationItems()[0],
                    LaporanTransaksi::getActiveNavigationItems()[0]
                ])
        ];
    }
}
