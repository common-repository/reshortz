<?php
require_once RESHORTZ_ROOT . 'src/core/helpers.php';
require_once RESHORTZ_ROOT . 'src/core/Reshortz_Core.php';

require_once RESHORTZ_ROOT . 'src/core/Reshortz_PostTypes.php';
require_once RESHORTZ_ROOT . 'src/core/Reshortz_Post_Columns.php';

require_once RESHORTZ_ROOT . 'src/core/Reshortz_AdminMenu.php';


require_once RESHORTZ_ROOT . 'src/core/Reshortz_AssetManager.php';

require_once RESHORTZ_ROOT . 'src/core/Reshortz_TGM_Integration.php';
require_once RESHORTZ_ROOT . 'src/core/Reshortz_MetaBox.php';
require_once RESHORTZ_ROOT . 'src/core/Reshortz_Ajax.php';



/**
 * Initialize the core
 */
new Reshortz_Core();
new Reshortz_PostTypes();
new Reshortz_Post_Columns();
new Reshortz_AssetManager();
new Reshortz_TGM_Integration();
new Reshortz_MetaBox();
new Reshortz_AdminMenu();
new Reshortz_Ajax();
