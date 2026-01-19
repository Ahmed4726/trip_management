import './bootstrap';
import { Calendar } from '@fullcalendar/core'
import resourceTimelinePlugin from '@fullcalendar/resource-timeline'
import multiMonthPlugin from '@fullcalendar/multimonth'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'
import $ from 'jquery';
import 'select2';
import 'select2/dist/css/select2.min.css';


import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

window.initBoatCalendar = function (el, options) {
    const calendar = new Calendar(el, {
        plugins: [
            resourceTimelinePlugin,
            multiMonthPlugin,
            listPlugin,
            interactionPlugin
        ],
        ...options
    })

    calendar.render()
    return calendar
}


window.initFleetCalendar = function (el, options) {
    const calendar = new Calendar(el, {
        plugins: [
            resourceTimelinePlugin,
            interactionPlugin,
            listPlugin
        ],
        initialView: 'resourceTimelineMonth',
        resourceAreaWidth: '260px',
        height: 'auto',
        editable: true,
        selectable: true,
        nowIndicator: true,
        ...options
    })

    calendar.render()
    return calendar
}
$(document).ready(function() {
    $('#guestSelect').select2({
        placeholder: 'Select guests',
        width: '100%',
        closeOnSelect: false
    });
});
