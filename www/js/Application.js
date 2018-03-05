/**
 * Created by iyaro on 02.06.2016.
 */
var APPLICATION = {
    location: null,
    soundOn: true,
    pageModel: null,
    ajax: false,
    load: function(){
        //this.setLocation();
        //this.addPopStateEvent();
        this.clouds.load();
        this.leafs.load();
        this.blade.load();
        this.drop.load();
        this.ladybug.load();
        this.soundSwitch.load();
        this.page.load(location.href);
    },
    setLocation: function(){
        APPLICATION.location = window.history.location || window.location;
    },
    addPopStateEvent: function(){
        window.addEventListener(
            "popstate",
            function(event) {
                APPLICATION.page.load(APPLICATION.location.href);
                event.preventDefault();
            },
            false
        );
    },
    getCooki: function(cookie_name){
        var results = document.cookie.match ( '(^|;) ?' + cookie_name + '=([^;]*)(;|$)' );
        if ( results ){
            return (results[2]);
        }else {
            return null;
        }
    },
    getAjaxObject: function (){
        if(typeof XMLHttpRequest === 'undefined'){
            XMLHttpRequest = function() {
                try {
                    return new window.ActiveXObject( "Microsoft.XMLHTTP" );
                } catch(e) {}
            };
        }
        return new XMLHttpRequest();
    },
    getRandomBetween: function (min, max) {
        return Math.floor(Math.random()*(max-min+1)+min);
    },
    createRequest: function(){
        var Request = false;

        if (window.XMLHttpRequest)
        {
            //Gecko-совместимые браузеры, Safari, Konqueror
            Request = new XMLHttpRequest();
        }
        else if (window.ActiveXObject)
        {
            //Internet explorer
            try
            {
                Request = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (CatchException)
            {
                Request = new ActiveXObject("Msxml2.XMLHTTP");
            }
        }

        if (!Request)
        {
            alert("Невозможно создать XMLHttpRequest");
        }

        return Request;
    },
    sendRequest: function (r_method, r_path, r_args, r_handler){
        //Создаём запрос
        var Request = this.createRequest();

        //Проверяем существование запроса еще раз
        if (!Request)
        {
            return;
        }

        //Назначаем пользовательский обработчик
        Request.onreadystatechange = function()
        {
            //Если обмен данными завершен
            if (Request.readyState == 4)
            {
                //Передаем управление обработчику пользователя
                r_handler(eval("(" + Request.responseText + ")"));
            }
        };

        //Проверяем, если требуется сделать GET-запрос
        if (r_method.toLowerCase() == "get" && r_args.length > 0)
            r_path += "?" + r_args;

        //Инициализируем соединение
        Request.open(r_method, r_path, true);

        if (r_method.toLowerCase() == "post")
        {
            //Если это POST-запрос

            //Устанавливаем заголовок
            Request.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=utf-8");
            //Посылаем запрос
            Request.send(r_args);
        }
        else
        {
            //Если это GET-запрос

            //Посылаем нуль-запрос
            Request.send(null);
        }
    },
    page: {
        model: null,
        requestedPages: null,
        status: "close",
        loadPageModel: function(pageAdress){
            APPLICATION.sendRequest("get", pageAdress, "ajax="+APPLICATION.ajax, this.renderPageModel);
        },
        renderPageModel: function(pageModel){
            APPLICATION.page.model = pageModel;
            APPLICATION.page.loadMetaData();
        },
        load: function (pageAdress, type){
            //conl
            window.document.body.style.width = window.innerWidth+"px";
            if(type === "ajax"){
                //console.log(pageAdress);
                //window.location.href = pageAdress+"?ajax=true";
                this.loadPageModel(pageAdress);
            }else{
                this.loadMetaData();
                if(this.requestedPages !== null){
                    if(this.requestedPages.length >= 2){
                        APPLICATION.page.open();
                    }
                    if(this.requestedPages.length === 3){
                        APPLICATION.fotoMini.open();
                    }
                }
            }

            /*history.pushState(null, null, $page_adress);
            var ajaxObject = APPLICATION.getAjaxObject();
            ajaxObject.open('GET', $page_adress+="?ajax=true", true);
            ajaxObject.onreadystatechange = function() {
                if(ajaxObject.readyState == 4 && ajaxObject.status == 200) {
                    APPLICATION.page.model = JSON.parse(ajaxObject.responseText);*/
            /*        APPLICATION.page.loadMetaData();
            if(this.requestedPages.length >= 2){
                APPLICATION.page.open();
            }
            if(this.requestedPages.length === 3){
                APPLICATION.fotoMini.open();
            }*/
                /*}
            };
            ajaxObject.send(null);*/
        },
        loadMetaData: function(){
            if(this.model !== null){
                console.log(this.model);
                if(this.model.page.title !== undefined){
                    document.title = this.model.page.title;
                }
                var metaTags = document.getElementsByTagName('meta'),
                    metaTagsCount = metaTags.length;

                for(var counter = 0; counter < metaTagsCount; counter++){
                    var metaName = metaTags[counter].getAttribute('name');
                    if(this.model.page[metaName] !== undefined){
                        metaTags[counter].content = this.model.page[metaName];
                    }
                }
            }else{
                var pathName = document.location.pathname;
                var pagePath = pathName.split(".")["0"];
                var requestedPages = pagePath.split("/");
                for(var pageNumber = 0; pageNumber < requestedPages.length; pageNumber++){
                    if(pageNumber === 0){
                        requestedPages[pageNumber] = "index";
                    }else{
                        if(requestedPages[pageNumber] === ""){
                            requestedPages.splice(pageNumber, 1);
                        }
                    }
                }
                this.requestedPages = requestedPages;
            }
        },
        open: function(){
            //console.log(this.model.page.type);
            //if(this.model.page.type === "leaf"){
                var pageContent = document.getElementById("page_content"),
                    page = document.getElementById("page"),
                    openPageTimeLine = new TimelineMax({});
                this.pauseMainAnimation();
                openPageTimeLine
                    .to(page, 0, {
                        display: "block"
                    })
                    .to(page, 0.7, {
                        opacity: 1
                    });
                /*TweenMax.fromTo(pageContent, 0.7, {
                    y: "800px",
                    opacity: 0
                },{
                    y: "0px",
                    opacity: 1
                });*/
                APPLICATION.soundSwitch.makeGrassSoundLow();
                APPLICATION.ladybug.makeVolumesLow();
                this.status = "open";
            /*}else{
                this.close();
                this.playMainAnimation();
            }*/
        },
        pauseMainAnimation: function(){
            APPLICATION.leafs.pause();
            APPLICATION.ladybug.pause();
            APPLICATION.clouds.pause();
            APPLICATION.blade.pause();
            APPLICATION.drop.pause();
        },
        playMainAnimation: function(){
            APPLICATION.leafs.play();
            APPLICATION.ladybug.play();
            APPLICATION.clouds.play();
            APPLICATION.blade.play();
            APPLICATION.drop.play();
        },
        close: function(){
            var pageContent = document.getElementById("page_content"),
                page = document.getElementById("page"),
                closePageTimeLine = new TimelineMax({});
            closePageTimeLine
                .to(page, 1, {
                    opacity: 0
                })
                .to(page, 0, {
                    display: "none"
                });
            TweenMax.to(pageContent, 0.5,{
                y: "+=200px",
                opacity: 0
            });
            this.status = "close";
        }
},
    clouds: new ObjectsArray({
        selector: ".clouds",
        load: function(){
            var i;
            for (i=0; i<this.objectsDOMarray.length; i++){
                this.objectsDOMarray[i].tween = TweenMax.to(this.objectsDOMarray[i], APPLICATION.getRandomBetween(10, 15), {
                    ease:  Power2.easeInOut,
                    yoyo: true,
                    y: APPLICATION.getRandomBetween(20, 30),
                    repeat: -1
                });
            }
        },
        pause: function(){
            for(cloudNumber = 0; cloudNumber<this.objectsDOMarray.length; cloudNumber++){
                this.objectsDOMarray[cloudNumber].tween.pause();
            }
        },
        play: function(){
            for(cloudNumber = 0; cloudNumber<this.objectsDOMarray.length; cloudNumber++){
                this.objectsDOMarray[cloudNumber].tween.play();
            }
        }
    }),
    leafs: new ObjectsArray({
        selector: ".leaf_l",
        leafSound: null,
        playSound: function(){
            if(APPLICATION.soundOn === true){
                if(this.leafSound === null){
                    this.leafSound = document.getElementById("leafSound");
                }
                leafSound.play();
            }
        },
        click: function(event){
            //event.preventDefault();
            APPLICATION.leafs.playSound();
            APPLICATION.page.load(this.getElementsByTagName("a")[0].href);
        },
        mouseenter: function(){
            this.tween.pause();
            APPLICATION.leafs.playSound();
            TweenMax.to(this, 0.3, {
                ease:  Power2.easeInOut,
                scaleX: "+=0.2",
                scaleY: "+=0.2"
            });
        },
        mouseleave: function(){
            this.tween.play();
            APPLICATION.leafs.playSound();
            TweenMax.to(this, 0.3, {
                ease:  Power2.easeInOut,
                scaleX: "1",
                scaleY: "1"
            });
        },
        load: function(){
            var i;
            for (i=0; i<this.objectsDOMarray.length; i++){
                this.objectsDOMarray[i].tween = TweenMax.to(this.objectsDOMarray[i], APPLICATION.getRandomBetween(5, 10), {
                    ease:  Power2.easeInOut,
                    yoyo: true,
                    rotationZ: APPLICATION.getRandomBetween(10, 15),
                    transformOrigin:"left top",
                    repeat: -1
                });
            }
        },
        pause: function(){
            for (leafNumber = 0; leafNumber<this.objectsDOMarray.length; leafNumber++){
                this.objectsDOMarray[leafNumber].tween.pause();
            }
        },
        play: function(){
            for (leafNumber = 0; leafNumber<this.objectsDOMarray.length; leafNumber++){
                this.objectsDOMarray[leafNumber].tween.play();
            }
        }
    }),
    blade: {
        load: function(){
            this.tween = TweenMax.to("#blade", 10, {
                ease:  Power2.easeInOut,
                yoyo: true,
                rotationZ: 2,
                transformOrigin:"left bottom",
                repeat: -1
            });
        },
        pause: function(){
            this.tween.pause();
        },
        play: function(){
            this.tween.play();
        }
    },
    soundSwitch: new Button({
        selector: "#sound",
        grassSound: null,
        grassSoundLow: 0.2,
        grassSoundHight: 0.8,
        makeGrassSoundLow: function(){
            this.grassSound.volume = this.grassSoundLow;
        },
        makeGrassSoundHight: function(){
            this.grassSound.volume = this.grassSoundHight;
        },
        load: function(){
            this.grassSound = document.getElementById("grassSound");
            var soundOn = APPLICATION.getCooki("soundOn");
            if(soundOn === "no"){
                this.click();
            }
        },
        mouseenter: function(){
            TweenMax.to(this, 0.3, {
                opacity: 1
            })
        },
        mouseleave: function(){
            TweenMax.to(this, 0.3, {
                opacity: 0.5
            })
        },
        click: function(){
            var soundSwitch = APPLICATION.soundSwitch,
                soundOn = APPLICATION.soundOn;
            if(soundOn === true){
                TweenMax.to("#stroke", 0.3, {
                    opacity: 1
                });
                soundSwitch.grassSound.pause();
                APPLICATION.ladybug.bugSound.pause();
                APPLICATION.soundOn = false;
                document.cookie = "soundOn=no";
            }else{
                TweenMax.to("#stroke", 0.3, {
                    opacity: 0
                });
                soundSwitch.grassSound.play();
                soundSwitch.makeGrassSoundHight();
                APPLICATION.soundOn = true;
                document.cookie = "soundOn=yes";
            }
        }
    }),
    drop:{
        load: function(){
            this.dropTimeLine = new TimelineMax({repeat:-1, repeatDelay:10});
            this.dropTimeLine
                .to("#drop", 20, {height: "+=30px", width: "+=15px", left: "-=7px", ease: RoughEase.ease.config({ template: Sine.easeOut, strength: 2, points: 20, taper: "both", randomize: true, clamp: true})})
                .to("#drop", 2, {top:"+=1000px"});
        },
        pause: function(){
            this.dropTimeLine.pause();
        },
        play: function(){
            this.dropTimeLine.play();
        }
    },
    ladybug:{
        bugSound: null,
        dropSound: null,
        flyVolumeHight: 0.3,
        dropVolumeHight: 1,
        flyVolumeLow: 0.1,
        dropVolumeLow: 0.5,
        makeVolumesLow: function(){
            dropSound.volume = APPLICATION.ladybug.dropVolumeLow;
            bugSound.volume = APPLICATION.ladybug.flyVolumeLow;
        },
        makeVolumesHight: function(){
            dropSound.volume = APPLICATION.ladybug.dropVolumeHight;
            bugSound.volume = APPLICATION.ladybug.flyVolumeHight;
        },
        load: function(){
            this.bugSound = document.getElementById("bugSound");
            this.dropSound = document.getElementById("dropSound");
            var ladybug = document.getElementById("ladybug"),
                runLadybug = function(){
                    if(APPLICATION.ladybug.fly !== undefined){
                        APPLICATION.ladybug.fly.pause();
                    }
                    if(APPLICATION.ladybug.run !== undefined){
                        APPLICATION.ladybug.run.play();
                    }else{
                        APPLICATION.ladybug.run = TweenMax.to(ladybug, 0.1, {
                            css:{backgroundPosition :"-50px 10px"},
                            yoyo: true,
                            ease: SteppedEase.config(1),
                            repeat:-1
                        });
                    }
                },
                flyLadyBug = function(){
                    APPLICATION.ladybug.run.pause();
                    if(APPLICATION.soundOn === true){
                        APPLICATION.ladybug.dropSound.play();
                        APPLICATION.ladybug.bugSound.play();
                        if(APPLICATION.page.status === "close"){
                            APPLICATION.ladybug.makeVolumesHight();
                        }else{
                            APPLICATION.ladybug.makeVolumesLow();
                        }
                    }
                    if(APPLICATION.ladybug.fly !== undefined){
                        APPLICATION.ladybug.fly.play();
                    }else{
                        APPLICATION.ladybug.fly = TweenMax.to(ladybug, 0, {
                            css:{
                                backgroundPosition :"-100px 0px",
                                width: "68px"
                            }
                        })
                    }
                };

            runLadybug();
            this.ladybugTimeLine = new TimelineMax({repeat:-1, repeatDelay:2, onRepeat: runLadybug});
            this.ladybugTimeLine
                .to(ladybug, 0, {
                    width: "50px"
                })
                .to(ladybug, 20, {
                    bezier:{
                        type:"soft",
                        timeResolution: 3,
                        values:[
                            {left: -45, top:150},
                            {left: -30, top:55},
                            {left: 40, top:0},
                            {left: 67, top:-15},
                            {left: 103, top:-35},
                            {left: 143, top:-30},
                            {left: 163, top:-35},
                            {left: 203, top:-30},
                            {left: 512, top:138}
                        ],
                        autoRotate:true
                    },
                    onComplete: flyLadyBug,
                    ease: RoughEase.ease.config({ template: Power0.easeNone, strength: 0.1, points: 10, taper: "both", randomize: true, clamp: true})
                })
                .to(ladybug, 10, {
                    bezier:{
                        type:"soft",
                        timeResolution: 4,
                        values:[
                            {left: 558, top:113},
                            {left: 616, top:222},
                            {left: 872, top:293},
                            {left: 1078, top:100},
                            {left: 1378, top:350},
                            {left: 1548, top:-100}
                        ],
                        autoRotate:false
                    },
                    ease: RoughEase.ease.config({ template: Power0.easeNone, strength: 0.1, points: 10, taper: "both", randomize: true, clamp: true})
                })
        },
        pause: function(){
            this.ladybugTimeLine.pause();
        },
        play: function(){
            this.ladybugTimeLine.play();
        }
    },
    stowns: new ObjectsArray({
        selector: ".stone",
        playStoneSound: function(){
            if(APPLICATION.soundOn === true){
                var stoneSound = document.getElementById("stoneSound");
                stoneSound.play();
            }
        },
        mouseenter: function(){
            APPLICATION.stowns.playStoneSound();
            TweenMax.to(this, 0.5, {
                ease:  Power2.easeInOut,
                rotationZ: "+=6",
                transformOrigin:"center"
            });
        },
        mouseleave: function(){
            APPLICATION.stowns.playStoneSound();
            TweenMax.to(this, 0.5, {
                ease:  Power2.easeInOut,
                rotationZ: "-=6",
                transformOrigin:"center"
            });
        },
        click: function(event){
            //event.preventDefault();
            APPLICATION.stowns.playStoneSound();
            TweenMax.to(this, 0.25, {
                ease:  Power2.easeInOut,
                rotationZ: "-=6",
                transformOrigin:"center",
                yoyo: true,
                repeat:1
            });
            //window.location = this.firstElementChild.href;
            //APPLICATION.page.load(this.firstElementChild.href);
        }
    }),
    pageMenuButs: new ObjectsArray({
        selector: ".page_menu_line a, #close_page",
        mouseenter: function(){
            TweenMax.to(this, 0.3, {
                ease:  Power2.easeInOut,
                opacity: 1,
                y: "+=2px"
            })
        },
        mouseleave: function(){
            TweenMax.to(this, 0.7, {
                ease:  Power1.easeInOut,
                opacity: 0.7,
                y: "-=2px"
            })
        }
    }),
    closePageButton: new Button({
        selector: "#close_page",
        click: function(){
            var page = APPLICATION.page;
            page.close();
            page.load("../");
        }
    }),
    fotoMini: new ObjectsArray({
        selector: ".foto",
        idPrefix: null,
        getFotoNumber: function(fotoObject){
            var fotoId = fotoObject.id,
                idParts = fotoId.split("_"),
                fotoNumber = idParts.pop();
                APPLICATION.fotoMini.idPrefix = idParts.join("_");
            return parseInt(fotoNumber);
        },
        getNextFotoId: function(fotoObject){
            var fotoNumber = APPLICATION.fotoMini.getFotoNumber(fotoObject),
                nextFotoNumber = fotoNumber+1;
            if(nextFotoNumber > APPLICATION.fotoMini.objectsDOMarray.length){
                nextFotoNumber = 1;
            }
            return APPLICATION.fotoMini.idPrefix+"_"+nextFotoNumber;
        },
        setButtonNext: function(fotoObject){
            var buttonObject = document.getElementById("next"),
                nextFotoId = APPLICATION.fotoMini.getNextFotoId(fotoObject),
                nextFotoObject = document.getElementById(nextFotoId);
            buttonObject.fotoObject = nextFotoObject;
            buttonObject.href = nextFotoObject.href;
        },
        getPrevFotoId: function(fotoObject){
            var fotoNumber = APPLICATION.fotoMini.getFotoNumber(fotoObject),
                prevFotoNumber = fotoNumber-1;
            if(prevFotoNumber === 0){
                prevFotoNumber = APPLICATION.fotoMini.objectsDOMarray.length;
            }
            return APPLICATION.fotoMini.idPrefix+"_"+prevFotoNumber;
        },
        setButtonPrev: function(fotoObject){
            var buttonObject = document.getElementById("before"),
                prevFotoId = APPLICATION.fotoMini.getPrevFotoId(fotoObject),
                prevFotoObject = document.getElementById(prevFotoId);
            buttonObject.fotoObject = prevFotoObject;
            buttonObject.href = prevFotoObject.href;
        },
        setNavButtons: function(fotoObject){
            APPLICATION.fotoMini.setButtonNext(fotoObject);
            APPLICATION.fotoMini.setButtonPrev(fotoObject);
        },
        setImage: function(foto_object){
            var image_div = document.querySelector("#current_image");
            console.log(foto_object);
            image_div.innerHTML = "<h3 id='foto_project'>"+foto_object.title+"</h3><img src='"+foto_object.href+"'/>";
        },
        click: function(event){
            //event.preventDefault();
            var image_page = document.querySelector("#image_page");
            //console.log(APPLICATION.fotoMini.getFotoNumber(event.currentTarget));
            //console.log(event.currentTarget);
            //APPLICATION.fotoMini.setNavButtons(event.currentTarget);
            this.open();
            APPLICATION.fotoMini.setImage(this);
        },
        open: function(){
            imagePageTimeLine = new TimelineMax({});
            imagePageTimeLine
                .to(image_page, 0, {
                    display: "block",
                    opacity: 0
                })
                .to(image_page, 0.7, {
                    opacity: 1
                });
        },
        mouseenter: function(){
            //console.log("enter");
        }
    }),
    closeImageButton: new Button({
        selector: "#close_image",
        click: function(){
            var image_page = document.querySelector("#image_page");
            closeImageTimeLine = new TimelineMax({});
            closeImageTimeLine
                .to(image_page, 1, {
                    opacity: 0
                })
                .to(image_page, 0, {
                    display: "none"
                });
        }
    }),
    slideButtons: new ObjectsArray({
        selector: "#next, #before",
        click: function(){
            /*event.preventDefault();
            APPLICATION.fotoMini.setImage(this.fotoObject);
            APPLICATION.fotoMini.setNavButtons(this.fotoObject);*/
        }
    })
};
APPLICATION.load();

/*
TweenMax.to(".clouds", 1, {
    y: function() {
        return Math.random() * 300;
    }
});*/
