<?php
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Middleware;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response as Psr7Response; 

trait guzzelRetryDecider
{
	static function retryDecider() {
	   return function (
	      int $retries,
	      Psr7Request $request,
	      Psr7Response $response = null,
	      RequestException $exception = null
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
	private static $client = null;
	public static function getClient()
	{
		if(self::$client === null)
		{
			$handlerStack = HandlerStack::create( new CurlHandler() );
			$handlerStack->push( Middleware::retry( self::retryDecider(), self::retryDelay() ) );
			self::$client = new Client(array( 'handler' => $handlerStack));
		}
		return self::$client;
	}
}
