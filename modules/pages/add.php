<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMSPageAddTitle');

$pages	= Flux::config('FluxTables.CMSPagesTable'); 
$title	= trim($params->get('page_title'));
$path	= trim($params->get('page_path'));
$body	= trim($params->get('page_body'));

$tinymce_key = Flux::config('TinyMCEKey'); 

if(count($_POST))
{
    if($page_title === '') {
        $errorMessage = Flux::Message('CMSPageTitleError');
    }
    elseif($page_path === '') {
        $errorMessage = Flux::Message('CMSPagePathError');
    }
    elseif($page_body === '') {
        $errorMessage = Flux::Message('CMSPageBodyError');    
    }
    else {
        $sql = "INSERT INTO {$server->loginDatabase}.$pages (title, path, body, modified)";
        $sql .= "VALUES (?, ?, ?, NOW())";
        $sth = $server->connection->getStatement($sql);
        $sth->execute(array($title, $path, $body)); 
		
        $session->setMessageData(Flux::message('CMSPagesAdded'));
        if ($auth->actionAllowed('pages', 'index')) {
            $this->redirect($this->url('pages','index'));
        }
        else {
            $this->redirect();
        }
    }
}
?>
