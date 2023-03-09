<?php

namespace gp_brief\components;
require_once 'Logger.php';
require_once 'brief.php';

use gp_brief\components\Logger;
use gp_brief\components\Brief;

class EmptyBriefCleaner
{
	private $log;
	
	public function __construct()
	{
		$this->log = new Logger();
		add_action('gp_clear_empty_briefs',[$this,'clean']);
		$this->startCron();
	}

	public function startCron(): void
	{
		if (!wp_next_scheduled('gp_clear_empty_briefs')) 
		{
		    $time = strtotime('03:30:00'); 
		    wp_schedule_event($time, 'daily', 'gp_clear_empty_briefs');
		}
	}

	public function clean(): void
	{
		$this->log->write_log("EmptyBriefCleaner start …");
		$candidates =$this->findCandidates();
		if ( empty($candidates) ) return;
		$this->deletePosts($candidates);
		$this->cleanOptions($candidates);
	}

	private function deletePosts(array $candidates): void
	{
		$ids = $this->idsFromCandidates($candidates);
		$emptyPostIds = $this->emptyPostsIds($ids);
		array_walk($emptyPostIds, function($id) { 
			$deletedPost = wp_delete_post($id);
			if ( $deletedPost == false || $deletedPost == null )
			{
				$this->log->write_log("Failure delete brief post with id = $id.");
			} else
			{
				$this->log->write_log("Delete brief post with id = $id.");
			}
		});

	}

	private function idsFromCandidates(array $candidates): array
	{
		$res = array_map(function ($item) {
			return $item['postId'];
		}, $candidates);
		return array_values($res);
	}
	private function cleanOptions(array $candidatesForClean): void
	{
		$meta = json_decode(get_option( Brief::TOKENS_KEY ), true);
		$res = array_diff_ukey($meta, $candidatesForClean, function ($key1, $key2) {
			if ( $key1 == $key2 ) return 0;
			else if ($key1 > $key2) return 1;
			else return -1;
		});
		$this->log->write_log("Updating option \"".Brief::TOKENS_KEY."\". Number of records before is ".count($meta).", after ".count($res)."." );
		update_option(Brief::TOKENS_KEY,json_encode($res));
	}
	private function findCandidates()
	{
		$meta = json_decode(get_option( Brief::TOKENS_KEY ), true);
		$candidates = array_filter($meta, function($item) {
			return ( $this->isMoreThenOneDayBeforeAndNotSubmitted($item) );
		});
		return $candidates;
	}

	private function emptyPostsIds(array $ids): array
	{
		$args['include'] = $ids;
		$args['post_type'] = 'gp_brief';
		$args['post_status'] = 'any';
		$args['posts_per_page'] = -1;
		$args['fields'] = 'ids'; 
		$candidatesPosts = get_posts($args);
		$emptyPostIds = array_filter($candidatesPosts, [$this, 'isEmptyJson'] );
		return $emptyPostIds;
	}

	private function isEmptyJson (int $id): bool
	{
		$meta = get_post_meta( $id, '_brief_json' );
		$json = $meta[0];
		return boolval(empty($json));
	}
	private function isMoreThenOneDayBeforeAndNotSubmitted( array $item ): bool
	{

		$oneDayBefore = strtotime('-1 day', time() );
		return ( $oneDayBefore > $item['updatedTimestamp'] && $item['submited'] === false );
	}

}
