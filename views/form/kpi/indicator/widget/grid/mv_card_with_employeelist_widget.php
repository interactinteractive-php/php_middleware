<style type="text/css">
.mv_card_with_employeelist_widget {
    display: inline-block;
    width: 90px;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    -ms-border-radius: 4px;
    -o-border-radius: 4px;
    border-radius: 4px;
}
.mv_card_with_employeelist_widget:hover {
    box-shadow: none;
}
.mv_card_with_employeelist_widget:hover .no-dataview {
    display: block !important;
}
.mv_card_with_employeelist_widget .card {
    margin-bottom: 0;
}
.mv_card_with_employeelist_widget .card-body .card-img {
    border-radius: 0;
    border-bottom: 1px #eee solid;
}
.mv_card_with_employeelist_widget h5 {
    display: block;
    padding: 0 10px;
    font-size: 12px;
    color: #333;
    line-height: 20px;
    text-align: center;
    overflow: hidden;
}
#objectdatacustomgrid-<?php echo $this->indicatorId ?> {
/*    background-color: transparent;
    border-bottom: 5px solid;*/
    /*border-image: linear-gradient(to right, #519157 25%, #de6e0a 25%, #de6e0a 50%,#33a9ca 50%, #33a9ca 75%, #189e6b 75%) 5;*/
}
.mv_card_with_employeelist_widget_main {
/*    background-image: url('middleware/assets/img/layout-themes/image/card/mv_card_with_employeelist_widget_group_21984.png');
    background-size: 230px 180px;
    background-repeat: no-repeat;
    background-position: right bottom;    */
}
.mv_card_with_employeelist_widget_main .mv_card_with_employeelist_widget.active img {
    border-color: #ca3361 !important;
}
</style>

<div class="dv-process-buttons mt-2 ml-2">
    <div class="btn-group btn-group-devided">
    <?php 
    $contextMenu = array();
    $deleteClickAction = '';
    $deleteCrudId = '';
    $mapId = '';
    
    foreach ($this->process as $process) {

        $srcIndicatorId = $process['structure_indicator_id'];
        $crudIndicatorId = issetParam($process['crud_indicator_id']);
        $isFillRelation = issetParam($process['is_fill_relation']);
        $isNormalRelation = issetParam($process['is_normal_relation']);
        $typeCode = $process['type_code'];
        $kpiTypeId = $process['kpi_type_id'];
        $buttonName = $className = $onClick = $description = '';
        $opt = ', undefined';

        if ($srcIndicatorId == $this->indicatorId) {

            if ($typeCode == 'create') {

                $mapId = issetParam($process['map_id']);
                $labelName = $process['label_name'] == 'Нэмэх' ? $this->lang->line('add_btn') : $this->lang->line($process['label_name']);
                $className = 'btn btn-success btn-circle btn-sm';
                $buttonName = '<i class="far fa-plus"></i> '.$labelName;
                
                if ($isFillRelation) {
                    $opt = ', {fillSelectedRow: true, mode: \'create\'}';
                } elseif ($isDfillRelation) {
                    $opt = ', {fillDynamicSelectedRow: true, mode: \'create\'}';
                }
                
                if ($isNormalRelation) {
                    $onClick = "mvNormalRelationRender(this, '$kpiTypeId', '".$this->indicatorId."', {methodIndicatorId: $crudIndicatorId, structureIndicatorId: $srcIndicatorId});";
                } else {
                    $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '".$this->indicatorId."', false$opt, undefined, 'mvWidgetFileViewCreateCallback');";
                }

            } elseif ($typeCode == 'update') {

                $labelName = $process['label_name'] == 'Засах' ? $this->lang->line('edit_btn') : $this->lang->line($process['label_name']);
                $isUpdate = true;

                if ($isFillRelation) {
                    $opt = ', {fillSelectedRow: true, mode: \'update\'}';
                } 

                $className = 'btn btn-warning btn-circle btn-sm';
                $buttonName = '<i class="far fa-edit"></i> '.$labelName;

                if ($isNormalRelation) {
                    $onClick = "mvNormalRelationRender(this, '$kpiTypeId', '".$this->indicatorId."', {methodIndicatorId: $crudIndicatorId, structureIndicatorId: $srcIndicatorId, mode: 'update'});";
                } else {
                    if (issetParam($process['widget_code']) !== '') {
                        $onClick = "mvWidgetRelationRender(this, '$kpiTypeId', '".$this->indicatorId."', {methodIndicatorId: $crudIndicatorId, structureIndicatorId: $srcIndicatorId, mode: 'update', widgetCode: '". $process['widget_code'] ."'});";
                    } else {
                        $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '".$this->indicatorId."', true$opt);";
                    }
                }

                $contextMenu[] = array(
                    'crudIndicatorId' => $crudIndicatorId, 
                    'labelName' => $labelName,
                    'onClick' => $onClick,
                    'actionName' => 'edit',
                    'iconName' => 'edit', 
                    'data-actiontype' => $typeCode, 
                    'data-main-indicatorid' => $this->indicatorId, 
                    'data-structure-indicatorid' => $this->indicatorId, 
                    'data-crud-indicatorid' => $crudIndicatorId,
                    'data-mapid' => issetParam($process['map_id'])
                );

            } elseif ($typeCode == 'read') {

                $isUpdate = true;
                $className = 'btn purple btn-circle btn-sm';
                $buttonName = '<i class="fas fa-eye"></i> '.$this->lang->line('view_btn');
                $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '".$this->indicatorId."', true, {mode: 'view'});";

                $contextMenu[] = array(
                    'crudIndicatorId' => $crudIndicatorId, 
                    'labelName' => $this->lang->line('view_btn'),
                    'onClick' => $onClick,
                    'actionName' => 'view',
                    'iconName' => 'eye', 
                    'data-actiontype' => $typeCode, 
                    'data-main-indicatorid' => $this->indicatorId, 
                    'data-structure-indicatorid' => $this->indicatorId, 
                    'data-crud-indicatorid' => $crudIndicatorId,
                    'data-mapid' => issetParam($process['map_id'])
                );

            } elseif ($typeCode == 'delete') {

                $isDelete = true;
                $className = 'btn btn-danger btn-circle btn-sm';
                $buttonName = '<i class="far fa-trash"></i> '.$this->lang->line('delete_btn');
                $onClick = "removeKpiIndicatorValue(this, '".$this->indicatorId."', 'mvWidgetFileViewDeleteCallback');";
                $deleteClickAction = $onClick;
                $deleteCrudId = $process['crud_indicator_id'];

                $contextMenu[] = array(
                    'crudIndicatorId' => $crudIndicatorId, 
                    'labelName' => $this->lang->line('delete_btn'),
                    'onClick' => $onClick,
                    'actionName' => 'delete',
                    'iconName' => 'trash', 
                    'data-actiontype' => $typeCode, 
                    'data-main-indicatorid' => $this->indicatorId, 
                    'data-structure-indicatorid' => $this->indicatorId, 
                    'data-crud-indicatorid' => $crudIndicatorId,
                    'data-mapid' => issetParam($process['map_id'])
                );

            } elseif ($typeCode == 'config') {

                $isDelete = true;
                $className = 'btn blue-steel btn-circle btn-sm';
                $buttonName = '<i class="far fa-tools"></i> '.$this->lang->line('Config');
                $onClick = "mapKpiIndicatorValue(this, '$kpiTypeId', '".$this->indicatorId."', 'config');";

                $contextMenu[] = array(
                    'crudIndicatorId' => $crudIndicatorId, 
                    'labelName' => $this->lang->line('Config'),
                    'onClick' => $onClick,
                    'actionName' => 'config',
                    'iconName' => 'tools', 
                    'data-actiontype' => $typeCode, 
                    'data-main-indicatorid' => $this->indicatorId, 
                    'data-structure-indicatorid' => $this->indicatorId, 
                    'data-crud-indicatorid' => $crudIndicatorId,
                    'data-mapid' => issetParam($process['map_id'])
                );

            } elseif ($typeCode == '360') {

                $isDelete = true;
                $className = 'btn blue-steel btn-circle btn-sm';
                $buttonName = '<i class="far fa-tools"></i> '.$this->lang->line('360');
                $onClick = "mapKpiIndicatorValue(this, '$kpiTypeId', '".$this->indicatorId."', '360');";

                $contextMenu[] = array(
                    'crudIndicatorId' => $crudIndicatorId, 
                    'labelName' => $this->lang->line('360'),
                    'onClick' => $onClick,
                    'actionName' => '360',
                    'iconName' => 'tools', 
                    'data-actiontype' => $typeCode, 
                    'data-main-indicatorid' => $this->indicatorId, 
                    'data-structure-indicatorid' => $this->indicatorId, 
                    'data-crud-indicatorid' => $crudIndicatorId,
                    'data-mapid' => issetParam($process['map_id'])
                );

            } elseif ($typeCode == 'excel') {

                $className = 'btn green btn-circle btn-sm';
                $buttonName = '<i class="far fa-file-excel"></i> '.$this->lang->line('pf_excel_import');
                $onClick = "excelImportKpiIndicatorValue(this, '".$this->indicatorId."');";

            } elseif ($typeCode == 'excel_export_one_line') {

                $className = 'btn green btn-circle btn-sm';
                $buttonName = '<i class="far fa-file-excel"></i> Эксель нэг мөрөөр татах';
                $onClick = "exportExcelOneLineKpiIndicatorValue(this, '".$this->indicatorId."');";

            } elseif ($typeCode == 'export') {

                $isDelete = true;
                $className = 'btn green btn-circle btn-sm';
                $buttonName = '<i class="far fa-download"></i> '.$this->lang->line('excel_export_btn');
                $onClick = "exportKpiIndicatorValue(this, '".$this->indicatorId."');";

            } elseif ($kpiTypeId == '1191') {

                $className = 'btn blue-steel btn-circle btn-sm';
                $buttonName = '<i class="far fa-play"></i> ' . $this->lang->line($process['label_name'] ? $process['label_name'] : $process['name']);
                $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '$crudIndicatorId', false, {transferSelectedRow: true});";

            } elseif ($kpiTypeId == '1080') {

                $className = 'btn blue-steel btn-circle btn-sm';
                $buttonName = '<i class="far fa-play"></i> ' . $this->lang->line($process['label_name'] ? $process['label_name'] : $process['name']);
                $onClick = "callWebServiceKpiIndicatorValue(this, '$crudIndicatorId');";
            } 

        } else {

            $description = $this->lang->line(issetParam($process['description']));
            $processName = $this->lang->line(issetParam($process['label_name']));
            $isDfillRelation = issetParam($process['is_dfill_relation']);

            if ($typeCode == 'create') {

                $className = 'btn btn-success btn-circle btn-sm';
                $buttonName = '<i class="far fa-plus"></i> '.$processName;
                $mapId = issetParam($process['map_id']);

                if ($isFillRelation) {
                    $opt = ', {fillSelectedRow: true, mode: \'create\'}';
                } elseif ($isDfillRelation) {
                    $opt = ', {fillDynamicSelectedRow: true, mode: \'create\'}';
                }

                if ($isNormalRelation) {
                    $onClick = "mvNormalRelationRender(this, '$kpiTypeId', '".$this->indicatorId."', {methodIndicatorId: $crudIndicatorId, structureIndicatorId: $srcIndicatorId});";
                } else {
                    $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '".$srcIndicatorId."', false$opt, undefined, 'mvWidgetFileViewCreateCallback');";
                }

            } elseif ($typeCode == 'update') {

                $className = 'btn btn-warning btn-circle btn-sm';
                $buttonName = '<i class="far fa-edit"></i> '.$processName;

                if ($isFillRelation) {
                    $opt = ', {fillSelectedRow: true, mode: \'update\'}';
                } elseif ($isDfillRelation) {
                    $opt = ', {fillDynamicSelectedRow: true, mode: \'update\'}';
                }

                if ($isNormalRelation) {
                    $onClick = "mvNormalRelationRender(this, '$kpiTypeId', '".$this->indicatorId."', {methodIndicatorId: $crudIndicatorId, structureIndicatorId: $srcIndicatorId, mode: 'update'});";
                } else {
                    if (issetParam($process['widget_code']) !== '') {
                        $onClick = "mvWidgetRelationRender(this, '$kpiTypeId', '".$this->indicatorId."', {methodIndicatorId: $crudIndicatorId, structureIndicatorId: $srcIndicatorId, mode: 'update', widgetCode: '". $process['widget_code'] ."'});";
                    } else {
                        $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '$srcIndicatorId', true$opt);";
                    }
                }

            } elseif ($typeCode == 'read') {

                $className = 'btn purple btn-circle btn-sm';
                $buttonName = '<i class="far fa-eye"></i> '.$processName;

                if ($isFillRelation) {
                    $opt = ', {fillSelectedRow: true, mode: \'view\'}';
                } elseif ($isDfillRelation) {
                    $opt = ', {fillDynamicSelectedRow: true, mode: \'view\'}';
                } else {
                    $opt = ', {mode: \'view\'}';
                }

                $onClick = "manageKpiIndicatorValue(this, '$kpiTypeId', '$srcIndicatorId', true$opt);";

            } elseif ($typeCode == 'delete') {

                $className = 'btn btn-danger btn-circle btn-sm';
                $buttonName = '<i class="far fa-trash"></i> '.$processName;
                $onClick = "removeKpiIndicatorValue(this, '$srcIndicatorId', 'mvWidgetFileViewDeleteCallback');";
                $deleteClickAction = $onClick;
                
                $deleteCrudId = $process['crud_indicator_id'];

            } elseif ($typeCode == 'excel') {

                $className = 'btn green btn-circle btn-sm';
                $buttonName = '<i class="far fa-file-excel"></i> '.$processName;
                $onClick = "excelImportKpiIndicatorValue(this, '$srcIndicatorId');";
            }
        }

        echo html_tag('a', 
            array( 
                'href' => 'javascript:;', 
                'class' => $className, 
                'data-qtip-title' => $description, 
                'data-qtip-pos' => 'top', 
                'onclick' => $onClick, 
                'data-actiontype' => $typeCode, 
                'data-main-indicatorid' => $this->indicatorId, 
                'data-structure-indicatorid' => $this->indicatorId, 
                'data-crud-indicatorid' => $crudIndicatorId,
                'data-mapid' => issetParam($process['map_id'])
            ), 
            $buttonName, true
        );
    } ?>
    </div>        
</div>        
        
<div class="mv_card_with_employeelist_widget_main">
    <?php                                            
    $dataResult = $this->response['rows'];
    ?>
    <?php
    foreach ($dataResult as $row) {
        $rowJson = htmlentities(json_encode($row), ENT_QUOTES, 'UTF-8');
    ?>
        <a href="javascript:;" style="width: 170px;margin: 15px;" class="mv_card_with_employeelist_widget no-dataview" data-rowdata="<?php echo $rowJson; ?>">
            <div class="card" style="border: none;box-shadow: none;padding: 0;background: transparent;">
                <div class="card-body">
                    <div class="card-img-actions mb-2 mt-2 pl-2">
                        <?php if (file_exists(issetParam($row[$this->relationViewConfig['position-1']]))) { ?>
                            <img class="directory-img" style="height: 150px;width: 150px;border-radius: 150px;border: 3px solid #33a9ca;background: #fff;" src="<?php echo issetParam($row[$this->relationViewConfig['position-1']]) ?>"/>
                        <?php } else { ?>
                            <img class="directory-img" style="height: 150px;width: 150px;border-radius: 150px;border: 3px solid #33a9ca;background: #fff;" src="assets/core/global/img/noimage.png"/>
                        <?php } ?>
                    </div>
                    <h5>
                        <?php echo issetParam($row[$this->relationViewConfig['position-2']]) ?>
                    </h5>
                    <h5 style="color:#01a8ff">
                        <?php echo issetParam($row[$this->relationViewConfig['position-3']]) ?>
                    </h5>
                </div>
            </div>
        </a>
    <?php
    } 
    ?>
</div>

<script type="text/javascript">
$('.mv_card_with_employeelist_widget').click(function() {
    var $this = $(this);
    if ($this.hasClass('active')) {
        $this.removeClass('active');
        return;
    }
    $this.closest('.mv_card_with_employeelist_widget_main').find('.active').removeClass('active');
    $this.addClass('active');
}); 
function mvWidgetFileViewCreateCallback() {
    $('div[data-menu-id="164723019841110"]').remove();
    $('a[data-menu-id="164723019841110"]').trigger('click');
}    
function mvWidgetFileViewDeleteCallback(elem) {
    mvWidgetFileViewCreateCallback();
}
</script>