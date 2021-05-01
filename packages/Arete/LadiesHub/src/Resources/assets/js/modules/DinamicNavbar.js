import {limit} from '../support/utils';

/**
 * Calculates and sets the padding of an element according to the
 * height of another. Usually the padding of the body, acording to the
 * height of navbar.
 */
class calculatePadding {

    /**
     * Constructs the padding controller
     * @param {string} headSelector selector of the element to watch height
     * @param {string} paddedSelector selector of the element to add the padding
     */
    constructor(headSelector, paddedSelector = null) {
        
        this.paddedEl = paddedSelector ? document.querySelector(paddedSelector) : document;
        this.headSelector = headSelector;
        window.addEventListener('DOMContentLoaded', () => {
            this.setPadding();
        });
        window.addEventListener('resize', () => {
            // Updates after the rest of the UI has been updated
            window.setTimeout( () =>  this.setPadding() );
        })

    }

    setPadding() {
        let size = document.querySelector(this.headSelector).offsetHeight;
        document.body.style.paddingTop=size + 'px';
    }
}

/**
 * Hides/Shows navbar when scrolling down/up.
 */
class DinamicNavbar {
    /**
     * @param {string} navbar - Selector 
     * @param {Object} options
     * @param {number} options.disableWidth - Screen width in px <= at wich disable the styling of 
     *                 the dinamic navbar.
     */
    constructor(navbar, options) {
        this.elSelector = navbar;
        this.options = options;
        this.styleCache = new Map();
        this.initialized = false;
    }

    /** @returns HTMLELement */
    navEl() {
        return document.querySelector(this.elSelector);
    }

    /**
     *  Tries to init and return the initialization status
     * 
     * @returns {boolean}  false if not initialized */
    init() {
        if (this.initialized) {
            return true;
        }

        let el = this.navEl()
        if(el.style.display === 'none' || this.outOfWidth()) {
            return false; // init height can't be calc if not dispalyed normally.
        }

        this.initHeight = el.offsetHeight;
        this.height(this.initHeight);
        this.recordHistory;
        this.initialized = true;
        return true;
    }
    
    update() {
        if (this.disabled()) return;
        let calcHeight = limit(this.navEl().offsetHeight + this.getChange(1/3), this.initHeight, 0);
        this.height(calcHeight);
        this.recordHistory();
    }

  

    recordHistory() {
        this.lastPageYOffset = window.pageYOffset;
    }

    getChange(speed) {
        return (this.lastPageYOffset - window.pageYOffset) * speed;
    }

    /** @param {number} h height in px */
    height(h) {
        this.setStyle('height', h + 'px');
    }
    
    /**
     * Sets style of the element and caches it,
     * if it's not disabled.
     * 
     * @param {string} style
     * @param {string} value
     * @return {boolean} - false if is disabled, true otherwise.
     */
    setStyle(style, value) {
        this.styleCache.set(style, value)
        this.navEl().style[style] = value
        return true;
    }

    responsiveController(event) {
        if (this.disabled(event.target)) {
            // console.log('no permitido')
            this.clearStyles();
        }
        // } else {
        //     console.log('permitido')
        // }
    }

    clearStyles() {
        let el = this.navEl();
        this.styleCache.forEach((v, k) => el.style[k] = '');
    }

    /** 
     * @param {Window} w -the window object 
     * 
     * @returns {boolean}
     */
    disabled(w = null) {
        if (!this.init()) return true;
        w = w ? w : window;
        return this.outOfWidth(w);
    }

    /**
     * Reuturns if the el. is outh of enable width
     * 
     * @returns {boolean}
     */
    outOfWidth(w = null) {
        w = w ? w : window;
        return w.document.body.clientWidth <= this.options.disableWidth;
    }
    
    listen() {
        window.addEventListener('scroll', () => {
            this.update();
        })

        window.addEventListener('DOMContentLoaded', (event) => {
            this.init(); // tries to init
        })

        window.addEventListener('resize', (e) => {
            this.responsiveController(e);
        })
    }

}

class DinamicTopHeader {
    /**
     * 
     * @param {string} topHedaerSelector 
     * @param {object} options 
     * @param {number} options.maxOffset The end offset where the reduction of size ends. 
     * @param {number} options.minSize The end size. 
     * @param {number} options.disabled The width <= at wich the dinamic controller is disabled
     * 
     */
    constructor(topHedaerSelector, options = {}) {
        this.topHedaerSelector = topHedaerSelector;
        this.opt = {};
        this.opt.maxOffset = options.maxOffset ?? 200;
        this.opt.minSize = options.minSize ?? 48;
        this.opt.disabledWidth = options.disabledWidth ?? 900;
        this.opt.logoOffset = options.logoOffset ?? 100;
        this.opt.logoClass = options.logoClass ?? 'logo-smaller'
        this.opt.logoSelector = options.logoSelector ?? '#main-logo'
    }

    update() {
        // console.log('actualizando')
        if (this.isDisabled()) {
            // console.log('deshabilitado')
            this.cleanStyles();
            return false;
        } else if (!this.isInitialized) {
            // console.log('sin iniciar')
            return this.init(); // TRIES TO INITIALIZE AND RETURN THE RESULT OF THE INTENT
            // BUT NOT STATUS
        }
        // console.log('modificando tamaño')
        let actualSize = limit(this.startSize - (window.scrollY * this.speed), this.startSize, this.opt.minSize);
        this.topHeader.style.height = actualSize + 'px';

        this.updateLogo()


        return true;
    }

    updateLogo() {
        if (window.scrollY >= this.opt.logoOffset) {
            if (!this.logo.classList.contains(this.opt.logoClass)) {
                // console.log('debería agregar la clase');
                this.logo.classList.add(this.opt.logoClass);
            }
        } else if (this.logo.classList.contains(this.opt.logoClass)) {
            // console.log('debería quitar la clase');
            this.logo.classList.remove(this.opt.logoClass);
        }
    }

    init() {
        // check the DOM after it is loaded;
        if(this.isDisabled()) {
            return false;
        }
        window.setTimeout(() => {
            this.topHeader = document.querySelector(this.topHedaerSelector);
            this.startSize = this.topHeader.offsetHeight;
            this.speed = (this.startSize - this.opt.minSize) / this.opt.maxOffset;
            this.logo = document.querySelector(this.opt.logoSelector)
            this.isInitialized = true;
        })
        return true; // initialization startet BUT NOT COMPLETED YET!
    }

    isDisabled() {
        return window.innerWidth <= this.opt.disabledWidth;
    }

    cleanStyles() {
        // console.log('por limpiar')
        if (!this.isInitialized) return false;
        // console.log('limpiando')
        this.topHeader.style.height = '';
    }

    listen() {
        window.addEventListener('scroll', () => this.update())

        window.addEventListener('resize', () => this.update())

        window.addEventListener('DOMContentLoaded', () => this.init())
    }
}

export {DinamicNavbar as default, calculatePadding, DinamicTopHeader}