class HtmlTemplate {

    constructor(templateSelector) {
        this.templateSelector = templateSelector
    }

    render(variables) {
        let html = $(this.templateSelector).html()
        for (let key in variables) {
            let value = variables[key]
            html = html.replaceAll('[' + key + ']', value)
        }
        return html
    }
}