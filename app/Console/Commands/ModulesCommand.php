<?php
/**
 *
 * PHP version >= 7.0
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;


/**
 * Class deletePostsCommand
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class ModulesCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "make:module {name}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Generate Module include Controller, Model, Route";


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name       = ucwords($this->argument('name'));
        $path       = './app/Http/Modules/'.$name;
        if(!is_dir($path)) {

            mkdir($path);
            $this->generate_controller($path, $name);
            $this->generate_model($path, $name);
            $this->generate_routes($path, $name);

        }
    }

    public function generate_controller($path, $name) {
        $file_path = $path.'/'.$name.'Controller.php';
        $myfile = fopen($file_path, "w") or die("Unable to open file!");
        $controller = '<?php
namespace App\Http\Modules\{name};

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use App\Http\Modules\{name}\{name}Model;
use Illuminate\Http\Request;

class {name}Controller extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }
}
        ';
        $write_controller = str_replace('{name}', $name, $controller);
        fwrite($myfile, $write_controller);
        fclose($myfile);
    }

    public function generate_model($path, $name) {
        $file_path  = $path.'/'.$name.'Model.php';
        $table      = strtolower($name).'_table';
        $myfile     = fopen($file_path, "w") or die("Unable to open file!");
        $controller = '<?php
namespace App\Http\Modules\{name};

use Illuminate\Database\Eloquent\Model;

class {name}Model extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        //
    ];
    /**
     * The attributes excluded from the model\'s JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public $tables = "'.$table.'";
}
';
        $write_controller = str_replace('{name}', $name, $controller);
        fwrite($myfile, $write_controller);
        fclose($myfile);
    }

    function generate_routes($path, $name) {
        $file_path   = $path.'/routes.php';
        $prefix      = strtolower($name);
        $myfile      = fopen($file_path, "w") or die("Unable to open file!");
        $controller  = '<?php

$router->group([\'prefix\' => \''.$prefix.'\', \'middleware\' => [\'cors\', \'key-api\']], function() use ($router) {
    $controller     = \'\App\Http\Modules\\'.$prefix.'\{name}Controller\';

    $router->post(\'/\', [\'uses\' => $controller.\'@index\']);
});
';
        $write = str_replace('{name}', $name, $controller);
        fwrite($myfile, $write);
        fclose($myfile);
    }
}
