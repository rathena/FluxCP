<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('XCMSPageAddTitle');

$pages	= Flux::config('FluxTables.CMSPagesTable'); 
$title	= trim($params->get('page_title'));
$path	= trim($params->get('page_path'));
$body	= trim($params->get('page_body'));

if(count($_POST))
{
    if($page_title === '') {
        $errorMessage = Flux::Message('XCMSPageTitleError');
    }
    elseif($page_path === '') {
        $errorMessage = Flux::Message('XCMSPagePathError');
    }
    elseif($page_body === '') {
        $errorMessage = Flux::Message('XCMSPageBodyError');    
    }
    else {
        $sql = "INSERT INTO {$server->loginDatabase}.$pages (title, path, body, modified)";
        $sql .= "VALUES (?, ?, ?, NOW())";
        $sth = $server->connection->getStatement($sql);
        $sth->execute(array($title, $path, $body)); 
		
        $session->setMessageData(Flux::message('XCMSPagesAdded'));
        if ($auth->actionAllowed('pages', 'index')) {
            $this->redirect($this->url('pages','index'));
        }
        else {
            $this->redirect();
        }
    }
}
?>