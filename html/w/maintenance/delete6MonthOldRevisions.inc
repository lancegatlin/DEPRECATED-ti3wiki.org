<?php

/**
 * Support functions for the deleteOldRevisions script
 *
 * @addtogroup Maintenance
 * @author Rob Church <robchur@gmail.com>
 */
 
require_once( 'purgeOldText.inc' );

function Delete6MonthOldRevisions( $delete = false ) {

	# Data should come off the master, wrapped in a transaction
	$dbw = wfGetDB( DB_MASTER );
	$dbw->begin();
	
	$tbl_pag = $dbw->tableName( 'page' );
	$tbl_rev = $dbw->tableName( 'revision' );
	
	# Get "active" revisions from the page table
	echo( "Searching for active revisions..." );
	$res = $dbw->query( "SELECT page_id,page_latest FROM $tbl_pag" );
	while( $row = $dbw->fetchObject( $res ) ) {
			$active[] = $row->page_latest;
			$page[] = $row->page_id;
	}
	$count = count($active);
	echo( "$count active revisions found. Done.\n" );

	# Keep the "first" revision from the revision table for dating purposes
	echo ( "Searching for the first revision of pages...");
	foreach($page as $page_id)
	{
		$res = $dbw->query( "SELECT rev_id FROM $tbl_rev WHERE rev_page=$page_id ORDER BY rev_timestamp" );
		if($row = $dbw->fetchObject($res))
			$first[] = $row->rev_id;
	}
	$count = count($first);
	echo( "$count first revisions found. Done.\n" );
	
	$cur = array_merge($active, $first);
	
	# Get all revisions that aren't in this set
	echo( "Searching for inactive revisions..." );
/*	$tmp = in_array(1405, $cur) ? 'yes' : 'no';
	echo("\nrev_id=1405 excluded? $tmp\n");
	$tmp = in_array(1, $cur) ? 'yes' : 'no';
	echo("rev_id=1 excluded? $tmp\n");
	$tmp = in_array(343236, $cur) ? 'yes' : 'no';
	echo("rev_id=343236 excluded? $tmp\n");*/
	$set = implode( ', ', $cur );
	
	$timestamp = date("YmdGis", strtotime("-3 months"));
	// timestamp for 6 months ago
//	echo("six month ago timestamp=$timestamp\n");

	$res = $dbw->query( "SELECT rev_id FROM $tbl_rev WHERE rev_timestamp<$timestamp AND rev_id NOT IN ( $set ) ORDER BY rev_page,rev_timestamp" );
	
//	$count = 0;
	while( $row = $dbw->fetchObject( $res ) ) {
//			$tmp1 = $row->rev_id;
//			$tmp2 = $row->rev_timestamp;
//			echo ("Old rev_id=$tmp1 timestamp=$tmp2\n");
			$old[] = $row->rev_id;
//		}
//		if($count++ == 16)
//			break;
	}
	echo( "done.\n" );
	
	# Inform the user of what we're going to do
	$count = count( $old );
	echo( "$count 6 month old revisions found. (Excluding first and active revisions.)\n" );
	
	# Delete as appropriate
	if( $delete && $count ) {
		echo( "Deleting..." );
		$set = implode( ', ', $old );
		$dbw->query( "DELETE FROM $tbl_rev WHERE rev_id IN ( $set )" );
		echo( "done.\n" );
	}
	
	# This bit's done
	# Purge redundant text records
	$dbw->commit();
	if( $delete ) {
		PurgeRedundantText( true );
	}

}

?>