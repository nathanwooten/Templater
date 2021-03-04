<?php

namespace Pf\Templater;

use Pf\Templater\TemplaterInterface;
use Pf\Templater\TemplateInterface;

class RenderTemplate extends TemplaterAbstract
{

	public static $delimiters = [ '{{', '}}' ];

	public static function render( TemplateInterface $template )
	{

		$templateString = $template->getTemplate();

		$named = TemplaterEngine::get();

		foreach ( $named as $name => $obj ) {

			$match = static::match( $templateString, $name );
			foreach ( $match as $tag ) {

					$render = static::render( $obj );

				$templateString = str_replace( $tag, $render, $templateString );
			}
		}

		$file = dirname( __FILE__ ) . DS . 'temp' . DS . 'template.php';
		$put = file_put_contents( $file, $templateString );
		if ( ! $put ) {
			return false;
		}

		ob_start();
			include $file;
		$rendered = ob_get_clean();

		return $rendered;

	}

	public static function match( $template, $specific = null )
	{

		$delimiters = static::delimiters( static::$delimiters );
		$expression = is_null( $specific ) ? '(.*?)' : $specific;

		$regex = '/' . $delimiters[0] . $expression . $delimiters[1] . '/';
		preg_match_all( $regex, $template, $match );

		$match = $match[0];

		return $match;

	}

	public static function delimiters( array $delimiters = [] )
	{

		$delimiters = array_values( $delimiters );
		$delimiters[0] = '\\' . implode( '\\', str_split( $delimiters[0] ) );
		$delimiters[1] = '\\' . implode( '\\', str_split( $delimiters[1] ) );
		return $delimiters;

	}

}
