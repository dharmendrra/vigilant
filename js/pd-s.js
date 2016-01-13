
//script goes here for the map editor

//trim

//form validation starts here..
function validate_pd(){
    var email,filter;
    email = $('input#EmailAddress');
    if(!email.is(':disabled')){
        filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        //if email not confirmed
        if(!filter.test($.trim(email.val()))){
            alert('Please enter valid email address!!');
            email.focus();
            return false;
        }
        
        var confirm_email;
        confirm_email =document.getElementById('ConfirmEmailAddress');
        if ($.trim(email.value) != $.trim(confirm_email.value)){
            // Yay! valid
            alert('Please confirm email address!');
            confirm_email.focus();
            return false;
        }
    }
}

//confirm reset password
//form validation starts here..
function validate_rsetPass(){
    var NewPassword,filter;
    NewPassword = document.getElementById('NewPassword');    
    var ConfirmPassword;
    ConfirmPassword =document.getElementById('ConfirmPassword');
    if ($.trim(NewPassword.value) != $.trim(ConfirmPassword.value)){
        // Yay! valid
        alert('Please confirm password!');
        ConfirmPassword.focus();
        return false;
    }
}


var lineItemMaxUnit=0;
var currentMaxUnit=0;
//validate line-item
function validate_lineItem(){
    $('#OrderLineItemJSONdata').val(JSON.stringify(form_details));
    console.log(form_details.length);
    console.log(form_details);
    if(!$.trim($('#OrderLineItemJSONdata').val()) || form_details.length<=0){
        alert('There is no line item for making an Order! Please add line items to make an Order submission.')
        return false;
    }
    /*
    if(parseInt(lineItemMaxUnit)>parseInt($('#MAX_QTY').val())){
        if(!confirm('Quantity Exceeds Maximum Order Value. This order will need to additional approval and may delay order. Please adjust quantity or submit completed order for automatic notification of additional approval.\nClick "Ok" to submit or click "Cancel" to stay on this page to edit the quantity.')){
            return false;
        }
    }
    */
}

//responsive table design
function makeTableResponsive(){
    if(document.getElementById('pd-table')){
        var headertext = [],
        headers = document.querySelectorAll("#pd-table th"),
        tablerows = document.querySelectorAll("#pd-table th"),
        tablebody = document.querySelector("#pd-table tbody");
        
        for(var i = 0; i < headers.length; i++) {
          var current = headers[i];
          headertext.push(current.textContent.replace(/\r?\n|\r/,""));
        } 
        for (var i = 0, row; row = tablebody.rows[i]; i++) {
          for (var j = 0, col; col = row.cells[j]; j++) {
            col.setAttribute("data-th", headertext[j]);
          } 
        }
    }
}

//call function
makeTableResponsive();

//filter PartNumber based in equipment
$(document).ready(function(){
   
   //load part numbers based on equipment
   $('#Equipment').change(function(){
        $('#EquipmentID').val($("#Equipment option[value='" + this.value + "']").data('id'));
        $("#PartNumber").html('<option value=""> Loading Part Number </option>');
        $.get('ajax/data.php?data=EQ_DATA&EQ_ID='+$("#Equipment option[value='" + this.value + "']").data('id'), function (data) {
            //var jsonData = JSON.parse(data);
            $("#PartNumber").html(data);
        });
   });
   
   //fill description and quantity
   $('#PartNumber').change(function(){
        if(this.value){
            $('#ItemDescription').val($(this).find(':selected').data('desc'));
            $('#Org_MaxUnit').val($(this).find(':selected').data('qty'));
            currentMaxUnit=parseInt($(this).find(':selected').data('qty'));
            console.log(currentMaxUnit);
            $('#MaxUnit').val('0');
        }else{
            $("#PartNumber option").show();
        }
   });
   
   // show respective field for program director and other type of user
    $('#AccountType').change(function(){
        $('.login-field').hide();
        var id = $(this).find('option[value="'+this.value+'"]').data('id');
        var disable = $(this).find('option[value="'+this.value+'"]').data('disable');
        $('div#'+id).show();
        $('input#'+id).removeAttr('disabled');
        $('input#'+disable).attr('disabled','true');
    });
    
    $('.login-field').each(function(){
        if(!$(this).hasClass('active')){
            $(this).hide();
            $('input#LOG-INID').attr('disabled','true');
        }
    });
    
    //program director list filtering with blue book code search
    var bbc;//(blue book code)//var optionbbc = $('#ProgramDirectorID option').size();
    var optionsHidden;
    $('#pd-selector').keyup(function(){
    if($.trim($('#pd-selector').val())){
        //$('#ProgramDirectorID option').hide();
        i=0;
        if(optionsHidden){$('#ProgramDirectorID').html(optionsHidden);}
        else{optionsHidden = $('#ProgramDirectorID').html();}
        console.log(optionsHidden);
        bbc = $.trim(this.value);
            $('#ProgramDirectorID option').each(function(index,value){
                //$('#ProgramDirectorID option[data-name^="'+bbc+'"]')
                var optionHTML = $(this).html();
                if(optionHTML.indexOf(bbc)>-1){
                    i++;
                }else{
                    $(this).remove();
                }
            });
            $('#pd-count').html('&nbsp;&nbsp;(Total matched rows: '+i+')');
            $('#ProgramDirectorID').attr('size',10).css('height','120px');
        }else{
            $('#ProgramDirectorID option').show();
            if(optionsHidden){$('#ProgramDirectorID').html(optionsHidden);}
            $('#pd-count').html('');$('#ProgramDirectorID').removeAttr('size').css('height','42px');
        }
        /*
        $('#ProgramDirectorID').html(optionsHidden);
        var optionsHidden = $('#ProgramDirectorID').html();
        console.log(optionsHidden);
        */
    });
    
});

function del_rec(record_id){
    $('#del_id').val(record_id);
    $('#del_form').submit();
}

//update line item status - component approval admin
function updateStatus(type,status,record_id){
    if(type=='ORDER'){
        if(status=='APPROVE'){
            if(!confirm('The Order will not appear once you refresh this page after "Approving". \n Click "OK" if you want to mark this Order as "Approved" else click "Cancel".')){
                return false;
            }
        }
            if(status!="" && record_id!=""){
                $.get("ajax/status-update.php?type=ORDER&status="+status+"&record_id="+record_id,function(data){
                    if(data=='APPROVED'){
                        $('#icon_'+record_id).html('<a title="Deny" href="javascript:void(0);" onclick="updateStatus(\'ORDER\',\'DENY\',\''+record_id+'\');"><i class="icon-deny"></i></a>');
                        $('#status_'+record_id).html('Approved');
                    }
                    if(data=='DENIED'){
                        $('#icon_'+record_id).html('<a title="Approve" href="javascript:void(0);" onclick="updateStatus(\'ORDER\',\'APPROVE\',\''+record_id+'\');"><i class="icon-approve"></i></a>');
                        $('#status_'+record_id).html('Denied');
                    }
                });
            }
    }else{
        if(status!="" && record_id!=""){
            $.get("ajax/status-update.php?type=LINE_ITEM&status="+status+"&record_id="+record_id,function(json){
                var data = JSON.parse(json);
                if(data.RESPONSE=='APPROVED'){
                    $('#icon_'+record_id).html('<a title="Deny" href="javascript:void(0);" onclick="updateStatus(\'LINE_ITEM\',\'DENY\',\''+record_id+'\');"><i class="icon-deny"></i></a>');
                    $('#status_'+record_id).html('Approved');
                }
                if(data.RESPONSE=='DENIED'){
                    $('#icon_'+record_id).html('<a title="Approve" href="javascript:void(0);" onclick="updateStatus(\'LINE_ITEM\',\'APPROVE\',\''+record_id+'\');"><i class="icon-approve"></i></a>');
                    $('#status_'+record_id).html('Denied');
                }
                if(data.SET_BUTTON=='ALL_DONE'){$('#submitOrder').removeAttr('disabled');}
                else{$('#submitOrder').attr('disabled','true');}
            });
        }
    }
}

//random password generator
$.extend({
  password: function (length, special) {
    var iteration = 0;
    var password = "";
    var randomNumber;
    if(special == undefined){
        var special = false;
    }
    while(iteration < length){
        randomNumber = (Math.floor((Math.random() * 100)) % 94) + 33;
        if(!special){
            if ((randomNumber >=33) && (randomNumber <=47)) { continue; }
            if ((randomNumber >=58) && (randomNumber <=64)) { continue; }
            if ((randomNumber >=91) && (randomNumber <=96)) { continue; }
            if ((randomNumber >=123) && (randomNumber <=126)) { continue; }
        }
        iteration++;
        password += String.fromCharCode(randomNumber);
    }
    return password;
  }
});


//use random password function
$(document).ready(function() {
 
    $('.link-password').click(function(e){
 
        // First check which link was clicked
        linkId = $(this).attr('id');
        if (linkId == 'generate'){
 
            // If the generate link then create the password variable from the generator function
            password = $.password(12,true);
 
            // Empty the random tag then append the password and fade In
            $('#random').val(password).fadeIn('slow');
 
            // Also fade in the confirm link
            $('#confirm').fadeIn('slow');
        } else {
            // If the confirm link is clicked then input the password into our form field
            $('#Password').val(password);
            // remove password from the random tag
            $('#random').empty().hide();
            // Hide the confirm link again
            $(this).hide();
            // Also fade in the confirm link
            $('#generate').fadeIn('slow');
        }
        e.preventDefault();
    });
});


//script for order export
$(document).ready(function(){
   var orders = new Array;
    $('input[name="check-all"]').click(function(){
       if($(this).is(':checked')){
        orders = new Array();
        $('input[name="up_orders"').prop('checked','true');
        $('input[name="up_orders"').each(function(index,value){
            orders.push(this.value);
        });
        enableControl();
       }else{
        $('input[name="up_orders"').removeProp('checked');
        orders = new Array();
        enableControl();
       }
    });
    
    $('input[name="up_orders"]').click(function(){
       if($(this).is(':checked')){
        orders.push(this.value);
        enableControl();
       }else{
        orders.pop(this.value);
        enableControl();
       }
    });
    
    $('#export_coli').click(function(){
        $('input[name="fileName"').val('COLI');
        $('#export_form').attr('action','export-coli.php');
        return isOrdersSet('export-coli.php');
    });
    
    function triggerCOLI(){
        $('#export_coli').trigger('click');
    }
    
    $('#export_co').click(function(){
        $('input[name="fileName"').val('CO');
        $('#export_form').attr('action','export-co.php');
        if(isOrdersSet('export-co.php')){
            //setTimeout(triggerCOLI,1000);   
        }
    });
    
    $('#export_trigger').click(function(){
        $('#export_co').trigger('click');
    });
    
    function isOrdersSet(action){
        if(orders && orders.length>0){
            $('#export_orders').val(JSON.stringify(orders));
            $('#export_form').submit();
            /*
            $.ajax({
               url: action,
               method: 'POST',
               data: {export_orders:JSON.stringify(orders)} 
            }).success(function(response){
                console.log(response);
                window.open(response,'new');
            });
            */
            return true;
        }else{
            alert('Please select atleast one Order Number to export CSV');
            return false;
        }
    }
    
    function enableControl(){
        var visibility;
        $('input[name="up_orders"').each(function(index,value){
            if($(this).is(':checked')){
                visibility = true;
                return false;
            }
        });
        if(visibility){
            $('.export_co').fadeIn();
            $('.frm-controls').fadeIn();
            $('.export_co').parent().addClass('form-body-control').removeClass('form-body');
        }else{
            $('.export_co').hide();
            $('.frm-controls').hide();
            $('.export_co').parent().addClass('form-body').removeClass('form-body-control');
        }
    }
    
    enableControl(); 
});