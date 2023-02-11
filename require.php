<?php

/**
 * The upFind function is kept in this file, this file is a stand-alone script
 */

//////////////////////////////////////////////////
if ( ! function_exists( 'upFind' ) ) {
function upFind( $directory, array $targetDirectoryContains )
{

  $directory = (string) $directory;
  if ( ! is_string( $directory ) ) {
    throw new Exception( 'An error has occurred, please contact the administrator. search 4805' );
  }

  if ( is_file( $directory ) ) {
    $directory = dirname( $directory ) . DIRECTORY_SEPARATOR;
  }

  // no contents, no search
  if ( empty( $targetDirectoryContains ) ) {
    return false;
  }

  while( $directory && ( ! isset( $count ) || ! $count ) ) {

    $directory = rtrim( $directory, DIRECTORY_SEPARATOR . '\\/' ) . DIRECTORY_SEPARATOR;

    $is = [];

    // loop through 'contains'
    foreach ( $targetDirectoryContains as $contains ) {
      $item = $directory . $contains;

      // readable item?, add to $is
      if ( is_readable( $item ) ) {

        $is[] = $item;
 
     }
    }

    // expected versus is
    $isCount = count( $is );
    $containsCount = count( $targetDirectoryContains );

    $count = ( $isCount === $containsCount );

    if ( $count ) {

      break;
    } else {

      $parent = dirname( $directory );

      if ( $parent === $directory ) {

        // if root reached break the loop
        $directory = false;

      } else {

        // continue up
        $directory = $parent;

      }

      continue;
    }

    $directory = rtrim( $directory, DIRECTORY_SEPARATOR . '\\/' );

  }

  return $directory;

}
}
//////////////////////////////////////////////////

$entry = upFind( __FILE__, [ 'entry.php' ] ) . '/entry.php';
$has = file_exists( $entry );
if ( $has ) {
  require $entry;

  extract( ENTRY );
}

/**
 * Top of Editing Area
 */

// this is the name of the variable that contains the name of the top file, this can be anything but "topApplication", see below
// this is used as a variable variable... if ( isset( ${$topVar} ) ) { use it
$topVar = isset( $topVar ) ? $topVar : 'top';

// the name of the root folder define
$topDefine = isset( $topDefine ) ? $topDefine : 'PUBLIC_HTML';

// if no contents are defined, a search result can not be produced, searching for a directory that contains the folder "public_html"
$topContents = isset( $topContents ) ? $topContents : [ 'public_html' ];

// this allows you to search for the directory you want instead of it's contents. A string you define is appended to the target directory
$topAppend = 'public_html';

/**
 * Bottom of Editing Area
 */

// this file returns this value by default, it returns the result of the top file, if the file is found
$topReturn = 1;

// re-definition causes an error
if ( ! defined( $topDefine ) ) {

  $has = upFind( __FILE__, $topContents );

  if ( ! $has ) {
    throw new Exception( 'Please contact the administrator, search 3985' );
  }

  $topDefineContents = rtrim( $has, DIRECTORY_SEPARATOR );

  if ( isset( $topAppend ) ) {

    $topDefineContents .= DIRECTORY_SEPARATOR . $topAppend;

  }

  define( $topDefine, $topDefineContents );

}

/**
 *
 *
 * The application top file
 *
 *
 */

$topApplication = isset( ${$topvar} ) ? ${$topvar} : 'top.php';
$topApplication = constant( $topDefine ) . DIRECTORY_SEPARATOR . $topApplication;

if ( ! file_exists( $topApplication ) || ! is_readable( $topApplication ) ) {
  throw new Exception( 'An error has occurred, please contact the administrator: search 2302' );
}

$topReturn = require $topApplication;

/**
 * Additional Files to load
 */

if ( isset( $topFiles ) ) {
  if ( ! is_array( $topFiles ) ) {
    $topFiles = [ $topFiles ];
  }
  $constant = constant( $topDefine );
  foreach ( $topFiles as $filepath ) {
    if ( is_readable( $constant . DIRECTORY_SEPARATOR . $filepath ) ) {
      $additionalFile = $constant . DIRECTORY_SEPARATOR . $filepath;

      if ( ! file_exists( $additionalFile ) || ! is_readable( $additionalFile ) ) {
        throw new Exception( 'An error has occurred, please contact the administrator: search 2302' );
      }

      require $additionalFile;
    }
  }
}

/**
 * Optionally return a top file/object, file include results default to "1", hence the variable preset
 */

return $topReturn;