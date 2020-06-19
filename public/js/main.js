;(() => {
    let hrefMatch = window.location.href.match(/[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}/)
    const currentId = hrefMatch ? hrefMatch[0] : ''

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

    const messagesContainer = document.getElementById('flash-message')
    messagesContainer.addEventListener('click', () => {
        const messages = messagesContainer.getElementsByClassName('message')
        for (const message of messages) message.remove()
        messagesContainer.classList.add('hidden')
    });

    const loading = document.getElementById('loading')
    const showLoading = (show = true) => loading.hidden = !show

    const apiCallers = document.querySelectorAll('.api-caller')
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
            showError(data.message)
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
                    { href: `/api/customer/${currentId}/add_familiar`, callback: 'updateFamily' },
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
                    { href: `/api/customer/${currentId}/remove_familiar`, callback: 'updateFamily' },
                    'familiar_id',
                    familiar.id,
                    'times',
                    'confirmable',
                ))
            }
        }
    }
})()
