<?php

class contact_management_persons_list extends WP_List_Table
{ 
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'person',
            'plural' => 'persons',
        ));
    }


    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }


    function column_name($item)
    {
        $actions = array(
            'edit' => sprintf('<a href="?page=contact_management_new_person&id=%s">%s</a>', $item['id'], __('Edit', 'contact_management')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'contact_management')),
        );

        return sprintf('%s %s',
            $item['name'],
            $this->row_actions($actions)
        );
    }


    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', 
            'name' => __('Person Name', 'contact_management'),
            'email' => __('Person Email', 'contact_management'),
        );
        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = array(
            'name' => array('name', true),
            'email' => array('email', true),
        );
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'contact_management_persons'; 

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }

    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'contact_management_persons'; 

        $per_page = 10;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();

        // $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'desc';

        $OFFSET = $paged * $per_page;
        $search_term = isset($_REQUEST['s']) ? trim($_REQUEST['s']) : "";
        if(!empty($search_term)){
            $this->items = $wpdb->get_results("SELECT * FROM $table_name WHERE name LIKE '%".$search_term."%' ORDER BY ". $orderby ." ". $order ." LIMIT ".$per_page." OFFSET ".$OFFSET, ARRAY_A);
            $total_items = $wpdb->get_results("SELECT * FROM $table_name WHERE name LIKE '%".$search_term."%' ORDER BY ". $orderby ." ". $order);
            $total_items = count($total_items);
        }else{
            $this->items = $wpdb->get_results("SELECT * FROM $table_name WHERE name LIKE '%".$search_term."%' ORDER BY ". $orderby ." ". $order ." LIMIT ".$per_page." OFFSET ".$OFFSET, ARRAY_A);
            $total_items = $wpdb->get_results("SELECT * FROM $table_name ORDER BY $orderby $order");
            $total_items = count($total_items);
        }


        $this->set_pagination_args(array(
            'total_items' => $total_items, 
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page) 
        ));
    }
}

function contact_management_admin_menu(){
    add_menu_page(__('Contact Management', 'contact_management'), __('Contact Management', 'contact_management'), 'activate_plugins', 'contact-management', 'contact_management_persons_list');

    add_submenu_page('contact-management', __('All Persons', 'contact_management'), __('All Persons', 'contact_management'), 'activate_plugins', 'contact_management_persons', 'contact_management_persons_list');
    add_submenu_page('contact-management', __('Add New Person', 'contact_management'), __('Add New Person', 'contact_management'), 'manage_options', 'contact_management_new_person', 'contact_management_new_person_func');

    add_submenu_page('contact-management', __('All Contacts', 'contact_management'), __('All Contacts', 'contact_management'), 'activate_plugins', 'contact_management_contacts', 'contact_management_contacts_list');
    add_submenu_page('contact-management', __('Add New Contact', 'contact_management'), __('Add New Contact', 'contact_management'), 'manage_options', 'contact_management_new_contact', 'contact_management_new_contact_func');

    remove_submenu_page('contact-management', 'contact-management');
}

add_action('admin_menu', 'contact_management_admin_menu');


function contact_management_persons_list()
{
    global $wpdb;

    $table = new contact_management_persons_list();
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'contact_management'), count($_REQUEST['id'])) . '</p></div>';
    }
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php _e('Persons', 'contact_management')?></h1>
        <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=contact_management_new_person');?>"><?php _e('Add New', 'contact_management')?></a>
       <?php echo $message; ?>

       <form id="contacts-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php 
        $table->search_box("Search Post", "search_post_id");
        $table->display();
        ?>
    </form>

</div>
<?php
}


function contact_management_new_person_func()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_management_persons'; 

    $message = '';
    $notice = '';

    $default = array(
        'id' => 0,
        'name' => '',
        'email' => '',
    );

    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {

        $item = shortcode_atts($default, $_REQUEST);     

        $item_valid = contact_management_validate_person($item);
        if ($item_valid === true) {
            if ($item['id'] == 0) {
                $result = $wpdb->insert($table_name,
                    array(
                        'name' => $item['name'],
                        'email' => $item['email']
                    )
                );
                $item['id'] = $wpdb->insert_id;
                $message = __('Item added successfully', 'contact_management');
            } else {
                $result = $wpdb->update($table_name,
                    array(
                        'name' => $item['name'],
                        'email' => $item['email'],
                    ),
                    array(
                        'id' => $item['id'],
                    )
                );
                $message = __('Item saved successfully', 'contact_management');
            }
        } else {

            $notice = $item_valid;
        }
    }

    $item = $default;
    if (isset($_GET['id'])) {
        $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
        if (!$item) {
            $item = $default;
            $notice = __('Item not found', 'contact_management');
        }
    }

    add_meta_box('contact_management_person_form_meta_box', __('Person Informations', 'contact_management'), 'contact_management_person_meta_box_func', 'person', 'normal', 'default');

    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php _e('Add New Person', 'contact_management')?></h1>
        <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=contact_management_persons');?>"><?php _e('back to list', 'contact_management')?></a>

        <?php if (!empty($notice)): ?>
            <div id="notice" class="error"><p><?php echo $notice ?></p></div>
        <?php endif;?>
        <?php if (!empty($message)): ?>
            <div id="message" class="updated"><p><?php echo $message ?></p></div>
        <?php endif;?>

        <form id="form" method="POST">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>

            <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

            <div class="metabox-holder" id="poststuff">
                <div id="post-body">
                    <div id="post-body-content">
                        <?php do_meta_boxes('person', 'normal', $item); ?>
                    </div>
                </div>
            </div>
        </form>
        
    </div>
    <?php
}

function contact_management_person_meta_box_func($item)
{
    ?>
    <tbody>
        <div class="formdata">
            <form>
                <p>			
                  <label for="name"><?php _e('Name:', 'contact_management')?></label>
                  <br>	
                  <input id="name" name="name" type="text" style="width: 100%" value="<?php echo esc_attr($item['name'])?>"
                  required>              
                  <br>
              </p>
              <p>			
                  <label for="email"><?php _e('Email:', 'contact_management')?></label>
                  <br>	
                  <input id="email" name="email" type="text" style="width: 100%" value="<?php echo esc_attr($item['email'])?>"
                  required>              
                  <br>
              </p>
              <p>
                  <input type="submit" value="<?php _e('Save', 'contact_management')?>" id="submit" class="button-primary" name="submit" style='margin-top: 15px;'>
              </p>
          </form>
      </div>
  </tbody>
  <?php
}


function contact_management_validate_person($item){
    $messages = array();

    if (empty($item['name'])) $messages[] = __('Name is required', 'contact_management');   
    if (empty($item['email'])) $messages[] = __('Email is required', 'contact_management');  

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}