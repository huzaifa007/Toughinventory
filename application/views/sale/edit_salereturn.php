<?php $row = $saleData->row(); ?>
<div id="container" class="row-fluid">
<?php $this->load->view('sidebar'); ?>
<div id="main-content">
         <!-- BEGIN PAGE CONTAINER-->
         <div class="container-fluid">
            <!-- BEGIN PAGE HEADER-->   
            <div class="row-fluid">
               <div class="span12">
                 <h3 class="page-title">
                     Edit Sale Return
                     <small>Please enter the information below.</small>
                   </h3>
                   <?php $this->load->view('msg'); ?>
               </div>
            </div>
            <div class="row-fluid">
               <div class="span12">
               <input type="hidden" id="id" value="<?php echo $row->id; ?>">
               <div class="row-fluid">
                 <div class="span6">
                   <div class="control-group">
                      <label class="control-label">Customer</label>
                         <div class="controls">
                                  <select id="customer_id" class="span6 chzn-select" tabindex="1">
                                        <?php foreach ($customers->result() as $customer) 
                                        {
                                          $attr = ( ($customer->id == $row->customer_id) ? " selected='selected' " : null );
                                         ?>
                                          <option <?php echo $attr; ?> value="<?php echo $customer->id; ?>"><?php echo $customer->name; ?></option>
                                         <?php
                                        } ?>
                                    </select>
                                </div>
                    </div>
                 </div>
                 <div class="span6">
                   <div class="control-group">
                      <label class="control-label">Reference(Optional)</label>
                        <div class="controls">
                          <input type="text" id="reference_no" value="<?php echo $row->reference_no; ?>" data-placement="right" 
                          data-original-title="Reference is an optional field, You can store any kind of data for reference e.g(Company Invoice No - INV01010)" 
                          placeholder="Reference" class="input-xlarge tooltips" />
                          <!-- <span class="help-inline">Some hint here</span> -->
                      </div>
                    </div>
                 </div>
               </div>
               <div class="row-fluid">
                 <div class="span6">
                    <div class="control-group">
                      <label class="control-label">Date</label>
                        <div class="controls">
                          <input type="date" id="date" value="<?php echo $row->date; ?>" class="input-xlarge" />
                          <!-- <span class="help-inline">Some hint here</span> -->
                      </div>
                    </div>
                 </div>
                 <div class="span6">
                    <div class="control-group">
                      <label class="control-label">NOTE(Optional)</label>
                        <div class="controls">
                          <textarea id="note"><?php echo $row->note; ?></textarea>
                          <!-- <span class="help-inline">Some hint here</span> -->
                      </div>
                    </div>
                 </div>
               </div>
               <div class="row-fluid">
                 <div class="span4">
                 <div class="widget green" id="draggable">
                   <div class="widget-title"><h4>Dragable</h4></div>
                   <div class="widget-body">
                      <div class="control-group">
                      <label class="control-label black">Product Search</label>
                        <div class="controls">
                          <input type="text" id="autoCompleteSearch" class="input-medium" placeholder="Search Item" />
                      </div>
                    </div>
                   </div>
                 </div>
                   
                 </div>
               </div>
               <div class="row-fluid">
                 <div class="span12">
                 <div class="widget green">
                   <div class="widget-title"><h4>Inventory</h4></div>
                   <div class="widget-body">
                      <table class="table table-striped table-bordered table-advance table-hover">
                       <thead>
                         <tr>
                           <td>Product Name(Code)</td>
                           <td>Quantity</td>
                           <td>Remove</td>
                         </tr>
                       </thead>
                       <tbody id="tbody">
                         <?php $items = $this->dbo->viewSaleReturnItems(array('sale_id'=>$row->id)); 
                       foreach ($items->result() as $item) 
                       {
                          ?> 
                          <tr>
                            <td><?php echo $item->product_name; ?>(<span data-span='code'><?php echo $item->product_id; ?></span>)</td>
                            <td><input type='text' value='<?php echo $item->quantity; ?>' data-size='<?=$item->size;?>' class='calculateQuantity' onchange='calculate();'></td>
                            <td><button data-id="<?=$item->product_id?>" class='btn btn-danger removeRow'><i class='icon-trash'></i></button></td>
                          </tr>
                          <?php 
                       }?>
                       </tbody>
                     </table>
                   </div>
                 </div>
                 </div>
               </div>
               <div class="row-fluid">
                 <div class="span3 pull-right">
                     <div class="control-group">
                                <div class="controls">
                                        <input type="button" id="btn-Submit" class="btn btn-primary" id="pulsate-hover" value="Submit">
                                </div>
                      </div>
                 </div>
               </div>
               </div>
            </div>
        </div>
</div>
</div>
<?php $this->load->view('scripts'); ?>
<script type="text/javascript">
  $(function(){
    $(".chzn-select").chosen();

     $( "#draggable" ).draggable();


      $.ajax({
        url: "<?php echo site_url(); ?>/products/productsAutoComplete",
        type: 'POST',
        dataType : 'json',
        success: function(data) {
            var autoCompleteData = new Array();
            for (var i = 0; i < data.length; i++) {
                autoCompleteData[i] = {
                      "label": data[i].id + " - IN: " + data[i].name,
                    "id": data[i].id ,
                    "name": data[i].name,
                    "price" : data[i].price,
                    "size" : data[i].size
                };
            }

           $("#autoCompleteSearch").autocomplete({
                    source: autoCompleteData,
                    
                    select: function(event, ui) {
                      addInventory(ui.item.name,ui.item.id,ui.item.price,ui.item.size);
                    }
            });
        },
        error: function(xhr, status, error) {
            console.log(xhr.responseText + " Error ");
        }
    });

  $("#autoCompleteSearch").blur(function(event) {
    $(this).val("");
  });

  window.itemsArray = [];

  function addInventory (name,id,price,size) {
    var check = $.inArray(id, window.itemsArray);
    if(check == -1)
    {
      window.itemsArray.push(id);
      var name_code = "<span id='item_name_"+id+"'>"+name+"</span>(<span data-span='code' id='item_id_"+id+"'>"+id+"</span>)";
      var html = "<tr>";
      html += "<td>"+name_code+"</td>";
      html += "<td><input type='text' data-size='"+size+"' value='0' class='calculateQuantity' onchange='calculate();' id='item_quantity_"+id+"'></td>";
      html += "<td><button data-id="+id+" class='btn btn-danger removeRow'><i class='icon-trash'></i></button></td>";
      html += "</tr>";
      $("#tbody").append(html);
    }
    else
    {
      alert("Item already in list ! Please Edit");
    }
  }

  $("#tbody").on('click', '.removeRow', function(event) {
    event.preventDefault();
    /* Act on the event */
    var id = $(this).attr('data-id');
    deleteArrayItem(id,window.itemsArray);
    $(this).closest('tr').remove();
    calculate();
  });


  $("#btn-Submit").click(function(event) {
    /* Act on the event */
    event.preventDefault();
    $(".loader").show();
    var sale_id = $("#id").val();
    var customer_id , reference_no , date , note;
    customer_id = $("#customer_id").val();
    reference_no = $("#reference_no").val();
    date = $("#date").val();
    note = $("#note").val();
   
    var singleRow = {
      'customer_id' : customer_id,
      'reference_no' : reference_no,
      'date' : date,
      'note' : note
    };
    console.log(singleRow);
    var length = $("#tbody tr").length;

    if (date.length < 1 || length < 1) 
      {
        alert("Fields with * must be filled out!");
        $(".loader").hide();
      }
      else
      {
        var multiRowsValues = [];
        for (var i = 1; i <= length; i++) 
        {
          var product_id = $("#tbody").find('tr:nth-child(' + i + ')').find('td:nth-child(1)').find('span[data-span="code"]').text();
          var quantity = $("#tbody").find('tr:nth-child(' + i + ')').find('td:nth-child(2)').find('input').val();
          var rowData = {
                            "sale_id": sale_id,
                            "product_id": product_id,
                            "quantity": quantity
                        };              
          multiRowsValues.push(rowData);
      }

      $.ajax({
        url: '<?php echo site_url("sale/saveReturn"); ?>',
        type: 'POST',
        dataType: 'json',
        data: {"sRow": singleRow , "mRow" : multiRowsValues , "id" : sale_id },
      })
      .done(function(data) {
       $(".loader").hide();
        if (data.result == true) 
          {
            resetOrder();
            alert("Order Saved");
            window.location = window.location;
          }
          else
          {
            alert("Error Occured ! Try Again");
          }
      })
      .fail(function(data) {
        $(".loader").hide();
        console.log(data.responseText);
      });
    }

  });


  $(document).on('blur', '.calculateQuantity', function(event) {
     event.preventDefault();
    /* Act on the event */
      var size = Number($(this).attr('data-size'));
      var quantity = $(this).val().toString();
      var q_split = quantity.split("p");
      console.log(q_split.length);
      if(q_split.length > 1)
      {
      console.log(q_split);
      var ctn = q_split[0];
      var pieces = Number(q_split[1]);
      console.log(q_split);
      if (pieces >= size) 
        {
          alert('Please enter the valid quantity');
          $(this).val("0");
          $(this).focus();
          return false;
        }
        else
        {
            var calcctn = 1 / size * pieces;
            console.log(calcctn.toPrecision(2));
            var totalQ = (Number(ctn) + Number(calcctn.toFixed(2)));
            $(this).val(totalQ);
            calculate();
        }
      }
      else
      {
        return false;
      }
  });


// End of ready
  });

  function calculate () 
  {
    // var length = $("#tbody tr").length;
    // var total_amount = 0;
    // for (var i = 1; i <= length; i++) 
    // {
    //         var quantity = $("#tbody").find('tr:nth-child(' + i + ')').find('td:nth-child(2)').find('input').val();
    //         var unit_price = $("#tbody").find('tr:nth-child(' + i + ')').find('td:nth-child(3)').text();
    //         var scheme = $("#tbody").find('tr:nth-child(' + i + ')').find('td:nth-child(4)').find('input').val();
    //         var comission = $("#tbody").find('tr:nth-child(' + i + ')').find('td:nth-child(5)').find('input').val();
    //         var total = (Number(quantity) * Number(unit_price)) - (Number(scheme) + Number(comission));
    //         $("#tbody").find('tr:nth-child(' + i + ')').find('td:nth-child(6)').text(total);
    //         total_amount += total;
    // }
    // var discount_percent = $("#discount_amount").val();
    // discount_percent = ((discount_percent.length > 0) ? discount_percent : 0);
    // discount_value = Math.round((total_amount/100*discount_percent) * 100) / 100;
    // var net_Total = Math.round((total_amount - discount_value) * 100) / 100;
    // $("#disc_value").text(discount_value);
    // $("#total_amount").val(total_amount);
    // $("#total_payble").val(net_Total);
  }

  function resetOrder() 
  {
    $("#supplier_id").val("");
    $("#reference_no").val("");
    $("#date").val("");
    $("#note").val("");
    $("#discount_amount").val("");
    var length = $("#tbody tr").length;
    for (var i = 1; i <= length; i++) 
    {
            $("#tbody").find('tr:nth-child(1)').remove();
    }
    calculate();

  }

</script>