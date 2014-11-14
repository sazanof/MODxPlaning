$(document).ready(function() {
    var event_cal_id = $('#cal_id');
    var event_cat_id = $('#cat_id');
    var event_cat = $('#event_cat');
    var event_start = $('#event_start');
    var event_end = $('#event_end');
    var event_type = $('#event_type');
    var event_text = $('#event_text');
    var event_color = $('#event_color');
    var event_noty = $('#event_noty');
    var alarm_start = $('#alarm_start');
    var ch_calendar = $('#ch_calendar');

    var calendar = $('#calendar');
    var form = $('#dialog-form');
    var event_id = $('#event_id');
    var format = "dd.mm.yy";
    var result = $("#result");

    var cal = $('.addCal');
    var cal_res = $('.addCal span');
    var calendar_id = $('#calendar_id');
    var cal_def = $('#cal_def');
    var cal_title = $('#cal_title');
    var cal_description = $('#cal_description');
    function alarmCheck(){
        if (event_noty.prop('checked')) {
            $(".add_alarm").css("display","inline-block");
        }
        else
        {
            $(".add_alarm").css("display","none");
            alarm_start.val("");
        }
    }
    event_noty.click(function(){
        alarmCheck();
    });


    event_color.simplecolorpicker();
    event_start.datetimepicker({
        dateFormat :format,
        stepMinute:5
    });
    alarm_start.datetimepicker({
        dateFormat :format,
        stepMinute:5
    });
    event_end.datetimepicker({
        dateFormat :format,
        stepMinute:5
    });
    $('#add_event_button').click(function(){
        formOpen('add');
    });
    /** функция очистки формы */
    function emptyForm() {
        event_start.val("");
        event_end.val("");
        event_type.val("");
        event_id.val("");
        event_text.val("");
        event_color.val("");
        event_noty.removeAttr("checked");
        event_cat.removeAttr("selected");
        cal.val("");
        calendar_id.val("");
        cal_title.val("");
        cal_description.val("");
    }
    /* режимы открытия формы */
    function formOpen(mode) {
        if(mode == 'add') {
            /* скрываем кнопки Удалить, Изменить и отображаем Добавить*/
            $('#add').show();
            $('#edit').hide();
            $("#delete").button("option", "disabled", true);
            $(".form_hide").hide();
        }
        else if(mode == 'edit') {
            /* скрываем кнопку Добавить, отображаем Изменить и Удалить*/
            $('#edit').show();
            $('#add').hide();
            $("#delete").button("option", "disabled", false);
            $(".form_hide").show();
        }
        form.dialog("open");
    }
    // грузин FullCalendar
    calendar.fullCalendar({
        //theme: true,
        lang:'ru',//сделать мультиязычность
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        allDaySlot:false,
        scrollTime:'08:00:00',
        editable: true,
        selectable: true,
        eventLimit: true, // allow "more" link when too many events
        height:770,
        eventResize: function(calEvent, delta, revertFunc) {
            $.ajax({
                type: "POST",
                url: "/assets/modules/modxplaning/inc/ajax.inc.php",
                data: {
                    event_id: calEvent.id,
                    event_start: moment(calEvent.start).format('DD.MM.YYYY HH:mm'),
                    event_end: moment(calEvent.end).format('DD.MM.YYYY HH:mm'),
                    op: 'delta'
                },
                success: function (id) {
                    calendar.fullCalendar('refetchEvents');
                    result.html(id);
                }
            })
        },
        eventDrop: function(calEvent, delta, revertFunc) {
            $.ajax({
                type: "POST",
                url: "/assets/modules/modxplaning/inc/ajax.inc.php",
                data: {
                    event_id: calEvent.id,
                    event_start: moment(calEvent.start).format('DD.MM.YYYY HH:mm'),
                    event_end: moment(calEvent.end).format('DD.MM.YYYY HH:mm'),
                    op: 'delta'
                },
                success: function (id) {
                    calendar.fullCalendar('refetchEvents');
                    result.html(id);
                }
            })
        },
        //select:  function(start, end, jsEvent, view) {
        //    event_start.val(moment(start).format('DD.MM.YYYY HH:mm'));
        //    event_end.val(moment(end).format('DD.MM.YYYY HH:mm'));
        //    formOpen('add');
        //},
        dayClick: function(date) {
            var newDate = moment(date).format('DD.MM.YYYY HH:mm'); //задаем корректный формат времени а дате
            event_start.val(newDate);
            event_end.val(newDate);
            alarmCheck();
            formOpen('add');
        },
        eventClick: function(calEvent, jsEvent, view) {
            event_cat.val(calEvent.cat_id).attr("selelted");
            if(calEvent.notify==1)
            {
                event_noty.attr("checked","checked");
                $(".add_alarm").css("display","inline-block");
                alarm_start.val(moment(calEvent.alarm_start).format('DD.MM.YYYY HH:mm'));
            }
            else
            {
                event_noty.removeAttr("checked");
                $(".add_alarm").css("display","none");
                alarm_start.val("");
            }
            event_id.val(calEvent.id);
            event_type.val(calEvent.title);
            event_start.val(moment(calEvent.start).format('DD.MM.YYYY HH:mm'));
            event_end.val(moment(calEvent.end).format('DD.MM.YYYY HH:mm'));
            event_text.val(calEvent.description);
            event_color.val(calEvent.color);
            event_color.simplecolorpicker('selectColor', calEvent.color);
            formOpen('edit');
        },
        /* источник записей */
        eventSources: [{
            url: '/assets/modules/modxplaning/inc/ajax.inc.php',
            type: 'POST',
            data: {
                cal_id:event_cal_id.val(),
                cat_id:event_cat_id.val(),
                op: 'source'
            },
            error: function() {
                //result.html('Пусто!');
            }
        }]
    });

    $('#change_parent').click(function(){
        $.ajax({
            type: "POST",
            url: "/assets/modules/modxplaning/inc/ajax.inc.php",
            data: {
                event_id: event_id.val(),
                ch_cal: ch_calendar.val(),
                op: 'change_parent'
            },
            success: function(id){
                form.dialog('close');
                emptyForm();
                calendar.fullCalendar('refetchEvents');
                result.html(id);
            }
        });
    });

    /* обработчик формы добавления */
    form.dialog({
        autoOpen: false,
        modal: true,
        width:400,
        close: function( event, ui ) {
            result.html("");
            emptyForm();
        },
        buttons: [{
            id: 'add',
            text: 'Добавить',
            click: function() {
                if (event_noty.prop("checked")) {
                    var noty=1
                }
                else
                {
                    var noty=0
                }
                $.ajax({
                    type: "POST",
                    url: "/assets/modules/modxplaning/inc/ajax.inc.php",
                    data: {
                        cal_id: event_cal_id.val(),
                        cat_id: event_cat.val(),
                        event_start: event_start.val(),
                        event_end: event_end.val(),
                        title: event_type.val(),
                        description: event_text.val(),
                        color: event_color.val(),
                        notify: noty,
                        alarm_start:alarm_start.val(),
                        sticky:0,
                        status:0,
                        op: 'add'
                    },
                    success: function(err){
                        if (err) {
                            result.html(err);
                        } else {
                            calendar.fullCalendar('refetchEvents');
                            form.dialog('close');
                            result.html("");
                            emptyForm();
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
            }
        },
            {   id: 'edit',
                text: 'Изменить',
                click: function() {
                    if (event_noty.prop("checked")) {
                        var noty=1
                    }
                    else
                    {
                        var noty=0
                    }
                    $.ajax({
                        type: "POST",
                        url: "/assets/modules/modxplaning/inc/ajax.inc.php",
                        data: {
                            event_id: event_id.val(),
                            cal_id: event_cal_id.val(),
                            cat_id: event_cat.val(),
                            event_start: event_start.val(),
                            event_end: event_end.val(),
                            title: event_type.val(),
                            description: event_text.val(),
                            color: event_color.val(),
                            notify: noty,
                            alarm_start:alarm_start.val(),
                            sticky:0,
                            status:0,
                            op: 'edit'
                        },
                        success: function(err){
                            if (err) {
                                result.html(err);
                            } else {
                                calendar.fullCalendar('refetchEvents');
                                form.dialog('close');
                                result.html("");
                                emptyForm();
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                    $(this).dialog('close');
                    emptyForm();
                }
            },
            {   id: 'cancel',
                text: 'Отмена',
                click: function() {
                    $(this).dialog('close');
                    emptyForm();
                }
            },
            {   id: 'delete',
                text: 'Удалить',
                click: function() {
                    $.ajax({
                        type: "POST",
                        url: "/assets/modules/modxplaning/inc/ajax.inc.php",
                        data: {
                            event_id: event_id.val(),
                            op: 'delete'
                        },
                        success: function(err){
                            if (err) {
                                result.html(err);
                            } else {
                                calendar.fullCalendar('refetchEvents');
                                form.dialog('close');
                                result.html("");
                                emptyForm();
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                    $(this).dialog('close');
                    emptyForm();
                },
                disabled: true
            }]
    });
    $("#change_cal").click(function(){
        $(".calendarLeft").dialog({
            modal:true,
            title:'Сменить календарь'
        });
        return false;
    });

        $("#add_calendar").click(function(){
            calOpen('add');
        });
        $("#edit_calendar").click(function(){
            calOpen('edit');
            return false;
        });
        function calOpen(mode) {
            if(mode == 'add') {
                /* скрываем кнопки Удалить, Изменить и отображаем Добавить*/
                $('#add_cal').show();
                $('#edit_cal').hide();
                $("#delete_cal").button("option", "disabled", true);
                calendar_id.val('');
                cal_title.val('');
                cal_description.val('');
                cal_res.html('');
                cal_def.removeAttr('checked');
            }
            else if(mode == 'edit') {
                /* скрываем кнопку Добавить, отображаем Изменить и Удалить*/
                $('#edit_cal').show();
                $('#add_cal').hide();
                $("#delete_cal").show();
                calendar_id.val($('#cal_h1_id').val());
                cal_title.val($('#cal_h1_title').val());
                if ($('#cal_h1_def').val()==1)
                    {
                        cal_def.attr("checked","checked");
                    }
                    else
                    {
                        cal_def.removeAttr("checked");
                    }
                cal_description.val($('#cal_h1_description').val());
                cal_res.html('');
            }
            cal.dialog("open");
        }
        cal.dialog({
            modal:true,
            autoOpen:false,
            buttons: [
                {
                    id:'add_cal',
                    text: 'Добавить',
                    click: function() {
                        $.ajax({
                            type: "POST",
                            url: "/assets/modules/modxplaning/inc/ajax.inc.php",
                            data: {
                                calendar_id: calendar_id.val(),
                                cal_title: cal_title.val(),
                                cal_description: cal_description.val(),
                                cal_def: cal_def.val(),
                                op: 'add_calendar'
                            },
                            success: function(err){
                                if (err) {
                                    cal_res.html(err);
                                } else {
                                    cal.dialog('close');
                                    cal_res.html("");
                                    emptyForm();
                                }
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.status);
                                alert(thrownError);
                            }
                        });
                    }
                },
                {
                    id:'delete_cal',
                    text:'Удалить',
                    click: function() {
                        $.ajax({
                            type: "POST",
                            url: "/assets/modules/modxplaning/inc/ajax.inc.php",
                            data: {
                                calendar_id: calendar_id.val(),
                                op: 'delete_calendar'
                            },
                            success: function(err){
                                if (err) {
                                    cal_res.html(err);
                                } else {
                                    cal.dialog('close');
                                    cal_res.html("");
                                    emptyForm();
                                }
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.status);
                                alert(thrownError);
                            }
                        });
                    }
                },
                {
                    id:'edit_cal',
                    text:'Изменить',
                    click: function() {
                        $.ajax({
                            type: "POST",
                            url: "/assets/modules/modxplaning/inc/ajax.inc.php",
                            data: {
                                calendar_id: calendar_id.val(),
                                cal_title: cal_title.val(),
                                cal_description: cal_description.val(),
                                cal_def: cal_def.val(),
                                op: 'edit_calendar'
                            },
                            success: function (id) {
                                cal_res.html(id);
                            }
                        });
                    }
                }

            ]
        });
    });

