<?php
function comment_cookie_toggle($post_array) {
	if(!empty($post_array['comment-remember']) && is_array($post_array['comment-remember']) && in_array('agree',$post_array['comment-remember'])) {
		$cookie_time = time()+3600*24*30;
		setcookie('c_uk_comment[name]', $post_array['comment-name-required'], $cookie_time, '/');
		setcookie('c_uk_comment[email]', $post_array['comment-email-required'], $cookie_time, '/');
		setcookie('c_uk_comment[website]', $post_array['comment-website'], $cookie_time, '/');
	}
	elseif(!empty($_COOKIE['c_uk_comment'])) {
		setcookie('c_uk_comment[name]', '', time()-3600, '/');
		setcookie('c_uk_comment[email]', '', time()-3600, '/');
		setcookie('c_uk_comment[website]', '', time()-3600, '/');
	}
}
?>