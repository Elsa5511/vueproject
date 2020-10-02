<?php

namespace App\Http\Middleware;

use Closure;
use Lavary\Menu\Facade as Menu;
use Nwidart\Modules\Facades\Module;

class AdminMenuMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {


        if (!$request->ajax()) {
            Menu::make('MainMenu', function ($menu) use ($request) {
                $stats = $request->get('stats');

                $menu->raw('Menu', ['class' => 'header']);
                $menu->add('Home', ['route' => 'admin.home']);



                $menu->add('Usuarios', ['route' => 'admin.users.index'])->active('/admin/users/*');
                $menuProviders = $menu->add('Proveedores', ['route' => 'admin.providers.index'])->active('/admin/providers/*');
                $menuProviders->add('Validar cuentas <small class="badge pull-right bg-yellow">' . $stats['pendingProviders'] . '</small>', ['url' => route('admin.providers.index') . '?status=pending']);

                $menu->add('Citas', ['route' => 'admin.appointments'])->active('/admin/appointments/*');


                $menu->add('Gelolocalización de modelos', ['route' => 'admin.maps.providers'])->active('/admin/maps/*');
                $menu->add('Chat', ['route' => 'admin.chats.index'])->active('/admin/chats/*');

                $menu->add('Conversiones', ['route' => 'admin.currencies'])->active('/admin/currencies/*');
                $menu->add('Paginas', ['route' => 'admin.pages.index'])->active('/admin/pages/*');
                $menu->add('Mensajes automáticos', ['route' => 'admin.automessages.index'])->active('/admin/automessages/*');

                $menu->add('Configuración', ['route' => 'admin.settings'])->active('/admin/settings/*');

                //$pagesMenu = $menu->add('Pages', ['route' => 'admin.pages.index']);
                //$bannersMenu = $menu->add('Banners', ['route' => 'admin.banners.index']);



                /* $blogMenu = $menu->add('Blog', ['route' => 'admin.blog.index'])->active('/admin/blog/*');
                  $articles = $menu->add('Articles', ['route' => 'admin.articles.index'])->active('/admin/articles/*');
                  $guides = $menu->add('Guides', ['route' => 'admin.guides.index'])->active('/admin/guides/*');

                  $menu->add('Users', ['route' => 'admin.users.index'])->active('/admin/users/*');


                  $menu->add('Withdrawals', ['route' => 'admin.withdrawals.index'])->active('/admin/withdrawals/*');
                  //$menu->add('Users', ['route' => 'admin.users.index']);

                  $menu->add('Settings', ['route' => 'admin.settings']);

                  /* F$menu->add('Usuarios', ['route' => 'admin.users.index'])->active('/admin/users/*');
                  $menu->add('Validar cuentas <small class="badge pull-right bg-yellow">' . $stats['pendingUsers'] . '</small>', ['route' => 'admin.account-validations.index'])->active('/admin/account-validations/*');
                  $menu->add('Validar cbus <small class="badge pull-right bg-yellow">' . $stats['pendingCbus'] . '</small>', ['route' => 'admin.cbus.index'])->active('/admin/cbus/*');
                  $menuOperations = $menu->add('Operaciones <small class="badge pull-right bg-yellow">' . ($stats['pendingFunds'] + $stats['pendingSellOrBuy']) . '</small>', ['route' => 'admin.users.index', 'class' => 'treeview'])->active('/admin/funds/*');

                  $menuOperations->add('Depósitos/Retiros de ARS <small class="badge pull-right bg-yellow">' . $stats['pendingFunds'] . '</small>', ['route' => 'admin.funds.index']);
                  $menuOperations->add('Compraventa <small class="badge pull-right bg-yellow">' . $stats['pendingSellOrBuy'] . '</small>', ['route' => 'admin.buysell.index']);

                  $menu->add('Configuración', ['route' => 'admin.settings']); */
            });
        }

        return $next($request);
    }

}
