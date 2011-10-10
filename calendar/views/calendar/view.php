<?php
/**
 * Displays information for a particular {@link ACalendar} model
 * @var ACalendar $model The ACalendar model to show
 */
$this->breadcrumbs=array(
	'Acalendars'=>array('index'),
	$model->name,
);
Yii::app()->clientScript->registerCoreScript("jquery.ui");
$this->menu=array(
	array('label'=>'List ACalendar', 'url'=>array('index')),
	array('label'=>'Create ACalendar', 'url'=>array('create')),
	array('label'=>'Update ACalendar', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ACalendar', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ACalendar', 'url'=>array('admin')),
);
?>
<article class='width_2'>
<h1><?php echo CHtml::encode($model->name); ?></h1>
<p><?php echo nl2br(CHtml::encode($model->description)); ?></p>
<?php
$this->widget("packages.qtip.AQtipWidget");
$createUrl = CJavaScript::quote($this->createUrl("/calendars/event/create",array("calendarId" => $model->id)));
$dayClick = <<<EOD
js:function(date, allDay, jsEvent, view) {
	jQuery("#calendar").fullCalendar("gotoDate",date);
	if (view.name == "month") {
		jQuery("#calendar").fullCalendar("changeView","agendaDay");
	}
}
EOD;

$select = <<<EOD
js:function(start, end, allDay) {
	var calendar = $("#calendar"), event = {}, className = "event-" + (new Date).getTime();
	event.title = "New Event";
	event.start = start;
	event.end = end;
	event.allDay = allDay;
	event.className = className;
	calendar.fullCalendar('renderEvent', event);
	setTimeout(function() {
		$("." + className).qtip({
			overwrite: false,
            content: {
                text: "<span class='loading'>Loading...</span>",
                ajax: {
                	url: "{$createUrl}",
                	data: {
                		title: event.title,
                		start: event.start.getTime(),
                		end: (event.end === null ? undefined : event.end.getTime()),
                		allDay: event.allDay,
                		className: event.className
                	},
               	}
            },
            events: {
            	hide: function(e, api) {
            		console.log(e);
            		if ($("#ACalendarEvent_title").val() == "") {
            			calendar.fullCalendar('removeEvents',[event._id]);
            		}
            		$(this).remove();
            	}
            },
            position: {
                my: 'bottom center',
                at: 'top center',
                viewport: jQuery("#calendar"),
                adjust: {
                	method: "flip"
                }
            },
            style: {
                classes: 'ui-tooltip-light ui-tooltip-shadow',
                tip: {
					border: 2,
					width: 22,
					height: 16
				}
            },
            show: {
            	event: 'click',
                solo: true,
                ready: true,
                modal: {
                	on: true
                }
            },
            hide: {
      			event: 'unfocus'
   			}
		});
		}, 50);
}
EOD;

$pageScripts = <<<JS

JS;
Yii::app()->clientScript->registerScript("calendarScript",$pageScripts);
$this->widget("packages.calendar.components.ACalendarWidget",
				array(
					'id' => 'calendar',
					'model' => $model,
					'options' => array(
						'selectable' => true,
						'selectHelper' => true,
						'select' => $select,
						'editable' => true,
						'dayClick' => $dayClick,
						'header' => array(
								'left' => 'prev,next today',
								'center' => 'title',
								'right' => 'month,agendaWeek,agendaDay',
							),
						),
				));
?>
</article>