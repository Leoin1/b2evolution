<?php
/**
 * This file implements the UI controller for the antispam management.
 *
 * This file is part of the b2evolution/evocms project - {@link http://b2evolution.net/}.
 * See also {@link http://sourceforge.net/projects/evocms/}.
 *
 * @copyright (c)2003-2007 by Francois PLANQUE - {@link http://fplanque.net/}.
 * Parts of this file are copyright (c)2004 by Vegar BERG GULDAL - {@link http://funky-m.com/}.
 *
 * @license http://b2evolution.net/about/license.html GNU General Public License (GPL)
 *
 * {@internal Open Source relicensing agreement:
 * Daniel HAHLER grants Francois PLANQUE the right to license
 * Daniel HAHLER's contributions to this file and the b2evolution project
 * under any OSI approved OSS license (http://www.opensource.org/licenses/).
 * Vegar BERG GULDAL grants Francois PLANQUE the right to license
 * Vegar BERG GULDAL's contributions to this file and the b2evolution project
 * under any OSI approved OSS license (http://www.opensource.org/licenses/).
 * Halton STEWART grants Francois PLANQUE the right to license
 * Halton STEWART's contributions to this file and the b2evolution project
 * under any OSI approved OSS license (http://www.opensource.org/licenses/).
 * }}
 *
 * @package admin
 *
 * {@internal Below is a list of authors who have contributed to design/coding of this file: }}
 * @author blueyed: Daniel HAHLER.
 * @author fplanque: Francois PLANQUE.
 * @author vegarg: Vegar BERG GULDAL.
 * @author halton: Halton STEWART.
 *
 * @todo Allow applying / re-checking of the known data, not just after an update!
 *
 * @version $Id$
 */
if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

load_funcs('antispam/_antispam.funcs.php');

$AdminUI->set_path( 'tools', 'antispam' );

param( 'action', 'string' );
param( 'confirm', 'string' );
param( 'keyword', 'string' );
param( 'domain', 'string' );
param( 'filteron', 'string', '', true );
param( 'filter', 'array', array() );

if( isset($filter['off']) )
{
	unset( $filteron );
	forget_param( 'filteron' );
}

// Check permission:
$current_User->check_perm( 'spamblacklist', 'view', true );

switch( $action )
{
	case 'ban': // only an action if further "actions" given
		// Check permission:
		$current_User->check_perm( 'spamblacklist', 'edit', true ); // TODO: This should become different for 'edit'/'add' perm level - check for 'add' here.

		$keyword = substr( $keyword, 0, 80 );
		param( 'delhits', 'integer', 0 );
		param( 'delcomments', 'integer', 0 );
		param( 'blacklist_locally', 'integer', 0 );
		param( 'report', 'integer', 0 );

		// Check if the string is too short,
		// it has to be a minimum of 5 characters to avoid being too generic
		if( strlen($keyword) < 5 )
		{
			$Messages->add( sprintf( T_('The keyword &laquo;%s&raquo; is too short, it has to be a minimum of 5 characters!'), htmlspecialchars($keyword) ), 'error' );
			break;
		}

		if( $delhits )
		{ // Delete all banned hit-log entries
			$r = $DB->query('DELETE FROM T_hitlog
												WHERE hit_referer LIKE '.$DB->quote('%'.$keyword.'%'),
												'Delete all banned hit-log entries' );

			$Messages->add( sprintf( T_('Deleted %d logged hits matching &laquo;%s&raquo;.'), $r, htmlspecialchars($keyword) ), 'success' );
		}

		if( $delcomments )
		{ // Then all banned comments
			$r = $DB->query('DELETE FROM T_comments
			                  WHERE comment_author LIKE '.$DB->quote('%'.$keyword.'%').'
			                     OR comment_author_email LIKE '.$DB->quote('%'.$keyword.'%').'
			                     OR comment_author_url LIKE '.$DB->quote('%'.$keyword.'%').'
			                     OR comment_content LIKE '.$DB->quote('%'.$keyword.'%') );
			$Messages->add( sprintf( T_('Deleted %d comments matching &laquo;%s&raquo;.'), $r, htmlspecialchars($keyword) ), 'success' );
		}

		if( $blacklist_locally )
		{ // Local blacklist:
			if( antispam_create( $keyword ) )
			{
				$Messages->add( sprintf( T_('The keyword &laquo;%s&raquo; has been blacklisted locally.'), htmlspecialchars($keyword) ), 'success' );
			}
			else
			{ // TODO: message?
			}
		}

		if( $report && $report_abuse )
		{ // Report this keyword as abuse:
			antispam_report_abuse( $keyword );
		}

		// We'll ask the user later what to do, if no "sub-action" given.
		break;


	case 'remove':
		// Remove a domain from ban list:

		// Check permission:
		$current_User->check_perm( 'spamblacklist', 'edit', true );

		param( 'hit_ID', 'integer', true );	// Required!
		$Messages->add( sprintf( T_('Removing entry #%d from the ban list...'), $hit_ID), 'note' );
		antispam_delete( $hit_ID );
		break;


	case 'report':
		// Report an entry as abuse to centralized blacklist:

		// Check permission:
		$current_User->check_perm( 'spamblacklist', 'edit', true );

		// Report this keyword as abuse:
		antispam_report_abuse( $keyword );
		break;


	case 'poll':
		// request abuse list from central blacklist:

		// Check permission:
		$current_User->check_perm( 'spamblacklist', 'edit', true );

		ob_start();
		antispam_poll_abuse();
		$Debuglog->add( ob_get_contents(), 'antispam_poll' );
		ob_end_clean();
		break;
}


// Display <html><head>...</head> section! (Note: should be done early if actions do not redirect)
$AdminUI->disp_html_head();

// Display title, menu, messages, etc. (Note: messages MUST be displayed AFTER the actions)
$AdminUI->disp_body_top();

// Begin payload block:
$AdminUI->disp_payload_begin();


if( !$Messages->count('error') && $action == 'ban' && !( $delhits || $delcomments || $blacklist_locally || $report ) )
{ // Nothing to do, ask user:
	?>

	<div class="panelblock">
		<form action="admin.php?ctrl=antispam" method="post">
		<input type="hidden" name="confirm" value="confirm" />
		<input type="hidden" name="keyword" value="<?php echo format_to_output( $keyword, 'formvalue' ) ?>" />
		<input type="hidden" name="action" value="ban" />
		<h2><?php echo T_('Confirm ban & delete') ?></h2>

		<?php
		// Check for junk:
		// Check for potentially affected logged hits:
		$sql = 'SELECT hit_ID, UNIX_TIMESTAMP(hit_datetime) as hit_datetime, hit_uri, hit_referer, dom_name,
										hit_blog_ID, hit_remote_addr, blog_shortname
						 FROM T_hitlog INNER JOIN T_basedomains ON hit_referer_dom_ID = dom_ID
						 			LEFT JOIN T_blogs ON hit_blog_ID = blog_ID
						WHERE hit_referer LIKE '.$DB->quote('%'.$keyword.'%').'
						ORDER BY dom_name ASC
						LIMIT 500';
		$res_affected_hits = $DB->get_results( $sql, ARRAY_A );
		if( $DB->num_rows == 0 )
		{ // No matching hits.
			printf( '<p><strong>'.T_('No log-hits match the keyword [%s].').'</strong></p>', htmlspecialchars($keyword) );
		}
		else
		{
		?>
			<p>
				<input type="checkbox" name="delhits" id="delhits_cb" value="1" checked="checked" />
				<label for="delhits_cb">
				<strong><?php printf ( T_('Delete the following %s referer hits:'), $DB->num_rows == 500 ? '500+' : $DB->num_rows ) ?></strong>
				</label>
			</p>
			<table class="grouped" cellspacing="0">
				<thead>
				<tr>
					<th><?php echo T_('Date') ?></th>
					<th><?php echo T_('Referer') ?></th>
					<th><?php echo T_('Ref. IP') ?></th>
					<th><?php echo T_('Target Blog') ?></th>
					<th><?php echo T_('Target URL') ?></th>
				</tr>
				</thead>
				<tbody>
				<?php
				$count = 0;
				foreach( $res_affected_hits as $row_stats )
				{
					?>
					<tr <?php if($count%2 == 1) echo 'class="odd"' ?>>
						<td class="firstcol"><?php stats_time() ?></td>
						<td><a href="<?php stats_referer() ?>"><?php stats_basedomain() ?></a></td>
						<td><?php stats_hit_remote_addr() ?></td>
						<td><?php echo format_to_output( $row_stats['blog_shortname'], 'htmlbody' ); ?></td>
						<td><a href="<?php stats_req_URI() ?>"><?php stats_req_URI() ?></a></td>
					</tr>
					<?php
					$count++;
				} ?>
				</tbody>
			</table>
		<?php
		}

		// Check for potentially affected comments:
		$sql = 'SELECT comment_ID, comment_date, comment_author, comment_author_url,
										comment_author_IP, comment_content
						  FROM T_comments
						 WHERE comment_author LIKE '.$DB->quote('%'.$keyword.'%').'
									 OR comment_author_email LIKE '.$DB->quote('%'.$keyword.'%').'
							 		 OR comment_author_url LIKE '.$DB->quote('%'.$keyword.'%').'
    				   		 OR comment_content LIKE '.$DB->quote('%'.$keyword.'%').'
						 ORDER BY comment_date ASC
						 LIMIT 500';
		$res_affected_comments = $DB->get_results( $sql, ARRAY_A, 'Find matching comments' );
		if( $DB->num_rows == 0 )
		{ // No matching hits.
			printf( '<p><strong>'.T_('No comments match the keyword [%s].').'</strong></p>', htmlspecialchars($keyword) );
		}
		else
		{
		?>
			<p>
				<input type="checkbox" name="delcomments" id="delcomments_cb" value="1" checked="checked" />
				<label for="delcomments_cb">
				<strong><?php printf ( T_('Delete the following %s comments:'), $DB->num_rows == 500 ? '500+' : $DB->num_rows ) ?></strong>
				</label>
			</p>
			<table class="grouped" cellspacing="0">
				<thead>
				<tr>
					<th><?php echo T_('Date') ?></th>
					<th><?php echo T_('Author') ?></th>
					<th><?php echo T_('Auth. URL') ?></th>
					<th><?php echo T_('Auth. IP') ?></th>
					<th><?php echo T_('Content starts with...') ?></th>
				</tr>
				</thead>
				<tbody>
				<?php
				$count = 0;
				foreach( $res_affected_comments as $row_stats )
				{ // TODO: new Comment( $row_stats )
					?>
					<tr <?php if($count%2 == 1) echo 'class="odd"' ?>>
					<td class="firstcol"><?php echo mysql2date(locale_datefmt().' '.locale_timefmt(), $row_stats['comment_date'] ); ?></td>
					<td><?php echo $row_stats['comment_author'] ?></a></td>
					<td><?php echo $row_stats['comment_author_url'] ?></td>
					<td><?php echo $row_stats['comment_author_IP'] ?></td>
					<td><?php
					$comment_content = strip_tags( $row_stats['comment_content'] );
					if ( strlen($comment_content) > 70 )
					{
						// Trail off (truncate and add '...') after 70 chars
						echo substr($comment_content, 0, 70) . "...";
					}
					else
					{
						echo $comment_content;
					}
					?></td>
					</tr>
					<?php
				$count++;
				} ?>
				</tbody>
			</table>
		<?php
		}

		// Check if the string is already in the blacklist:
		if( antispam_check($keyword) )
		{ // Already there:
			printf( '<p><strong>'.T_('The keyword [%s] is already handled by the blacklist.').'</strong></p>', htmlspecialchars($keyword) );
		}
		else
		{ // Not in blacklist
			?>
			<p>
			<input type="checkbox" name="blacklist_locally" id="blacklist_locally_cb" value="1" checked="checked" />
			<label for="blacklist_locally_cb">
				<strong><?php printf ( T_('Blacklist the keyword [%s] locally.'), htmlspecialchars($keyword) ) ?></strong>
			</label>
			</p>

			<?php
			if( $report_abuse )
			{
				?>
				<p>
				<input type="checkbox" name="report" id="report_cb" value="1" checked="checked" />
				<label for="report_cb">
					<strong><?php printf ( T_('Report the keyword [%s] as abuse to b2evolution.net.'), htmlspecialchars($keyword) ) ?></strong>
				</label>
				[<a href="http://b2evolution.net/about/terms.html"><?php echo T_('Terms of service') ?></a>]
				</p>
				<?php
			}
		}
		?>

		<input type="submit" value="<?php echo T_('Perform selected operations') ?>" class="search" />
		</form>
	</div>
	<?php
}


// ADD KEYWORD FORM:
if( $current_User->check_perm( 'spamblacklist', 'edit' ) ) // TODO: check for 'add' here once it's mature.
{ // add keyword or domain
	echo '<div class="panelblock">';
	$Form = & new Form( NULL, 'antispam_add', 'post', '' );
	$Form->begin_form('fform');
		$Form->hidden_ctrl();
		$Form->hidden( 'action', 'ban' );
		$Form->text( 'keyword', $keyword, 30, T_('Add a banned keyword'), '', 80 ); // TODO: add note
		/*
		 * TODO: explicitly add a domain?
		 * $add_Form->text( 'domain', $domain, 30, T_('Add a banned domain'), 'note..', 80 ); // TODO: add note
		 */
	$Form->end_form( array( array( 'submit', 'submit', T_('Check & ban...'), 'SaveButton' ) ) );
	echo '</div>';
}


echo '<div class="panelblock">';
// Display blacklist:
$AdminUI->disp_view( 'antispam/_antispam_list.view.php' );
echo '</div>';

// End payload block:
$AdminUI->disp_payload_end();

// Display body bottom, debug info and close </html>:
$AdminUI->disp_global_footer();


/*
 * $Log$
 * Revision 1.1  2007/06/25 10:59:23  fplanque
 * MODULES (refactored MVC)
 *
 * Revision 1.10  2007/04/26 00:11:14  fplanque
 * (c) 2007
 *
 * Revision 1.9  2007/03/01 02:42:03  fplanque
 * prevent miserable failure when trying to delete heavy spam.
 *
 * Revision 1.8  2006/12/07 21:16:55  fplanque
 * killed templates
 */
?>