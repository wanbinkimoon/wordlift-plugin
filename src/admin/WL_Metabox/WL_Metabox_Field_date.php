<?php


class WL_Metabox_Field_date extends WL_Metabox_Field {
    
    public function html_input( $date ) {
        
        $pickerDate  = '';
        if( !empty( $date ) ){
            $pickerDate = date( 'Y/m/d H:i', strtotime( $date ) );
        }
        $pickerDate = esc_attr( $pickerDate );
        
        $html = '<div class="wl-input-wrapper">
            <input type="text" class="' . $this->meta_name . '" value="' . $pickerDate . '" style="width:88%" />
            <input type="hidden" class="' . $this->meta_name . '_hidden" name="wl_metaboxes[' . $this->meta_name . '][]" value="' . $date . '" />      
            <button class="wl-remove-input" type="button" style="width:10%">Remove</button>
        </div>';
        
        return $html;

    }
    
    public function html_wrapper_close(){
          
        $html = "<script type='text/javascript'>
        $ = jQuery;
        $(document).ready(function() {

            $('." . $this->meta_name . "').datetimepicker({
                onChangeDateTime:function(dp, input){
                    // format must be: 'YYYY-MM-DDTHH:MM:SSZ' from '2014/11/21 04:00'
                    var currentDate = input.val();
                    currentDate = currentDate.replace(/(\d{4})\/(\d{2})\/(\d{2}) (\d{2}):(\d{2})/,'$1-$2-$3T$4:$5:00Z')
                    // store value to save in the hidden input field
                    $('." . $this->meta_name . "_hidden').val( currentDate );
                }
            });
        });
        </script>";
        
        $html .= parent::html_wrapper_close();
        
        return $html;
    }
}
