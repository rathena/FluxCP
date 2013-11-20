<?php
if (!defined('FLUX_ROOT')) exit;       
$title = Flux::message('NewsAddTitle');

// Form values.
$news	= Flux::config('FluxTables.CMSNewsTable');
$title	= trim($params->get('news_title'));
$body	= trim($params->get('news_body'));
$link	= trim($params->get('news_link'));
$author	= trim($params->get('news_author'));

if(count($_POST))
{
    if($title === '') {
        $errorMessage = Flux::Message('XCMSNewsTitleError');
    }
    elseif($body === '') {
        $errorMessage = Flux::Message('XCMSNewsBody');
    }
    elseif($author === '') {
        $errorMessage = Flux::Message('XCMSNewsAuthor');
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
        
        $session->setMessageData(Flux::message('XCMSNewsAdded'));
        if ($auth->actionAllowed('news', 'index')) {
            $this->redirect($this->url('news','index'));
        }
        else {
            $this->redirect();
        }          
    }
}
?>