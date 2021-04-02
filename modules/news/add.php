<?php
if (!defined('FLUX_ROOT')) exit;
$title = Flux::message('NewsAddTitle');

// Form values.
$news	= Flux::config('FluxTables.CMSNewsTable');
$title	= trim($params->get('news_title'));
$body	= trim($params->get('news_body'));
$link	= trim($params->get('news_link'));
$author	= trim($params->get('news_author'));

$tinymce_key = Flux::config('TinyMCEKey'); 

if(count($_POST)){
    if($title === '') {
        $errorMessage = Flux::Message('CMSNewsTitleError');
    }
    elseif($body === '') {
        $errorMessage = Flux::Message('CMSNewsBody');
    }
    elseif($author === '') {
        $errorMessage = Flux::Message('CMSNewsAuthor');
    }
	else {
		if($link) {
			if(!preg_match('!^http://!i', $link)) {
				$news_link = "http://$link";
			}
		}
		
        $sql = "INSERT INTO {$server->loginDatabase}.$news (title, body, link, author, created, modified)";
        $sql .= "VALUES (?, ?, ?, ?, NOW(), NOW())"; 
        $sth = $server->connection->getStatement($sql);
        $sth->execute(array($title, $body, $link, $author));
        
        $session->setMessageData(Flux::message('CMSNewsAdded'));
        if ($auth->actionAllowed('news', 'index')) {
            $this->redirect($this->url('news','index'));
        }
        else {
            $this->redirect();
        }
    }
}
?>
