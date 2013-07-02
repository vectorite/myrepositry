/*------------------------------------------------------------------------
 # VM - Flexible Product Slider   - Version 1.0
 # ------------------------------------------------------------------------
 # Copyright (C) 2011 Flexible Web Design. All Rights Reserved.
 # @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Author: Flexible Web Design Team
 # Websites: http://www.flexiblewebdesign.com
 -------------------------------------------------------------------------*/
 


var Flexiblescroller = new Class({
    initialize: function (container, options) {
        this.setOptions({
            onScroll: Class.empty,
            onPage: Class.empty,
            onRotate: Class.empty,
            onStop: Class.empty,
            onAutoPlay: Class.empty,
            panelSelector: '.FlexiblePanel',
            slidesSelector: '.slide',
            knobSelector: '.scrollknob',
            knobSizeSelector: '.scrollknob-size',
            scrollbarSelector: '.scrollbar',
            scrollLinkSelector: {
                forward: '.forward',
                back: '.back'
            },
            startIndex: 0,
            slideInterval: 4000,
            autoplay: true,
            knobOffset: 0,
            maxThumbSize: 10,
            mode: 'vertical',
            width: 0,
            scrollSteps: 10,
            wheel: true
        }, options);
        this.horz = (this.options.mode == 'horizontal');
        this.FlexiblePanel = $(container).getElement(this.options.panelSelector).setStyle('overflow', 'hidden');
        this.slides = $(container).getElements(this.options.slidesSelector);
        this.knob = $(container).getElement(this.options.knobSelector);
        this.ksize = $(container).getElement(this.options.knobSizeSelector);
        this.track = $(container).getElement(this.options.scrollbarSelector);
        this.rotateForward = true;
        this.rotateActive = true;
        this.currentSlide = this.options.startIndex;
        this.setPositions();
        if (this.horz && this.options.width) {
            this.wrapper = new Element('div');
            this.FlexiblePanel.getChildren().each(function (child) {
                this.wrapper.adopt(child)
            });
            this.wrapper.injectInside(this.FlexiblePanel).setStyle('width', this.options.width)
        }
        this.bound = {
            'start': this.start.bind(this),
            'end': this.end.bind(this),
            'drag': this.drag.bind(this),
            'wheel': this.wheel.bind(this),
            'page': this.page.bind(this)
        };
        this.position = {};
        this.mouse = {};
        this.update();
        this.attach();
        var clearScroll = function () {
                $clear(this.scrolling)
            }.bind(this);
        ['forward', 'back'].each(function (direction) {
            var lnk = $(container).getElement(this.options.scrollLinkSelector[direction]);
            if (lnk) {
                lnk.addEvents({
                    mousedown: function () {
                        this.scrolling = this[direction].periodical(50, this)
                    }.bind(this),
                    mouseup: clearScroll.bind(this),
                    click: clearScroll.bind(this)
                })
            }
        }, this);
        this.knob.addEvent('click', clearScroll.bind(this));
        window.addEvent('domready', function () {
            try {
                $(document.body).addEvent('mouseup', clearScroll.bind(this))
            } catch (e) {}
        }.bind(this));
        if (this.options.autoplay) this.autoplay()
    },
    setPositions: function () {
        [this.track, this.knob].each(function (el) {
            if (el.getStyle('position') == 'static') el.setStyle('position', 'relative')
        })
    },
    update: function () {
        var plain = this.horz ? 'Width' : 'Height';
        this.contentSize = this.FlexiblePanel['offset' + plain];
        this.contentScrollSize = this.FlexiblePanel['scroll' + plain];
        this.trackSize = this.track['offset' + plain];
        this.contentRatio = this.contentSize / this.contentScrollSize;
        this.knobSize = (this.trackSize * this.contentRatio + this.options.knobOffset).limit(this.options.maxThumbSize, this.trackSize);
        this.scrollRatio = this.contentScrollSize / this.trackSize;
        this.ksize.setStyle(plain.toLowerCase(), this.knobSize + 'px');
        this.updateThumbFromContentScroll();
        this.updateContentFromThumbPosition()
    },
    updateContentFromThumbPosition: function () {
        this.FlexiblePanel[this.horz ? 'scrollLeft' : 'scrollTop'] = this.position.now * this.scrollRatio
    },
    updateThumbFromContentScroll: function () {
        this.position.now = (this.FlexiblePanel[this.horz ? 'scrollLeft' : 'scrollTop'] / this.scrollRatio).limit(0, (this.trackSize - this.knobSize));
        this.knob.setStyle(this.horz ? 'left' : 'top', this.position.now + 'px')
    },
    attach: function () {
        this.knob.addEvent('mousedown', this.bound.start);
        if (this.options.scrollSteps) this.FlexiblePanel.addEvent('mousewheel', this.bound.wheel);
        this.track.addEvent('mouseup', this.bound.page)
    },
    wheel: function (event) {
        event = new Event(event);
        this.scroll(-(event.wheel * this.options.scrollSteps));
        this.updateThumbFromContentScroll();
        event.stop()
    },
    scroll: function (steps) {
        if (this.rotateActive) this.stop();
        steps = steps || this.options.scrollSteps;
        this.FlexiblePanel[this.horz ? 'scrollLeft' : 'scrollTop'] += steps;
        this.updateThumbFromContentScroll()
    },
    forward: function (steps) {
        this.scroll(steps)
    },
    back: function (steps) {
        steps = steps || this.options.scrollSteps;
        this.scroll(-steps)
    },
    page: function (event) {
        var axis = this.horz ? 'x' : 'y';
        event = new Event(event);
        var forward = (event.page[axis] > this.knob.getPosition()[axis]);
        this.scroll((forward ? 1 : -1) * this.FlexiblePanel['offset' + (this.horz ? 'Width' : 'Height')]);
        this.updateThumbFromContentScroll();
        this.fireEvent('onPage', forward);
        event.stop()
    },
    start: function (event) {
        event = new Event(event);
        var axis = this.horz ? 'x' : 'y';
        this.mouse.start = event.page[axis];
        this.position.start = this.knob.getStyle(this.horz ? 'left' : 'top').toInt();
        document.addEvent('mousemove', this.bound.drag);
        document.addEvent('mouseup', this.bound.end);
        this.knob.addEvent('mouseup', this.bound.end);
        event.stop()
    },
    end: function (event) {
        event = new Event(event);
        document.removeEvent('mousemove', this.bound.drag);
        document.removeEvent('mouseup', this.bound.end);
        this.knob.removeEvent('mouseup', this.bound.end);
        event.stop()
    },
    drag: function (event) {
        if (this.rotateActive) this.stop();
        event = new Event(event);
        var axis = this.horz ? 'x' : 'y';
        this.mouse.now = event.page[axis];
        this.position.now = (this.position.start + (this.mouse.now - this.mouse.start)).limit(0, (this.trackSize - this.knobSize));
        this.updateContentFromThumbPosition();
        this.updateThumbFromContentScroll();
        event.stop()
    },
    autoplay: function () {
        this.rotateInt = this.rotate.periodical(this.options.slideInterval, this);
        this.fireEvent('onAutoPlay')
    },
    stop: function () {
        clearInterval(this.rotateInt);
        this.rotateActive = false;
        this.fireEvent('onStop')
    },
    rotate: function () {
        if (this.FlexiblePanel[this.horz ? 'scrollLeft' : 'scrollTop'] <= 0) this.rotateForward = true;
        if (this.contentSize + this.FlexiblePanel[this.horz ? 'scrollLeft' : 'scrollTop'] >= this.contentScrollSize) this.rotateForward = false;
        if (this.currentSlide - 1 < 0) this.rotateForward = true;
        if (this.currentSlide + 1 >= this.slides.length) this.rotateForward = false;
        var next = this.currentSlide + (this.rotateForward ? 1 : -1);
        var scroll = new Fx.Scroll(this.FlexiblePanel, {
            duration: 500
        }).toElement(this.slides[next]).addEvent('onComplete', function () {
            this.updateThumbFromContentScroll()
        }.bind(this));
        this.currentSlide = next;
        this.fireEvent('onRotate')
    }
});
Flexiblescroller.implement(new Events);
Flexiblescroller.implement(new Options);