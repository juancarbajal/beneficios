<?php namespace App\Http\Middleware;

use App\Model\Usuario;
use Closure;
use Illuminate\Hashing\BcryptHasher;

class ApiAuth {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if ( isset($_SERVER['PHP_AUTH_USER']) and isset($_SERVER['PHP_AUTH_PW']) ) {

			if ($_SERVER['PHP_AUTH_USER'] == env('USER_API') and
				$_SERVER['PHP_AUTH_PW'] == env('PASS_API')){
				return $next($request);
			}
			/*
			$query = array(
                'Correo' => $_SERVER['PHP_AUTH_USER'],
            );

            $usuario = Usuario::where($query)->first();
			if ($usuario){

				$bcrypt = new BcryptHasher();

				$securePass = $usuario->Contrasenia;
				$password = $_SERVER['PHP_AUTH_PW'];

				if ($bcrypt->check($password, $securePass)) {
					return $next($request);
				}
			} */
	    }

		return response()->json([
			'error' => 1,
			'mensaje' => 'Acceso Denegado.']);

	}

}
