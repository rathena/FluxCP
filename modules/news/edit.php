<?php
if (!defined('FLUX_ROOT')) exit;
$title = Flux::message('XCMSNewsEditTitle');
$news	= Flux::config('FluxTables.XCMSNewsTable');
$id		= $params->get('id');
$sql	= "SELECT id, title, body, link, author, modified FROM {$server->loginDatabase}.$news WHERE id = ?";
$sth	= $server->connection->getStatement($sql);
$sth->execute(array($id));
$new	= $sth->fetch();

if($new) {
    $title	= $new->title;
    $body	= $new->body;
    $link	= $new->link;
    $author	= $new->author;
    
    if(count($_POST)) {
        $title	= trim($params->get('news_title'));
        $body 	= trim($params->get('news_body'));
		$link 	= trim($params->get('news_link'));
		$author = trim($params->get('news_author'));
        
        if($title === '') {
            $errorMessage = Flux::Message('XCMSNewsTitleError');
        }
        elseif($body === '') {
            $errorMessage = Flux::Message('XCMSNewsBody');
        }
		elseif($author == '') {
				 $errorMessage = Flux::Message('XCMSNewsAuthor');
		}
		else {
			if($link) {
				if (!preg_match('!^http://!i', $news_link)) {
					$news_link = "http://$news_link";
				}
			}
			
			$sql = "UPDATE {$server->loginDatabase}.$news SET ";
			$sql .= "title = ?, body = ?, link = ?, author = ?, modified = NOW() ";
			$sql .= "WHERE id = ?";
			$sth = $server->connection->getStatement($sql);
			$sth->execute(array($title, $body, $link, $author, $id));
			
			$session->setMessageData(Flux::message('XCMSNewsUpdated'));
			if ($auth->actionAllowed('news', 'index')) {
				$this->redirect($this->url('news','index'));
			}
			else {
				$this->redirect();
			}           
		}
    }
}
?>