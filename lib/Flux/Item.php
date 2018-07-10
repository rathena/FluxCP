<?php
class Flux_Item {
    /**
     * @access public
     * @var Flux_Athena
     */
    public $server;

    public $table;
    public $table_database;

    public $select_string;

    protected $join_string_tmp;
    protected $named_item_string_tmp;

    protected $item_forge_flag;
    protected $item_creation_flag;
    protected $item_egg_flag;

    public $join_string;
    public $named_item_string;

    public $random_options_enabled;
    public $random_options_select;

    /**
     * $itemLib = new Flux_Item($server);
     */
    public function __construct(Flux_Athena $server, $main_table = '', $column_id = '') {
        $this->server = $server;

        $special = Flux::config('ItemSpecial');
        $this->item_forge_flag = $special->get('forge');
        $this->item_creation_flag = $special->get('create');
        $this->item_egg_flag = $special->get('pet');

        $this->random_options_enabled = Flux::config('RandomOptions');
        $this->random_options_select = "";
        if ($this->random_options_enabled) {
            $this->random_options_select  = ",`option_id0`,`option_val0`,`option_parm0`";
            $this->random_options_select .= ",`option_id1`,`option_val1`,`option_parm1`";
            $this->random_options_select .= ",`option_id2`,`option_val2`,`option_parm2`";
            $this->random_options_select .= ",`option_id3`,`option_val3`,`option_parm3`";
            $this->random_options_select .= ",`option_id4`,`option_val4`,`option_parm4`";
        }

        $this->table = "items";
        $this->table_database = "`{$server->charMapDatabase}`.".$this->table;

        $char_table_name = "c";
        $this->select_string = ",".$this->table.".`name_japanese`,".$this->table.".`type`,".$this->table.".`slots`";
        $this->select_string .= ",".$char_table_name.".`char_id`,".$char_table_name.".`name` AS char_name";
        $this->select_string .= $this->random_options_select." ";

        $this->join_string_tmp = "LEFT JOIN ".$this->table_database." ON `%s`.`%s` = `".$this->table."`.id ";

        $special = Flux::config('ItemSpecial');
        $this->named_item_string_tmp  = "LEFT JOIN {$server->charMapDatabase}.`char` AS ".$char_table_name." ";
        $this->named_item_string_tmp .= "ON ".$char_table_name.".char_id = IF(%s.`card0` IN ('".$special->get('forge')."', '".$special->get('create')."'), ";
        $this->named_item_string_tmp .= "IF(%s.`card2` < 0, %s.`card2` + 65536, %s.`card2`) ";
        $this->named_item_string_tmp .= "| (%s.`card3` << 16), NULL) ";

        if ($main_table) {
            $this->join_string = self::getJoinString($main_table, $column_id);
            $this->named_item_string = self::getNamedItemString($main_table);
        }
    }

    /**
     * Get join string from join_string_tmp by assigning table and column as ON clause
     * @param $table Table name
     * @param $column Column name
     */
    public function getJoinString($table, $column) {
        return sprintf($this->join_string_tmp, $table, $column);
    }

    /**
     * Get string for 'named' item as join table
     * @param $table Table name
     */
    public function getNamedItemString($table) {
        return sprintf($this->named_item_string_tmp, $table, $table, $table, $table, $table);
    }

    /**
     * Check if item has random options
     * @param $item Item object fetched from table
     * @return Number of options or 0 if doesn't have
     */
    public function itemHasOptions($item) {
        $opt = 0;
        if ($item->option_id0)
            ++$opt;
        if ($item->option_id1)
            ++$opt;
        if ($item->option_id2)
            ++$opt;
        if ($item->option_id3)
            ++$opt;
        if ($item->option_id4)
            ++$opt;
        return $opt;
    }

    /**
     * Check if card0 slot is used for flag of forged, creation, or pet egg item
     * @param $card0
     */
    public function itemIsSpecial($card0) {
        if ($card0 == $this->item_forge_flag || $card0 == $this->item_creation_flag || $card0 == $this->item_egg_flag)
            return true;
        return false;
    }

    public function getCardsOver($item) {
        $cardsOver = -$item->slots;
        if ($item->card0) {
            $cardsOver++;
        }
        if ($item->card1) {
            $cardsOver++;
        }
        if ($item->card2) {
            $cardsOver++;
        }
        if ($item->card3) {
            $cardsOver++;
        }
        return $cardsOver;
    }

    public function getItemSpecialValues($item, $keepCard = false) {
        $itemAttributes = Flux::config('Attributes')->toArray();
        if ($item->card0 == $this->item_forge_flag) {
            $item->slots = 0;
            // $item->card1 holds of ((star_crumb_num*5)<<8) + element
            // 1280 is value of if star_crumb_num = 1
            if (intval($item->card1/1280) > 0) {
                $itemcard1 = intval($item->card1/1280);
                $item->forged_prefix = '';
                for ($i = 0; $i < $itemcard1; $i++)
                    $item->forged_prefix .= Flux::message('ForgedWeaponVeryLabel').' ';
                $item->forged_prefix .= Flux::message('ForgedWeaponStrongLabel').' ';
            }
            $item->is_forged = true;

            if (array_key_exists($item->card1%1280, $itemAttributes))
                $item->element = htmlspecialchars($itemAttributes[$item->card1%1280]);
        }

        if ($item->card0 == $this->item_creation_flag) {
            $item->is_creation = true;
        }

        if ($item->card0 == $this->item_egg_flag) {
            $item->is_egg = true;
            $item->egg_renamed = $item->card3;
        }

        if (!$keepCard)
            $item->card0 = $item->card1 = $item->card2 = $item->card3 = 0;
        return $item;
    }

    /**
     * Check if card0 slot is used for flag of forged, creation, or pet egg item
     * @param $items List of items as result by table fetching
     * @param $tmp Flux_Template instance
     */
    public function prettyPrint($items, Flux_Template $tmp = null) {
        $cardIDs = array();

        foreach ($items as $item) {
            $item->cardsOver = self::getCardsOver($item);

            if ($tmp) {
                if ($item->card0) {
                    $cardIDs[] = $item->card0;
                }
                if ($item->card1) {
                    $cardIDs[] = $item->card1;
                }
                if ($item->card2) {
                    $cardIDs[] = $item->card2;
                }
                if ($item->card3) {
                    $cardIDs[] = $item->card3;
                }
            }

            if (self::itemIsSpecial($item->card0) || $item->cardsOver < 0) {
                $item->cardsOver = 0;
            }

            $item->options = ($this->random_options_enabled ? self::itemHasOptions($item) : 0);

            if (!self::itemIsSpecial($item->card0))
                continue;

            $item = self::getItemSpecialValues($item);
            $item->special = 1;
        }

        if ($tmp)
            $tmp->cardIDs = $cardIDs;

        return $items;
    }

}
