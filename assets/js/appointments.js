require('../css/appointments.scss');

import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'
import bootstrapPlugin from '@fullcalendar/bootstrap';
import deLocale from '@fullcalendar/core/locales/de';
import Choices from "choices.js";
let bsn = require('bootstrap.native');


require('@fullcalendar/core/locales-all');

document.addEventListener('DOMContentLoaded', function () {
    let options = {
        itemSelectText: '',
        shouldSort: false,
        shouldSortItems: false,
        removeItemButton: true
    };

    let studentChoice = new Choices(document.getElementById('student'), options);
    let studyGroupChoice = new Choices(document.getElementById('study_group'), options);
    let teacherChoice = new Choices(document.getElementById('teacher'), options);
    let categoriesChoice = new Choices(document.getElementById('categories'), options);
    let examGradesChoice = new Choices(document.getElementById('exam_grades'), options);

    let suppressFilterChangedEvent = false;
    let appEl = document.getElementById('appointments');
    let lastQuery = { };
    let popovers = { };

    let calendarEl = document.getElementById('calendar');
    let calendar = new Calendar(calendarEl, {
        plugins: [
            dayGridPlugin,
            timeGridPlugin,
            listPlugin,
            interactionPlugin,
            bootstrapPlugin
        ],
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        weekNumbers: true,
        themeSystem: 'bootstrap',
        locales: [ deLocale ],
        locale: 'de',
        eventSources: [
            {
                id: 'json',
                url: appEl.getAttribute('data-url'),
                method: 'GET',
                extraParams: function() {
                    return lastQuery;
                }
            }
        ],
        eventMouseEnter: function(info) {
            let event = info.event;
            let title = event.title;
            let content = event.extendedProps.content;
            let view = event.extendedProps.view;

            let template = '<div class="popover" role="tooltip">' +
                '<div class="arrow"></div>' +
                '<h3 class="popover-header">' + title + ' <span class="badge" style="background: ' + event.backgroundColor + '; color: ' + event.textColor + '">' + event.extendedProps.category + '</span></h3>' +
                '<div class="popover-body">';

            if(content !== null) {
                template += '<p>' + content + '</p>';
            }

            view.forEach(function(viewItem) {
                template += '<p><span class="text-muted">' + viewItem.label + '</span> ' + viewItem.content + '</p>';
            });

            template += '</div></div>';

            let popover = new bsn.Popover(info.el, {
                placement: 'right',
                template: template,
                trigger: 'focus',
                dismissible: true,
                animation: 'none',
                delay: 1 // 0 is not sufficient enough, probably bsn thinks, that delay is not set if set to 0?!
            });
            popover.show();

            let eventId = event.id;
            popovers[eventId] = popover;
        },
        eventMouseLeave: function(info) {
            let eventId = info.event.id;
            if(popovers[eventId] !== null) {
                popovers[eventId].hide();
            }
        },
        loading: function(isLoading) {
            if(isLoading) {
                document.getElementById('loading-indicator')?.classList.remove('hide');
            } else {
                document.getElementById('loading-indicator')?.classList.add('hide');
            }

            [studentChoice, studyGroupChoice, teacherChoice, categoriesChoice, examGradesChoice ].forEach(function(choices) {
                if(isLoading) {
                    choices.disable();
                } else {
                    choices.enable();
                }
            });
        }
    });

    [studentChoice, studyGroupChoice, teacherChoice, categoriesChoice, examGradesChoice ].forEach(function(choices) {
        choices.passedElement.element.addEventListener('change', function(el) {
            if(suppressFilterChangedEvent === false) {
                loadEvents(choices);
            }
        });
    });

    function loadEvents(initiator) {
        let query = { };

        // Ensure that filters are not combined
        suppressFilterChangedEvent = true; // suppress any other changed events

        if(initiator === studentChoice) {
            studyGroupChoice.setChoiceByValue('');
            teacherChoice.setChoiceByValue('');
        } else if(initiator === studyGroupChoice) {
            studentChoice.setChoiceByValue('');
            teacherChoice.setChoiceByValue('');
        } else if(initiator === teacherChoice) {
            studentChoice.setChoiceByValue('');
            studyGroupChoice.setChoiceByValue('');
        }

        suppressFilterChangedEvent = false;

        // Serialize the filter data
        [studentChoice, studyGroupChoice, teacherChoice, categoriesChoice, examGradesChoice ].forEach(function(el) {
            query[el.passedElement.element.name] = el.getValue(true);
        });

        let eventSource = calendar.getEventSourceById('json');
        lastQuery = query;
        eventSource.refetch();
    }

    calendar.render();

    // Insert loading indicator
    let heading = calendarEl.querySelector('.fc-center h2');
    heading.innerHTML = heading.innerHTML + " <i class=\"fas fa-spinner fa-pulse hide\" id=\"loading-indicator\"></i>";

    loadEvents(null);
});