;(() => {
    const currentIds = (() => {
        const uuidRegExp = /\/([0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}|[0-9a-zA-Z]{22})/g
        const currentIds = []
        let match;
        while (match = uuidRegExp.exec(window.location.href)) {
            currentIds.push(match[1])
        }
        return currentIds
    })()

    const clickables = document.getElementsByClassName('clickable')
    for (const clickable of clickables) {
        clickable.addEventListener('click', (e) => {
            e.preventDefault()
            window.location = clickable.dataset.href
        })
    }

    const confirmables = document.getElementsByClassName('confirmable')
    for (const confirmable of confirmables) {
        confirmable.addEventListener('click', (e) => {
            if (!confirm('¿Estas seguro?')) e.preventDefault()
        })
    }

    const autoSubmits = document.getElementsByClassName('auto-submit')
    for (const autoSubmit of autoSubmits) {
        const eventType = autoSubmit.tagName === 'SELECT' ? 'change' : 'input'
        const form = autoSubmit.form
        let timeout
        autoSubmit.addEventListener(eventType, () => {
            clearTimeout(timeout)
            timeout = setTimeout(() => form.submit(), 500)
        })
    }

    const messagesContainer = document.getElementById('flash-message')
    messagesContainer.addEventListener('click', () => {
        const messages = messagesContainer.getElementsByClassName('message')
        for (const message of messages) message.remove()
        messagesContainer.classList.add('hidden')
    });

    const loading = document.getElementById('loading')
    const showLoading = (show = true) => loading.hidden = !show

    const apiCallers = document.getElementsByClassName('api-caller')
    for (const apiCaller of apiCallers) {
        if (apiCaller.nodeName === 'INPUT') {
            let timeout
            apiCaller.addEventListener('input', () => {
                clearTimeout(timeout)
                timeout = setTimeout(() => handleResponse(apiCaller), 500)
            })
        } else if (apiCaller.nodeName === 'BUTTON') {
            apiCaller.addEventListener('click', () => handleResponse(apiCaller))
        }
    }

    const handleResponse = async (element) => {
        showLoading()
        const body = new FormData
        body.set(element.name, element.value)
        const response = await fetch(element.dataset.href, {method: 'post', body: body})
        const data = await response.json()
        if (response.status === 200) {
            actions[element.dataset.callback](data)
        } else {
            showError(data.message || 'Ocurrió un error');
        }
        showLoading(false)
    }

    const showError = (message) => {
        const error = document.createElement('div')
        error.className = 'message alert alert-danger m-2'
        error.innerHTML = `<p class="m-0">${message}</p>`
        messagesContainer.appendChild(error)
        messagesContainer.classList.remove('hidden')
    }

    const createListElement = (href, text, dataset, name, value, icon, classes = '') => {
        const element = document.createElement('li')
        const link = document.createElement('a')
        const button = document.createElement('button')
        link.href = href
        link.innerText = text
        button.className = `api-caller btn btn-sm ${classes}`
        button.dataset.href = dataset.href
        button.dataset.callback = dataset.callback
        button.name = name
        button.value = value
        button.innerHTML = `<i class="fa fa-${icon}"></i>`
        button.addEventListener('click', () => handleResponse(button))
        element.appendChild(link)
        element.appendChild(button)
        return element
    }

    const actions = {
        showCandidates: (candidates) => {
            const list = document.getElementById('family-candidates')
            list.innerHTML = ''
            for (const candidate of candidates) {
                list.appendChild(createListElement(
                    `/customer/${candidate.id}`,
                    `${candidate.name} ${candidate.surname}`,
                    { href: `/api/customer/${currentIds[0]}/add_familiar`, callback: 'updateFamily' },
                    'familiar_id',
                    candidate.id,
                    'plus'
                ))
            }
        },

        updateFamily: (familiars) => {
            const list = document.getElementById('family-members')
            list.innerHTML = ''
            for (const familiar of familiars) {
                list.appendChild(createListElement(
                    `/customer/${familiar.id}`,
                    `${familiar.name} ${familiar.surname}`,
                    { href: `/api/customer/${currentIds[0]}/remove_familiar`, callback: 'updateFamily' },
                    'familiar_id',
                    familiar.id,
                    'times',
                    'confirmable',
                ))
            }
        },

        updatePeriods: (periods) => {
            const list = document.getElementById('season-periods')
            list.innerHTML = ''
            if (periods.length === 0) {
                document.getElementById('generate-periods').hidden = false
            } else {
                document.getElementById('generate-periods').hidden = true
                for (const period of periods) {
                    list.appendChild(createListElement(
                        `/period/${period.id}`,
                        `${period.name} (${formatDate(period.initDate.date)} - ${formatDate(period.finishDate.date)})`,
                        { href: `/api/period/remove`, callback: 'updatePeriods' },
                        'period_id',
                        period.id,
                        'times',
                        'confirmable',
                    ))
                }
            }
        },

        updateHolidays: (holidays) => {
            const buttons = document.getElementsByClassName('day-btn')
            const formattedHolidays = [];
            for (const holiday of holidays) {
                formattedHolidays.push(formatDate(new Date(holiday * 1000), true))
            }
            for (const button of buttons) {
                if (formattedHolidays.includes(button.value)) {
                    button.dataset.href = button.dataset.href.replace('/add_holiday', '/remove_holiday')
                    button.classList.add('is-holiday')
                } else {
                    button.dataset.href = button.dataset.href.replace('/remove_holiday', '/add_holiday')
                    button.classList.remove('is-holiday')
                }
            }
        }
    }

    const activeElement = document.activeElement
    if (activeElement && activeElement.tagName === 'INPUT') {
        activeElement.selectionStart = activeElement.value.length
    }

    const formatDate = (date, iso = false) => {
        const d = new Date(date)
        if (iso) {
            return d.getFullYear() + '-' + twoDigits(d.getMonth() + 1) + '-' + twoDigits(d.getDate())
        }
        return twoDigits(d.getDate()) + '/' + twoDigits(d.getMonth() + 1) + '/' + d.getFullYear()
    }

    const twoDigits = (number) => ('0' + number).slice(-2)
})()
