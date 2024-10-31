<?php

require_once RESHORTZ_ROOT . 'src/core/Reshortz_MetaWorker.php';

class Reshortz_API_Adapter
{
    use Reshortz_MetaWorker;

    /**
     * @param WP_Comment $comment
     * @return array
     */
    public function build_comment($comment)
    {
        $data =  [
            'comment_id'         => $comment->comment_ID,
            'comment_author'     => $comment->comment_author,
            'comment_author_url' => $comment->comment_author_url,
            'comment_date'       => $comment->comment_date,
            'comment_content'    => $comment->comment_content,
            'comment_karma'      => $comment->comment_karma,
            'comment_approved'   => $comment->comment_approved,
            'comment_parent'     => $comment->comment_parent,
            'user_id'            => $comment->user_id,
        ];

        /**
         * @var WP_User $user
         */
        $user = get_user_by('id', $comment->user_id);

        $data['user'] = [
            'nicename'      => $user->user_nicename,
            'email'         => $user->user_email,
            'avatar_url'    => get_avatar_url($user->user_email),
        ];


        if($comment->comment_parent !== '0') {
            $parent = get_comment($comment->comment_parent);
            $data['reply_to'] = [
                'reply_to_content' => $parent->comment_content,
                'reply_to_author'  => $parent->comment_author,
            ];
        }

        return $data;

    }

    /**
     * Build a standardized Reshort data array. Can be used for API Extensions later.
     *
     * @param $item_id
     * @return false
     */
    public function build_data($item)
    {
        $item_id = $item;

        if($item instanceof WP_Post) {
            $item_id = $item->ID;
        }

        $video = reshortz_rwmb_meta($this->makeKey('video'), $item_id);
        $video = reset($video);

        // validate if video is set and valid
        if(!isset($video) || empty($video) || !isset($video['url'])) {
            return false;
        }

        // Handle likes & views count
        $likes = get_post_meta($item_id, $this->makeKey('likes'), true);
        $views = get_post_meta($item_id, $this->makeKey('views'), true);

        if(!isset($likes) || empty($likes)) {
            $likes = 0;
        }

        if(!isset($views) || empty($views)) {
            $views = 0;
        }

        $visit_url = reshortz_rwmb_meta($this->makeKey('visit_url'), $item_id);

        $item = array(
            'id'            => $item_id,
            'title'         => get_the_title($item_id),
            'excerpt'       => get_the_excerpt($item_id),
            'video'         => esc_url($video['url']),
            'thumbnail'     => get_the_post_thumbnail_url($item_id, 'reshortz_image_ad_mobile'),
            'full_thumbnail'=> get_the_post_thumbnail_url($item_id, 'full'),
            'likes'         => esc_html($likes),
            'views'         => esc_html($views),
            'comments_count'=> get_comments_number($item_id),
            'comments'      => [],
            'liked'         => false,
            'tags'          => [],
            'categories'    => [],
            'visit_url'     => $visit_url,
            'duration'      => esc_html(reshortz_rwmb_meta($this->makeKey('duration'), $item_id))
        );

        /**
         * Build categories array
         */
        $categories = get_the_terms( $item_id , 'reshortz-category' );

        if(is_array($categories) && !empty($categories)) {
            foreach ($categories as $term) {
                $item['categories'][] = [
                    'name' => $term->name,
                    'id'   => $term->term_id,
                    'slug' => $term->slug,
                    'url'  => get_term_link($term->term_id),
                ];
            }
        }

        /**
         * Build tags array
         */
        $tags = get_the_terms( $item_id , 'tag' );

        if(is_array($tags) && !empty($tags)) {
            foreach ($tags as $term) {
                $item['tags'][] = [
                    'name' => $term->name,
                    'id'   => $term->term_id,
                    'slug' => $term->slug,
                    'url'  => get_term_link($term->term_id),
                ];
            }
        }

        return $item;
    }
}
