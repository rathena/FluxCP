tinyMCE.init({
    // General options
    mode : "specific_textareas",
	editor_selector : "cmsEnabled",
    theme : "simple",
	skin : "default",
    plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount",

    // Theme options
    theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,fontselect,fontsizeselect|bullist,numlist,|,undo,redo",
    theme_advanced_buttons2 : "link,unlink,anchor,image,code,|,forecolorpicker,backcolorpicker,|,emotions",
    theme_advanced_buttons3 : "tablecontrols",
	theme_advanced_buttons4	: "removeformat,|,media,hr",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing : true,

    // Drop lists for link/image/media/template dialogs
    template_external_list_url : "lists/template_list.js",
    external_link_list_url : "lists/link_list.js",
    external_image_list_url : "lists/image_list.js",
    media_external_list_url : "lists/media_list.js",
});