<?php
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Middleware;

traint guzzelRetryDecider
{
	static function retryDecider() {
	   return function (
	      int $retries,
	      \GuzzleHttp\Psr7\Request $request,
	      \GuzzleHttp\Psr7\Response $response = null,
	      \GuzzleHttp\Psr7\RequestException $exception = null
	     ) {
	        // Limit the number of retries to 5
		if ( $retries >= 5 )
		{
						return false;
		}
		// Retry connection exceptions 
		if( $exception instanceof ConnectException )
		{
			return true;
		}
		// Retry on server errors
		if( $response && $response->getStatusCode() >= 500 )
		{
				return true;
		}
		return false;
		};
	}
}

trait guzzelRetryDelay
{
	static function retryDelay() {
		return function( $numberOfRetries ) {
			return 1000 * $numberOfRetries;
		}
	};
}

class guzzelRetry
{
	use guzzelRetryDecider,guzzelRetryDelay;
	public static function getClient()
	{
		$handlerStack = HandlerStack::create( new CurlHandler() );
		$handlerStack->push( Middleware::retry( self::retryDecider(), self::retryDelay() ) );
		return ( new Client( array( 'handler' => $handlerStack ) ) );
	}
}
