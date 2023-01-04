class RestApi {

    constructor() {
        this.baseUrl = '/api/'
    }

    request(api_method, request_params) {
        request_params.headers = {
            'Content-type': 'application/json'
        }
        console.log("RestApi::request", this.baseUrl + api_method, request_params)
        return fetch(this.baseUrl + api_method, request_params)
        .then(res => res.json())
    }

    get(api_method) {
        return this.request(api_method, {
            method: 'GET'
        })
    }

    post(api_method, data) {
        return this.request(api_method, {
            method: 'POST',
            body: JSON.stringify(data)
        })
    }

    put(api_method, data) {
        return this.request(api_method, {
            method: 'PUT',
            body: JSON.stringify(data)
        })
    }

}