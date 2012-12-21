<script type="text/javascript">
var baseUrl="<?php echo Yii::app()->baseUrl==null?Yii::app()->request->baseUrl:Yii::app()->baseUrl;?>";
var action="try";
</script>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style_lottery.css">

<?php
$this->pageCaption="乐透";
$this->pageDescription="";
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;

$this->widget('bootstrap.widgets.TbBreadcrumbs', array(
		'links'=>array('乐透'),
)); 
?>

<?php $this->widget('bootstrap.widgets.TbLabel', array(
    'type'=>'info', // 'success', 'warning', 'important', 'info' or 'inverse'
    'label'=>'试试手气吧,只有发过祝福的人才会被抽到哦',
)); ?>
<hr style="width:20%">

<?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
		'type'=>'info',
		'size'=>'large',
		'htmlOptions'=>array('id'=>'prizeLevel'),
        'buttons'=>array(
            array('label'=>'一等奖', 'items'=>array(
                array('label'=>'一等奖', 'url'=>'javascript:changePrize(1)'),
                array('label'=>'二等奖', 'url'=>'javascript:changePrize(2)'),
                array('label'=>'三等奖', 'url'=>'javascript:changePrize(3)'),
                // '---',
                // array('label'=>'Separate link', 'url'=>'#'),
            )),
        ),

    )); ?>

<div class="lotteryBtn" id="draw" onclick="Lottery(1);">Start
</div>
<!-- <div class="title" id="title">
</div>	 -->
<hr />


<div class="current" id="current">
<div class="line odd">0000000000</div>
</div>
<div class="historyTitle">
History
<hr />
<div class="history" id="history">
</div>
</div>


<script type="text/javascript" src="
<?php 
if (YII_DEBUG)
		echo Yii::app()->request->baseUrl."/js/draw.js";
	else
		echo Yii::app()->request->baseUrl."/js/draw.min.js";
	?>
	"></script>