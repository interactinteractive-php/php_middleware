<form method="post" id="kpiDataMartVisualConfigForm">
    
    <div class="row">
        <div class="col">
            <div class="mb10">
                <button type="button" class="btn btn-sm green-meadow" onclick="kpiDataMartAddObjectTable(this);">
                    <i class="icon-plus3 font-size-12"></i> <?php echo $this->lang->line('add_btn'); ?>
                </button>
            </div>
            <div class="heigh-editor-table">
                <div class="css-editor" id="datamart-editor" style="height: 400px; position: relative;"></div>
            </div>
        </div>
    </div>
    <div class="row editor-bottom-layout" style="border-top: 1px solid #ccc;">
        <div class="col" style="flex: 0 0 500px;border-right: 1px solid #ccc;">    
            <div class="editor-table-settings-area">
            </div>    
        </div>           
        <div class="col pl10">    
            <div class="mt10 d-none reload-datamart-btn" style="margin-left: -10px;">
                <button type="button" class="btn btn-sm green-meadow" onclick="refreshLoadDataListMart()">
                    <i class="far fa-database"></i> Датамарт шинэчлэх
                </button>
            </div>
            <div class="editor-table-datalist-area">
            </div>    
        </div>    
    </div>    
    
    <input type="hidden" name="id" value="<?php echo $this->id; ?>" data-kpidatamart-id="1"/>
</form>    

<style type="text/css">
    #kpiDataMartVisualConfigForm ._jsPlumb_overlay {
        display: none;
        width: 100px;
        background-color: rgba(223, 223, 223, 0.9);
        font-size: 11px;
        line-height: 12px;
        padding: 2px;
        border: 1px #b4b4b4 solid;
        color: #000;
        z-index: 2;
    }
    #kpiDataMartVisualConfigForm ._jsPlumb_overlay._jsPlumb_hover {
        display: block;
    }
    .relation-jtype.active::after {
        content: "\f00c";
        top: 0;
        font-family: "Font Awesome 5 Pro";
        position: absolute;
        margin-left: 60px;
        font-size: 18px;
        line-height: 1;
        color: #19d119;
        font-weight: bold; 
    }
    .editor-table-settings-area table tbody button {
        display: none;
    }
    .editor-table-settings-area table tbody tr:hover button {
        display: block;
    }
    .wfdmart.active {
        border: 1px solid #333;
    }
    .editor-table-settings-area {
        max-height: 315px;
        overflow-y: auto;
    }
    #dialog-dmart-relationconfig-table {
        overflow: hidden;
    }
</style>

<script type="text/javascript">
//    if (!$("link[href='assets/custom/addon/plugins/jsplumb/css/style.v55.css']").length){
//        $("head").append('<link rel="stylesheet" type="text/css" href="assets/custom/addon/plugins/jsplumb/css/style.v55.css"/>');
//    }    
//    if (typeof isKpiAddonScript === 'undefined') {
//        $.getScript('assets/custom/addon/plugins/jsplumb/jsplumb.min.js').done(function() {
//            $.getScript(URL_APP + 'middleware/assets/js/addon/kpinew.js').done(function() {
//            });
//        });
//    }    
//    var setHeight = $(window).height() - 550;
//    var $editor = $('#datamart-editor');
//
//    $editor.css({'height': setHeight - 2, 'max-height': setHeight - 2});    
</script>