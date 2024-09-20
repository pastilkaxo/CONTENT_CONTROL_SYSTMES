<?php

namespace FernleafSystems\Wordpress\Services\Core;

use FernleafSystems\Wordpress\Services\Services;

class Comments {

	/**
	 * @param string $email
	 */
	public function countApproved( $email ) :int {
		return Services::Data()->validEmail( $email ) ?
			(int)Services::WpDb()->getVar(
				sprintf(
					"SELECT COUNT(*) FROM %s WHERE `comment_author_email`='%s' AND `comment_approved`=1;",
					Services::WpDb()->getTable_Comments(),
					esc_sql( $email )
				)
			) : 0;
	}

	/**
	 * @param int $ID
	 * @return \WP_Comment|false
	 */
	public function getById( $ID ) {
		return \WP_Comment::get_instance( $ID );
	}

	public function getIfCommentsMustBePreviouslyApproved() :bool {
		return Services::WpGeneral()->getOption( 'comment_whitelist' ) == 1;
	}

	/**
	 * @param \WP_Post|null $thePost - queries the current post if null
	 */
	public function isCommentsOpen( $thePost = null ) :bool {
		if ( \is_null( $thePost ) || !\is_a( $thePost, 'WP_Post' ) ) {
			global $post;
			$thePost = $post;
		}
		return \is_a( $thePost, '\WP_Post' )
			   && comments_open( $thePost->ID )
			   && get_post_status( $thePost ) != 'trash';
	}

	public function isCommentsOpenByDefault() :bool {
		return Services::WpGeneral()->getOption( 'default_comment_status' ) === 'open';
	}

	public function isCommentAuthorPreviouslyApproved( $email ) :bool {
		return $this->countApproved( $email ) > 0;
	}

	public function isCommentSubmission() :bool {
		$postID = Services::Request()->post( 'comment_post_ID' );
		return Services::Request()->isPost()
			   && !empty( $postID )
			   && \is_numeric( $postID )
			   && Services::WpPost()->isCurrentPage( 'wp-comments-post.php' );
	}

	public function getCommentSubmissionEmail() :?string {
		$email = $this->isCommentSubmission() ? \trim( (string)Services::Request()->query( 'email', '' ) ) : '';
		return Services::Data()->validEmail( $email ) ? $email : null;
	}

	public function getCommentSubmissionComponents() :array {
		return [
			'comment_post_ID',
			'author',
			'email',
			'url',
			'comment',
			'comment_parent',
		];
	}
}