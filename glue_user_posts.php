<?php
// www/includes/functions_posting.php

...

/**
* Submit Post
* @todo Split up and create lightweight, simple API for this.
*/
function submit_post($mode, $subject, $username, $topic_type, &$poll, &$data, $update_message = true, $update_search_index = true) {
    global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path, $phpbb_container, $phpbb_dispatcher;

    if ($mode == 'reply' && empty($data['attachment_data'])) { // if it's REPLY and doesn't contains attachments

        // find uid of last post
        $sql = 'SELECT * FROM ' . POSTS_TABLE . ' WHERE post_id = '.intval($data['topic_last_post_id']).' LIMIT 1';
        $result = $db->sql_query($sql);
        $last_post = $db->sql_fetchrow($result);
        $db->sql_freeresult($result);

        if ($last_post) {

            if ($last_post['poster_id'] == $data['poster_id']) { // owner of last message and current the same
                if ((time() - intval($last_post['post_time'])) < 1*60*60) { // if last post posted less then 1h before

                    $minPassed = (ceil((time() - intval($last_post['post_time']))/60));

                    $bbcode_options = (($last_post['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0) +
                                      (($last_post['enable_smilies']) ? OPTION_FLAG_SMILIES : 0) +
                                      (($last_post['enable_magic_url']) ? OPTION_FLAG_LINKS : 0)
                    ;

                    $text = generate_text_for_display($last_post['post_text'], $last_post['bbcode_uid'], $last_post['bbcode_bitfield'], $bbcode_options);

                    $appendText = generate_text_for_display($data['message'], $data['bbcode_uid'], $data['bbcode_bitfield'], $bbcode_options);

                    $text .="\n\n"
                            ."[b]"
                            ."--[добавлено спустя ".$minPassed." мин]--"
                            ."[/b]"
                            ."\n\n"
                            .$appendText
                    ;

                    $bbcode_uid = $bbcode_bitfield = $options = '';
                    generate_text_for_storage($text, $bbcode_uid, $bbcode_bitfield, $options, $last_post['enable_bbcode'], $last_post['enable_magic_url'], $last_post['enable_smilies']);

                    $sql = 'UPDATE ' . POSTS_TABLE . " SET
                                `post_text` = '".$db->sql_escape($text)."',
                                `bbcode_uid` = '".$db->sql_escape($bbcode_uid)."',
                                `bbcode_bitfield` = '".$db->sql_escape($bbcode_bitfield)."'
                            WHERE post_id = ".intval($last_post['post_id']).' LIMIT 1
                    ';
                    $result = $db->sql_query($sql);
                    $db->sql_freeresult($result);

                    $params = '&amp;t=' . $data['topic_id'].'&amp;p=' . $last_post['post_id'];
                    $add_anchor = '#p' . $last_post['post_id'];
                    $url = "{$phpbb_root_path}viewtopic.$phpEx";
                    $url = append_sid($url, 'f=' . $data['forum_id'] . $params) . $add_anchor;
                    return $url;
                }
            }
        }
    }

    return submit_post_orig($mode, $subject, $username, $topic_type, $poll, $data, $update_message, $update_search_index);
}

function submit_post_orig($mode, $subject, $username, $topic_type, &$poll, &$data, $update_message = true, $update_search_index = true)
{
...
