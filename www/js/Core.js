/**
 * Created by iyaro on 13.07.2016.
 */

var EVENTSLIB = ["click", "mouseenter", "mouseleave"];
var EventObject = function(objectDOM, eventsHandlers){
    if(!objectDOM !== undefined){
        for (
            var eventNumber= 0,
                eventsLibLength = EVENTSLIB.length;
            eventNumber < eventsLibLength;
            eventNumber++
        ){
            var eventHandler = eventsHandlers[EVENTSLIB[eventNumber]];
            objectDOM.addEventListener(EVENTSLIB[eventNumber], eventHandler);
        }
        return objectDOM;
    }else{
        console.log("No objectDOM for event handlers: ");
        console.log(eventsHandlers);
    }
};
var Button = function(buttonSettings){
    function construct(){
        if(buttonSettings.selector !== undefined) {
            var objectDOM = document.querySelector(buttonSettings.selector);
            if(objectDOM === undefined || objectDOM === null){
                console.log("Object "+buttonSettings.selector+" not found.");
            }else{
                EventObject(objectDOM, buttonSettings);
            }
        }else{
            console.log("selector is required in");
            console.log(buttonSettings);
        }
    }
    construct();
    return buttonSettings;
};

var ObjectsArray = function(objectsSettings){
    function getDOMarray(objectsSelector){
        var classObject = document.querySelectorAll(objectsSelector),
            objectsDOMarray = [];
        if(classObject !== null){
            objectsDOMarray = Array.prototype.slice.call(classObject);
        }else{
            console.log("Objects "+objectsSelector+" not found in");
            console.log(objectsSettings);
        }
        return objectsDOMarray;
    }
    function construct(){
        if(typeof objectsSettings === "string"){
            return getDOMarray(objectsSettings);
        }else {
            if(objectsSettings.selector !== undefined) {
                var objectsDOMarray = getDOMarray(objectsSettings.selector);
                if(objectsDOMarray.length > 0){
                    for (
                        var objectNumber = 0,
                            objectsDOMarrayLength = objectsDOMarray.length;
                        objectNumber < objectsDOMarrayLength;
                        objectNumber++
                    ) {
                        objectsDOMarray[objectNumber] = EventObject(objectsDOMarray[objectNumber], objectsSettings);
                    }
                    objectsSettings.objectsDOMarray = objectsDOMarray;
                }
            }else{
                console.log("selector is required in");
                console.log(objectsSettings);
            }
            return objectsSettings;
        }
    }
    objectsArrayObject = construct();
    return objectsArrayObject;
};

var Slider = function(sliderSettings){
    sliderSettings.sliderType = sliderSettings.sliderType || "loop";
    sliderSettings.slideSize = sliderSettings.slideSize || 1;

    if(sliderSettings.loader !== undefined){
        window.addEventListener("DOMContentLoaded", function(){
            sliderSettings.loader();
        });
    }

    var slide = ObjectsArray(
        sliderSettings
    );
    if(slide.objectsDOMarray !== undefined){
        slide.itemsCount = slide.objectsDOMarray.length;
    }
    slide.slidesCount = Math.ceil(sliderSettings.itemsCount/sliderSettings.slideSize);
    slide.currentSlide = 0;
    slide.getCurrent = function(){
        var from = this.currentSlide * sliderSettings.slideSize,
            to = from + this.slideSize,
            itemsCount = this.itemsCount;
        if(to > itemsCount){
            to = itemsCount;
            from = to - this.slideSize;
            this.currentSlide--;
        }
        return this.objectsDOMarray.slice(from, to);
    };
    slide.getNext = function(){
        if(this.currentSlide !== sliderSettings.slidesCount){
            this.currentSlide++;
        }else if(sliderSettings.sliderType === "loop"){
            this.currentSlide = 0;
        }
        return this.getCurrent();
    };
    slide.getPrev = function(){
        if(this.currentSlide !== 0){
            this.currentSlide--;
        }else if(sliderSettings.sliderType === "loop"){
            this.currentSlide = sliderSettings.slidesCount;
        }
        return this.getCurrent();
    };
    return slide;
};