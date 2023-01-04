class TasksPage {

    constructor() {
        this.tasks = []
    }

    onReady() {
        this.bindListeners()
        this.loadTasks()
    }

    bindListeners() {
        $('.close-modal').click(() => {
            $('.modal').modal('hide')
        })
        $('.progress').progress()
        $('select.dropdown').dropdown()
        $('#modal-task-card .change-status-button').click(function(event) {
            let taskId = $(event.currentTarget).data('task-id')
            let newStatus = $(event.currentTarget).data('status')
            tasksPage.onChangeTaskStatusClick(taskId, newStatus)
        })
    }

    loadTasks() {
        $('#tasks-placeholder').show()
        $('#tasks-list').hide()
        this.refreshTasks()
    }

    onTasksLoad(response) {
        if (response && response.status && response.status == 'ok') {
            this.tasks = []

            $('#tasks-placeholder').hide()

            this.resetGroupsContainer()

            for (let groupKey in response.group_collection) {
                let groupData = response.group_collection[groupKey]

                this.createGroupContainer(groupData)

                for (let taskKey in groupData.tasks) {
                    let taskData = groupData.tasks[taskKey]
                    this.createTaskInGroup(groupData.type, taskData)
                    this.tasks.push(taskData)
                }
            }

            this.bindTasksButtons()
            this.bindCreatingButtons()

            $('.progress').progress()
        }

    }

    refreshTasks() {
        restApi.get('tasks')
            .then(response => {
                this.onTasksLoad(response)
            })
    }

    resetGroupsContainer() {
        $('#tasks-list').html('').show()
    }

    createGroupContainer(groupData) {
        let groupTemplate = new HtmlTemplate('#tpl-tasks-group')
        let doneTaskCount = 0
        for (let key in groupData.tasks) {
            let taskData = groupData.tasks[key]
            if (taskData.status === 'done') {
                doneTaskCount++
            }
        }
        let groupHtml = groupTemplate.render({
            id: groupData.type,
            title: groupData.title,
            progress_value: doneTaskCount,
            progress_total: groupData.tasks.length,
        })
        $('#tasks-list').append(groupHtml)
    }

    createTaskInGroup(groupType, taskData) {
        let groupItemTemplate = new HtmlTemplate('#tpl-tasks-group-item')
        let taskHtml = groupItemTemplate.render({
            id: taskData.id,
            status: taskData.status,
            title: taskData.title,
            description: taskData.description,
        })

        $('#group-' + groupType + ' .list').append(taskHtml)

        let iconSelector = '[data-task-id=' + taskData.id + '] .icon'
        let iconClass = this.statusToIconClass(taskData.status)
        $(iconSelector).addClass(iconClass)
    }

    statusToIconClass(status) {
        if (status === 'done') {
            return 'check'
        } else if (status === 'failed') {
            return 'close'
        }
        return 'question'
    }

    openModalNewTask(groupType) {
        $('#modal-task-create').modal('show')

        let currentDate = new Date()

        if (groupType == 'today') {
            $('#date-type').dropdown('set exactly', 'day')
            $('#day').calendar('set date', currentDate)
        }
        else if (groupType == 'this_week') {
            $('#date-type').dropdown('set exactly', 'day_range')

            let prevMonday = new Date()
            prevMonday.setDate(currentDate.getDate() - (currentDate.getDay() + 6) % 7)
            $('#rangestart').calendar('set date', prevMonday)

            let nextSunday = new Date()
            prevMonday.setDate(prevMonday.getDate() + 6)
            $('#rangeend').calendar('set date', nextSunday)
        }
        else if (groupType == 'this_month') {
            $('#date-type').dropdown('set exactly', 'month')
            $('#month').calendar('set date', currentDate)
        }
    }

    bindTasksButtons() {
        $('.task').click(function(event) {
            let taskId = $(event.currentTarget).data('task-id')
            let task = tasksPage.findTask(taskId)
            $('#modal-task-card #title').text(task.title)
            $('#modal-task-card #description').text(task.description)
            $('#modal-task-card').modal('show')
            $('#modal-task-card .change-status-button').data('task-id', task.id)

            if (task.status == 'pending') {
                $('#modal-task-card .change-status-button').show()
            } else {
                $('#modal-task-card .change-status-button').hide()
            }
        })
    }

    bindCreatingButtons() {
        $('.new-task-button').click((event) => {
            let groupType = $(event.target).attr('data-group-id')
            this.openModalNewTask(groupType)
        })
    }

    findTask(id) {
        for (let key in this.tasks) {
            let task = this.tasks[key]
            if (task.id == id) {
                return task
            }
        }
    }

    onChangeTaskStatusClick(taskId, newStatus) {
        $('.change-status-button').addClass('disabled')
        $('.change-status-button[data-status="' + newStatus + '"]').addClass('loading')
        this.changeTaskStatus(taskId, newStatus)
    }

    changeTaskStatus(taskId, newStatus) {
        restApi.put('tasks/' + taskId, {status: newStatus})
            .then(response => {
                $('.change-status-button').removeClass('disabled').removeClass('loading')
                if (response.status === 'ok') {
                    $('#modal-task-card').modal('hide')
                    tasksPage.refreshTasks()
                }
            })
    }

}