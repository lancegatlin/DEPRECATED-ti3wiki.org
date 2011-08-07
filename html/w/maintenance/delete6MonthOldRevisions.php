<?php

/**
 * Delete old (non-current) revisions from the database
 *
 * @addtogroup Maintenance
 * @author Rob Church <robchur@gmail.com>
 */

$options = array( 'delete', 'help' );
require_once( 'commandLine.inc' );
require_once( 'delete6MonthOldRevisions.inc' );

echo( "Delete 6 Month Old Revisions\n\n" );

if( @$options['help'] ) {
	ShowUsage();
} else {
	Delete6MonthOldRevisions( @$options['delete'] );
}

function ShowUsage() {
	echo( "Deletes 6 month old revisions from the database.\n\n" );
	echo( "Usage: php delete6MonthOldRevisions.php [--delete|--help]\n\n" );
	echo( "delete : Performs the deletion\n" );
	echo( "  help : Show this usage information\n" );
}

