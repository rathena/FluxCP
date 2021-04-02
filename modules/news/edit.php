<?php
if (!defined('FLUX_ROOT')) exit;
$title = Flux::message('CMSNewsEditTitle');
$news	= Flux::config('FluxTables.CMSNewsTable');
$id		= $params->get('id');
$sql	= "SELECT * FROM {$server->loginDatabase}.$news WHERE id = ?";
$sth	= $server->connection->getStatement($sql);
$sth->execute(array($id));
$new	= $sth->fetch();

$tinymce_key = Flux::config('TinyMCEKey'); 

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
            $errorMessage = Flux::Message('CMSNewsTitleError');
        }
        elseif($body === '') {
            $errorMessage = Flux::Message('CMSNewsBody');
        }
		elseif($author == '') {
				 $errorMessage = Flux::Message('CMSNewsAuthor');
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
			
			$session->setMessageData(Flux::message('CMSNewsUpdated'));
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
