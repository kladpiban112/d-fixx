<?php
$fullcalendar_path = "fullcalendar-4.4.2/packages/";
?>
<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users-cog"></i>&nbsp;ข้อมูลการแจ้งซ่อม
        </h3>
        <div class="card-toolbar">

        </div>
    </div>



    <div class="card-body">


        <!DOCTYPE html>
        <html lang='en'>

        <head>
            <meta charset='utf-8' />

            <link href='<?=$fullcalendar_path?>/core/main.css' rel='stylesheet' />
            <link href='<?=$fullcalendar_path?>/daygrid/main.css' rel='stylesheet' />
            <link rel="stylesheet"
                href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css">
            <!--   ส่วนที่เพิ่มเข้ามาใหม่-->
            <link href='<?=$fullcalendar_path?>/timegrid/main.css' rel='stylesheet' />
            <link href='<?=$fullcalendar_path?>/list/main.css' rel='stylesheet' />

            <script src='<?=$fullcalendar_path?>/core/main.js'></script>
            <script src='<?=$fullcalendar_path?>/daygrid/main.js'></script>
            <!--   ส่วนที่เพิ่มเข้ามาใหม่-->
            <script src='<?=$fullcalendar_path?>/core/locales/th.js'></script>
            <script src='<?=$fullcalendar_path?>/timegrid/main.js'></script>
            <script src='<?=$fullcalendar_path?>/interaction/main.js'></script>
            <script src='<?=$fullcalendar_path?>/list/main.js'></script>


            <style type="text/css">
            #calendar {
                width: 1300px;
                margin: auto;
            }
            </style>

        </head>

        <body>

            <div id='calendar'></div>



            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
                integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs=" crossorigin="anonymous"></script>
            <script type="text/javascript">
            $(function() {
                // กำหนด element ที่จะแสดงปฏิทิน
                var calendarEl = $("#calendar")[0];

                // กำหนดการตั้งค่า
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    plugins: ['interaction', 'dayGrid', 'timeGrid', 'list'], // plugin ที่เราจะใช้งาน
                    defaultView: 'dayGridMonth', // ค้าเริ่มร้นเมื่อโหลดแสดงปฏิทิน
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                    },
                    eventLimit: true, // allow "more" link when too many events
                    locale: 'th', // กำหนดให้แสดงภาษาไทย
                    firstDay: 0, // กำหนดวันแรกในปฏิทินเป็นวันอาทิตย์ 0 เป็นวันจันทร์ 1
                    showNonCurrentDates: false, // แสดงที่ของเดือนอื่นหรือไม่
                    eventTimeFormat: { // รูปแบบการแสดงของเวลา เช่น '14:30' 
                        hour: '2-digit',
                        minute: '2-digit',
                        meridiem: false
                    }
                });

                // แสดงปฏิทิน 
                calendar.render();

            });
            </script>


        </body>

        </html>

    </div>


    <div class="card-footer">
        <div class="row">

        </div>
    </div>


</div>
<!--end::Card-->

<script>

</script>