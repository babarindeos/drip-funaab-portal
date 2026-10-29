<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Log;

/**
 * Runs the cache commands from the browser, for a server without SSH.
 *
 * Admins only (the route sits in the admin group), POST only (so a link preview or a
 * crawler can never trigger it), and only the commands listed below can run.
 */
class Admin_MaintenanceController extends Controller
{
    /** Button => [label, help text, the artisan commands it runs, in order]. */
    public const ACTIONS = [
        'refresh' => [
            'Clear and rebuild everything',
            'Use this after uploading an update. Clears every cache, then rebuilds the config, route and view caches so the site stays fast.',
            ['optimize:clear', 'config:cache', 'route:cache', 'view:cache'],
        ],
        'clear_all' => [
            'Clear everything (no rebuild)',
            'Clears the config, route, view, event and application caches. The site rebuilds what it needs as pages are opened.',
            ['optimize:clear'],
        ],
        'cache' => [
            'Clear application cache',
            'php artisan cache:clear',
            ['cache:clear'],
        ],
        'config' => [
            'Clear config cache',
            'php artisan config:clear. Use after changing .env.',
            ['config:clear'],
        ],
        'view' => [
            'Clear view cache',
            'php artisan view:clear. Use after changing a page.',
            ['view:clear'],
        ],
        'route' => [
            'Clear route cache',
            'php artisan route:clear. Use after changing routes.',
            ['route:clear'],
        ],
    ];

    public function index()
    {
        return view('admin.maintenance.index', ['actions' => self::ACTIONS]);
    }

    public function run(Request $request)
    {
        $request->validate([
            'action' => ['required', 'in:'.implode(',', array_keys(self::ACTIONS))],
        ]);

        [$label, , $commands] = self::ACTIONS[$request->input('action')];

        $results = [];
        $ok = true;
        $who = optional(Auth::user())->email;

        // config:cache and route:cache build a fresh copy of the application to read the config and
        // routes, and that copy takes over the facades (Auth, Session...). We put THIS request's
        // application back after every command, so the page, the session and the messages keep working.
        $app = app();

        foreach ($commands as $command) {
            try {
                $code = Artisan::call($command);
                $output = trim(Artisan::output());
                $this->restore($app);
                $results[] = ['command' => 'php artisan '.$command, 'ok' => $code === 0, 'output' => $output];
                $ok = $ok && $code === 0;
            } catch (\Throwable $e) {
                $this->restore($app);
                report($e);
                $results[] = ['command' => 'php artisan '.$command, 'ok' => false, 'output' => $e->getMessage()];
                $ok = false;
                break; // do not rebuild on top of a failed step
            }
        }

        Log::info('Maintenance: '.$label.' run by '.$who.' - '.($ok ? 'ok' : 'failed'));

        return redirect()->route('admin.maintenance.index')
                         ->with($ok ? 'success' : 'error', $ok ? $label.': done.' : $label.': a step failed. See the details below.')
                         ->with('maintenance_results', $results);
    }

    private function restore($app): void
    {
        Container::setInstance($app);
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);
    }
}
