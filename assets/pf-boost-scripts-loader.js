// !!! Write about passive event if is not pass specific false will be true for touch events and scroll. If is supported from browser.

// polyfills
(function () {
    if (typeof window.CustomEvent === 'function') return false;

    function CustomEvent(event, params) {
        params = params || {
            bubbles: false,
            cancelable: false,
            detail: undefined,
        };
        var evt = document.createEvent('CustomEvent');
        evt.initCustomEvent(
            event,
            params.bubbles,
            params.cancelable,
            params.detail
        );
        return evt;
    }

    CustomEvent.prototype = window.Event.prototype;

    window.CustomEvent = CustomEvent;
})();

// passive support check.
var passiveIfSupported = false;

try {
    window.addEventListener(
        'test',
        null,
        Object.defineProperty({}, 'passive', {
            get: function () {
                passiveIfSupported = { passive: true };
            },
        })
    );
} catch (err) {}

// rewrite default addEventListener
try {

    function rewriteAddEventListener (event,handler, options) { 
        var passiveEvents = ['touchstart','touchmove'];
        var eventObject = {
            type : event,
            listener : handler
        }

        if(passiveIfSupported){
            // check if is only boolean
            options = typeof options === "boolean" ? {capture: options} : options; 

            if(typeof options.passive === 'undefined' && passiveEvents.indexOf(event) !== -1 ) {
                options.passive = true;
            }

            eventObject.passive = options.passive;
            eventObject.useCapture = options.capture;
        } else {
            eventObject.useCapture  = typeof options !== "boolean" && options.capture ? options.capture : false;
        }
        
        this.attachedEvents.push(eventObject);
        this.originalAddEventListener(event,handler, options);
    }
    
    
    HTMLElement.prototype.originalAddEventListener = HTMLElement.prototype.addEventListener;
    HTMLElement.prototype.addEventListener = rewriteAddEventListener;
    window.originalAddEventListener = window.addEventListener;
    window.addEventListener = rewriteAddEventListener;
} catch (e){
    console.error('holy shit we have problem')
}

(function () {
    var userEvents = ['click', 'mouseover', 'mousemove', 'touch', 'touchstart'];
    
    function registerEventListeners() {
        
        var userInteraction = pageYOffset ? true : false;

        

    }
    
    function scriptLoader() {

    }

    function stylesLoader(callback) {

    }

    function completeScriptLoad() {
        // register to load event to retrigger the event 
        

        var dclEvent = document.createEvent('DOMContentLoaded');
        var loadEvent = new Event('load');
        window.dispatchEvent('load');
    }
})();
