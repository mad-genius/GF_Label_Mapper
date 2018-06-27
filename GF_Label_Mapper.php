<?php

class GF_Label_Mapper {

    public $request;
    public $form;
    public $map;
    public $fields;

    function __construct( $form, $request = null, $for_js = false ) {
        $this->request = $request;
        $this->form = $form;
        $this->fields = array();
        $this->for_js = $for_js;
        $this->create_map();

        if( $request !== NULL ) {
            $this->map_fields();
        }
    }

    private function create_map() {
        foreach( $this->form['fields'] as $field ) {
            $field_label = strtolower( $field['label'] );

            if( ! empty( $field['inputs'] ) ) {
                $this->map[$field_label] = array();

                foreach( $field['inputs'] as $input ) {
                    // skip hidden fields
                    if( array_key_exists( 'isHidden', $input ) && $input['isHidden'] ) continue;

                    $id = $input['id'];
                    $id = str_replace( '.', '_', $id );
                
                    if( $this->for_js ) {
                        $id = '#input_' . $this->form['id'] . '_' . $id;
                    } else {
                        $id = 'input_' . $id;
                    }

                    $input_label = strtolower( $input['label'] );
                    $this->map[$field_label][$input_label] = $id;
                }
            } else {
                if( $this->for_js ) {
                    $id = '#input_' . $this->form['id'] . '_' . $field['id'];
                } else {
                    $id = 'input_' . $field['id'];
                }

                $this->map[$field_label] = $id;
            }

        }
    }

    private function map_fields() {
        foreach( $this->map as $name => $val ) {
            if( is_array( $val ) ) {
                $this->fields[$name] = array();

                foreach( $val as $sub_name => $sub_val ) {
                    // credit card expiration needs special handling
                    if( $sub_name === 'expiration month' ) {
                        $sub_val = str_replace( '_month', '', $sub_val );
                        $this->fields[$name][$sub_name] = $this->request[$sub_val][0];
                    } elseif( $sub_name === 'expiration year' ) {
                        $sub_val = str_replace( '_year', '', $sub_val );
                        $this->fields[$name][$sub_name] = $this->request[$sub_val][1];
                    } else {
                        $this->fields[$name][$sub_name] = $this->request[$sub_val];
                    }
                }

            } else {
                $this->fields[$name] = $this->request[$val];
            }
        }
    }

}
