<?php

// www/includes/message_parser.php

	/**
	* Parse BBCode
	*/
	function parse_bbcode()
	{
  ...
  foreach ($bbcode_data['regexp'] as $regexp => $replacement)
				{
					// The pattern gets compiled and cached by the PCRE extension,
					// it should not demand recompilation
					if (preg_match($regexp, $this->message))
					{
              $regexp = str_replace('#uise','#uis', $regexp);
              $CI = $this;
              $this->message = preg_replace_callback($regexp, function($a) use(&$CI, $replacement) {
                   $replacement = str_replace('$this->', '', $replacement);
                   $replacement = preg_replace('~\(.+$~', '', $replacement);
                   return $CI->$replacement($a[1]);
              }, $this->message);
              
						  $bitfield->set($bbcode_data['bbcode_id']);
					}
				}
  ...
