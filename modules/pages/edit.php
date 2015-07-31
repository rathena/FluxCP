<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('XCMSPageEditTitle');
$pages 	= Flux::config('FluxTables.CMSPagesTable');
$id 	= $params->get('id');
$sql 	= "SELECT id, title, path, body, modified FROM {$server->loginDatabase}.$pages WHERE id = ?";
$sth 	= $server->connection->getStatement($sql);
$sth->execute(array($id));
$page 	= $sth->fetch();

if($page) {
	$title	= $page->title;
	$path	= $page->path;
	$body	= $page->body;
    
    if(count($_POST)) {
        $title = trim($params->get('page_title'));
		$path 	= trim($params->get('page_path'));
        $body 	= trim($params->get('page_body'));
        
        if($title === '') {
            $errorMessage = Flux::Message('XCMSPageTitleError');
		}
        elseif($path === '') {
            $errorMessage = Flux::Message('XCMSPagePathError');
        }
        elseif($body === '') {
            $errorMessage = Flux::Message('XCMSPageBodyError');    
        }                                                  
        else {
            $sql  = "UPDATE {$server->loginDatabase}.$pages SET title = ?, path = ?, body = ?, modified = NOW() WHERE id = ?";            
            $sth = $server->connection->getStatement($sql);
            $sth->execute(array($title, $path, $body, $id)); 
			
            $session->setMessageData(Flux::message('XCMSPageUpdated'));
            if ($auth->actionAllowed('pages', 'index')) {
                $this->redirect($this->url('pages','index'));
            }
            else {
                $this->redirect();
            }      
        }
    }
}
?>