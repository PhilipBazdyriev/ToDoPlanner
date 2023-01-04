class UI {

    constructor() {

    }

    showDimmer(icon, message) {
        $('.dimmer .header i.icon').removeAttr('class').addClass('icon').addClass(icon)
        $('.dimmer .header .text').text(message)
        $('.dimmer').dimmer('show')
    }

}