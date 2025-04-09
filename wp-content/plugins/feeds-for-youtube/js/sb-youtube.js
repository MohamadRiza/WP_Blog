let xss = require("xss");
var sby_js_exists = (typeof sby_js_exists !== 'undefined') ? true : false;

if(!sby_js_exists) {

    /**
     * Sanitize string by escaping HTML entities
     * @param input
     * @returns {string}
     */
    function sbyEncodeInput(input)  {
        return xss(input);
    }

    function sbyAddImgLiquid() {
        /*! imgLiquid v0.9.944 / 03-05-2013 https://github.com/karacas/imgLiquid */
        var sby_imgLiquid = sby_imgLiquid || {VER: "0.9.944"};
        sby_imgLiquid.bgs_Available = !1, sby_imgLiquid.bgs_CheckRunned = !1, function (i) {
            function t() {
                if (!sby_imgLiquid.bgs_CheckRunned) {
                    sby_imgLiquid.bgs_CheckRunned = !0;
                    var t = i('<span style="background-size:cover" />');
                    i("body").append(t), !function () {
                        var i = t[0];
                        if (i && window.getComputedStyle) {
                            var e = window.getComputedStyle(i, null);
                            e && e.backgroundSize && (sby_imgLiquid.bgs_Available = "cover" === e.backgroundSize)
                        }
                    }(), t.remove()
                }
            }

            i.fn.extend({
                sby_imgLiquid: function (e) {
                    this.defaults = {
                        fill: !0,
                        verticalAlign: "center",
                        horizontalAlign: "center",
                        useBackgroundSize: !0,
                        useDataHtmlAttr: !0,
                        responsive: !0,
                        delay: 0,
                        fadeInTime: 0,
                        removeBoxBackground: !0,
                        hardPixels: !0,
                        responsiveCheckTime: 500,
                        timecheckvisibility: 500,
                        onStart: null,
                        onFinish: null,
                        onItemStart: null,
                        onItemFinish: null,
                        onItemError: null
                    }, t();
                    var a = this;
                    return this.options = e, this.settings = i.extend({}, this.defaults, this.options), this.settings.onStart && this.settings.onStart(), this.each(function (t) {
                        function e() {
                            -1 === u.css("background-image").indexOf(encodeURI(c.attr("src"))) && u.css({"background-image": 'url("' + encodeURI(c.attr("src")) + '")'}), u.css({
                                "background-size": g.fill ? "cover" : "contain",
                                "background-position": (g.horizontalAlign + " " + g.verticalAlign).toLowerCase(),
                                "background-repeat": "no-repeat"
                            }), i("a:first", u).css({
                                display: "block",
                                width: "100%",
                                height: "100%"
                            }), i("img", u).css({display: "none"}), g.onItemFinish && g.onItemFinish(t, u, c), u.addClass("sby_imgLiquid_bgSize"), u.addClass("sby_imgLiquid_ready"), l()
                        }

                        function o() {
                            function e() {
                                c.data("sby_imgLiquid_error") || c.data("sby_imgLiquid_loaded") || c.data("sby_imgLiquid_oldProcessed") || (u.is(":visible") && c[0].complete && c[0].width > 0 && c[0].height > 0 ? (c.data("sby_imgLiquid_loaded", !0), setTimeout(r, t * g.delay)) : setTimeout(e, g.timecheckvisibility))
                            }

                            if (c.data("oldSrc") && c.data("oldSrc") !== c.attr("src")) {
                                var a = c.clone().removeAttr("style");
                                return a.data("sby_imgLiquid_settings", c.data("sby_imgLiquid_settings")), c.parent().prepend(a), c.remove(), c = a, c[0].width = 0, void setTimeout(o, 10)
                            }
                            return c.data("sby_imgLiquid_oldProcessed") ? void r() : (c.data("sby_imgLiquid_oldProcessed", !1), c.data("oldSrc", c.attr("src")), i("img:not(:first)", u).css("display", "none"), u.css({overflow: "hidden"}), c.fadeTo(0, 0).removeAttr("width").removeAttr("height").css({
                                visibility: "visible",
                                "max-width": "none",
                                "max-height": "none",
                                width: "auto",
                                height: "auto",
                                display: "block"
                            }), c.on("error", n), c[0].onerror = n, e(), void d())
                        }

                        function d() {
                            (g.responsive || c.data("sby_imgLiquid_oldProcessed")) && c.data("sby_imgLiquid_settings") && (g = c.data("sby_imgLiquid_settings"), u.actualSize = u.get(0).offsetWidth + u.get(0).offsetHeight / 1e4, u.sizeOld && u.actualSize !== u.sizeOld && r(), u.sizeOld = u.actualSize, setTimeout(d, g.responsiveCheckTime))
                        }

                        function n() {
                            c.data("sby_imgLiquid_error", !0), u.addClass("sby_imgLiquid_error"), g.onItemError && g.onItemError(t, u, c), l()
                        }

                        function s() {
                            var i = {};
                            if (a.settings.useDataHtmlAttr) {
                                var t = u.attr("data-sby_imgLiquid-fill"),
                                  e = u.attr("data-sby_imgLiquid-horizontalAlign"),
                                  o = u.attr("data-sby_imgLiquid-verticalAlign");
                                ("true" === t || "false" === t) && (i.fill = Boolean("true" === t)), void 0 === e || "left" !== e && "center" !== e && "right" !== e && -1 === e.indexOf("%") || (i.horizontalAlign = e), void 0 === o || "top" !== o && "bottom" !== o && "center" !== o && -1 === o.indexOf("%") || (i.verticalAlign = o)
                            }
                            return sby_imgLiquid.isIE && a.settings.ieFadeInDisabled && (i.fadeInTime = 0), i
                        }

                        function r() {
                            var i, e, a, o, d, n, s, r, m = 0, h = 0, f = u.width(), v = u.height();
                            void 0 === c.data("owidth") && c.data("owidth", c[0].width), void 0 === c.data("oheight") && c.data("oheight", c[0].height), g.fill === f / v >= c.data("owidth") / c.data("oheight") ? (i = "100%", e = "auto", a = Math.floor(f), o = Math.floor(f * (c.data("oheight") / c.data("owidth")))) : (i = "auto", e = "100%", a = Math.floor(v * (c.data("owidth") / c.data("oheight"))), o = Math.floor(v)), d = g.horizontalAlign.toLowerCase(), s = f - a, "left" === d && (h = 0), "center" === d && (h = .5 * s), "right" === d && (h = s), -1 !== d.indexOf("%") && (d = parseInt(d.replace("%", ""), 10), d > 0 && (h = s * d * .01)), n = g.verticalAlign.toLowerCase(), r = v - o, "left" === n && (m = 0), "center" === n && (m = .5 * r), "bottom" === n && (m = r), -1 !== n.indexOf("%") && (n = parseInt(n.replace("%", ""), 10), n > 0 && (m = r * n * .01)), g.hardPixels && (i = a, e = o), c.css({
                                width: i,
                                height: e,
                                "margin-left": Math.floor(h),
                                "margin-top": Math.floor(m)
                            }), c.data("sby_imgLiquid_oldProcessed") || (c.fadeTo(g.fadeInTime, 1), c.data("sby_imgLiquid_oldProcessed", !0), g.removeBoxBackground && u.css("background-image", "none"), u.addClass("sby_imgLiquid_nobgSize"), u.addClass("sby_imgLiquid_ready")), g.onItemFinish && g.onItemFinish(t, u, c), l()
                        }

                        function l() {
                            t === a.length - 1 && a.settings.onFinish && a.settings.onFinish()
                        }

                        var g = a.settings, u = i(this), c = i("img:first", u);
                        return c.length ? (c.data("sby_imgLiquid_settings") ? (u.removeClass("sby_imgLiquid_error").removeClass("sby_imgLiquid_ready"), g = i.extend({}, c.data("sby_imgLiquid_settings"), a.options)) : g = i.extend({}, a.settings, s()), c.data("sby_imgLiquid_settings", g), g.onItemStart && g.onItemStart(t, u, c), void (sby_imgLiquid.bgs_Available && g.useBackgroundSize ? e() : o())) : void n()
                    })
                }
            })
        }(jQuery);

        // Use imagefill to set the images as backgrounds so they can be square
        !function () {
            var css = sby_imgLiquid.injectCss,
              head = document.getElementsByTagName('head')[0],
              style = document.createElement('style');
            style.type = 'text/css';
            if (style.styleSheet) {
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }
            head.appendChild(style);
        }();
    }

    /* JavaScript Linkify - v0.3 - 6/27/2009 - http://benalman.com/projects/javascript-linkify/ */
    window.sbyLinkify = (function () {
        var k = "[a-z\\d.-]+://",
          h = "(?:(?:[0-9]|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])\\.){3}(?:[0-9]|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])",
          c = "(?:(?:[^\\s!@#$%^&*()_=+[\\]{}\\\\|;:'\",.<>/?]+)\\.)+",
          n = "(?:ac|ad|aero|ae|af|ag|ai|al|am|an|ao|aq|arpa|ar|asia|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|biz|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|cat|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|coop|com|co|cr|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|info|int|in|io|iq|ir|is|it|je|jm|jobs|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mil|mk|ml|mm|mn|mobi|mo|mp|mq|mr|ms|mt|museum|mu|mv|mw|mx|my|mz|name|na|nc|net|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pro|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tel|tf|tg|th|tj|tk|tl|tm|tn|to|tp|travel|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|xn--0zwm56d|xn--11b5bs3a9aj6g|xn--80akhbyknj4f|xn--9t4b11yi5a|xn--deba0ad|xn--g6w251d|xn--hgbk6aj7f53bba|xn--hlcj6aya9esc7a|xn--jxalpdlp|xn--kgbechtv|xn--zckzah|ye|yt|yu|za|zm|zw)",
          f = "(?:" + c + n + "|" + h + ")", o = "(?:[;/][^#?<>\\s]*)?", e = "(?:\\?[^#<>\\s]*)?(?:#[^<>\\s]*)?",
          d = "\\b" + k + "[^<>\\s]+", a = "\\b" + f + o + e + "(?!\\w)", m = "mailto:",
          j = "(?:" + m + ")?[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@" + f + e + "(?!\\w)",
          l = new RegExp("(?:" + d + "|" + a + "|" + j + ")", "ig"), g = new RegExp("^" + k, "i"),
          b = {"'": "`", ">": "<", ")": "(", "]": "[", "}": "{", "B;": "B+", "b:": "b9"}, i = {
              callback: function (q, p) {
                  return p ? '<a href="' + p + '" title="' + p + '" target="_blank" rel="noopener">' + q + "</a>" : q
              }, punct_regexp: /(?:[!?.,:;'"]|(?:&|&amp;)(?:lt|gt|quot|apos|raquo|laquo|rsaquo|lsaquo);)$/
          };
        return function (u, z) {
            z = z || {};
            var w, v, A, p, x = "", t = [], s, E, C, y, q, D, B, r;
            for (v in i) {
                if (z[v] === undefined) {
                    z[v] = i[v]
                }
            }
            while (w = l.exec(u)) {
                A = w[0];
                E = l.lastIndex;
                C = E - A.length;
                if (/[\/:]/.test(u.charAt(C - 1))) {
                    continue
                }
                do {
                    y = A;
                    r = A.substr(-1);
                    B = b[r];
                    if (B) {
                        q = A.match(new RegExp("\\" + B + "(?!$)", "g"));
                        D = A.match(new RegExp("\\" + r, "g"));
                        if ((q ? q.length : 0) < (D ? D.length : 0)) {
                            A = A.substr(0, A.length - 1);
                            E--
                        }
                    }
                    if (z.punct_regexp) {
                        A = A.replace(z.punct_regexp, function (F) {
                            E -= F.length;
                            return ""
                        })
                    }
                } while (A.length && A !== y);
                p = A;
                if (!g.test(p)) {
                    p = (p.indexOf("@") !== -1 ? (!p.indexOf(m) ? "" : m) : !p.indexOf("irc.") ? "irc://" : !p.indexOf("ftp.") ? "ftp://" : "http://") + p
                }
                if (s != C) {
                    t.push([u.slice(s, C)]);
                    s = E
                }
                t.push([A, p])
            }
            t.push([u.substr(s)]);
            for (v = 0; v < t.length; v++) {
                x += z.callback.apply(window, t[v])
            }
            return x || u
        }
    })();

    //Checks whether browser support HTML5 video element
    function sby_supports_video() {
        return !!document.createElement('video').canPlayType;
    }

    // Carousel
    !function (a, b, c, d) {
        function e(b, c) {
            this.settings = null, this.options = a.extend({}, e.Defaults, c), this.$element = a(b), this._handlers = {}, this._plugins = {}, this._supress = {}, this._current = null, this._speed = null, this._coordinates = [], this._breakpoint = null, this._width = null, this._items = [], this._clones = [], this._mergers = [], this._widths = [], this._invalidated = {}, this._pipe = [], this._drag = {
                time: null,
                target: null,
                pointer: null,
                stage: {start: null, current: null},
                direction: null
            }, this._states = {
                current: {},
                tags: {initializing: ["busy"], animating: ["busy"], dragging: ["interacting"]}
            }, a.each(["onResize", "onThrottledResize"], a.proxy(function (b, c) {
                this._handlers[c] = a.proxy(this[c], this)
            }, this)), a.each(e.Plugins, a.proxy(function (a, b) {
                this._plugins[a.charAt(0).toLowerCase() + a.slice(1)] = new b(this)
            }, this)), a.each(e.Workers, a.proxy(function (b, c) {
                this._pipe.push({filter: c.filter, run: a.proxy(c.run, this)})
            }, this)), this.setup(), this.initialize()
        }

        e.Defaults = {
            items: 3,
            loop: !1,
            center: !1,
            rewind: !1,
            mouseDrag: !0,
            touchDrag: !0,
            pullDrag: !0,
            freeDrag: !1,
            margin: 0,
            stagePadding: 0,
            merge: !1,
            mergeFit: !0,
            autoWidth: !1,
            startPosition: 0,
            rtl: !1,
            smartSpeed: 250,
            fluidSpeed: !1,
            dragEndSpeed: !1,
            responsive: {},
            responsiveRefreshRate: 200,
            responsiveBaseElement: b,
            fallbackEasing: "swing",
            info: !1,
            nestedItemSelector: !1,
            itemElement: "div",
            stageElement: "div",
            refreshClass: "sby-owl-refresh",
            loadedClass: "sby-owl-loaded",
            loadingClass: "sby-owl-loading",
            rtlClass: "sby-owl-rtl",
            responsiveClass: "sby-owl-responsive",
            dragClass: "sby-owl-drag",
            itemClass: "sby-owl-item",
            stageClass: "sby-owl-stage",
            stageOuterClass: "sby-owl-stage-outer",
            grabClass: "sby-owl-grab"
        }, e.Width = {Default: "default", Inner: "inner", Outer: "outer"}, e.Type = {
            Event: "event",
            State: "state"
        }, e.Plugins = {}, e.Workers = [{
            filter: ["width", "settings"], run: function () {
                this._width = this.$element.width()
            }
        }, {
            filter: ["width", "items", "settings"], run: function (a) {
                a.current = this._items && this._items[this.relative(this._current)]
            }
        }, {
            filter: ["items", "settings"], run: function () {
                this.$stage.children(".cloned").remove()
            }
        }, {
            filter: ["width", "items", "settings"], run: function (a) {
                var b = this.settings.margin || "", c = !this.settings.autoWidth, d = this.settings.rtl,
                  e = {width: "auto", "margin-left": d ? b : "", "margin-right": d ? "" : b};
                !c && this.$stage.children().css(e), a.css = e
            }
        }, {
            filter: ["width", "items", "settings"], run: function (a) {
                var b = (this.width() / this.settings.items).toFixed(3) - this.settings.margin, c = null,
                  d = this._items.length, e = !this.settings.autoWidth, f = [];
                for (a.items = {
                    merge: !1,
                    width: b
                }; d--;) c = this._mergers[d], c = this.settings.mergeFit && Math.min(c, this.settings.items) || c, a.items.merge = c > 1 || a.items.merge, f[d] = e ? b * c : this._items[d].width();
                this._widths = f
            }
        }, {
            filter: ["items", "settings"], run: function () {
                var b = [], c = this._items, d = this.settings, e = Math.max(2 * d.items, 4),
                  f = 2 * Math.ceil(c.length / 2), g = d.loop && c.length ? d.rewind ? e : Math.max(e, f) : 0, h = "",
                  i = "";
                for (g /= 2; g--;) b.push(this.normalize(b.length / 2, !0)), h += c[b[b.length - 1]][0].outerHTML, b.push(this.normalize(c.length - 1 - (b.length - 1) / 2, !0)), i = c[b[b.length - 1]][0].outerHTML + i;
                this._clones = b, a(h).addClass("cloned").appendTo(this.$stage), a(i).addClass("cloned").prependTo(this.$stage)
            }
        }, {
            filter: ["width", "items", "settings"], run: function () {
                for (var a = this.settings.rtl ? 1 : -1, b = this._clones.length + this._items.length, c = -1, d = 0, e = 0, f = []; ++c < b;) d = f[c - 1] || 0, e = this._widths[this.relative(c)] + this.settings.margin, f.push(d + e * a);
                this._coordinates = f
            }
        }, {
            filter: ["width", "items", "settings"], run: function () {
                var a = this.settings.stagePadding, b = this._coordinates, c = {
                    width: Math.ceil(Math.abs(b[b.length - 1])) + 2 * a,
                    "padding-left": a || "",
                    "padding-right": a || ""
                };
                this.$stage.css(c)
            }
        }, {
            filter: ["width", "items", "settings"], run: function (a) {
                var b = this._coordinates.length, c = !this.settings.autoWidth, d = this.$stage.children();
                if (c && a.items.merge) for (; b--;) a.css.width = this._widths[this.relative(b)], d.eq(b).css(a.css); else c && (a.css.width = a.items.width, d.css(a.css))
            }
        }, {
            filter: ["items"], run: function () {
                this._coordinates.length < 1 && this.$stage.removeAttr("style")
            }
        }, {
            filter: ["width", "items", "settings"], run: function (a) {
                a.current = a.current ? this.$stage.children().index(a.current) : 0, a.current = Math.max(this.minimum(), Math.min(this.maximum(), a.current)), this.reset(a.current)
            }
        }, {
            filter: ["position"], run: function () {
                this.animate(this.coordinates(this._current))
            }
        }, {
            filter: ["width", "position", "items", "settings"], run: function () {
                var a, b, c, d, e = this.settings.rtl ? 1 : -1, f = 2 * this.settings.stagePadding,
                  g = this.coordinates(this.current()) + f, h = g + this.width() * e, i = [];
                for (c = 0, d = this._coordinates.length; c < d; c++) a = this._coordinates[c - 1] || 0, b = Math.abs(this._coordinates[c]) + f * e, (this.op(a, "<=", g) && this.op(a, ">", h) || this.op(b, "<", g) && this.op(b, ">", h)) && i.push(c);
                this.$stage.children(".active").removeClass("active"), this.$stage.children(":eq(" + i.join("), :eq(") + ")").addClass("active"), this.settings.center && (this.$stage.children(".center").removeClass("center"), this.$stage.children().eq(this.current()).addClass("center"))
            }
        }], e.prototype.initialize = function () {
            if (this.enter("initializing"), this.trigger("initialize"), this.$element.toggleClass(this.settings.rtlClass, this.settings.rtl), this.settings.autoWidth && !this.is("pre-loading")) {
                var b, c, e;
                b = this.$element.find("img"), c = this.settings.nestedItemSelector ? "." + this.settings.nestedItemSelector : d, e = this.$element.children(c).width(), b.length && e <= 0 && this.preloadAutoWidthImages(b)
            }
            this.$element.addClass(this.options.loadingClass), this.$stage = a("<" + this.settings.stageElement + ' class="' + this.settings.stageClass + '"/>').wrap('<div class="' + this.settings.stageOuterClass + '"/>'), this.$element.append(this.$stage.parent()), this.replace(this.$element.children().not(this.$stage.parent())), this.$element.is(":visible") ? this.refresh() : this.invalidate("width"), this.$element.removeClass(this.options.loadingClass).addClass(this.options.loadedClass), this.registerEventHandlers(), this.leave("initializing"), this.trigger("initialized")
        }, e.prototype.setup = function () {
            var b = this.viewport(), c = this.options.responsive, d = -1, e = null;
            c ? (a.each(c, function (a) {
                a <= b && a > d && (d = Number(a))
            }), e = a.extend({}, this.options, c[d]), "function" == typeof e.stagePadding && (e.stagePadding = e.stagePadding()), delete e.responsive, e.responsiveClass && this.$element.attr("class", this.$element.attr("class").replace(new RegExp("(" + this.options.responsiveClass + "-)\\S+\\s", "g"), "$1" + d))) : e = a.extend({}, this.options), this.trigger("change", {
                property: {
                    name: "settings",
                    value: e
                }
            }), this._breakpoint = d, this.settings = e, this.invalidate("settings"), this.trigger("changed", {
                property: {
                    name: "settings",
                    value: this.settings
                }
            })
        }, e.prototype.optionsLogic = function () {
            this.settings.autoWidth && (this.settings.stagePadding = !1, this.settings.merge = !1)
        }, e.prototype.prepare = function (b) {
            var c = this.trigger("prepare", {content: b});
            return c.data || (c.data = a("<" + this.settings.itemElement + "/>").addClass(this.options.itemClass).append(b)), this.trigger("prepared", {content: c.data}), c.data
        }, e.prototype.update = function () {
            for (var b = 0, c = this._pipe.length, d = a.proxy(function (a) {
                return this[a]
            }, this._invalidated), e = {}; b < c;) (this._invalidated.all || a.grep(this._pipe[b].filter, d).length > 0) && this._pipe[b].run(e), b++;
            this._invalidated = {}, !this.is("valid") && this.enter("valid")
        }, e.prototype.width = function (a) {
            switch (a = a || e.Width.Default) {
                case e.Width.Inner:
                case e.Width.Outer:
                    return this._width;
                default:
                    return this._width - 2 * this.settings.stagePadding + this.settings.margin
            }
        }, e.prototype.refresh = function () {
            this.enter("refreshing"), this.trigger("refresh"), this.setup(), this.optionsLogic(), this.$element.addClass(this.options.refreshClass), this.update(), this.$element.removeClass(this.options.refreshClass), this.leave("refreshing"), this.trigger("refreshed")
        }, e.prototype.onThrottledResize = function () {
            b.clearTimeout(this.resizeTimer), this.resizeTimer = b.setTimeout(this._handlers.onResize, this.settings.responsiveRefreshRate)
        }, e.prototype.onResize = function () {
            return !!this._items.length && (this._width !== this.$element.width() && (!!this.$element.is(":visible") && (this.enter("resizing"), this.trigger("resize").isDefaultPrevented() ? (this.leave("resizing"), !1) : (this.invalidate("width"), this.refresh(), this.leave("resizing"), void this.trigger("resized")))))
        }, e.prototype.registerEventHandlers = function () {
            a.support.transition && this.$stage.on(a.support.transition.end + ".owl.core", a.proxy(this.onTransitionEnd, this)), this.settings.responsive !== !1 && this.on(b, "resize", this._handlers.onThrottledResize), this.settings.mouseDrag && (this.$element.addClass(this.options.dragClass), this.$stage.on("mousedown.owl.core", a.proxy(this.onDragStart, this)), this.$stage.on("dragstart.owl.core selectstart.owl.core", function () {
                return !1
            })), this.settings.touchDrag && (this.$stage.on("touchstart.owl.core", a.proxy(this.onDragStart, this)), this.$stage.on("touchcancel.owl.core", a.proxy(this.onDragEnd, this)))
        }, e.prototype.onDragStart = function (b) {
            var d = null;
            3 !== b.which && (a.support.transform ? (d = this.$stage.css("transform").replace(/.*\(|\)| /g, "").split(","), d = {
                x: d[16 === d.length ? 12 : 4],
                y: d[16 === d.length ? 13 : 5]
            }) : (d = this.$stage.position(), d = {
                x: this.settings.rtl ? d.left + this.$stage.width() - this.width() + this.settings.margin : d.left,
                y: d.top
            }), this.is("animating") && (a.support.transform ? this.animate(d.x) : this.$stage.stop(), this.invalidate("position")), this.$element.toggleClass(this.options.grabClass, "mousedown" === b.type), this.speed(0), this._drag.time = (new Date).getTime(), this._drag.target = a(b.target), this._drag.stage.start = d, this._drag.stage.current = d, this._drag.pointer = this.pointer(b), a(c).on("mouseup.owl.core touchend.owl.core", a.proxy(this.onDragEnd, this)), a(c).one("mousemove.owl.core touchmove.owl.core", a.proxy(function (b) {
                var d = this.difference(this._drag.pointer, this.pointer(b));
                a(c).on("mousemove.owl.core touchmove.owl.core", a.proxy(this.onDragMove, this)), Math.abs(d.x) < Math.abs(d.y) && this.is("valid") || (b.preventDefault(), this.enter("dragging"), this.trigger("drag"))
            }, this)))
        }, e.prototype.onDragMove = function (a) {
            var b = null, c = null, d = null, e = this.difference(this._drag.pointer, this.pointer(a)),
              f = this.difference(this._drag.stage.start, e);
            this.is("dragging") && (a.preventDefault(), this.settings.loop ? (b = this.coordinates(this.minimum()), c = this.coordinates(this.maximum() + 1) - b, f.x = ((f.x - b) % c + c) % c + b) : (b = this.settings.rtl ? this.coordinates(this.maximum()) : this.coordinates(this.minimum()), c = this.settings.rtl ? this.coordinates(this.minimum()) : this.coordinates(this.maximum()), d = this.settings.pullDrag ? -1 * e.x / 5 : 0, f.x = Math.max(Math.min(f.x, b + d), c + d)), this._drag.stage.current = f, this.animate(f.x))
        }, e.prototype.onDragEnd = function (b) {
            var d = this.difference(this._drag.pointer, this.pointer(b)), e = this._drag.stage.current,
              f = d.x > 0 ^ this.settings.rtl ? "left" : "right";
            a(c).off(".owl.core"), this.$element.removeClass(this.options.grabClass), (0 !== d.x && this.is("dragging") || !this.is("valid")) && (this.speed(this.settings.dragEndSpeed || this.settings.smartSpeed), this.current(this.closest(e.x, 0 !== d.x ? f : this._drag.direction)), this.invalidate("position"), this.update(), this._drag.direction = f, (Math.abs(d.x) > 3 || (new Date).getTime() - this._drag.time > 300) && this._drag.target.one("click.owl.core", function () {
                return !1
            })), this.is("dragging") && (this.leave("dragging"), this.trigger("dragged"))
        }, e.prototype.closest = function (b, c) {
            var d = -1, e = 30, f = this.width(), g = this.coordinates();
            return this.settings.freeDrag || a.each(g, a.proxy(function (a, h) {
                return "left" === c && b > h - e && b < h + e ? d = a : "right" === c && b > h - f - e && b < h - f + e ? d = a + 1 : this.op(b, "<", h) && this.op(b, ">", g[a + 1] || h - f) && (d = "left" === c ? a + 1 : a), d === -1
            }, this)), this.settings.loop || (this.op(b, ">", g[this.minimum()]) ? d = b = this.minimum() : this.op(b, "<", g[this.maximum()]) && (d = b = this.maximum())), d
        }, e.prototype.animate = function (b) {
            var c = this.speed() > 0;
            this.is("animating") && this.onTransitionEnd(), c && (this.enter("animating"), this.trigger("translate")), a.support.transform3d && a.support.transition ? this.$stage.css({
                transform: "translate3d(" + b + "px,0px,0px)",
                transition: this.speed() / 1e3 + "s"
            }) : c ? this.$stage.animate({left: b + "px"}, this.speed(), this.settings.fallbackEasing, a.proxy(this.onTransitionEnd, this)) : this.$stage.css({left: b + "px"})
        }, e.prototype.is = function (a) {
            return this._states.current[a] && this._states.current[a] > 0
        }, e.prototype.current = function (a) {
            if (a === d) return this._current;
            if (0 === this._items.length) return d;
            if (a = this.normalize(a), this._current !== a) {
                var b = this.trigger("change", {property: {name: "position", value: a}});
                b.data !== d && (a = this.normalize(b.data)), this._current = a, this.invalidate("position"), this.trigger("changed", {
                    property: {
                        name: "position",
                        value: this._current
                    }
                })
            }
            return this._current
        }, e.prototype.invalidate = function (b) {
            return "string" === a.type(b) && (this._invalidated[b] = !0, this.is("valid") && this.leave("valid")), a.map(this._invalidated, function (a, b) {
                return b
            })
        }, e.prototype.reset = function (a) {
            a = this.normalize(a), a !== d && (this._speed = 0, this._current = a, this.suppress(["translate", "translated"]), this.animate(this.coordinates(a)), this.release(["translate", "translated"]))
        }, e.prototype.normalize = function (a, b) {
            var c = this._items.length, e = b ? 0 : this._clones.length;
            return !this.isNumeric(a) || c < 1 ? a = d : (a < 0 || a >= c + e) && (a = ((a - e / 2) % c + c) % c + e / 2), a
        }, e.prototype.relative = function (a) {
            return a -= this._clones.length / 2, this.normalize(a, !0)
        }, e.prototype.maximum = function (a) {
            var b, c, d, e = this.settings, f = this._coordinates.length;
            if (e.loop) f = this._clones.length / 2 + this._items.length - 1; else if (e.autoWidth || e.merge) {
                for (b = this._items.length, c = this._items[--b].width(), d = this.$element.width(); b-- && (c += this._items[b].width() + this.settings.margin, !(c > d));) ;
                f = b + 1
            } else f = e.center ? this._items.length - 1 : this._items.length - e.items;
            return a && (f -= this._clones.length / 2), Math.max(f, 0)
        }, e.prototype.minimum = function (a) {
            return a ? 0 : this._clones.length / 2
        }, e.prototype.items = function (a) {
            return a === d ? this._items.slice() : (a = this.normalize(a, !0), this._items[a])
        }, e.prototype.mergers = function (a) {
            return a === d ? this._mergers.slice() : (a = this.normalize(a, !0), this._mergers[a])
        }, e.prototype.clones = function (b) {
            var c = this._clones.length / 2, e = c + this._items.length, f = function (a) {
                return a % 2 === 0 ? e + a / 2 : c - (a + 1) / 2
            };
            return b === d ? a.map(this._clones, function (a, b) {
                return f(b)
            }) : a.map(this._clones, function (a, c) {
                return a === b ? f(c) : null
            })
        }, e.prototype.speed = function (a) {
            return a !== d && (this._speed = a), this._speed
        }, e.prototype.coordinates = function (b) {
            var c, e = 1, f = b - 1;
            return b === d ? a.map(this._coordinates, a.proxy(function (a, b) {
                return this.coordinates(b)
            }, this)) : (this.settings.center ? (this.settings.rtl && (e = -1, f = b + 1), c = this._coordinates[b], c += (this.width() - c + (this._coordinates[f] || 0)) / 2 * e) : c = this._coordinates[f] || 0, c = Math.ceil(c))
        }, e.prototype.duration = function (a, b, c) {
            return 0 === c ? 0 : Math.min(Math.max(Math.abs(b - a), 1), 6) * Math.abs(c || this.settings.smartSpeed)
        }, e.prototype.to = function (a, b) {
            var c = this.current(), d = null, e = a - this.relative(c), f = (e > 0) - (e < 0), g = this._items.length,
              h = this.minimum(), i = this.maximum();
            this.settings.loop ? (!this.settings.rewind && Math.abs(e) > g / 2 && (e += f * -1 * g), a = c + e, d = ((a - h) % g + g) % g + h, d !== a && d - e <= i && d - e > 0 && (c = d - e, a = d, this.reset(c))) : this.settings.rewind ? (i += 1, a = (a % i + i) % i) : a = Math.max(h, Math.min(i, a)), this.speed(this.duration(c, a, b)), this.current(a), this.$element.is(":visible") && this.update()
        }, e.prototype.next = function (a) {
            a = a || !1, this.to(this.relative(this.current()) + 1, a)
        }, e.prototype.prev = function (a) {
            a = a || !1, this.to(this.relative(this.current()) - 1, a)
        }, e.prototype.onTransitionEnd = function (a) {
            if (a !== d && (a.stopPropagation(), (a.target || a.srcElement || a.originalTarget) !== this.$stage.get(0))) return !1;
            this.leave("animating"), this.trigger("translated")
        }, e.prototype.viewport = function () {
            var d;
            return this.options.responsiveBaseElement !== b ? d = a(this.options.responsiveBaseElement).width() : b.innerWidth ? d = b.innerWidth : c.documentElement && c.documentElement.clientWidth ? d = c.documentElement.clientWidth : console.warn("Can not detect viewport width."), d
        }, e.prototype.replace = function (b) {
            this.$stage.empty(), this._items = [], b && (b = b instanceof jQuery ? b : a(b)), this.settings.nestedItemSelector && (b = b.find("." + this.settings.nestedItemSelector)), b.filter(function () {
                return 1 === this.nodeType
            }).each(a.proxy(function (a, b) {
                b = this.prepare(b), this.$stage.append(b), this._items.push(b), this._mergers.push(1 * b.find("[data-merge]").addBack("[data-merge]").attr("data-merge") || 1)
            }, this)), this.reset(this.isNumeric(this.settings.startPosition) ? this.settings.startPosition : 0), this.invalidate("items")
        }, e.prototype.add = function (b, c) {
            var e = this.relative(this._current);
            c = c === d ? this._items.length : this.normalize(c, !0), b = b instanceof jQuery ? b : a(b), this.trigger("add", {
                content: b,
                position: c
            }), b = this.prepare(b), 0 === this._items.length || c === this._items.length ? (0 === this._items.length && this.$stage.append(b), 0 !== this._items.length && this._items[c - 1].after(b), this._items.push(b), this._mergers.push(1 * b.find("[data-merge]").addBack("[data-merge]").attr("data-merge") || 1)) : (this._items[c].before(b), this._items.splice(c, 0, b), this._mergers.splice(c, 0, 1 * b.find("[data-merge]").addBack("[data-merge]").attr("data-merge") || 1)), this._items[e] && this.reset(this._items[e].index()), this.invalidate("items"), this.trigger("added", {
                content: b,
                position: c
            })
        }, e.prototype.remove = function (a) {
            a = this.normalize(a, !0), a !== d && (this.trigger("remove", {
                content: this._items[a],
                position: a
            }), this._items[a].remove(), this._items.splice(a, 1), this._mergers.splice(a, 1), this.invalidate("items"), this.trigger("removed", {
                content: null,
                position: a
            }))
        }, e.prototype.preloadAutoWidthImages = function (b) {
            b.each(a.proxy(function (b, c) {
                this.enter("pre-loading"), c = a(c), a(new Image).one("load", a.proxy(function (a) {
                    c.attr("src", a.target.src), c.css("opacity", 1), this.leave("pre-loading"), !this.is("pre-loading") && !this.is("initializing") && this.refresh()
                }, this)).attr("src", c.attr("src") || c.attr("data-src") || c.attr("data-src-retina"))
            }, this))
        }, e.prototype.destroy = function () {
            this.$element.off(".owl.core"), this.$stage.off(".owl.core"), a(c).off(".owl.core"), this.settings.responsive !== !1 && (b.clearTimeout(this.resizeTimer), this.off(b, "resize", this._handlers.onThrottledResize));
            for (var d in this._plugins) this._plugins[d].destroy();
            this.$stage.children(".cloned").remove(), this.$stage.unwrap(), this.$stage.children().contents().unwrap(), this.$stage.children().unwrap(), this.$element.removeClass(this.options.refreshClass).removeClass(this.options.loadingClass).removeClass(this.options.loadedClass).removeClass(this.options.rtlClass).removeClass(this.options.dragClass).removeClass(this.options.grabClass).attr("class", this.$element.attr("class").replace(new RegExp(this.options.responsiveClass + "-\\S+\\s", "g"), "")).removeData("owl.carousel")
        }, e.prototype.op = function (a, b, c) {
            var d = this.settings.rtl;
            switch (b) {
                case"<":
                    return d ? a > c : a < c;
                case">":
                    return d ? a < c : a > c;
                case">=":
                    return d ? a <= c : a >= c;
                case"<=":
                    return d ? a >= c : a <= c
            }
        }, e.prototype.on = function (a, b, c, d) {
            a.addEventListener ? a.addEventListener(b, c, d) : a.attachEvent && a.attachEvent("on" + b, c)
        }, e.prototype.off = function (a, b, c, d) {
            a.removeEventListener ? a.removeEventListener(b, c, d) : a.detachEvent && a.detachEvent("on" + b, c)
        }, e.prototype.trigger = function (b, c, d, f, g) {
            var h = {item: {count: this._items.length, index: this.current()}},
              i = a.camelCase(a.grep(["on", b, d], function (a) {
                  return a
              }).join("-").toLowerCase()),
              j = a.Event([b, "owl", d || "carousel"].join(".").toLowerCase(), a.extend({relatedTarget: this}, h, c));
            return this._supress[b] || (a.each(this._plugins, function (a, b) {
                b.onTrigger && b.onTrigger(j)
            }), this.register({
                type: e.Type.Event,
                name: b
            }), this.$element.trigger(j), this.settings && "function" == typeof this.settings[i] && this.settings[i].call(this, j)), j
        }, e.prototype.enter = function (b) {
            a.each([b].concat(this._states.tags[b] || []), a.proxy(function (a, b) {
                this._states.current[b] === d && (this._states.current[b] = 0), this._states.current[b]++
            }, this))
        }, e.prototype.leave = function (b) {
            a.each([b].concat(this._states.tags[b] || []), a.proxy(function (a, b) {
                this._states.current[b]--
            }, this))
        }, e.prototype.register = function (b) {
            if (b.type === e.Type.Event) {
                if (a.event.special[b.name] || (a.event.special[b.name] = {}), !a.event.special[b.name].owl) {
                    var c = a.event.special[b.name]._default;
                    a.event.special[b.name]._default = function (a) {
                        return !c || !c.apply || a.namespace && a.namespace.indexOf("owl") !== -1 ? a.namespace && a.namespace.indexOf("owl") > -1 : c.apply(this, arguments)
                    }, a.event.special[b.name].owl = !0
                }
            } else b.type === e.Type.State && (this._states.tags[b.name] ? this._states.tags[b.name] = this._states.tags[b.name].concat(b.tags) : this._states.tags[b.name] = b.tags, this._states.tags[b.name] = a.grep(this._states.tags[b.name], a.proxy(function (c, d) {
                return a.inArray(c, this._states.tags[b.name]) === d
            }, this)))
        }, e.prototype.suppress = function (b) {
            a.each(b, a.proxy(function (a, b) {
                this._supress[b] = !0
            }, this))
        }, e.prototype.release = function (b) {
            a.each(b, a.proxy(function (a, b) {
                delete this._supress[b]
            }, this))
        }, e.prototype.pointer = function (a) {
            var c = {x: null, y: null};
            return a = a.originalEvent || a || b.event, a = a.touches && a.touches.length ? a.touches[0] : a.changedTouches && a.changedTouches.length ? a.changedTouches[0] : a, a.pageX ? (c.x = a.pageX, c.y = a.pageY) : (c.x = a.clientX, c.y = a.clientY), c
        }, e.prototype.isNumeric = function (a) {
            return !isNaN(parseFloat(a))
        }, e.prototype.difference = function (a, b) {
            return {x: a.x - b.x, y: a.y - b.y}
        }, a.fn.sbyOwlCarousel = function (b) {
            var c = Array.prototype.slice.call(arguments, 1);
            return this.each(function () {
                var d = a(this), f = d.data("owl.carousel");
                f || (f = new e(this, "object" == typeof b && b), d.data("owl.carousel", f), a.each(["next", "prev", "to", "destroy", "refresh", "replace", "add", "remove"], function (b, c) {
                    f.register({
                        type: e.Type.Event,
                        name: c
                    }), f.$element.on(c + ".owl.carousel.core", a.proxy(function (a) {
                        a.namespace && a.relatedTarget !== this && (this.suppress([c]), f[c].apply(this, [].slice.call(arguments, 1)), this.release([c]))
                    }, f))
                })), "string" == typeof b && "_" !== b.charAt(0) && f[b].apply(f, c)
            })
        }, a.fn.sbyOwlCarousel.Constructor = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        var e = function (b) {
            this._core = b, this._interval = null, this._visible = null, this._handlers = {
                "initialized.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.autoRefresh && this.watch()
                }, this)
            }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers)
        };
        e.Defaults = {autoRefresh: !0, autoRefreshInterval: 500}, e.prototype.watch = function () {
            this._interval || (this._visible = this._core.$element.is(":visible"), this._interval = b.setInterval(a.proxy(this.refresh, this), this._core.settings.autoRefreshInterval))
        }, e.prototype.refresh = function () {
            this._core.$element.is(":visible") !== this._visible && (this._visible = !this._visible, this._core.$element.toggleClass("sby-owl-hidden", !this._visible), this._visible && this._core.invalidate("width") && this._core.refresh())
        }, e.prototype.destroy = function () {
            var a, c;
            b.clearInterval(this._interval);
            for (a in this._handlers) this._core.$element.off(a, this._handlers[a]);
            for (c in Object.getOwnPropertyNames(this)) "function" != typeof this[c] && (this[c] = null)
        }, a.fn.sbyOwlCarousel.Constructor.Plugins.AutoRefresh = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        var e = function (b) {
            this._core = b, this._loaded = [], this._handlers = {
                "initialized.owl.carousel change.owl.carousel resized.owl.carousel": a.proxy(function (b) {
                    if (b.namespace && this._core.settings && this._core.settings.lazyLoad && (b.property && "position" == b.property.name || "initialized" == b.type)) for (var c = this._core.settings, e = c.center && Math.ceil(c.items / 2) || c.items, f = c.center && e * -1 || 0, g = (b.property && b.property.value !== d ? b.property.value : this._core.current()) + f, h = this._core.clones().length, i = a.proxy(function (a, b) {
                        this.load(b)
                    }, this); f++ < e;) this.load(h / 2 + this._core.relative(g)), h && a.each(this._core.clones(this._core.relative(g)), i), g++
                }, this)
            }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers)
        };
        e.Defaults = {lazyLoad: !1}, e.prototype.load = function (c) {
            var d = this._core.$stage.children().eq(c), e = d && d.find(".sby-owl-lazy");
            !e || a.inArray(d.get(0), this._loaded) > -1 || (e.each(a.proxy(function (c, d) {
                var e, f = a(d), g = b.devicePixelRatio > 1 && f.attr("data-src-retina") || f.attr("data-src");
                this._core.trigger("load", {
                    element: f,
                    url: g
                }, "lazy"), f.is("img") ? f.one("load.owl.lazy", a.proxy(function () {
                    f.css("opacity", 1), this._core.trigger("loaded", {element: f, url: g}, "lazy")
                }, this)).attr("src", g) : (e = new Image, e.onload = a.proxy(function () {
                    f.css({
                        "background-image": 'url("' + g + '")',
                        opacity: "1"
                    }), this._core.trigger("loaded", {element: f, url: g}, "lazy")
                }, this), e.src = g)
            }, this)), this._loaded.push(d.get(0)))
        }, e.prototype.destroy = function () {
            var a, b;
            for (a in this.handlers) this._core.$element.off(a, this.handlers[a]);
            for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null)
        }, a.fn.sbyOwlCarousel.Constructor.Plugins.Lazy = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        var e = function (b) {
            this._core = b, this._handlers = {
                "initialized.owl.carousel refreshed.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.autoHeight && this.update()
                }, this), "changed.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.autoHeight && "position" == a.property.name && this.update()
                }, this), "loaded.owl.lazy": a.proxy(function (a) {
                    a.namespace && this._core.settings.autoHeight && a.element.closest("." + this._core.settings.itemClass).index() === this._core.current() && this.update()
                }, this)
            }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers)
        };
        e.Defaults = {autoHeight: !1, autoHeightClass: "sby-owl-height"}, e.prototype.update = function () {
            var b = this._core._current, c = b + this._core.settings.items,
              d = this._core.$stage.children().toArray().slice(b, c), e = [], f = 0;
            a.each(d, function (b, c) {
                e.push(a(c).height())
            }), f = Math.max.apply(null, e), this._core.$stage.parent().height(f).addClass(this._core.settings.autoHeightClass)
        }, e.prototype.destroy = function () {
            var a, b;
            for (a in this._handlers) this._core.$element.off(a, this._handlers[a]);
            for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null)
        }, a.fn.sbyOwlCarousel.Constructor.Plugins.AutoHeight = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        var e = function (b) {
            this._core = b, this._videos = {}, this._playing = null, this._handlers = {
                "initialized.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.register({type: "state", name: "playing", tags: ["interacting"]})
                }, this), "resize.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.video && this.isInFullScreen() && a.preventDefault()
                }, this), "refreshed.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.is("resizing") && this._core.$stage.find(".cloned .sby-owl-video-frame").remove()
                }, this), "changed.owl.carousel": a.proxy(function (a) {
                    a.namespace && "position" === a.property.name && this._playing && this.stop()
                }, this), "prepared.owl.carousel": a.proxy(function (b) {
                    if (b.namespace) {
                        var c = a(b.content).find(".sby-owl-video");
                        c.length && (c.css("display", "none"), this.fetch(c, a(b.content)))
                    }
                }, this)
            }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers), this._core.$element.on("click.owl.video", ".sby-owl-video-play-icon", a.proxy(function (a) {
                this.play(a)
            }, this))
        };
        e.Defaults = {video: !1, videoHeight: !1, videoWidth: !1}, e.prototype.fetch = function (a, b) {
            var c = function () {
                  return a.attr("data-vimeo-id") ? "vimeo" : a.attr("data-vzaar-id") ? "vzaar" : "youtube"
              }(), d = a.attr("data-vimeo-id") || a.attr("data-youtube-id") || a.attr("data-vzaar-id"),
              e = a.attr("data-width") || this._core.settings.videoWidth,
              f = a.attr("data-height") || this._core.settings.videoHeight, g = a.attr("href");
            if (!g) throw new Error("Missing video URL.");
            if (d = g.match(/(http:|https:|)\/\/(player.|www.|app.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com)|vzaar\.com)\/(video\/|videos\/|embed\/|channels\/.+\/|groups\/.+\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/), d[3].indexOf("youtu") > -1) c = "youtube"; else if (d[3].indexOf("vimeo") > -1) c = "vimeo"; else {
                if (!(d[3].indexOf("vzaar") > -1)) throw new Error("Video URL not supported.");
                c = "vzaar"
            }
            d = d[6], this._videos[g] = {
                type: c,
                id: d,
                width: e,
                height: f
            }, b.attr("data-video", g), this.thumbnail(a, this._videos[g])
        }, e.prototype.thumbnail = function (b, c) {
            var d, e, f, g = c.width && c.height ? 'style="width:' + c.width + "px;height:" + c.height + 'px;"' : "",
              h = b.find("img"), i = "src", j = "", k = this._core.settings, l = function (a) {
                  e = '<div class="sby-owl-video-play-icon"></div>', d = k.lazyLoad ? '<div class="sby-owl-video-tn ' + j + '" ' + i + '="' + a + '"></div>' : '<div class="sby-owl-video-tn" style="opacity:1;background-image:url(' + a + ')"></div>', b.after(d), b.after(e)
              };
            if (b.wrap('<div class="sby-owl-video-wrapper"' + g + "></div>"), this._core.settings.lazyLoad && (i = "data-src", j = "sby-owl-lazy"), h.length) return l(h.attr(i)), h.remove(), !1;
            "youtube" === c.type ? (f = "//img.youtube.com/vi/" + c.id + "/hqdefault.jpg", l(f)) : "vimeo" === c.type ? a.ajax({
                type: "GET",
                url: "//vimeo.com/api/v2/video/" + c.id + ".json",
                jsonp: "callback",
                dataType: "jsonp",
                success: function (a) {
                    f = a[0].thumbnail_large, l(f)
                }
            }) : "vzaar" === c.type && a.ajax({
                type: "GET",
                url: "//vzaar.com/api/videos/" + c.id + ".json",
                jsonp: "callback",
                dataType: "jsonp",
                success: function (a) {
                    f = a.framegrab_url, l(f)
                }
            })
        }, e.prototype.stop = function () {
            this._core.trigger("stop", null, "video"), this._playing.find(".sby-owl-video-frame").remove(), this._playing.removeClass("sby-owl-video-playing"), this._playing = null, this._core.leave("playing"), this._core.trigger("stopped", null, "video")
        }, e.prototype.play = function (b) {
            var c, d = a(b.target), e = d.closest("." + this._core.settings.itemClass),
              f = this._videos[e.attr("data-video")], g = f.width || "100%",
              h = f.height || this._core.$stage.height();
            this._playing || (this._core.enter("playing"), this._core.trigger("play", null, "video"), e = this._core.items(this._core.relative(e.index())), this._core.reset(e.index()), "youtube" === f.type ? c = '<iframe width="' + g + '" height="' + h + '" src="//www.youtube.com/embed/' + f.id + "?autoplay=1&rel=0&v=" + f.id + '" frameborder="0" allowfullscreen></iframe>' : "vimeo" === f.type ? c = '<iframe src="//player.vimeo.com/video/' + f.id + '?autoplay=1" width="' + g + '" height="' + h + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>' : "vzaar" === f.type && (c = '<iframe frameborder="0"height="' + h + '"width="' + g + '" allowfullscreen mozallowfullscreen webkitAllowFullScreen src="//view.vzaar.com/' + f.id + '/player?autoplay=true"></iframe>'), a('<div class="sby-owl-video-frame">' + c + "</div>").insertAfter(e.find(".sby-owl-video")), this._playing = e.addClass("sby-owl-video-playing"))
        }, e.prototype.isInFullScreen = function () {
            var b = c.fullscreenElement || c.mozFullScreenElement || c.webkitFullscreenElement;
            return b && a(b).parent().hasClass("sby-owl-video-frame")
        }, e.prototype.destroy = function () {
            var a, b;
            this._core.$element.off("click.owl.video");
            for (a in this._handlers) this._core.$element.off(a, this._handlers[a]);
            for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null)
        }, a.fn.sbyOwlCarousel.Constructor.Plugins.Video = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        var e = function (b) {
            this.core = b, this.core.options = a.extend({}, e.Defaults, this.core.options), this.swapping = !0, this.previous = d, this.next = d, this.handlers = {
                "change.owl.carousel": a.proxy(function (a) {
                    a.namespace && "position" == a.property.name && (this.previous = this.core.current(), this.next = a.property.value)
                }, this), "drag.owl.carousel dragged.owl.carousel translated.owl.carousel": a.proxy(function (a) {
                    a.namespace && (this.swapping = "translated" == a.type)
                }, this), "translate.owl.carousel": a.proxy(function (a) {
                    a.namespace && this.swapping && (this.core.options.animateOut || this.core.options.animateIn) && this.swap()
                }, this)
            }, this.core.$element.on(this.handlers)
        };
        e.Defaults = {animateOut: !1, animateIn: !1}, e.prototype.swap = function () {
            if (1 === this.core.settings.items && a.support.animation && a.support.transition) {
                this.core.speed(0);
                var b, c = a.proxy(this.clear, this), d = this.core.$stage.children().eq(this.previous),
                  e = this.core.$stage.children().eq(this.next), f = this.core.settings.animateIn,
                  g = this.core.settings.animateOut;
                this.core.current() !== this.previous && (g && (b = this.core.coordinates(this.previous) - this.core.coordinates(this.next), d.one(a.support.animation.end, c).css({left: b + "px"}).addClass("animated sby-owl-animated-out").addClass(g)), f && e.one(a.support.animation.end, c).addClass("animated sby-owl-animated-in").addClass(f))
            }
        }, e.prototype.clear = function (b) {
            a(b.target).css({left: ""}).removeClass("animated sby-owl-animated-out sby-owl-animated-in").removeClass(this.core.settings.animateIn).removeClass(this.core.settings.animateOut), this.core.onTransitionEnd()
        }, e.prototype.destroy = function () {
            var a, b;
            for (a in this.handlers) this.core.$element.off(a, this.handlers[a]);
            for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null)
        },
          a.fn.sbyOwlCarousel.Constructor.Plugins.Animate = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        var e = function (b) {
            this._core = b, this._timeout = null, this._paused = !1, this._handlers = {
                "changed.owl.carousel": a.proxy(function (a) {
                    a.namespace && "settings" === a.property.name ? this._core.settings.autoplay ? this.play() : this.stop() : a.namespace && "position" === a.property.name && this._core.settings.autoplay && this._setAutoPlayInterval()
                }, this), "initialized.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.autoplay && this.play()
                }, this), "play.owl.autoplay": a.proxy(function (a, b, c) {
                    a.namespace && this.play(b, c)
                }, this), "stop.owl.autoplay": a.proxy(function (a) {
                    a.namespace && this.stop()
                }, this), "mouseover.owl.autoplay": a.proxy(function () {
                    this._core.settings.autoplayHoverPause && this._core.is("rotating") && this.pause()
                }, this), "mouseleave.owl.autoplay": a.proxy(function () {
                    this._core.settings.autoplayHoverPause && this._core.is("rotating") && this.play()
                }, this), "touchstart.owl.core": a.proxy(function () {
                    this._core.settings.autoplayHoverPause && this._core.is("rotating") && this.pause()
                }, this), "touchend.owl.core": a.proxy(function () {
                    this._core.settings.autoplayHoverPause && this.play()
                }, this)
            }, this._core.$element.on(this._handlers), this._core.options = a.extend({}, e.Defaults, this._core.options)
        };
        e.Defaults = {
            autoplay: !1,
            autoplayTimeout: 5e3,
            autoplayHoverPause: !1,
            autoplaySpeed: !1
        }, e.prototype.play = function (a, b) {
            this._paused = !1, this._core.is("rotating") || (this._core.enter("rotating"), this._setAutoPlayInterval())
        }, e.prototype._getNextTimeout = function (d, e) {
            return this._timeout && b.clearTimeout(this._timeout), b.setTimeout(a.proxy(function () {
                this._paused || this._core.is("busy") || this._core.is("interacting") || c.hidden || this._core.next(e || this._core.settings.autoplaySpeed)
            }, this), d || this._core.settings.autoplayTimeout)
        }, e.prototype._setAutoPlayInterval = function () {
            this._timeout = this._getNextTimeout()
        }, e.prototype.stop = function () {
            this._core.is("rotating") && (b.clearTimeout(this._timeout), this._core.leave("rotating"))
        }, e.prototype.pause = function () {
            this._core.is("rotating") && (this._paused = !0)
        }, e.prototype.destroy = function () {
            var a, b;
            this.stop();
            for (a in this._handlers) this._core.$element.off(a, this._handlers[a]);
            for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null)
        }, a.fn.sbyOwlCarousel.Constructor.Plugins.autoplay = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        "use strict";
        var e = function (b) {
            this._core = b, this._initialized = !1, this._pages = [], this._controls = {}, this._templates = [], this.$element = this._core.$element, this._overrides = {
                next: this._core.next,
                prev: this._core.prev,
                to: this._core.to
            }, this._handlers = {
                "prepared.owl.carousel": a.proxy(function (b) {
                    b.namespace && this._core.settings.dotsData && this._templates.push('<div class="' + this._core.settings.dotClass + '">' + a(b.content).find("[data-dot]").addBack("[data-dot]").attr("data-dot") + "</div>")
                }, this), "added.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.dotsData && this._templates.splice(a.position, 0, this._templates.pop())
                }, this), "remove.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.dotsData && this._templates.splice(a.position, 1)
                }, this), "changed.owl.carousel": a.proxy(function (a) {
                    a.namespace && "position" == a.property.name && this.draw()
                }, this), "initialized.owl.carousel": a.proxy(function (a) {
                    a.namespace && !this._initialized && (this._core.trigger("initialize", null, "navigation"), this.initialize(), this.update(), this.draw(), this._initialized = !0, this._core.trigger("initialized", null, "navigation"))
                }, this), "refreshed.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._initialized && (this._core.trigger("refresh", null, "navigation"), this.update(), this.draw(), this._core.trigger("refreshed", null, "navigation"))
                }, this)
            }, this._core.options = a.extend({}, e.Defaults, this._core.options), this.$element.on(this._handlers)
        };
        e.Defaults = {
            nav: !1,
            navText: ["prev", "next"],
            navSpeed: !1,
            navElement: "div",
            navContainer: !1,
            navContainerClass: "sby-owl-nav",
            navClass: ["sby-owl-prev", "sby-owl-next"],
            slideBy: 1,
            dotClass: "sby-owl-dot",
            dotsClass: "sby-owl-dots",
            dots: !0,
            dotsEach: !1,
            dotsData: !1,
            dotsSpeed: !1,
            dotsContainer: !1
        }, e.prototype.initialize = function () {
            var b, c = this._core.settings;
            this._controls.$relative = (c.navContainer ? a(c.navContainer) : a("<div>").addClass(c.navContainerClass).appendTo(this.$element)).addClass("disabled"), this._controls.$previous = a("<" + c.navElement + ">").addClass(c.navClass[0]).html(c.navText[0]).prependTo(this._controls.$relative).on("click", a.proxy(function (a) {
                this.prev(c.navSpeed)
            }, this)), this._controls.$next = a("<" + c.navElement + ">").addClass(c.navClass[1]).html(c.navText[1]).appendTo(this._controls.$relative).on("click", a.proxy(function (a) {
                this.next(c.navSpeed)
            }, this)), c.dotsData || (this._templates = [a("<div>").addClass(c.dotClass).append(a("<span>")).prop("outerHTML")]), this._controls.$absolute = (c.dotsContainer ? a(c.dotsContainer) : a("<div>").addClass(c.dotsClass).appendTo(this.$element)).addClass("disabled"), this._controls.$absolute.on("click", "div", a.proxy(function (b) {
                var d = a(b.target).parent().is(this._controls.$absolute) ? a(b.target).index() : a(b.target).parent().index();
                b.preventDefault(), this.to(d, c.dotsSpeed)
            }, this));
            for (b in this._overrides) this._core[b] = a.proxy(this[b], this)
        }, e.prototype.destroy = function () {
            var a, b, c, d;
            for (a in this._handlers) this.$element.off(a, this._handlers[a]);
            for (b in this._controls) this._controls[b].remove();
            for (d in this.overides) this._core[d] = this._overrides[d];
            for (c in Object.getOwnPropertyNames(this)) "function" != typeof this[c] && (this[c] = null)
        }, e.prototype.update = function () {
            var a, b, c, d = this._core.clones().length / 2, e = d + this._core.items().length,
              f = this._core.maximum(!0), g = this._core.settings,
              h = g.center || g.autoWidth || g.dotsData ? 1 : g.dotsEach || g.items;
            if ("page" !== g.slideBy && (g.slideBy = Math.min(g.slideBy, g.items)), g.dots || "page" == g.slideBy) for (this._pages = [], a = d, b = 0, c = 0; a < e; a++) {
                if (b >= h || 0 === b) {
                    if (this._pages.push({
                        start: Math.min(f, a - d),
                        end: a - d + h - 1
                    }), Math.min(f, a - d) === f) break;
                    b = 0, ++c
                }
                b += this._core.mergers(this._core.relative(a))
            }
        }, e.prototype.draw = function () {
            var b, c = this._core.settings, d = this._core.items().length <= c.items,
              e = this._core.relative(this._core.current()), f = c.loop || c.rewind;
            this._controls.$relative.toggleClass("disabled", !c.nav || d), c.nav && (this._controls.$previous.toggleClass("disabled", !f && e <= this._core.minimum(!0)), this._controls.$next.toggleClass("disabled", !f && e >= this._core.maximum(!0))), this._controls.$absolute.toggleClass("disabled", !c.dots || d), c.dots && (b = this._pages.length - this._controls.$absolute.children().length, c.dotsData && 0 !== b ? this._controls.$absolute.html(this._templates.join("")) : b > 0 ? this._controls.$absolute.append(new Array(b + 1).join(this._templates[0])) : b < 0 && this._controls.$absolute.children().slice(b).remove(), this._controls.$absolute.find(".active").removeClass("active"), this._controls.$absolute.children().eq(a.inArray(this.current(), this._pages)).addClass("active"))
        }, e.prototype.onTrigger = function (b) {
            var c = this._core.settings;
            b.page = {
                index: a.inArray(this.current(), this._pages),
                count: this._pages.length,
                size: c && (c.center || c.autoWidth || c.dotsData ? 1 : c.dotsEach || c.items)
            }
        }, e.prototype.current = function () {
            var b = this._core.relative(this._core.current());
            return a.grep(this._pages, a.proxy(function (a, c) {
                return a.start <= b && a.end >= b
            }, this)).pop()
        }, e.prototype.getPosition = function (b) {
            var c, d, e = this._core.settings;
            return "page" == e.slideBy ? (c = a.inArray(this.current(), this._pages), d = this._pages.length, b ? ++c : --c, c = this._pages[(c % d + d) % d].start) : (c = this._core.relative(this._core.current()), d = this._core.items().length, b ? c += e.slideBy : c -= e.slideBy), c
        }, e.prototype.next = function (b) {
            a.proxy(this._overrides.to, this._core)(this.getPosition(!0), b)
        }, e.prototype.prev = function (b) {
            a.proxy(this._overrides.to, this._core)(this.getPosition(!1), b)
        }, e.prototype.to = function (b, c, d) {
            var e;
            !d && this._pages.length ? (e = this._pages.length, a.proxy(this._overrides.to, this._core)(this._pages[(b % e + e) % e].start, c)) : a.proxy(this._overrides.to, this._core)(b, c)
        }, a.fn.sbyOwlCarousel.Constructor.Plugins.Navigation = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        "use strict";
        var e = function (c) {
            this._core = c, this._hashes = {}, this.$element = this._core.$element, this._handlers = {
                "initialized.owl.carousel": a.proxy(function (c) {
                    c.namespace && "URLHash" === this._core.settings.startPosition && a(b).trigger("hashchange.owl.navigation")
                }, this), "prepared.owl.carousel": a.proxy(function (b) {
                    if (b.namespace) {
                        var c = a(b.content).find("[data-hash]").addBack("[data-hash]").attr("data-hash");
                        if (!c) return;
                        this._hashes[c] = b.content
                    }
                }, this), "changed.owl.carousel": a.proxy(function (c) {
                    if (c.namespace && "position" === c.property.name) {
                        var d = this._core.items(this._core.relative(this._core.current())),
                          e = a.map(this._hashes, function (a, b) {
                              return a === d ? b : null
                          }).join();
                        if (!e || b.location.hash.slice(1) === e) return;
                        b.location.hash = e
                    }
                }, this)
            }, this._core.options = a.extend({}, e.Defaults, this._core.options), this.$element.on(this._handlers), a(b).on("hashchange.owl.navigation", a.proxy(function (a) {
                var c = b.location.hash.substring(1), e = this._core.$stage.children(),
                  f = this._hashes[c] && e.index(this._hashes[c]);
                f !== d && f !== this._core.current() && this._core.to(this._core.relative(f), !1, !0)
            }, this))
        };
        e.Defaults = {URLhashListener: !1}, e.prototype.destroy = function () {
            var c, d;
            a(b).off("hashchange.owl.navigation");
            for (c in this._handlers) this._core.$element.off(c, this._handlers[c]);
            for (d in Object.getOwnPropertyNames(this)) "function" != typeof this[d] && (this[d] = null)
        }, a.fn.sbyOwlCarousel.Constructor.Plugins.Hash = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        function e(b, c) {
            var e = !1, f = b.charAt(0).toUpperCase() + b.slice(1);
            return a.each((b + " " + h.join(f + " ") + f).split(" "), function (a, b) {
                if (g[b] !== d) return e = !c || b, !1
            }), e
        }

        function f(a) {
            return e(a, !0)
        }

        var g = a("<support>").get(0).style, h = "Webkit Moz O ms".split(" "), i = {
            transition: {
                end: {
                    WebkitTransition: "webkitTransitionEnd",
                    MozTransition: "transitionend",
                    OTransition: "oTransitionEnd",
                    transition: "transitionend"
                }
            },
            animation: {
                end: {
                    WebkitAnimation: "webkitAnimationEnd",
                    MozAnimation: "animationend",
                    OAnimation: "oAnimationEnd",
                    animation: "animationend"
                }
            }
        }, j = {
            csstransforms: function () {
                return !!e("transform")
            }, csstransforms3d: function () {
                return !!e("perspective")
            }, csstransitions: function () {
                return !!e("transition")
            }, cssanimations: function () {
                return !!e("animation")
            }
        };
        j.csstransitions() && (a.support.transition = new String(f("transition")), a.support.transition.end = i.transition.end[a.support.transition]), j.cssanimations() && (a.support.animation = new String(f("animation")), a.support.animation.end = i.animation.end[a.support.animation]), j.csstransforms() && (a.support.transform = new String(f("transform")), a.support.transform3d = j.csstransforms3d())
    }(window.Zepto || window.jQuery, window, document);

    // Two Row Carousel
    ;(function ($, window, document, undefined) {
        Owl2row = function (scope) {
            this.owl = scope;
            this.owl.options = $.extend({}, Owl2row.Defaults, this.owl.options);
            //link callback events with owl carousel here

            this.handlers = {
                'initialize.owl.carousel': $.proxy(function (e) {
                    if (this.owl.settings.owl2row) {
                        this.build2row(this);
                    }
                }, this)
            };

            this.owl.$element.on(this.handlers);
        };

        Owl2row.Defaults = {
            owl2row: false,
            owl2rowTarget: 'sby_item',
            owl2rowContainer: 'sby_owl2row-item',
            owl2rowDirection: 'utd' // ltr
        };

        //mehtods:
        Owl2row.prototype.build2row = function (thisScope) {

            var carousel = $(thisScope.owl.$element);
            var carouselItems = carousel.find('.' + thisScope.owl.options.owl2rowTarget);

            var aEvenElements = [];
            var aOddElements = [];

            $.each(carouselItems, function (index, item) {
                if (index % 2 === 0) {
                    aEvenElements.push(item);
                } else {
                    aOddElements.push(item);
                }
            });

            //carousel.empty();

            switch (thisScope.owl.options.owl2rowDirection) {
                case 'ltr':
                    thisScope.leftToright(thisScope, carousel, carouselItems);
                    break;

                default :
                    thisScope.upTodown(thisScope, aEvenElements, aOddElements, carousel);
            }

        };

        Owl2row.prototype.leftToright = function (thisScope, carousel, carouselItems) {

            var o2wContainerClass = thisScope.owl.options.owl2rowContainer;
            var owlMargin = thisScope.owl.options.margin;
            var carouselItemsLength = carouselItems.length;
            var firsArr = [];
            var secondArr = [];

            if (carouselItemsLength % 2 === 1) {
                carouselItemsLength = ((carouselItemsLength - 1) / 2) + 1;
            } else {
                carouselItemsLength = carouselItemsLength / 2;
            }

            $.each(carouselItems, function (index, item) {


                if (index < carouselItemsLength) {
                    firsArr.push(item);
                } else {
                    secondArr.push(item);
                }
            });

            $.each(firsArr, function (index, item) {
                var rowContainer = $('<div class="' + o2wContainerClass + '"/>');

                var firstRowElement = firsArr[index];
                firstRowElement.style.marginBottom = owlMargin + 'px';

                rowContainer
                  .append(firstRowElement)
                  .append(secondArr[index]);

                carousel.append(rowContainer);
            });

        };

        Owl2row.prototype.upTodown = function (thisScope, aEvenElements, aOddElements, carousel) {

            var o2wContainerClass = thisScope.owl.options.owl2rowContainer;
            var owlMargin = thisScope.owl.options.margin;

            $.each(aEvenElements, function (index, item) {

                var rowContainer = $('<div class="' + o2wContainerClass + '"/>');
                var evenElement = aEvenElements[index];

                evenElement.style.marginBottom = owlMargin + 'px';

                rowContainer
                  .append(evenElement)
                  .append(aOddElements[index]);

                carousel.append(rowContainer);
            });
        };

        /**
         * Destroys the plugin.
         */
        Owl2row.prototype.destroy = function () {
            var handler, property;
        };

        $.fn.sbyOwlCarousel.Constructor.Plugins['owl2row'] = Owl2row;
    })(window.Zepto || window.jQuery, window, document);

    (function($){

        function sbyAddVisibilityListener() {
            /* Detect when element becomes visible. Used for when the feed is initially hidden, in a tab for example. https://github.com/shaunbowe/jquery.visibilityChanged */
            !function (i) {
                var n = {
                    callback: function () {
                    }, runOnLoad: !0, frequency: 100, sbyPreviousVisibility: null
                }, c = {};
                c.sbyCheckVisibility = function (i, n) {
                    if (jQuery.contains(document, i[0])) {
                        var e = n.sbyPreviousVisibility, t = i.is(":visible");
                        n.sbyPreviousVisibility = t, null == e ? n.runOnLoad && n.callback(i, t) : e !== t && n.callback(i, t), setTimeout(function () {
                            c.sbyCheckVisibility(i, n)
                        }, n.frequency)
                    }
                }, i.fn.sbyVisibilityChanged = function (e) {
                    var t = i.extend({}, n, e);
                    return this.each(function () {
                        c.sbyCheckVisibility(i(this), t)
                    })
                }
            }(jQuery);
        }

        function Sby() {
            this.feeds = {};
            this.ctas = {};
            this.options = sbyOptions;
            this.isTouch = sbyIsTouch();
        }

        Sby.prototype = {
            createPage: function (createFeeds, createFeedsArgs) {
                if (typeof window.sbyajaxurl === 'undefined' || window.sbyajaxurl.indexOf(window.location.hostname) === -1) {
                    window.sbyajaxurl = window.location.hostname + '/wp-admin/admin-ajax.php';
                }

                $('.sby_no_js_error_message').remove();
                $('.sby_no_js').removeClass('sby_no_js');

                createFeeds(createFeedsArgs);
            },
            maybeAddYTAPI: function() {
                var youtubeScriptId = "sby-youtube-api";
                var youtubeScript = document.getElementById(youtubeScriptId);

                if (youtubeScript === null) {
                    var tag = document.createElement("script");
                    var firstScript = document.getElementsByTagName("script")[0];

                    tag.src = "https://www.youtube.com/iframe_api";
                    tag.id = youtubeScriptId;
                    firstScript.parentNode.insertBefore(tag, firstScript);

                }
            },
            createLightbox: function() {
                var lbBuilder = sbyGetlightboxBuilder();
                var sby_lb_delay = (function () {
                    var sby_timer = 0;
                    return function (sby_callback, sby_ms) {
                        clearTimeout(sby_timer);
                        sby_timer = setTimeout(sby_callback, sby_ms);
                    };
                })();
                jQuery(window).on('resize',function () {
                    sby_lb_delay(function () {
                        lbBuilder.afterResize();
                    }, 200);
                });
                /* Lightbox v2.7.1 by Lokesh Dhakar - http://lokeshdhakar.com/projects/lightbox2/ - Heavily modified specifically for this plugin */
                (function() {
                    var a = jQuery,
                      b = function() {
                          function a() {
                              this.fadeDuration = 500, this.fitImagesInViewport = !0, this.resizeDuration = 700, this.positionFromTop = 50, this.showImageNumberLabel = !0, this.alwaysShowNavOnTouchDevices = !1, this.wrapAround = !1
                          }
                          return a.prototype.albumLabel = function(a, b) {
                              return a + " / " + b
                          }, a
                      }(),
                      c = function() {
                          function b(a) {
                              this.options = a, this.album = [], this.currentImageIndex = void 0, this.init()
                          }
                          return b.prototype.init = function() {
                              this.enable(), this.build()
                          }, b.prototype.enable = function() {
                              var b = this;
                              a("body").on("click", "a[data-sby-lightbox]", function(c) {
                                  return b.start(a(c.currentTarget)), !1
                              })
                          }, b.prototype.build = function() {
                              var b = this;
                              a(""+
                                lbBuilder.template()).appendTo(a("body")), this.$lightbox = a("#sby_lightbox"), this.$overlay = a("#sby_lightboxOverlay"), this.$outerContainer = this.$lightbox.find(".sby_lb-outerContainer"), this.$container = this.$lightbox.find(".sby_lb-container"), this.containerTopPadding = parseInt(this.$container.css("padding-top"), 10), this.containerRightPadding = parseInt(this.$container.css("padding-right"), 10), this.containerBottomPadding = parseInt(this.$container.css("padding-bottom"), 10), this.containerLeftPadding = parseInt(this.$container.css("padding-left"), 10), this.$overlay.hide().on("click", function() {
                                  return b.end(), !1
                              }), jQuery(document).on('click', function(event, b, c) {
                                  //Fade out the lightbox if click anywhere outside of the two elements defined below
                                  if (!jQuery(event.target).closest('.sby_lb-outerContainer').length) {
                                      if (!jQuery(event.target).closest('.sby_lb-dataContainer').length) {
                                          //Fade out lightbox
                                          lightboxOnClose();
                                          lbBuilder.pausePlayer();
                                          jQuery('#sby_lightboxOverlay, #sby_lightbox').fadeOut();
                                      }
                                  }
                              }), this.$lightbox.hide(),
                                jQuery('#sby_lightboxOverlay').on("click", function(c) {
                                    lbBuilder.pausePlayer();
                                    jQuery('.sby_gdpr_notice').remove();
                                    return "sby_lightbox" === a(c.target).attr("id") && b.end(), !1
                                }), this.$lightbox.find(".sby_lb-prev").on("click", function() {
                                  lbBuilder.pausePlayer();
                                  jQuery('.sby_gdpr_notice').remove();

                                  return b.changeImage(0 === b.currentImageIndex ? b.album.length - 1 : b.currentImageIndex - 1), !1
                              }), this.$lightbox.find(".sby_lb-container").on("swiperight", function() {
                                  lbBuilder.pausePlayer();
                                  jQuery('.sby_gdpr_notice').remove();

                                  return b.changeImage(0 === b.currentImageIndex ? b.album.length - 1 : b.currentImageIndex - 1), !1
                              }), this.$lightbox.find(".sby_lb-next").on("click", function() {
                                  lbBuilder.pausePlayer();
                                  jQuery('.sby_gdpr_notice').remove();

                                  return b.changeImage(b.currentImageIndex === b.album.length - 1 ? 0 : b.currentImageIndex + 1), !1
                              }), this.$lightbox.find(".sby_lb-container").on("swipeleft", function() {
                                  lbBuilder.pausePlayer();
                                  jQuery('.sby_gdpr_notice').remove();

                                  return b.changeImage(b.currentImageIndex === b.album.length - 1 ? 0 : b.currentImageIndex + 1), !1
                              }), 
                              this.$lightbox.find(".sby_lb-loader, .sby_lb-close").on("click", function() {
                                    lightboxOnClose();
                                    lbBuilder.pausePlayer();
                                  return b.end(), !1
                              })
                          }, b.prototype.start = function(b) {
                              function c(a) {
                                  d.album.push(lbBuilder.getData(a))
                              }
                              var d = this,
                                e = a(window);
                              e.on("resize", a.proxy(this.sizeOverlay, this)), a("select, object, embed").css({
                                  visibility: "hidden"
                              }), this.sizeOverlay(), this.album = [];
                              var f, g = 0,
                                h = b.attr("data-sby-lightbox");
                              if (h) {
                                  f = a(b.prop("tagName") + '[data-sby-lightbox="' + h + '"]');
                                  for (var i = 0; i < f.length; i = ++i) c(a(f[i])), f[i] === b[0] && (g = i)
                              } else if ("lightbox" === b.attr("rel")) c(b);
                              else {
                                  f = a(b.prop("tagName") + '[rel="' + b.attr("rel") + '"]');
                                  for (var j = 0; j < f.length; j = ++j) c(a(f[j])), f[j] === b[0] && (g = j)
                              }
                              var k = e.scrollTop() + this.options.positionFromTop - 50,
                                l = e.scrollLeft();
                              this.$lightbox.css({
                                  top: k + "px",
                                  left: l + "px"
                              }).fadeIn(this.options.fadeDuration), this.changeImage(g)
                          }, b.prototype.changeImage = function(b) {
                              var c = this;
                              this.disableKeyboardNav();
                              var d = this.$lightbox.find(".sby_lb-image");
                              this.$overlay.fadeIn(this.options.fadeDuration), a(".sby_lb-loader").fadeIn("slow"), this.$lightbox.find(".sby_lb-image, .sby_lb-nav, .sby_lb-prev, .sby_lb-next, .sby_lb-dataContainer, .sby_lb-numbers, .sby_lb-caption").hide(), this.$outerContainer.addClass("animating");
                              var e = new Image;
                              e.onload = function() {
                                  var f, g, h, i, j, k, l;
                                  var sbyArrowWidth = 100;
                                  d.attr("src", c.album[b].link), f = a(e), d.width(e.width), d.height(e.height), c.options.fitImagesInViewport && (l = a(window).width(), k = a(window).height(), j = l - c.containerLeftPadding - c.containerRightPadding - 20 - sbyArrowWidth, i = k - c.containerTopPadding - c.containerBottomPadding - 150, (e.width > j || e.height > i) && (e.width / j > e.height / i ? (h = j, g = parseInt(e.height / (e.width / h), 10), d.width(h), d.height(g)) : (g = i, h = parseInt(e.width / (e.height / g), 10), d.width(h), d.height(g)))), c.sizeContainer(d.width(), d.height())
                              }, e.src = this.album[b].link, this.currentImageIndex = b
                          }, b.prototype.sizeOverlay = function() {
                              this.$overlay.width(a(window).width()).height(a(document).height())
                          }, b.prototype.sizeContainer = function(a, b) {
                              function c() {
                                  d.$lightbox.find(".sby_lb-dataContainer").width(g), d.$lightbox.find(".sby_lb-prevLink").height(h), d.$lightbox.find(".sby_lb-nextLink").height(h), d.showImage()
                              }
                              var d = this,
                                e = this.$outerContainer.outerWidth(),
                                f = this.$outerContainer.outerHeight(),
                                g = a + this.containerLeftPadding + this.containerRightPadding,
                                h = b + this.containerTopPadding + this.containerBottomPadding;
                              e !== g || f !== h ? this.$outerContainer.animate({
                                  width: g,
                                  height: h
                              }, this.options.resizeDuration, "swing", function() {
                                  c()
                              }) : c()
                          }, b.prototype.showImage = function() {
                              this.$lightbox.find(".sby_lb-loader").hide(), this.$lightbox.find(".sby_lb-image").fadeIn("slow"), this.updateNav(), this.updateDetails(), this.preloadNeighboringImages(), this.enableKeyboardNav()
                          }, b.prototype.updateNav = function() {
                              var a = !1;
                              try {
                                  document.createEvent("TouchEvent"), a = this.options.alwaysShowNavOnTouchDevices ? !0 : !1
                              } catch (b) {}
                              this.$lightbox.find(".sby_lb-nav").show(), this.album.length > 1 && (this.options.wrapAround ? (a && this.$lightbox.find(".sby_lb-prev, .sby_lb-next").css("opacity", "1"), this.$lightbox.find(".sby_lb-prev, .sby_lb-next").show()) : (this.currentImageIndex > 0 && (this.$lightbox.find(".sby_lb-prev").show(), a && this.$lightbox.find(".sby_lb-prev").css("opacity", "1")), this.currentImageIndex < this.album.length - 1 && (this.$lightbox.find(".sby_lb-next").show(), a && this.$lightbox.find(".sby_lb-next").css("opacity", "1"))))
                          }, b.prototype.updateDetails = function() {
                              var b = this;

                              /** NEW PHOTO ACTION **/
                              if(jQuery('iframe.sby_lb-player-loaded').length) {
                                  jQuery('.sby_lb-player-placeholder').replaceWith(jQuery('iframe.sby_lb-player-loaded'));
                                  jQuery('iframe.sby_lb-player-loaded').removeClass('sby_lb-player-loaded').show();
                              }
                              //Switch video when either a new popup or navigating to new one
                              var feed = window.sby.feeds[this.album[this.currentImageIndex].feedIndex];
                              lbBuilder.beforePlayerSetup(this.$lightbox,this.album[this.currentImageIndex],this.currentImageIndex,this.album,feed);

                              if( sby_supports_video() ){
                                  jQuery('#sby_lightbox').removeClass('sby_video_lightbox');
                                  if (feed.settings.consentGiven && this.album[this.currentImageIndex].video.length){
                                      jQuery('.sby_gdpr_notice').remove();

                                      var playerID = 'sby_lb-player';
                                      jQuery('#sby_lightbox').addClass('sby_video_lightbox');
                                      if ( ! window.sbyOptions.isPro ) {
                                          jQuery('#sby_lightbox').addClass('sby_lightbox_free');
                                      }

                                      var videoID = this.album[this.currentImageIndex].video,
                                        autoplay = sbyOptions.autoplay;
                                      if (typeof window.sbyLightboxPlayer === 'undefined') {
                                          var args = {
                                              host: window.location.protocol + feed.embedURL,
                                              videoId: videoID,
                                              playerVars: {
                                                  modestbranding: 1,
                                                  rel: 0,
                                                  autoplay: autoplay
                                              },
                                              events: {
                                                  'onStateChange': function(data) {
                                                      var videoID = data.target.getVideoData()['video_id'];
                                                      feed.afterStateChange(playerID,videoID,data,$('#' + playerID).closest('.sby_video_thumbnail_wrap'));
                                                  }
                                              }
                                          };
                                          feed.maybeAddCTA(playerID);

                                          window.sbyLightboxPlayer = new window.YT.Player(playerID, args);
                                      } else {
                                          window.sbyLightboxPlayer.loadVideoById(videoID);
                                      }

                                      this.$outerContainer.removeClass("animating");
                                      this.$lightbox.find(".sby_lb-dataContainer").fadeIn(this.options.resizeDuration, function() {
                                          return b.sizeOverlay()
                                      });

                                      setTimeout(function() {
                                          $('#sby_lightbox .sby_lb-player').css({
                                              'height' : $('#sby_lightbox .sby_lb-outerContainer').height()+'px',
                                              'width' : $('#sby_lightbox .sby_lb-outerContainer').width()+'px',
                                              'top': 0
                                          });
                                      },1);

                                      if (this.$lightbox.find('iframe').length) {
                                          this.$lightbox.find('iframe').attr('title',this.album[this.currentImageIndex].videoTitle);
                                      }


                                  } else {
                                      var fullImage = $('.sby_item[data-video-id=' + this.album[this.currentImageIndex].video+']').find('.sby_video_thumbnail').attr('data-full-res');
                                      $('.sby_lb-image').attr('src',fullImage);
                                      this.$outerContainer.removeClass("animating");
                                      this.$lightbox.find(".sby_lb-dataContainer").fadeIn(this.options.resizeDuration, function() {
                                          return b.sizeOverlay()
                                      });
                                      jQuery(".sby_lb-container").prepend('<a href="https://www.youtube.com/watch?v='+this.album[this.currentImageIndex].video+'" target="_blank" rel="noopener noreferrer" class="sby_gdpr_notice"><svg style="color: rgba(255,255,255,1)" class="svg-inline--fa fa-play fa-w-14 sby_playbtn" aria-label="Play" aria-hidden="true" data-fa-processed="" data-prefix="fa" data-icon="play" role="presentation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"></path></svg></a>');
                                  }
                                  lbBuilder.afterPlayerSetup(this.$lightbox,this.album[this.currentImageIndex],this.currentImageIndex,this.album);

                                  if (this.album.length > 1 && this.options.showImageNumberLabel) {
                                      this.$lightbox.find(".sby_lb-number").text(this.options.albumLabel(this.currentImageIndex + 1, this.album.length)).fadeIn("fast");
                                  } else {
                                      this.$lightbox.find(".sby_lb-number").hide();
                                  }

                              }
                          }, b.prototype.preloadNeighboringImages = function() {
                              if (this.album.length > this.currentImageIndex + 1) {
                                  var a = new Image;
                                  a.src = this.album[this.currentImageIndex + 1].link
                              }
                              if (this.currentImageIndex > 0) {
                                  var b = new Image;
                                  b.src = this.album[this.currentImageIndex - 1].link
                              }
                          }, b.prototype.enableKeyboardNav = function() {
                              a(document).on("keyup.keyboard", a.proxy(this.keyboardAction, this))
                          }, b.prototype.disableKeyboardNav = function() {
                              a(document).off(".keyboard")
                          }, b.prototype.keyboardAction = function(a) {

                              var KEYCODE_ESC        = 27;
                              var KEYCODE_LEFTARROW  = 37;
                              var KEYCODE_RIGHTARROW = 39;

                              var keycode = event.keyCode;
                              var key     = String.fromCharCode(keycode).toLowerCase();
                              if (keycode === KEYCODE_ESC || key.match(/x|o|c/)) {
                                  if( sby_supports_video() ) $('#sby_lightbox video.sby_video')[0].pause();
                                  $('#sby_lightbox iframe').attr('src', '');
                                  this.end();
                              } else if (key === 'p' || keycode === KEYCODE_LEFTARROW) {
                                  if (this.currentImageIndex !== 0) {
                                      this.changeImage(this.currentImageIndex - 1);
                                  } else if (this.options.wrapAround && this.album.length > 1) {
                                      this.changeImage(this.album.length - 1);
                                  }

                                  if( sby_supports_video() ) $('#sby_lightbox video.sby_video')[0].pause();
                                  $('#sby_lightbox iframe').attr('src', '');

                              } else if (key === 'n' || keycode === KEYCODE_RIGHTARROW) {
                                  if (this.currentImageIndex !== this.album.length - 1) {
                                      this.changeImage(this.currentImageIndex + 1);
                                  } else if (this.options.wrapAround && this.album.length > 1) {
                                      this.changeImage(0);
                                  }
                                  lbBuilder.pausePlayer();
                              }

                          }, b.prototype.end = function() {
                              this.disableKeyboardNav(), a(window).off("resize", this.sizeOverlay), this.$lightbox.fadeOut(this.options.fadeDuration), this.$overlay.fadeOut(this.options.fadeDuration), a("select, object, embed").css({
                                  visibility: "visible"
                              })
                          }, b
                      }();
                    a(function() {
                        {
                            var a = new b;
                            new c(a);
                            //Lightbox hide photo function
                            $('.sby_lightbox_action a').off().on('click', function(){
                                $(this).parent().find('.sby_lightbox_tooltip').toggle();
                            });
                        }
                    })
                }).call(this);
                window.sbyOptions.lightboxCreated = true;
            },
            createFeeds: function (args) {
                if ( !sbyOptions.isAdmin && sbyOptions.lightboxCreated === undefined ) {
                    window.sby.createLightbox();
                }
                args.whenFeedsCreated(
                  $('.sb_youtube').each(function (index) {
                      $(this).attr('data-sby-index', index + 1);
                      $(this).find('.sby_player').replaceWith('<div id="sby_player'+index+'"></div>');
                      var $self = $(this),
                        flags = typeof $self.attr('data-sby-flags') !== 'undefined' ? $self.attr('data-sby-flags').split(',') : [],
                        general = typeof $self.attr('data-options') !== 'undefined' ? JSON.parse($self.attr('data-options')) : {};
                      if (flags.indexOf('testAjax') > -1) {
                          window.sby.triggeredTest = true;
                          var submitData = {
                                'action' : 'sby_on_ajax_test_trigger'
                            },
                            onSuccess = function(data) {
                                console.log('did test');
                            };
                          sbyAjax(submitData,onSuccess)
                      }
                      var feedOptions = {
                          cols : $self.attr('data-cols'),
                          colsmobile : $self.attr('data-colsmobile') !== 'same' ? $self.attr('data-colsmobile') : $self.attr('data-cols'),
                          num : $self.attr('data-num'),
                          imgRes : $self.attr('data-res'),
                          feedID : $self.attr('data-feedid'),
                          postID : typeof $self.attr( 'data-postid' ) !== 'undefined' ? $self.attr( 'data-postid' ) : 'unknown',
                          shortCodeAtts : $self.attr('data-shortcode-atts'),
                          resizingEnabled : (flags.indexOf('resizeDisable') === -1),
                          imageLoadEnabled : (flags.indexOf('imageLoadDisable') === -1),
                          debugEnabled : (flags.indexOf('debug') > -1),
                          favorLocal : (flags.indexOf('favorLocal') > -1),
                          ajaxPostLoad : (flags.indexOf('ajaxPostLoad') > -1),
                          checkWPPosts : (flags.indexOf('checkWPPosts') > -1),
                          singleCheckPosts : (flags.indexOf('singleCheckPosts') > -1),
                          narrowPlayer : (flags.indexOf('narrowPlayer') > -1),
                          gdpr : (flags.indexOf('gdpr') > -1),
                          consentGiven : (flags.indexOf('gdpr') === -1),
                          noCDN : (flags.indexOf('disablecdn') > -1),
                          allowCookies: (flags.indexOf('allowcookies') > -1),
                          lightboxEnabled : typeof $self.attr('data-sby-supports-lightbox') !== 'undefined',
                          locator : (flags.indexOf('locator') > -1),
                          autoMinRes : 1,
                          general : general,
                          subscribeBarEnabled: true
                      };

                      window.sby.feeds[index] = sbyGetNewFeed(this, index, feedOptions);
                      if (typeof window.sbyAPIReady !== 'undefined') {
                          window.sby.feeds[index].playerAPIReady = true;
                      }
                      window.sby.feeds[index].setResizedImages();
                      window.sby.feeds[index].init();

                      var evt = jQuery.Event('sbyafterfeedcreate');
                      evt.feed = window.sby.feeds[index];
                      jQuery(window).trigger(evt);

                  })
                );
            },
            afterFeedsCreated: function () {
                // enable header hover action
                $('.sb_youtube_header').each(function () {
                    var $thisHeader = $(this);
                    $thisHeader.find('.sby_header_link').on('mouseenter mouseleave', function(e) {
                        switch(e.type) {
                            case 'mouseenter':
                                $thisHeader.find('.sby_header_img_hover').addClass('sby_fade_in');
                                break;
                            case 'mouseleave':
                                $thisHeader.find('.sby_header_img_hover').removeClass('sby_fade_in');
                                break;
                        }
                    });
                });

                if (window.sbyAPIReady) {
                    var evt = jQuery.Event('sbyfeedandytready');
                    jQuery(window).trigger(evt);
                }

            },
            encodeHTML: function(raw) {
                // make sure passed variable is defined
                if (typeof raw === 'undefined') {
                    return '';
                }
                // replace greater than and less than symbols with html entity to disallow html in comments
                var encoded = raw.replace(/(>)/g,'&gt;'),
                  encoded = encoded.replace(/(<)/g,'&lt;');
                encoded = encoded.replace(/(&lt;br\/&gt;)/g,'<br>');
                encoded = encoded.replace(/(&lt;br&gt;)/g,'<br>');

                return encoded;
            },
            urlDetect: function(text) {
                var urlRegex = /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g;
                return text.match(urlRegex);
            },
            ctaDetect: function(text) {

                var ctaMatches = text.match(/{Link:(.*)}/g),
                  cta = false;

                if (ctaMatches !== null) {
                    var urlMatches = window.sby.urlDetect(ctaMatches[0]);

                    if (urlMatches !== null) {
                        var url = urlMatches[0].trim(),
                          sbyButtonText = ctaMatches[0].replace('{Link:','').replace('}','').replace(url,'').replace('  ',' ').trim();
                        cta = {
                            callback: 'link',
                            url: url,
                            text: sbyButtonText
                        };
                    } else {
                        console.log('CTA found but no URL');
                    }
                }

                return cta;
            },
            shuffle: function(array) {
                var currentIndex = array.length,
                  temporaryValue,
                  randomIndex;

                // While there remain elements to shuffle...
                while (0 !== currentIndex) {
                    // Pick a remaining element...
                    randomIndex = Math.floor(Math.random() * currentIndex);
                    currentIndex -= 1;

                    // And swap it with the current element.
                    temporaryValue = array[currentIndex];
                    array[currentIndex] = array[randomIndex];
                    array[randomIndex] = temporaryValue;
                }

                return array;
            }
        };

        function SbyFeed(el, index, settings) {
            this.el = el;
            this.index = index;
            this.settings = settings;
            this.placeholderURL = window.sby.options.placeholder;
            if (settings.narrowPlayer) {
                this.placeholderURL = window.sby.options.placeholderNarrow
            }
            this.playerAPIReady = false;
            this.consentGiven = settings.consentGiven;
            this.players = {};
            this.minImageWidth = 0;
            this.imageResolution = 150;
            this.resizedImages = {};
            this.needsResizing = [];
            this.outOfPages = false;
            this.isInitialized = false;
            this.mostRecentlyLoadedPosts = [];
            this.embedURL = '//www.youtube-nocookie.com';
            if (settings.allowCookies) {
                this.embedURL = '//www.youtube.com'
            }

        }

        SbyFeed.prototype = {
            init: function() {
                var feed = this;
                feed.settings.consentGiven = feed.checkConsent();

                if (feed.settings.consentGiven) {
                    window.sby.maybeAddYTAPI();
                }

                if (feed.settings.noCDN && !feed.settings.consentGiven) {
                    if ($(this.el).find('.sb_youtube_header').length) {
                        $(this.el).find('.sb_youtube_header').addClass('sby_no_consent')
                    } else if ($(this.el).prev('.sb_youtube_header').length) {
                        $(this.el).prev('.sb_youtube_header').addClass('sby_no_consent')
                    }
                }
                if ($(this.el).find('#sby_mod_error').length) {
                    $(this.el).prepend($(this.el).find('#sby_mod_error'));
                }
                if (this.settings.ajaxPostLoad) {
                    this.getNewPostSet();
                } else {
                    this.afterInitialImagesLoaded();
                    //Only check the width once the resize event is over
                }
                var sby_delay = (function () {
                    var sby_timer = 0;
                    return function (sby_callback, sby_ms) {
                        clearTimeout(sby_timer);
                        sby_timer = setTimeout(sby_callback, sby_ms);
                    };
                })();
                jQuery(window).on('resize',function () {
                    sby_delay(function () {
                        feed.afterResize();
                    }, 1);
                });
            },
            initLayout: function() {
                this.initGalleryLayout();
            },
            initGalleryLayout: function() {
                var $self = $(this.el),
                  feed = this;
                if ($self.hasClass('sby_layout_gallery') && $self.find('.sby_player_outer_wrap').length) {
                    this.maybeRaiseSingleImageResolution($self.find('.sby_player_outer_wrap'), 0, true);
                    $self.find('.sby_player_outer_wrap .sby_video_thumbnail').off().on('click',function (event) {
                        if ((!feed.settings.lightboxEnabled || (feed.settings.lightboxEnabled && feed.settings.noCDN))
                          && (feed.settings.noCDN || !feed.settings.consentGiven)) {
                            if ($(this).closest('.sby_item').length
                              && typeof $(this).closest('.sby_item').attr('data-video-id') !== 'undefined') {
                                $(this).attr('href','https://www.youtube.com/watch?v='+$(this).closest('.sby_item').attr('data-video-id'));
                            }
                            return;
                        }
                        event.preventDefault();
                        feed.onThumbnailClick($(this), true);

                    });

                    $self.find('.sby_item').first().addClass('sby_current');

                    $self.on('mouseenter',function() {
                        if (!feed.canCreatePlayer()) {
                            return;
                        }
                        if (!$self.find('.sby_player_outer_wrap iframe').length) {
                            $self.addClass('sby_player_added').find('.sby_player_outer_wrap').addClass('sby_player_loading');
                            $self.find('.sby_player_outer_wrap .sby_video_thumbnail').find('.sby_loader').show().removeClass('sby_hidden');
                            feed.createPlayer('sby_player'+feed.index);
                        } else if (typeof feed.player === 'undefined' && feed.playerEagerLoaded()) {
                            feed.createPlayer('sby_player'+feed.index);
                        }
                    });

                    if (window.sbySemiEagerLoading) {
                        feed.createPlayer('sby_player'+feed.index);
                    }

                    if (feed.settings.noCDN) {
                        $self.find('.sby_player_outer_wrap').append('<div class="sby_play_btn">\n' +
                          '                        <span class="sby_play_btn_bg"></span>\n' +
                          '                    <svg aria-hidden="true" focusable="false" data-prefix="fab" data-icon="youtube" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-youtube fa-w-18"><path fill="currentColor" d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z" class=""></path></svg>                    </div>');
                    }

                }
            },
            createPlayer: function(playerID,videoID,autoplay,args) {
                var $self = $(this.el),
                  feed = this;
                videoID = typeof videoID !== 'undefined' ? videoID : this.getVideoID($self.find('.sby_item').first());
                autoplay = typeof autoplay !== 'undefined' ? autoplay : 0;

                // do not create player in customizer preview
                if ( sbyOptions.isCustomizer !== undefined && sbyOptions.isCustomizer ) {
                    return;
                }

                if (typeof args === 'undefined') {
                    args = {
                        host: window.location.protocol + feed.embedURL,
                        videoId: videoID,
                        playerVars: {
                            modestbranding: 1,
                            rel: 0,
                            autoplay: autoplay
                        },
                    }
                }
                if (typeof args.events === 'undefined') {
                    args.events = {
                        'onReady': function () {
                            $self.find('.sby_player_outer_wrap').removeClass('sby_player_loading').find('.sby_video_thumbnail').css('z-index', -1).find('.sby_loader').hide().addClass('sby_hidden');
                            if ($('#' + playerID).length && $('#' + playerID).closest('.sby_video_thumbnail_wrap').find('.sby_video_thumbnail').length) {
                                $('#' + playerID).closest('.sby_video_thumbnail_wrap').find('.sby_video_thumbnail').fadeTo(0, 'slow', function () {
                                    $(this).css('z-index', -1);
                                    $(this).find('.sby_loader').hide().addClass('sby_hidden');
                                    $(this).closest('.sby_item').removeClass('sby_player_loading');
                                });
                            }
                            var evt = jQuery.Event('sbyafterplayerready');
                            evt.feed = feed;
                            evt.player = this;
                            jQuery(window).trigger(evt);
                        },
                        'onStateChange': function(data) {
                            $self.find('.sby_player_outer_wrap').removeClass('sby_player_loading').find('.sby_video_thumbnail').css('z-index', -1).find('.sby_loader').hide().addClass('sby_hidden');
                            feed.afterStateChange(playerID,videoID,data,$('#' + playerID).closest('.sby_video_thumbnail_wrap'));


                            if (data.data !== 1) return;

                            let feedID;

                            if(feed.el) {
                                const shortcodeAttr = feed.el.getAttribute('data-shortcode-atts');
                                if(shortcodeAttr) {
                                    feedID = JSON.parse(shortcodeAttr)?.feed;
                                }
                            }

                            document.dispatchEvent(new CustomEvent('sby-video-interaction', {
                                detail: {
                                    videoID: videoID,
                                    feedID: feedID
                                }
                            }));

                            if (typeof feed.players !== 'undefined') {
                                $self.find('.sby_item').each(function() {
                                    var itemVidID = feed.getVideoID($(this));
                                    if ($(this).find('iframe').length && (itemVidID !== videoID)) {
                                        if (typeof feed.players[itemVidID] !== 'undefined' && typeof feed.players[itemVidID].pauseVideo === 'function') {
                                            feed.players[itemVidID].pauseVideo();
                                        }
                                    }
                                });
                            }

                        },
                    }
                }
                if (window.sbyEagerLoading) {
                    var newPlayer = YT.get(playerID);
                } else {
                    var newPlayer = new window.YT.Player(playerID, args);
                }

                this.maybeAddCTA(playerID);

                if ($self.hasClass('sby_layout_list') && typeof this.players[videoID] === 'undefined') {
                    this.players[videoID] = newPlayer;
                } else if (typeof this.player === 'undefined') {
                    this.player = newPlayer;
                }

                var evt = jQuery.Event('sbyafterplayercreated');
                evt.feed = this;
                jQuery(window).trigger(evt);

                $self.find('.sby_player_outer_wrap .sby_play_btn').remove();

                return newPlayer;
            },
            afterStateChange: function(playerID,videoID,data,$player) {
            },
            afterInitialImagesLoaded: function() {
                this.initLayout();
                this.loadMoreButtonInit();
                this.hideExtraItemsForWidth();
                this.beforeNewImagesRevealed();
                this.revealNewImages();
                this.afterNewImagesRevealed();
                this.afterFeedSet();
                this.sizePlayer();
                this.sizeItems();
                if (this.settings.consentGiven) {
                    this.applyFullFeatures();
                } else {
                    this.removeFeatures();
                }
            },
            afterResize: function() {
                this.setImageHeight();
                this.setImageResolution();
                this.maybeRaiseImageResolution();
                this.setImageSizeClass();
            },
            afterLoadMoreClicked: function($button) {
                $button.find('.sby_loader').removeClass('sby_hidden');
                $button.find('.sby_btn_text').addClass('sby_hidden');
                $button.closest('.sb_youtube').find('.sby_num_diff_hide').addClass('sby_transition').removeClass('sby_num_diff_hide');
            },
            afterNewImagesLoaded: function() {
                var $self = $(this.el),
                  feed = this;
                this.beforeNewImagesRevealed();
                this.revealNewImages();
                this.afterNewImagesRevealed();
                this.sizePlayer();
                this.sizeItems();
                setTimeout(function () {
                    //Hide the loader in the load more button
                    $self.find('.sby_loader').addClass('sby_hidden');
                    $self.find('.sby_btn_text').removeClass('sby_hidden');
                    feed.maybeRaiseImageResolution();
                }, 1);
                if (this.settings.consentGiven) {
                    this.applyFullFeatures();
                } else {
                    this.removeFeatures();
                }
            },
            beforeNewImagesRevealed: function() {
                this.setImageHeight();
                this.maybeRaiseImageResolution(true);
                this.setImageSizeClass();
            },
            afterFeedSet: function() {

            },
            sizePlayer: function() {
                var $self = $(this.el),
                  feed = this;
                if ($self.hasClass('sby_layout_gallery')) {
                    $playerThumbnail = $self.find('.sby_player_item').find('.sby_player_video_thumbnail');
                    var playerWidth = $playerThumbnail.innerWidth(),
                      newPlayerHeight = Math.floor(playerWidth * 9 / 16);
                    if (feed.settings.narrowPlayer) {
                        newPlayerHeight = Math.floor(playerWidth * 3 / 4);
                    }
                    $playerThumbnail.css('height',newPlayerHeight+'px').css('overflow','hidden');

                } else if ($self.hasClass('sby_layout_list')) {
                    $self.find('.sby_item').each(function(){
                        $playerThumbnail = $(this).find('.sby_item_video_thumbnail');
                        var playerWidth = $playerThumbnail.innerWidth(),
                          newPlayerHeight = Math.floor(playerWidth * 9 / 16);
                        if (feed.settings.narrowPlayer) {
                            newPlayerHeight = Math.floor(playerWidth * 3 / 4);
                        }
                        $playerThumbnail.css('height',newPlayerHeight+'px').css('overflow','hidden');
                    });
                }
            },
            sizeItems: function() {
                var $self = $(this.el),
                  feed = this;
                if (!$self.hasClass('sby_layout_list')) {
                    $self.find('.sby_item').find('.sby_item_video_thumbnail').each(function() {
                        if ($(this).hasClass('sby_imgLiquid_ready')) {
                            var thumbWidth = $(this).innerWidth(),
                              newThumbHeight = Math.floor(thumbWidth * 9 / 16);
                            $(this).css('height',newThumbHeight+'px').css('overflow','hidden');
                        }

                    });
                }
            },
            revealNewImages: function() {
                var $self = $(this.el),
                  feed = this;
                this.applyImageLiquid();

                // Call Custom JS if it exists
                if (typeof sbyCustomJS == 'function') setTimeout(function(){ sbyCustomJS(); }, 100);

                $self.find('.sby-screenreader').find('img').remove();

                $self.find('.sby_item.sby_new').each(function (index) {
                    var $self = jQuery(this);

                    //Photo links
                    //If lightbox is disabled
                    videoID = $self.attr('data-video-id');

                    if (window.sbyEagerLoading && feed.canCreatePlayer() && $('#sby_player_'+videoID).length) {
                        player = new YT.Player('sby_player_'+videoID, {
                            height: '100',
                            width: '100',
                            videoId: videoID,
                            playerVars: {
                                modestbranding: 1,
                                rel: 0,
                                autoplay: 0
                            },
                            events: {
                                'onStateChange': function(data) {
                                    var videoID = data.target.getVideoData()['video_id'];
                                    if (data.data !== 1) return;
                                    document.dispatchEvent(videoInteractionEvent, {
                                        videoID: videoID,
                                    })
                                    $self.find('.sby_item').each(function() {
                                        var itemVidID = jQuery(this).attr('data-video-id');

                                        if (jQuery(this).find('iframe').length && jQuery(data.target.a).attr('id') !== jQuery(this).find('iframe').attr('id')) {
                                            YT.get('sby_player_'+itemVidID).pauseVideo();
                                        }
                                    });
                                }
                            }
                        });
                    }

                    $self.find('.sby_video_thumbnail').on('mouseenter',function() {
                        feed.onThumbnailEnter($(this), false);
                    });
                    $self.find('.sby_player_wrap').on('mouseleave',function() {
                        feed.onThumbnailLeave($(this), false);
                    });
                    //init click
                    $self.find('.sby_video_thumbnail').on('click',function(event) {
                        if ((!feed.settings.lightboxEnabled || (feed.settings.lightboxEnabled && feed.settings.noCDN))
                          && (feed.settings.noCDN || !feed.settings.consentGiven)) {
                            if ($(this).closest('.sby_item').length
                              && typeof $(this).closest('.sby_item').attr('data-video-id') !== 'undefined') {
                                $(this).attr('href','https://www.youtube.com/watch?v='+$(this).closest('.sby_item').attr('data-video-id'));
                            }
                            return;
                        }
                        event.preventDefault();
                        feed.onThumbnailClick($(this), false);
                    });

                    // lightbox
                    if (feed.settings.lightboxEnabled) {
                        $self.find('.sby_video_thumbnail').attr('data-sby-lightbox',feed.index);
                        if (typeof sbyOptions !== 'undefined' && typeof sbyOptions.lightboxPlaceholder !== 'undefined') {
                            if (feed.settings.narrowPlayer) {
                                $self.find('.sby_video_thumbnail').attr('href',sbyOptions.lightboxPlaceholderNarrow);
                            } else {
                                $self.find('.sby_video_thumbnail').attr('href',sbyOptions.lightboxPlaceholder);
                            }
                        }
                    }
                    feed.afterItemRevealed($self);

                    // no info
                    if ($self.find('.sby_info_item').text().trim() === '') {
                        $self.find('.sby_info_item').addClass('sby_no_space');
                    }
                }); //End .sby_item each

                $self.find('.sby_player_item').each(function (index) {
                    var $self = jQuery(this);

                    feed.afterItemRevealed($self);
                }); //End .sby_item each

                //Remove the new class after 500ms, once the sorting is done
                setTimeout(function () {
                    $self.find('.sby_item.sby_new').removeClass('sby_new');
                    //Loop through items and remove class to reveal them
                    var time = 1,
                      num = $self.find('.sby_transition').length;
                    $self.find('.sby_transition').each(function(index) {
                        var $sby_item_transition_el = jQuery(this);

                        setTimeout( function(){
                            $sby_item_transition_el.removeClass('sby_transition');
                        }, time);
                        //time += 10;
                    });
                }, 1);

            },
            afterItemRevealed: function() {

            },
            afterNewImagesRevealed: function() {
                this.listenForVisibilityChange();
                this.sendNeedsResizingToServer();
                this.sendCheckWPPostsToServer();
                if (!this.settings.imageLoadEnabled) {
                    $('.sby_no_resraise').removeClass('sby_no_resraise');
                }

                var evt = $.Event('sbyafterimagesloaded');
                evt.el = $(this.el);
                $(window).trigger(evt);
            },
            setResizedImages: function () {
                if ($(this.el).find('.sby_resized_image_data').length
                  && typeof $(this.el).find('.sby_resized_image_data').attr('data-resized') !== 'undefined'
                  && $(this.el).find('.sby_resized_image_data').attr('data-resized').indexOf('{"') === 0) {
                    this.resizedImages = JSON.parse($(this.el).find('.sby_resized_image_data').attr('data-resized'));
                    $(this.el).find('.sby_resized_image_data').remove();
                }
            },
            sendNeedsResizingToServer: function() {
                var feed = this;
                if (feed.needsResizing.length > 0 && feed.settings.resizingEnabled) {
                    var itemOffset = $(this.el).find('.sby_item').length;

                    var submitData = {
                        action: 'sby_resized_images_submit',
                        needs_resizing: feed.needsResizing,
                        offset: itemOffset,
                        feed_id: feed.settings.feedID,
                        location: feed.locationGuess(),
                        post_id: feed.settings.postID,
                        atts: feed.settings.shortCodeAtts,
                    };
                    var onSuccess = function(data) {
                        if (data.trim().indexOf('{') === 0) {
                            var response = JSON.parse(data);
                            if (feed.settings.debugEnabled) {
                                console.log(response);
                            }
                        }
                    };
                    sbyAjax(submitData,onSuccess);
                }
            },
            sendCheckWPPostsToServer: function() {
                var feed = this;
                if (feed.settings.checkWPPosts || feed.settings.singleCheckPosts) {
                    var feedID = typeof feed.settings.feedID !== 'undefined' ? feed.settings.feedID : 'sby_single',
                      posts = feed.mostRecentlyLoadedPosts;
                    feed.mostRecentlyLoadedPosts = [];
                    var submitData = {
                        action: 'sby_check_wp_submit',
                        feed_id: feedID,
                        atts: feed.settings.shortCodeAtts,
                        location: feed.locationGuess(),
                        post_id: feed.settings.postID,
                        offset: ! $(this.el).hasClass('sby_layout_carousel') ? $(this.el).find('.sby_item').length : Math.floor(($(this.el).find('.sby_item').length / 2) -1),
                        posts: posts
                    };
                    var onSuccess = function(data) {
                        if (data.trim().indexOf('{') === 0) {
                            var response = JSON.parse(data);
                            if (feed.settings.debugEnabled) {
                                console.log(response);
                            }
                            feed.afterSendCheckWPPostsToServer(response);

                        }
                    };
                    sbyAjax(submitData,onSuccess);
                }
            },
            afterSendCheckWPPostsToServer: function (response) {

            },
            loadMoreButtonInit: function () {
                var $self = $(this.el),
                  feed = this;
                $self.find('.sby_footer .sby_load_btn').off().on('click', function () {
                    feed.afterLoadMoreClicked(jQuery(this));
                    feed.getNewPostSet();
                }); //End click event
            },
            getNewPostSet: function () {
                var $self = $(this.el),
                  feed = this;
                var itemOffset = $self.find('.sby_item').length,
                  submitData = {
                      action: 'sby_load_more_clicked',
                      offset: itemOffset,
                      feed_id: feed.settings.feedID,
                      atts: feed.settings.shortCodeAtts,
                      location: feed.locationGuess(),
                      post_id: feed.settings.postID,
                      current_resolution: feed.imageResolution
                  };
                var onSuccess = function (data) {
                    if (data.trim().indexOf('{') === 0) {
                        var response = JSON.parse(data),
                          checkWPPosts = typeof response.feedStatus.checkWPPosts !== 'undefined' ? response.feedStatus.checkWPPosts : false;;
                        if (feed.settings.debugEnabled) {
                            console.log(response);
                        }
                        if (checkWPPosts) {
                            feed.settings.checkWPPosts = true;
                        } else {
                            feed.settings.checkWPPosts = false;
                        }
                        feed.appendNewPosts(response.html);
                        feed.addResizedImages(response.resizedImages);
                        if (feed.settings.ajaxPostLoad) {
                            feed.settings.ajaxPostLoad = false;
                            feed.afterInitialImagesLoaded();
                        } else {
                            feed.afterNewImagesLoaded();
                        }

                        if (!response.feedStatus.shouldPaginate) {
                            feed.outOfPages = true;
                            $self.find('.sby_load_btn').hide();
                        } else {
                            feed.outOfPages = false;
                        }

                        $('.sby_no_js').removeClass('sby_no_js');
                        openComments();
                    }

                };
                sbyAjax(submitData, onSuccess);
            },
            appendNewPosts: function (newPostsHtml) {
                var $self = $(this.el),
                  feed = this;
                if ($self.find('.sby_items_wrap .sby_item').length) {
                    $self.find('.sby_items_wrap .sby_item').last().after(newPostsHtml);
                } else {
                    $self.find('.sby_items_wrap').append(newPostsHtml);
                }
            },
            addResizedImages: function (resizedImagesToAdd) {
                for (var imageID in resizedImagesToAdd) {
                    this.resizedImages[imageID] = resizedImagesToAdd[imageID];
                }
            },
            setImageHeight: function() {
            },
            maybeRaiseSingleImageResolution: function ($item, index, forceChange) {
                var feed = this,
                  imgSrcSet = feed.getImageUrls($item),
                  currentUrl = $item.find('.sby_video_thumbnail > img').attr('src'),
                  currentRes = 150,
                  aspectRatio = 1, // all thumbnails are oriented the same so the best calculation uses 1
                  forceChange = typeof forceChange !== 'undefined' ? forceChange : false;

                if ($item.hasClass('sby_no_resraise')   ||
                  (!feed.settings.consentGiven && feed.settings.noCDN) ) {
                    return;
                }

                $.each(imgSrcSet, function (index, value) {
                    if (value === currentUrl) {
                        currentRes = parseInt(index);
                        // If the image has already been changed to an existing real source, don't force the change
                        forceChange = false;
                    }
                });
                //Image res
                var newRes = 640;
                switch (feed.settings.imgRes) {
                    case 'thumb':
                        newRes = 120;
                        break;
                    case 'medium':
                        newRes = 320;
                        break;
                    case 'large':
                        newRes = 480;
                        break;
                    case 'full':
                        newRes = 640;
                        break;
                    default:
                        var minImageWidth = Math.max(feed.settings.autoMinRes,$item.find('.sby_video_thumbnail').innerWidth()),
                          thisImageReplace = feed.getBestResolutionForAuto(minImageWidth, aspectRatio, $(this.el).find('sby_item').first());
                        switch (thisImageReplace) {
                            case 480:
                                newRes = 480;
                                break;
                            case 320:
                                newRes = 320;
                                break;
                            case 120:
                                newRes = 120;
                                break;
                        }
                        break;
                }

                if (newRes > currentRes || currentUrl === feed.placeholderURL || forceChange) {
                    if (feed.settings.debugEnabled) {
                        var reason = currentUrl === feed.placeholderURL ? 'was placeholder' : 'too small';
                        console.log('rais res for ' + currentUrl, reason);
                    }
                    var newUrl = imgSrcSet[newRes];
                    $item.find('.sby_video_thumbnail > img').attr('src', newUrl);
                    if ($item.find('.sby_video_thumbnail').hasClass('sby_imgLiquid_ready')) {
                        $item.find('.sby_video_thumbnail').css('background-image', 'url("' + newUrl + '")');
                    }
                }

                $item.find('img').on('error', function () {
                    if (!$(this).hasClass('sby_img_error')) {
                        $(this).addClass('sby_img_error');
                        var sourceFromAPI = ($(this).attr('src').indexOf('i.ytimg.com') > -1);

                        if (!sourceFromAPI) {
                            if (typeof $(this).closest('.sby_video_thumbnail').attr('data-full-res') !== 'undefined') {
                                $(this).attr('src', $(this).closest('.sby_video_thumbnail').attr('data-full-res'));
                                $(this).closest('.sby_video_thumbnail').css('background-image', 'url(' + $(this).closest('.sby_video_thumbnail').attr('data-full-res') + ')');
                            } else if ($(this).closest('.sby_video_thumbnail').attr('href') !== 'undefined') {
                                $(this).attr('src', $(this).closest('.sby_video_thumbnail').attr('href') + 'media?size=l');
                                $(this).closest('.sby_video_thumbnail').css('background-image', 'url(' + $(this).closest('.sby_video_thumbnail').attr('href') + 'media?size=l)');
                            }
                        } else {
                            feed.settings.favorLocal = true;
                            var srcSet = feed.getImageUrls($(this).closest('.sby_item'));
                            if (typeof srcSet[640] !== 'undefined') {
                                $(this).attr('src', srcSet[640]);
                                $(this).closest('.sby_video_thumbnail').css('background-image', 'url(' + srcSet[640] + ')');
                            }
                        }
                        setTimeout(function() {
                            feed.afterResize();
                        }, 1)
                    } else {
                        console.log('unfixed error ' + $(this).attr('src'));
                    }
                });
            },
            maybeRaiseImageResolution: function (justNew) {
                var feed = this,
                  itemsSelector = typeof justNew !== 'undefined' && justNew === true ? '.sby_item.sby_new' : '.sby_item',
                  forceChange = !feed.isInitialized ? true : false;
                $(feed.el).find(itemsSelector).each(function (index) {
                    if (!$(this).hasClass('sby_num_diff_hide')
                      && $(this).find('.sby_video_thumbnail').length
                      && typeof $(this).find('.sby_video_thumbnail').attr('data-img-src-set') !== 'undefined') {
                        feed.maybeRaiseSingleImageResolution($(this),index,forceChange);
                    }
                }); //End .sby_item each
                feed.isInitialized = true;
            },
            getBestResolutionForAuto: function(colWidth, aspectRatio, $item) {
                if (isNaN(aspectRatio) || aspectRatio < 1) {
                    aspectRatio = 1;
                }
                var bestWidth = colWidth * aspectRatio,
                  bestWidthRounded = Math.ceil(bestWidth / 10) * 10,
                  customSizes = [120, 320, 480, 640];

                if ($item.hasClass('sby_highlighted')) {
                    bestWidthRounded = bestWidthRounded *2;
                }

                if (customSizes.indexOf(parseInt(bestWidthRounded)) === -1) {
                    var done = false;
                    $.each(customSizes, function (index, item) {
                        if (item > parseInt(bestWidthRounded) && !done) {
                            bestWidthRounded = item;
                            done = true;
                        }
                    });
                }

                return bestWidthRounded;
            },
            hideExtraItemsForWidth: function() {
                if (this.layout === 'carousel') {
                    return;
                }
                var $self = $(this.el),
                  num = typeof $self.attr('data-num') !== 'undefined' && $self.attr('data-num') !== '' ? parseInt($self.attr('data-num')) : 1,
                  nummobile = typeof $self.attr('data-nummobile') !== 'undefined' && $self.attr('data-nummobile') !== '' ? parseInt($self.attr('data-nummobile')) : num;

                if (!$self.hasClass('.sby_layout_carousel')) {
                    if ($(window).width() < 480) {
                        if (nummobile < $self.find('.sby_item').length) {
                            $self.find('.sby_item').slice(nummobile - $self.find('.sby_item').length).addClass('sby_num_diff_hide');
                        }
                    } else {
                        if (num < $self.find('.sby_item').length) {
                            $self.find('.sby_item').slice(num - $self.find('.sby_item').length).addClass('sby_num_diff_hide');
                        }
                    }
                }

            },
            setImageSizeClass: function () {
                var $self = $(this.el);
                $self.removeClass('sby_small sby_medium');
                var feedWidth = $self.innerWidth(),
                  photoPadding = parseInt(($self.find('.sby_items_wrap').outerWidth() - $self.find('.sby_items_wrap').width())) / 2,
                  cols = this.getColumnCount(),
                  feedWidthSansPadding = feedWidth - (photoPadding * (cols+2)),
                  colWidth = (feedWidthSansPadding / cols);

                if (colWidth > 140 && colWidth < 240) {
                    $self.addClass('sby_medium');
                } else if (colWidth <= 140) {
                    $self.addClass('sby_small');
                }
            },
            setMinImageWidth: function () {
                if ($(this.el).find('.sby_item .sby_video_thumbnail').first().length) {
                    this.minImageWidth = $(this.el).find('.sby_item .sby_video_thumbnail').first().innerWidth();
                } else {
                    this.minImageWidth = 150;
                }
            },
            setImageResolution: function () {
                if (this.settings.imgRes === 'auto') {
                    this.imageResolution = 'auto';
                } else {
                    switch (this.settings.imgRes) {
                        case 'thumb':
                            this.imageResolution = 150;
                            break;
                        case 'medium':
                            this.imageResolution = 320;
                            break;
                        default:
                            this.imageResolution = 640;
                    }
                }
            },
            getImageUrls: function ($item) {
                var srcSet = JSON.parse($item.find('.sby_video_thumbnail').attr('data-img-src-set').replace(/\\\//g, '/')),
                  id = $item.attr('id').replace('sby_', '').replace('player_','');
                if (typeof this.resizedImages[id] !== 'undefined'
                  && this.resizedImages[id] !== 'video'
                  && this.resizedImages[id] !== 'pending'
                  && this.resizedImages[id].id !== 'error'
                  && this.resizedImages[id].id !== 'video'
                  && this.resizedImages[id].id !== 'pending') {

                    if (typeof this.resizedImages[id]['sizes'] !== 'undefined') {
                        var foundSizes = [];
                        if (typeof this.resizedImages[id]['sizes']['full'] !== 'undefined') {
                            foundSizes.push(640);
                            srcSet[640] = sbyOptions.resized_url + this.resizedImages[id].id + 'full.jpg';
                            $item.find('.sby_link_area').attr( 'href', sbyOptions.resized_url + this.resizedImages[id].id + 'full.jpg' );
                            $item.find('.sby_video_thumbnail').attr( 'data-full-res', sbyOptions.resized_url + this.resizedImages[id].id + 'full.jpg' );
                        }
                        if (typeof this.resizedImages[id]['sizes']['low'] !== 'undefined') {
                            foundSizes.push(320);
                            srcSet[320] = sbyOptions.resized_url + this.resizedImages[id].id + 'low.jpg';
                            if (this.settings.favorLocal && typeof this.resizedImages[id]['sizes']['full'] === 'undefined') {
                                $item.find('.sby_link_area').attr( 'href', sbyOptions.resized_url + this.resizedImages[id].id + 'low.jpg' );
                                $item.find('.sby_video_thumbnail').attr( 'data-full-res', sbyOptions.resized_url + this.resizedImages[id].id + 'low.jpg' );
                            }
                        }
                        if (typeof this.resizedImages[id]['sizes']['thumb'] !== 'undefined') {
                            foundSizes.push(150);
                            srcSet[150] = sbyOptions.resized_url + this.resizedImages[id].id + 'thumb.jpg';
                        }
                        if (this.settings.favorLocal) {
                            if (foundSizes.indexOf(640) === -1) {
                                if (foundSizes.indexOf(320) > -1) {
                                    srcSet[640] = sbyOptions.resized_url + this.resizedImages[id].id + 'low.jpg';
                                }
                            }
                            if (foundSizes.indexOf(320) === -1) {
                                if (foundSizes.indexOf(640) > -1) {
                                    srcSet[320] = sbyOptions.resized_url + this.resizedImages[id].id + 'full.jpg';
                                } else if (foundSizes.indexOf(150) > -1) {
                                    srcSet[320] = sbyOptions.resized_url + this.resizedImages[id].id + 'thumb.jpg';
                                }
                            }
                            if (foundSizes.indexOf(150) === -1) {
                                if (foundSizes.indexOf(320) > -1) {
                                    srcSet[150] = sbyOptions.resized_url + this.resizedImages[id].id + 'low.jpg';
                                } else if (foundSizes.indexOf(640) > -1) {
                                    srcSet[150] = sbyOptions.resized_url + this.resizedImages[id].id + 'full.jpg';
                                }
                            }
                        }
                    }
                } else if (typeof this.resizedImages[id] === 'undefined'
                  || (typeof this.resizedImages[id]['id'] !== 'undefined' && this.resizedImages[id]['id'] !== 'pending' && this.resizedImages[id]['id'] !== 'error')) {
                    this.addToNeedsResizing(id);
                }

                return srcSet;
            },
            getVideoID: function ($el) {
                if ($el.hasClass('sby_item') || $el.hasClass('sby_player_item')) {
                    if (typeof $el.find('.sby_video_thumbnail').attr('data-video-id') !== 'undefined') {
                        return $el.find('.sby_video_thumbnail').attr('data-video-id');
                    }
                } else if ($el.closest('sby_item').length || $el.closest('sby_player_item').length) {
                    var $targeEl = $el.closest('sby_item').length ? $el.closest('sby_item') : $el.closest('sby_player_item');
                    if (typeof $targeEl.find('.sby_video_thumbnail').attr('data-video-id') !== 'undefined') {
                        return $targeEl.find('.sby_video_thumbnail').attr('data-video-id');
                    }
                } else if ($el.hasClass('sb_youtube')) {
                    return $el.find('.sby_item').first().find('.sby_video_thumbnail').attr('data-video-id');
                } else if ($(this.el).find('.sby_video_thumbnail').first().length && typeof $(this.el).find('.sby_video_thumbnail').first().attr('data-video-id') !== 'undefined'){
                    return $(this.el).find('.sby_video_thumbnail').first().attr('data-video-id');
                }
                return '';
            },
            getAvatarUrl: function (username,favorType) {
                if (username === '') {
                    return '';
                }

                var availableAvatars = this.settings.general.avatars,
                  favorType = typeof favorType !== 'undefined' ? favorType : 'local';

                if (favorType === 'local') {
                    if (typeof availableAvatars['LCL'+username] !== 'undefined' && parseInt(availableAvatars['LCL'+username]) === 1) {
                        return sbyOptions.resized_url + username + '.jpg';
                    } else if (typeof availableAvatars[username] !== 'undefined') {
                        return availableAvatars[username];
                    } else {
                        return '';
                    }
                } else {
                    if (typeof availableAvatars[username] !== 'undefined') {
                        return availableAvatars[username];
                    } else if (typeof availableAvatars['LCL'+username] !== 'undefined' && parseInt(availableAvatars['LCL'+username]) === 1)  {
                        return sbyOptions.resized_url + username + '.jpg';
                    } else {
                        return '';
                    }
                }
            },
            addToNeedsResizing: function (id) {
                if (this.needsResizing.indexOf(id) === -1) {
                    this.needsResizing.push(id);
                }
            },
            applyImageLiquid: function () {
                var $self = $(this.el),
                  feed = this;
                sbyAddImgLiquid();
                if (typeof $self.find(".sby_player_item").sby_imgLiquid == 'function') {
                    if ($self.find('.sby_player_item').length) {
                        $self.find(".sby_player_item .sby_player_video_thumbnail").sby_imgLiquid({fill: true});
                    }
                    $self.find(".sby_item .sby_item_video_thumbnail").sby_imgLiquid({fill: true});
                }
            },
            listenForVisibilityChange: function() {
                var feed = this;
                sbyAddVisibilityListener();
                if (typeof $(this.el).filter(':hidden').sbyVisibilityChanged == 'function') {
                    //If the feed is initially hidden (in a tab for example) then check for when it becomes visible and set then set the height
                    $(this.el).filter(':hidden').sbyVisibilityChanged({
                        callback: function (element, visible) {
                            feed.afterResize();
                        },
                        runOnLoad: false
                    });
                }
            },
            getColumnCount: function() {
                var $self = $(this.el),
                  cols = this.settings.cols,
                  colsmobile = this.settings.colsmobile,
                  returnCols = cols;

                sbyWindowWidth = window.innerWidth;

                if ($self.hasClass('sby_mob_col_auto')) {
                    if (sbyWindowWidth < 640 && (parseInt(cols) > 2 && parseInt(cols) < 7)) returnCols = 2;
                    if (sbyWindowWidth < 640 && (parseInt(cols) > 6 && parseInt(cols) < 11)) returnCols = 4;
                    if (sbyWindowWidth <= 480 && parseInt(cols) > 2) returnCols = 1;
                } else if (sbyWindowWidth <= 480) {
                    returnCols = colsmobile;
                }

                return parseInt(returnCols);
            },
            onThumbnailClick: function($clicked,isPlayer,videoID) {
                if (!this.canCreatePlayer()) {
                    return;
                }
                var $self = $(this.el);
                if ($self.hasClass('sby_layout_gallery')) {
                    $self.find('.sby_current').removeClass('sby_current');
                    $clicked.closest('.sby_item').addClass('sby_current');

                    $clicked.closest('.sby_item').addClass('sby_current');
                    $self.addClass('sby_player_added').find('.sby_player_outer_wrap').addClass('sby_player_loading');
                    $self.find('.sby_player_outer_wrap .sby_video_thumbnail').find('.sby_loader').show().removeClass('sby_hidden');
                    if (!$self.find('.sby_player_outer_wrap iframe').length) {
                        if (isPlayer) {
                            this.createPlayer('sby_player'+this.index);
                        } else {
                            var videoID = typeof videoID === 'undefined' ? this.getVideoID($clicked.closest('.sby_item')) : videoID;
                            this.createPlayer('sby_player'+this.index,videoID);
                        }
                    } else {
                        if (isPlayer) {
                            var videoID = typeof videoID === 'undefined' ? this.getVideoID($self.find('.sby_item').first()) : videoID;

                            this.playVideoInPlayer(videoID);
                        } else {
                            var videoID = typeof videoID === 'undefined' ? this.getVideoID($clicked.closest('.sby_item')) : videoID;

                            this.changePlayerInfo($clicked.closest('.sby_item'));
                            this.playVideoInPlayer(videoID);
                            this.afterVideoChanged();
                        }
                    }
                    this.updateGalleryPlayerSubscribeBtn($clicked);

                } else if ($(this.el).hasClass('sby_layout_grid') || $(this.el).hasClass('sby_layout_carousel')) {
                    var $sbyItem = $clicked.closest('.sby_item'),
                      videoID = typeof videoID === 'undefined' ? this.getVideoID($sbyItem) : videoID;
                    this.playVideoInPlayer(videoID);
                    this.afterVideoChanged();
                } else if ($(this.el).hasClass('sby_layout_list')) {
                    var $sbyItem = $clicked.closest('.sby_item'),
                      videoID = typeof videoID === 'undefined' ? this.getVideoID($sbyItem) : videoID;
                    if ($sbyItem.length && !$sbyItem.find('iframe').length) {
                        $sbyItem.find('.sby_loader').show().removeClass('sby_hidden');
                        $sbyItem.addClass('sby_player_loading sby_player_loaded');
                        this.createPlayer('sby_player_'+videoID,videoID);
                    } else {
                        this.playVideoInPlayer(videoID,$sbyItem.attr('data-video-id'));
                        this.afterVideoChanged();
                    }
                }
            },
            onThumbnailEnter: function($hovered) {
                if (!this.canCreatePlayer()) {
                    return;
                }
                var $self = $(this.el);
                if ($self.hasClass('sby_layout_list')) {
                    var $sbyItem = $hovered.closest('.sby_item'),
                      videoID = this.getVideoID($sbyItem);
                    if (!$sbyItem.find('iframe').length) {
                        $sbyItem.find('.sby_loader').show().removeClass('sby_hidden');
                        $sbyItem.addClass('sby_player_loading sby_player_loaded');
                        this.createPlayer('sby_player_'+videoID,videoID,0);
                    }
                }
            },
            onThumbnailLeave: function($hovered) {
            },
            changePlayerInfo: function($newItem) {

            },
            playerEagerLoaded: function() {
                if (typeof this.player !== 'undefined' || $(this.el).hasClass('sby_player_loaded')) {
                    return true;
                }
            },
            canCreatePlayer: function() {
                if ($(this.el).find('#sby_blank').length) {
                    return false;
                }

                const concentGiven = this.settings.consentGiven

                // Fix for elementor builder for list view. Where video would not load on hocer.
                const elementorCheck = window.sby.feeds[this.index].playerAPIReady && concentGiven;

                return this.playerEagerLoaded() || (this.playerAPIReady && concentGiven) || (window.sbyAPIReady && concentGiven) || elementorCheck;
            },
            playVideoInPlayer: function(videoID,playerID) {
                if (typeof this.player !== 'undefined' && typeof this.player.loadVideoById !== 'undefined') {
                    this.player.loadVideoById(videoID);
                } else if (typeof window.sbyLightboxPlayer !== 'undefined'
                  && typeof window.sbyLightboxPlayer.loadVideoById !== 'undefined') {
                    window.sbyLightboxPlayer.loadVideoById(videoID);
                } else if (typeof playerID !== 'undefined'
                  && typeof this.players !== 'undefined'
                  && typeof this.players[playerID] !== 'undefined'
                  && typeof this.players[playerID].loadVideoById !== 'undefined') {
                    this.players[playerID].loadVideoById(videoID);
                }
            },
            afterVideoChanged: function() {
                if ($(this.el).hasClass('sby_layout_gallery')) {
                    $(this.el).find('.sby_player_outer_wrap').removeClass('sby_player_loading');
                    $(this.el).find('.sby_player_outer_wrap .sby_video_thumbnail').find('.sby_loader').hide().addClass('sby_hidden');

                    $('html, body').animate({
                        scrollTop: $(this.el).find('.sby_player_outer_wrap').offset().top
                    }, 300);

                }
            },
            updateGalleryPlayerSubscribeBtn: function($clicked) {
                const itemURL = $clicked.attr('href');
                const regex = /channel\/(.*)$/;
                const match = itemURL.match(regex);
                if ( ! match ) {
                    return;
                }
                const channelId = match[1];
                const subscribeBtnURL = 'http://www.youtube.com/channel/'+ channelId +'?sub_confirmation=1&feature=subscribe-embed-click';
                
                $('.sby-channel-subscribe-btn a').attr('href', subscribeBtnURL);
            },
            checkConsent: function() {
                if (this.settings.consentGiven || !this.settings.gdpr) {
                    this.settings.noCDN = false;
                    return true;
                }
                if (typeof window.WPConsent !== 'undefined') {
                    this.settings.consentGiven = window.WPConsent.hasConsent('marketing');
                } else if (typeof CLI_Cookie !== "undefined") { // GDPR Cookie Consent by WebToffee
                    if (CLI_Cookie.read(CLI_ACCEPT_COOKIE_NAME) !== null)  {

                        // WebToffee no longer uses this cookie but being left here to maintain backwards compatibility
                        if (CLI_Cookie.read('cookielawinfo-checkbox-non-necessary') !== 'null') {
                            this.settings.consentGiven = CLI_Cookie.read('cookielawinfo-checkbox-non-necessary') === 'yes';
                        }

                        if (CLI_Cookie.read('cookielawinfo-checkbox-necessary') !== 'null') {
                            this.settings.consentGiven = CLI_Cookie.read('cookielawinfo-checkbox-necessary') === 'yes';
                        }
                    }

                } else if (typeof window.cnArgs !== "undefined") { // Cookie Notice by dFactory
                    var value = "; " + document.cookie,
                      parts = value.split( '; cookie_notice_accepted=' );

                    if ( parts.length === 2 ) {
                        var val = parts.pop().split( ';' ).shift();

                        this.settings.consentGiven = (val === 'true');
                    }
                } else if (typeof window.complianz !== 'undefined') { // Complianz by Really Simple Plugins
                    this.settings.consentGiven = ( sbyCmplzGetCookie('cmplz_marketing') === 'allow' || jQuery('body').hasClass('cmplz-status-marketing') );
                } else if (typeof window.Cookiebot !== "undefined") { // Cookiebot by Cybot A/S
                    this.settings.consentGiven = Cookiebot.consented;
                } else if (typeof window.BorlabsCookie !== 'undefined') { // Borlabs Cookie by Borlabs
                    this.settings.consentGiven = typeof window.BorlabsCookie.Consents !== 'undefined' ? window.BorlabsCookie.Consents.hasConsent('youtube') : window.BorlabsCookie.checkCookieConsent('youtube');
                } else if (sbyCmplzGetCookie('moove_gdpr_popup')) { // Moove GDPR Popup
                    var moove_gdpr_popup = JSON.parse(decodeURIComponent(sbyCmplzGetCookie('moove_gdpr_popup')));
                    this.settings.consentGiven = typeof moove_gdpr_popup.thirdparty !== "undefined" && moove_gdpr_popup.thirdparty === "1";
                }

                var evt = jQuery.Event('sbycheckconsent');
                evt.feed = this;
                jQuery(window).trigger(evt);

                if (this.settings.consentGiven) {
                    this.settings.noCDN = false;
                }

                return this.settings.consentGiven; // GDPR not enabled
            },
            afterConsentToggled: function() {
                if (this.checkConsent()) {
                    var feed = this;
                    window.sby.maybeAddYTAPI();
                    feed.maybeRaiseImageResolution();
                    feed.applyFullFeatures();
                    setTimeout(function() {
                        feed.afterResize();
                    },500);
                }
            },
            removeFeatures: function() {
                var feed = this;
                if (feed.settings.noCDN) {
                    $(feed.el).find('.sby_video_thumbnail').each(function() {
                        $(this).removeAttr('data-sby-lightbox');
                    });
                }
            },
            applyFullFeatures: function() {
                var feed = this;

                $(feed.el).find('.sby_header_img img').attr('src',$(feed.el).find('.sby_header_img').attr('data-avatar-url'));
                if (typeof $(feed.el).find('.sby_video_thumbnail').first().attr('data-sby-lightbox') === 'undefined'
                  && feed.settings.lightboxEnabled) {
                    $(feed.el).find('.sby_video_thumbnail').each(function() {
                        $(this).attr('data-sby-lightbox',feed.index);
                    });
                }
                var $self = $(feed.el);
                $self.find('.sby_no_consent').removeClass('sby_no_consent');
                if ($self.hasClass('sby_layout_gallery') && $self.find('.sby_player_outer_wrap').length) {
                    this.maybeRaiseSingleImageResolution($self.find('.sby_player_outer_wrap'), 0, true);
                    $self.find('.sby_item').first().addClass('sby_current');

                    if (!feed.canCreatePlayer()) {
                        return;
                    }
                    if (!$self.find('.sby_player_outer_wrap iframe').length) {
                        feed.createPlayer('sby_player'+feed.index);
                    }

                }
            },
            locationGuess: function() {
                var $feed = $(this.el),
                  location = 'content';

                if ($feed.closest('footer').length) {
                    location = 'footer';
                } else if ($feed.closest('.header').length
                  || $feed.closest('header').length) {
                    location = 'header';
                } else if ($feed.closest('.sidebar').length
                  || $feed.closest('aside').length) {
                    location = 'sidebar';
                }

                return location;
            }
        };

        function SbyFeedPro(el, index, settings) {
            SbyFeed.call(this, el, index, settings);

            this.CTA = {};

            this.initLayout = function() {
                this.initGalleryLayout();
                this.initGrid();

                this.initCarousels();
                var evt = jQuery.Event('sbyafterlayoutinit');
                evt.feed = this;
                jQuery(window).trigger(evt);
                openComments(); 
            };

            this.initGrid = function() {
                if (window.sbySemiEagerLoading && jQuery('#sby_lightbox').length) {
                    var feed = this;
                    playerID = 'sby_lb-player';
                    jQuery('#sby_lightbox').addClass('sby_video_lightbox');
                    if ( ! window.sbyOptions.isPro ) {
                        jQuery('#sby_lightbox').addClass('sby_lightbox_free');
                    }

                    var videoID = $(this.el).find('sby_item').first().attr('data-video-id'),
                      autoplay = sbyOptions.autoplay;
                    if (typeof window.sbyLightboxPlayer === 'undefined') {
                        var args = {
                            host: window.location.protocol + feed.embedURL,
                            videoId: videoID,
                            playerVars: {
                                modestbranding: 1,
                                rel: 0,
                                autoplay: autoplay
                            },
                            events: {
                                'onStateChange': function (data) {
                                    var videoID = data.target.getVideoData()['video_id'];
                                    feed.afterStateChange(playerID, videoID, data, $('#' + playerID).closest('.sby_video_thumbnail_wrap'));
                                }
                            }
                        };
                        feed.maybeAddCTA(playerID);

                        window.sbyLightboxPlayer = new window.YT.Player(playerID, args);
                    }
                }
            };

            this.initCarousels = function() {
                var feed = this,
                  $self = $(this.el);

                if (typeof this.settings.general.carousel === 'undefined') {
                    return;
                }
                var cols = this.settings.cols,
                  colsmobile = this.settings.colsmobile;

                $self.find('.sby_items_wrap').addClass('sby_carousel');
                $self.find('.sby_load_btn').remove();
                $self.find('.sby_item').css({
                    'padding-top' : $self.find('.sby_items_wrap').css('padding-top'),
                    'padding-right' : $self.find('.sby_items_wrap').css('padding-top'),
                    'padding-bottom' : $self.find('.sby_items_wrap').css('padding-top'),
                    'padding-left' : $self.find('.sby_items_wrap').css('padding-top')
                });
                $self.find('.sby_item').each(function() {
                    $(this).attr('style',$(this).attr('style').replace('padding: '+$self.find('.sby_items_wrap').css('padding-top'),'padding: '+$self.find('.sby_items_wrap').css('padding-top') + ' !important'));
                });

                var arrows = feed.settings.general.carousel[0],
                  pagination = feed.settings.general.carousel[1],
                  autoplay = feed.settings.general.carousel[2],
                  time = feed.settings.general.carousel[3],
                  loop = feed.settings.general.carousel[4],
                  rows = feed.settings.general.carousel[5];
                //Initiate carousel
                if( !autoplay ) time = false;

                //Set defaults for responsive breakpoints
                var itemsTabletSmall = cols,
                  itemsMobile = cols,
                  arrows = arrows ? 'onhover' : 'hide',
                  autoplay = time !== false,
                  has2rows = (rows == 2),
                  loop = (!loop),
                  onChange = function() {
                      setTimeout(function(){
                          feed.afterResize();
                      }, 1);
                  },
                  afterInit = function() {
                      var $self = jQuery(feed.el);
                      $self.find('.sby_items_wrap.sby_carousel').fadeIn();
                      setTimeout(function(){
                          $self.find('.sby_items_wrap.sby_carousel .sby_info, .sby_owl2row-item,.sby_items_wrap.sby_carousel').fadeIn();

                      }, 1);

                      setTimeout(function(){

                          var $navElementsWrapper = $self.find('.sby-owl-nav');
                          if (arrows === 'onhover') {

                          } else if (arrows === 'below') {
                              var $dots = $self.find('.sby-owl-dots'),
                                $prev = $self.find('.sby-owl-prev'),
                                $next = $self.find('.sby-owl-next'),
                                $nav = $self.find('.sby-owl-nav'),
                                $dot = $self.find('.sby-owl-dot'),
                                widthDots = $dot.length * $dot.innerWidth(),
                                maxWidth = $self.innerWidth();

                              $prev.after($dots);

                              $nav.css('position', 'relative');
                              $next.css('position', 'absolute').css('top', '-6px').css('right', Math.max((.5 * $nav.innerWidth() - .5 * (widthDots) - $next.innerWidth() - 6), 0));
                              $prev.css('position', 'absolute').css('top', '-6px').css('left', Math.max((.5 * $nav.innerWidth() - .5 * (widthDots) - $prev.innerWidth() - 6), 0));
                          } else if (arrows === 'hide') {
                              $navElementsWrapper.addClass('hide').hide();
                          }

                      }, 1);
                  };

                //Disable mobile layout
                if( $self.hasClass('sby_mob_col_auto') ) {
                    itemsTabletSmall = 2;
                    if( parseInt(cols) != 2 ) itemsMobile = 1;
                    if( parseInt(cols) == 2 ) itemsMobile = 2; //If the cols are set to 2 then don't change to 1 col on mobile
                } else {
                    itemsMobile = colsmobile;
                }

                this.carouselArgs = {
                    items: cols,
                    loop: loop,
                    rewind: !loop,
                    autoplay: autoplay,
                    autoplayTimeout: Math.max(time,2000),
                    autoplayHoverPause: true,
                    nav: true,
                    navText: ['<svg class="svg-inline--fa fa-chevron-left fa-w-10" aria-hidden="true" data-fa-processed="" data-prefix="fa" data-icon="chevron-left" role="presentation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M34.52 239.03L228.87 44.69c9.37-9.37 24.57-9.37 33.94 0l22.67 22.67c9.36 9.36 9.37 24.52.04 33.9L131.49 256l154.02 154.75c9.34 9.38 9.32 24.54-.04 33.9l-22.67 22.67c-9.37 9.37-24.57 9.37-33.94 0L34.52 272.97c-9.37-9.37-9.37-24.57 0-33.94z"></path></svg>', '<svg class="svg-inline--fa fa-chevron-right fa-w-10" aria-hidden="true" data-fa-processed="" data-prefix="fa" data-icon="chevron-right" role="presentation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path></svg>'],
                    dots: pagination,
                    owl2row: has2rows,
                    responsive: {
                        0: {
                            items: itemsMobile
                        },
                        480: {
                            items: itemsTabletSmall
                        },
                        640: {
                            items: cols
                        }
                    },
                    onChange: onChange,
                    onInitialize: afterInit
                };

            };

            this.stripEmojihtml = function ($el) {
                $el.find('.emoji').each(function() {
                    $(this).replaceWith($(this).attr('alt'));
                });

                return $el.html();
            };

            this.afterItemRevealed = function($item) {

                var feed = this;
                if ($item.find('.sby_caption').length && ! $item.find('.sby_caption').hasClass('sby_full_caption')) {
                    //Expand post
                    var $caption = $item.find('.sby_item_caption_wrap .sby_caption'),
                      $hoverCaption = $item.find('.sby_item_video_thumbnail .sby_caption'),
                      text_limit = typeof feed.settings.general.descriptionlength !== 'undefined' ? parseInt(feed.settings.general.descriptionlength) : 150;
                    if (text_limit < 1) text_limit = 99999;
                    //Set the full text to be the caption (used in the image alt)

                    var captionText = this.stripEmojihtml($item.find('.sby_caption').first()),
                      brCount = (captionText.match(/<br>/g) || []).length,
                      brAdjust = (typeof sbyOptions.brAdjust === 'undefined' || sbyOptions.brAdjust === '1' || sbyOptions.brAdjust === true);
                      // comment out unnecessary code that stripes out text limit with wrong text limit
                    // replace emoji with alt for more accurate shortening
//                     if (brAdjust && brCount > 0 && captionText.indexOf('<br>') < text_limit) {
//                         var $sizingCaption = $item.find('.sby_video_title').first();
//                         captionWidth = $sizingCaption.width() > 20 ? $sizingCaption.width() : $item.width(),
//                           fontSize = $sizingCaption.css('font-size'),
//                           charactersPerLine = captionWidth / parseInt(fontSize) * 1.85,
//                           maxCharsPerLine = Math.floor(charactersPerLine),
//                           projectedMaxLines = Math.ceil(text_limit / charactersPerLine);
//                         var splitCaption = captionText.split('<br>'),
//                           linesConsumed = 0,
//                           adjustedTextLimit = 0;
//                         jQuery.each(splitCaption, function () {
//                             var linesLeft = projectedMaxLines - linesConsumed;
//                             if (linesLeft > 0) {
//                                 var thisLinesConsumed = Math.max(1, Math.ceil(this.length / charactersPerLine));
//                                 adjustedTextLimit += Math.min(this.length + 4, linesLeft * maxCharsPerLine);
//                                 linesConsumed += thisLinesConsumed;
//                             }
//                         });
//                         text_limit = adjustedTextLimit;
//                     }
                    var short_text = captionText.substring(0, text_limit);
                    short_text = captionText.length > text_limit ? short_text.substr(0, Math.min(short_text.length, short_text.lastIndexOf(" "))) : short_text;


                    //Cut the text based on limits set
                    if ($caption.length) {
                        $caption.html(sbyLinkify(short_text));
                        if (short_text === captionText) {
                            $caption.next('.sby_expand').remove();
                        }
                    }
                    if ($hoverCaption.length) {
                        var hoverCaptionText = short_text;
                        if (short_text !== captionText) {
                            hoverCaptionText += '<span class="sby_more">...</span>';
                        }
                        $hoverCaption.html(hoverCaptionText);
                    }

                    //Show the 'See More' link if needed
                    if (captionText.length > text_limit) {
                        $item.find('.sby_expand').show();
                    }
                    //Click function
                    $item.find('.sby_expand a').off('click').on('click', function (e) {
                        e.preventDefault();
                        var $expand = jQuery(this);
                        $caption = typeof $caption !== 'undefined' ? $caption : $item.find('.sby_info .sby_caption');
                        captionText = typeof captiontext !== 'undefined' ? captionText : sbyEncodeInput($item.find('.sby_item_video_thumbnail').attr('data-title'));
                        if ($item.hasClass('sby_caption_full') && typeof short_text !== 'undefined') {
                            $caption.html(short_text);
                            $item.removeClass('sby_caption_full');
                        } else {
                            $caption.html(sbyLinkify(captionText));
                            $item.addClass('sby_caption_full');
                        }
                        feed.afterResize();
                    });
                }

                this.setUpCTA($item);

                //Photo links
                //If lightbox is disabled
                var disablelightbox = typeof feed.settings.general.disablelightbox !== 'undefined' ? feed.settings.general.disablelightbox : false,
                  captionlinks = typeof feed.settings.general.captionlinks !== 'undefined' ? feed.settings.general.captionlinks : false;
                if( disablelightbox || captionlinks ){

                    if (captionlinks) {
                        function sbyUrlDetect(text) {
                            var urlRegex = /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g;
                            return text.match(urlRegex);
                        }

                        var cap = '';
                        if (typeof $item.find('img').attr('alt') !== 'undefined') {
                            cap = $item.find('img').attr('alt');
                        } else if (typeof $item.find('video').attr('alt') !== 'undefined') {
                            cap = $item.find('video').attr('alt');
                        }

                        var url = sbyUrlDetect(cap);
                        if(url) {
                            $item.find('a').attr('href', url);
                        }
                    }
                    $item.find('.sby_link').addClass('sby_disable_lightbox');
                    //If lightbox is enabled add lightbox links
                } else {

                    var $sby_photo_wrap = $item.find('.sby_photo_wrap'),
                      $sby_link = $sby_photo_wrap.find('.sby_link');
                    feedOptions = {
                        hovereffect: 'true'
                    };
                    if(feedOptions.hovereffect == 'none'){
                        //launch lightbox on click
                        $sby_link.css('background', 'none').show();
                        $sby_link.find('*').hide().end().find('.sby_link_area').show();
                    } else {
                        $sby_photo_wrap.on('mouseenter mouseleave', function(e) {
                            switch(e.type) {
                                case 'mouseenter':
                                    $item.addClass('sby_animate');
                                    break;
                                case 'mouseleave':
                                    $item.removeClass('sby_animate');
                                    break;
                            }
                        });

                    }

                }

                var videoID = typeof $item.attr('data-video-id') !== 'undefined' ? $item.attr('data-video-id') : $item.find('.sby_video_thumbnail').attr('data-video-id');
                this.mostRecentlyLoadedPosts.push(videoID);
            };

            this.afterFeedSet = function() {
                if (typeof this.carouselArgs !== 'undefined' ) {
                    $(this.el).find('.sby_carousel').sbyOwlCarousel(this.carouselArgs);
                    if (parseInt(this.settings.general.carousel[5]) === 2) {
                        $(this.el).addClass('sby_carousel_2_row');
                    }

                }
            };

            this.setUpCTA = function($item,videoID) {
                //window.sby.ctas

                var videoID = typeof videoID !== 'undefined' ? videoID : $item.find('.sby_item_video_thumbnail').attr('data-video-id'),
                  text = sbyEncodeInput(typeof $item.find('.sby_item_video_thumbnail').attr('data-title') !== 'undefined' ? $item.find('.sby_item_video_thumbnail').attr('data-title') : ''),
                  ctaInCaption = window.sby.ctaDetect(text);

                if (ctaInCaption) {
                    window.sby.ctas[videoID] = ctaInCaption;
                } else {
                    window.sby.ctas[videoID] = this.getDefaultCTA();
                }
            };

            this.getDefaultCTA = function(){
                if (typeof this.settings.general.cta !== 'undefined' && this.settings.general.cta.type !== 'default') {
                    if (this.settings.general.cta.type === 'link') {
                        return {
                            callback: 'link',
                            url: this.settings.general.cta.defaultLink,
                            text: this.settings.general.cta.defaultText,
                        }
                    } else {
                        return {
                            callback: 'related',
                            related: this.settings.general.cta.defaultPosts
                        }
                    }
                } else {
                    return false;
                }
            };

            this.afterResize = function() {
                this.setImageHeight();
                this.setImageResolution();
                this.maybeRaiseImageResolution();
                this.setImageSizeClass();
                this.setAllCTADimensions();
                this.sizePlayer();
                this.sizeItems();
            };

            this.setAllCTADimensions = function() {
                $.each(this.CTA, function(index, CTAObj) {
                    if (CTAObj.isInitialized) {
                        CTAObj.setCTAStyles();
                    }
                });
            };

            this.afterSendCheckWPPostsToServer = function(response){
                var $self = $(this.el);

                $self.find('.sby_item').each(function() {
                    if (typeof response[ $(this).attr('data-video-id') ] !== 'undefined') {
                        var data = response[ $(this).attr('data-video-id') ];
                        //sby_views_count
                        $(this).find('.sby_view_count').text(data.sby_view_count);
                        $(this).find('.sby_comment_count').text(data.sby_comment_count);
                        $(this).find('.sby_like_count').text(data.sby_like_count);

                        //Set for attributes too.
                        $(this).find('.sby_video_thumbnail').attr('data-views',data.sby_view_count);
                        $(this).find('.sby_video_thumbnail').attr('data-comment-count', data.sby_comment_count);

                        if (data.sby_live_broadcast.broadcast_type !== 'none') {
                            $(this).find('.sby_ls_message').text(data.sby_live_broadcast.live_streaming_string);
                            $(this).find('.sby_date').html(data.sby_live_broadcast.live_streaming_date);
                        }
                        if (typeof data.sby_live_broadcast.live_streaming_timestamp !== 'undefined') {
                            $(this).attr('data-live-date',data.sby_live_broadcast.live_streaming_timestamp);
                        }
                        if (typeof data.sby_description !== 'undefined') {
                            $(this).find('.sby_item_video_thumbnail').attr('data-title', sbyEncodeInput(data.sby_description) );
                        }
                    }
                });

                $self.find('.sby_player_item').each(function() {
                    if (typeof response[ $(this).find('.sby_video_thumbnail').attr('data-video-id') ] !== 'undefined') {
                        var data = response[ $(this).find('.sby_video_thumbnail').attr('data-video-id') ];
                        $(this).find('.sby_view_count').text(data.sby_view_count);
                        $(this).find('.sby_comment_count').text(data.sby_comment_count);
                        $(this).find('.sby_like_count').text(data.sby_like_count);
                        if (data.sby_live_broadcast.broadcast_type !== 'none') {
                            $(this).find('.sby_ls_message').text(data.sby_live_broadcast.live_streaming_string);
                            $(this).find('.sby_date').html(data.sby_live_broadcast.live_streaming_date);
                        }
                        if (typeof data.sby_live_broadcast.live_streaming_timestamp !== 'undefined') {
                            $(this).attr('data-live-date',data.sby_live_broadcast.live_streaming_timestamp);
                        }
                    }
                });

                var evt = jQuery.Event('sbyaftercheckposts');
                evt.feed = window.sby.feeds[index];
                evt.response = response;
                jQuery(window).trigger(evt);

            };

            this.afterStateChange = function(playerID,videoID,data,$player) {
                this.CTA[playerID].toggleCTA(videoID,data.data,$player);
            };

            this.changePlayerInfo = function($newItem) {
                var $self = $(this.el);
                $self.find('.sby_player_item').find('.sby_info').replaceWith(
                  $newItem.find('.sby_info').clone(true,true)
                );
 
                const videoTitle = checkValue($newItem.attr('data-video-title'));
                const videoPublishData = checkValue($newItem.find('.sby_video_thumbnail').attr('data-formatted-published-date')) ;

                $self.find('.sby-player-info .sby-video-header-info .sby-video-info-header h5').text( videoTitle );
                $self.find('.sby-player-info .sby-video-header-meta .sby-video-date').text( videoPublishData );

                resetComments($self);
                openComments();
            };

            this.maybeAddCTA = function(playerID,$el) {
                if (typeof this.CTA[playerID] === 'undefined') {
                    this.CTA[playerID] = new SbyCTA(playerID,this);
                }
            };
        }

        SbyFeedPro.prototype = Object.create(SbyFeed.prototype);

        function SbyLightboxBuilder() {}

        SbyLightboxBuilder.prototype = {
            getData: function(a){
                var closestFeedIndex = parseInt(a.closest('.sb_youtube').attr('data-sby-index')-1);
                return {
                    feedIndex : closestFeedIndex,
                    link: a.attr("href"),
                    videoTitle: typeof a.attr("data-video-title") !== 'undefined' ? sbyEncodeInput(a.attr("data-video-title")) : 'YouTube Video',
                    video: a.attr("data-video-id"),
                    channelID: a.attr("data-channel-id")
                }
            },
            template: function () {
                return "<div id='sby_lightboxOverlay' class='sby_lightboxOverlay'></div>"+
                  "<div id='sby_lightbox' class='sby_lightbox'>"+
                  "<div class='sby_lb-outerContainer'>"+
                  "<div class='sby_lb-container'>"+
                  "<img class='sby_lb-image' alt='Lightbox image placeholder' src='' />"+
                  "<div class='sby_lb-player sby_lb-player-placeholder' id='sby_lb-player'></div>" +
                  "<div class='sby_lb-nav'><a class='sby_lb-prev' href='#' ><p class='sby-screenreader'>Previous Slide</p><span></span></a><a class='sby_lb-next' href='#' ><p class='sby-screenreader'>Next Slide</p><span></span></a></div>"+
                  "<div class='sby_lb-loader'><a class='sby_lb-cancel'></a></div>"+
                  "</div>"+
                  "</div>"+
                  "<div class='sby_lb-dataContainer'>"+
                  "<div class='sby_lb-data'>"+
                  "<div class='sby_lb-details'>"+
                  "<div class='sby_lb-caption'></div>"+
                  "<div class='sby_lb-info'>"+
                  "<div class='sby_lb-number'></div>"+
                  "</div>"+
                  "</div>"+
                  "<div class='sby_lb-closeContainer'><a class='sby_lb-close'></a></div>"+
                  "</div>"+
                  "</div>"+
                  "</div>";
            },
            beforePlayerSetup: function($lightbox,data,index,album,feed){

            },
            afterPlayerSetup: function ($lightbox,data,index,album) {
            },
            afterResize: function(){
                var playerHeight = $('#sby_lightbox .sby_lb-player').height();

                if (playerHeight > 100) {
                    var heightDif = $('#sby_lightbox .sby_lb-outerContainer').height() - playerHeight;
                    if (heightDif > 10) {
                        $('#sby_lightbox .sby_lb-player').css('top',heightDif/2);
                    }
                }
            },
            pausePlayer: function () {
                if (typeof window.sbyLightboxPlayer === 'undefined'
                  && typeof YT === 'undefined') {
                    return;
                }
                if (typeof YT.get('sby_lb-player') !== 'undefined'
                  && typeof YT.get('sby_lb-player').pauseVideo === 'function') {
                    YT.get('sby_lb-player').pauseVideo()
                } else if (typeof window.sbyLightboxPlayer !== 'undefined'
                  && typeof window.sbyLightboxPlayer.pauseVideo === 'function') {
                    window.sbyLightboxPlayer.pauseVideo();
                }

            }
        };

        SbyLightboxBuilderPro.prototype = Object.create(SbyLightboxBuilder.prototype);

        function SbyLightboxBuilderPro() {
            SbyLightboxBuilder.call(this);

               

            this.getData = function(a){
                const feedParent = a.closest('.sb_youtube');
                var closestFeedIndex = parseInt(feedParent.attr('data-sby-index')-1);
                const subscribeBtnText = feedParent.attr('data-subscribe-btn-text');
                const subscribeBtn = feedParent.attr('data-subscribe-btn');
                const colorScheme = feedParent.hasClass('sby_palette_dark') ? 'dark' : 'light';
                const atts = feedParent.attr('data-shortcode-atts');
                const liveDataAttr = a.closest('.sby_item').attr('data-live-date');
                const channelHeaderColorsAttr = feedParent.attr('data_channel_header_colors') ?  JSON.parse(feedParent.attr('data_channel_header_colors')) : '';

                return {
                    feedIndex : closestFeedIndex,
                    link: a.attr("href"),
                    video: a.attr("data-video-id"),
                    title: sbyEncodeInput(a.attr("data-title")),
                    videoTitle: typeof a.attr("data-video-title") !== 'undefined' ? sbyEncodeInput(a.attr("data-video-title")) : 'YouTube Video',
                    avatar: a.attr("data-avatar"),
                    user: sbyEncodeInput(a.attr("data-user")),
                    channelURL: a.attr("data-url"),
                    channelID: a.attr("data-channel-id"),
                    channelSubscribers: a.closest('.sb_youtube').attr('data-channel-subscribers'),
                    subscribeBtn: subscribeBtn,
                    subscribeBtnText: subscribeBtnText,
                    colorScheme: colorScheme,
                    publishedDate: a.attr("data-published-date"),
                    commentCount: a.attr("data-comment-count"),
                    views: a.attr("data-views"),
                    liveData: liveDataAttr,
                    channelHeaderColors : channelHeaderColorsAttr,
                    atts: atts
                }
            };

            this.template = function() {
                return `
                <div id='sby_lightboxOverlay' class='sby_lightboxOverlay'></div>
                <div id='sby_lightbox' class='sby_lightbox'>
                 <div class='sby_lb-header'></div>
                  <div class='sby_lb-outerContainer'>
                    <a class='sby_lb-close'></a>
                    <div class='sby_lb-container'>
                      <div class='sby_lb_video_thumbnail_wrap'>
                        <span class='sby_lb_video_thumbnail'>
                          <img class='sby_lb-image' alt='Lightbox image placeholder' src='' />
                          <div class='sby_lb-player' id='sby_lb-player'></div>
                        </span>
                      </div>
                      <div class='sby_lb-nav'>
                        <a class='sby_lb-prev' href='#'>
                          <p class='sby-screenreader'>Previous Slide</p>
                          <span></span>
                        </a>
                        <a class='sby_lb-next' href='#'>
                          <p class='sby-screenreader'>Next Slide</p>
                          <span></span>
                        </a>
                      </div>
                      <div class='sby_lb-loader'>
                        <a class='sby_lb-cancel'></a>
                      </div>
                    </div>
                  </div>
                  <div class='sby_lb-dataContainer'>
                    <div class='sby_lb-data'>
                      <div class='sby_lb-details'>
                        <div class='sby_lb-caption'>
                        </div>
                        <div class='sby_lb-info'>
                          <div class='sby_lb-number'></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>`;
            };

            this.beforePlayerSetup = function($lightbox,data,index,album,feed){
                
                $('body').css('overflow', 'hidden');
                if (!$lightbox.find('.sby_cta_items_wraps').length) {
                    $lightbox.find('.sby_lb_video_thumbnail_wrap').append($(feed.el).find('.sby_cta_items_wraps').clone());
                } else {
                    $lightbox.find('.sby_cta_items_wraps').replaceWith($(feed.el).find('.sby_cta_items_wraps').clone());
                }
            };

            this.afterPlayerSetup = function($lightbox,data,index,album) {

                this.availableAvatarUrls = {};
                const subscribeSection = data?.subscribeBtn ? data.subscribeBtn : false;
                const subscribeBtnText = data?.subscribeBtnText ? data.subscribeBtnText : '';

                if (typeof sbyLightboxAction === 'function') {
                    setTimeout(function() {
                        sbyLightboxAction();
                    },100);
                }

                if(data?.colorScheme && 'dark' === data.colorScheme ) {
                    LightboxColorScheme(data.colorScheme, true);
                }

                let avatarImage = '',

                subscribeBtn = subscribeSection ? '<a class="sby-lb-subscribe-btn" href="http://www.youtube.com/channel/'+ data.channelID +'?sub_confirmation=1&feature=subscribe-embed-click" target="_blank" rel="noopener noreferrer">'+ getStaticSVG('youtube') +' <p>' + subscribeBtnText +'</p></a>' : '';
                if (typeof data.avatar !== 'undefined' && data.avatar !== '' && typeof data.user !== 'undefined') {
                    avatarImage = (data.avatar !== 'undefined') ? data.avatar : '';
                } else if (typeof data.user !== 'undefined') {
                    jQuery.each(window.sby.feeds, function() {
                        if (typeof this.availableAvatarUrls !== 'undefined' && typeof this.availableAvatarUrls[data.user] !== 'undefined' && this.availableAvatarUrls[data.user] !== 'undefined') {
                            avatarImage = this.availableAvatarUrls[data.user];
                        }
                    });
                }

                const channelSubscribers = data?.channelSubscribers ?? '';
                const avatarImageHtml = avatarImage ? '<img src="'+ avatarImage +'" referrerPolicy="no-referrer"/>' : getStaticSVG('profile-picture');
                const userHtml = subscribeSection && avatarImage ? '<div class="sby-lb-channel-header"><a class="sby_lightbox_username" href="'+ data.channelURL+'" target="_blank" rel="noopener">'+ avatarImageHtml + '<p class="sby-lb-channel-name-with-subs"><span>@'+data.user + '</span><span>' + channelSubscribers +'</span></p></a> ' + subscribeBtn + '</div>' : '';
                const subscribeClass = subscribeSection && avatarImage ? 'sby_lb-channel-info' : 'sby_lb-no-channel-info'

                if( window.sbyOptions.isPro ) {

                const description = data?.title ? addLinksTotext(data.title) : '';
                const publishedDate = data?.publishedDate ? timeAgo(convertUnixToMs(data.publishedDate)) : '';
                const views = data?.views ? data.views : '';

                const videoHeaderSection  = `
                    <div class="sby_lb-video-heading">
                        <h3>${data.videoTitle}</h3>
                        <div class="sby_lb-video-info">
                            <span>${views}</span>
                            <span class="sby_lb-spacer">·</span>
                            <span>${publishedDate}</span>
                        </div>
                    </div>
                `;

                const videoDescriptionhtml = `
                    <div class="sby_lb-video-description-wrap">
                        <div class="sby_lb-description sby-read-more-target">
                            ${description}
                        </div>
                        <button class="sby_lb-more-info-btn sby-read-more-trigger">Description${getStaticSVG('angle-down')}</button>
                    </div>
                `;

                const commentSectionHtml = `
                    <div class="sby-comments-wrap">
                    </div>
                `;

                const videoDescription = description ? videoDescriptionhtml : '';

                $lightbox.find(".sby_lb-caption").html( `<div class="sby_lb-caption-inner ${subscribeClass}">` + videoHeaderSection + userHtml + videoDescription + commentSectionHtml + `</div>` ).fadeIn("fast");

                if( data?.liveData && '0' === data.liveData ) {
                    const videoId = data?.video ? data.video : ''; 
                    const atts = data?.atts ? data.atts : '';
                    const currentCommentCount = data?.commentCount ? data.commentCount : ''; 
                    const target = $lightbox.find(".sby-comments-wrap");
                    generateCommentSection(videoId, atts, target, currentCommentCount);
                } else {
                    toggleReadMore();
                }

                if( data?.channelHeaderColors ) {
                    setColorsToChannelHeader(data.channelHeaderColors)
                }
                
            }
            };
        }

        function SbyCTA(videoID,feed) {
            this.isInitialized = false;
            this.videoID = videoID;
            this.callback = this.related;
            this.callbackArgs = {};
            this.feedObjInContext = feed;
            this.state = 1;
            this.numItems = 4;
            this.numItemColumns = 2;
            this.$player = false;
        }

        SbyCTA.prototype = {
            toggleCTA: function(videoID,dataNum,$player){
                this.$player = $player.length ? $player : $('.sby_lb-container'); // use the lightbox container if no player is set
                this.state = dataNum;
                this.videoID = videoID;
                this.isInitialized = true;
                this.resetCTA();

                //ctaDetect

                if (typeof window.sby.ctas[videoID] !== 'undefined') {
                    this.callbackArgs = window.sby.ctas[videoID];
                }
                var callback = this.callbackArgs.callback;

                if (callback === 'link') {
                    this.callback = this.link;
                } else if (callback === 'related') {
                    this.callback = this.related;
                } else {
                    return;
                }

                if (dataNum === 2 || dataNum === 0) {
                    this.$player.find('.sby_cta_items_wraps').addClass('sby_cta_is_open');

                    if ( dataNum === 2 ){
                        this.$player.find('.sby_cta_items_wraps').addClass('sby_cta_state_paused');
                    } else {
                        this.$player.find('.sby_cta_items_wraps').addClass('sby_cta_state_ended');
                    }

                    this.$player.find('.sby_cta_items_wraps').show();
                    this.callback();
                    this.setCTAStyles();

                } else {
                    this.$player.find('.sby_cta_items_wraps').removeClass('sby_cta_is_open');

                    this.$player.find('.sby_cta_items_wraps').hide()
                      .removeClass('sby_cta_state_paused')
                      .removeClass('sby_cta_state_ended')
                      .removeClass('sby_cta_is_open');
                }



            },
            related: function(args) {
                var ctaObj = this,
                  feedObjInContext = this.feedObjInContext,
                  related = window.sby.shuffle(this.getRelated(feedObjInContext)),
                  added = 0,
                  currentVideoId = this.videoID,
                  $player = this.$player;

                this.$player.find('.sby_cta_items_wraps')
                  .removeClass('sby_cta_cols_' + this.numItemColumns);

                this.numItems = 4;
                this.numItemColumns = 2;

                if ($player.width() < 480) {
                    this.numItems = 1;
                    this.numItemColumns = 1
                }

                var numItems = this.numItems;

                $.each(related, function(index, value) {
                    if (value.videoID !== currentVideoId && added < numItems) {
                        $player.find('.sby_cta_items_wraps .sby_cta_inner_wrap').append('<div class="sby_cta_item"><div class="sby_video_thumbnail_wrap">' +
                          '<a class="sby_video_thumbnail" href="javascript:void(0);" target="_blank" rel="noopener" data-video-id="'+value.videoID+'">' +
                          '<div class="sby_thumbnail_hover">' +
                          '<div class="sby_thumbnail_hover_inner">' +
                          '<span class="sby_video_title">'+value.title+'</span>' +
                          '</div>' +
                          '</div>' +
                          '<span class="sby-screenreader">Play</span>' +
                          '<img src="'+value.thumbnail+'" alt="'+value.title+'">' +
                          '<span class="sby_loader sby_hidden" style="background-color: rgb(255, 255, 255);"></span>' +
                          '</a>' +
                          '</div>' +
                          '</div>');
                        added++;
                    }
                });

                $player.find('.sby_cta_items_wraps .sby_video_thumbnail').each(function() {
                    $(this).off().on('click',function (event) {
                        event.preventDefault();
                        var newVideoID = $(this).attr('data-video-id');
                        feedObjInContext.onThumbnailClick($(this),true,newVideoID);
                        ctaObj.videoID = newVideoID;
                    });
                });

            },
            getRelated: function(feedObjInContext) {
                if (typeof feedObjInContext.settings.general.cta.defaultPosts[0] === 'undefined') {
                    var $feedEl = $(feedObjInContext.el),
                      relatedVids = [];
                    $feedEl.find('.sby_item').each(function() {
                        if (typeof $(this).find('.sby_item_video_thumbnail').attr('data-full-res') !== 'undefined') {
                            var thisVid = {
                                videoID: $(this).attr('data-video-id'),
                                title: sbyEncodeInput($(this).attr('data-video-title')),
                                thumbnail: $(this).find('.sby_item_video_thumbnail').attr('data-full-res'),
                            }
                            relatedVids.push(thisVid);
                        }
                    });
                    return relatedVids;
                }
                return feedObjInContext.settings.general.cta.defaultPosts;
            },
            link: function(args) {
                var $player = this.$player,
                  feedObjInContext = this.feedObjInContext;

                this.$player.find('.sby_cta_items_wraps')
                  .removeClass('sby_cta_cols_' + this.numItemColumns);

                this.numItems = 1;
                this.numItemColumns = 1;

                var style = '',
                  styleClass = '';

                if (feedObjInContext.settings.general.cta.color !== '' || feedObjInContext.settings.general.cta.textColor !== '') {
                    style = ' style="';
                    styleClass = ' sby_custom';

                    if (feedObjInContext.settings.general.cta.color !== '') {
                        style += 'background: rgb(' + feedObjInContext.settings.general.cta.color + ');';
                    }
                    if (feedObjInContext.settings.general.cta.textColor !== '') {
                        style += 'color: rgb(' + feedObjInContext.settings.general.cta.textColor + ');';
                    }
                    style += '"';
                }

                var openAtts = '';
                if (feedObjInContext.settings.general.cta.openType === 'newwindow') {
                    openAtts = ' target="_blank" rel="noopener"';
                }

                $player.find('.sby_cta_items_wraps .sby_cta_inner_wrap').append('<div class="sby_cta_item">' +
                  '<div class="sby_btn_wrap">' +
                  '<div class="sby_btn'+styleClass+'">' +

                  '<a class="sby_cta_button" href="'+this.callbackArgs.url+'"'+openAtts+' data-video-id="'+this.videoID+'"'+style+'>' +
                  this.callbackArgs.text +
                  '</a>' +
                  '</div>' +
                  '</div>' +

                  '</div>'
                );
            },
            setCTAStyles: function() {
                var playerTopHeight = 60,
                  playerBottomHeight = 49,
                  minimumHeight = 90,
                  ctaOverlayHeight = Math.max(minimumHeight,this.$player.height() - playerTopHeight - playerBottomHeight);

                this.$player.find('.sby_cta_items_wraps')
                  .css('height',ctaOverlayHeight+'px')
                  .css('width',(this.$player.find('iframe').width()-20)+'px')
                  .addClass('sby_cta_cols_' + this.numItemColumns);

                var numRows = Math.max(1,this.numItems/this.numItemColumns),
                  totalVerticalPadding = parseInt(this.$player.find('.sby_cta_items_wraps').css('padding-top').replace('px','')) * 2,
                  maxCTAItemHeight = Math.max(minimumHeight,(ctaOverlayHeight-totalVerticalPadding)/numRows);

                this.$player.find('.sby_cta_item').css('max-height',maxCTAItemHeight+'px').find('img').css({
                    'max-height': maxCTAItemHeight+'px',
                    'width': 'auto',
                    'margin': 'auto'
                });
                this.$player.find('.sby_btn_wrap').css('height',maxCTAItemHeight+'px');
            },
            resetCTA: function() {
                this.$player.find('.sby_cta_items_wraps .sby_cta_inner_wrap').empty();
            }

        };

        window.sby_init = function() {
            window.sby = new Sby();
            window.sby.createPage( window.sby.createFeeds, {whenFeedsCreated: window.sby.afterFeedsCreated});
        };



        window.sby_carousel_init = function() {
            console.log('log');
        }

        function sbyGetNewFeed(feed,index,feedOptions) {
            return new SbyFeedPro(feed,index,feedOptions);
        }

        function sbyGetlightboxBuilder() {
            return new SbyLightboxBuilderPro();
        }

        function sbyIsTouch() {
            if ("ontouchstart" in document.documentElement) {
                return true;
            }
            return false;
        }

        function sbyCmplzGetCookie(cname) {
            var name = cname + "="; //Create the cookie name variable with cookie name concatenate with = sign
            var cArr = window.document.cookie.split(';'); //Create cookie array by split the cookie by ';'

            //Loop through the cookies and return the cookie value if it find the cookie name
            for (var i = 0; i < cArr.length; i++) {
                var c = cArr[i].trim();
                //If the name is the cookie string at position 0, we found the cookie and return the cookie value
                if (c.indexOf(name) == 0)
                    return c.substring(name.length, c.length);
            }

            return "";
        }


    })(jQuery);

    if (typeof window.sbyEagerLoading === 'undefined') {
        window.sbyEagerLoading = typeof window.sbyOptions !== 'undefined' ? window.sbyOptions.eagerload : false;
        if (jQuery('.elementor-widget-video').length) {
            var settings = typeof jQuery('.elementor-widget-video').attr('data-settings') !== 'undefined' ? JSON.parse( jQuery('.elementor-widget-video').attr('data-settings')) : false;
            if (settings && typeof settings.youtube_url !== 'undefined') {
                window.sbyEagerLoading = true;
            }
        }

        if (jQuery('div[data-vc-video-bg]').length) {
            window.sbyEagerLoading = true;
        }
    }
    if (typeof window.sbySemiEagerLoading === 'undefined') {
        window.sbySemiEagerLoading = typeof window.sbyOptions !== 'undefined' ? window.sbyOptions.semiEagerload : false;
        if (jQuery('div[data-vc-video-bg]').length || window.sbyEagerLoading) {
            window.sbySemiEagerLoading = false;
        }
    }

    jQuery(document).ready(function($) {
        if (!window.sbySemiEagerLoading) {
            sby_init();
        }

        // Cookie Notice by dFactory
        $('#cookie-notice a').on('click',function() {
            setTimeout(function() {
                $.each(window.sby.feeds,function(index){
                    window.sby.feeds[ index ].afterConsentToggled();
                });
            },1000);
        });

        // Cookie Notice by dFactory
        $('#cookie-law-info-bar a').on('click',function() {
            setTimeout(function() {
                $.each(window.sby.feeds,function(index){
                    window.sby.feeds[ index ].afterConsentToggled();
                });
            },1000);
        });

        // GDPR Cookie Consent by WebToffee
        $('.cli-user-preference-checkbox').on('click',function(){
            setTimeout(function() {
                $.each(window.sby.feeds,function(index){
                    window.sby.feeds[ index ].settings.consentGiven = false;
                    window.sby.feeds[ index ].afterConsentToggled();
                });
            },1000);
        });

        // Cookiebot
        $(window).on('CookiebotOnAccept', function (event) {
            $.each(window.sby.feeds,function(index){
                window.sby.feeds[ index ].settings.consentGiven = true;
                window.sby.feeds[ index ].afterConsentToggled();
            });
        });

        // Complianz by Really Simple Plugins
        document.addEventListener('cmplz_status_change', function (e) {
            if (e.detail.category === 'marketing' && e.detail.value==='allow') {
                $.each(window.sby.feeds,function(index){
                    window.sby.feeds[ index ].settings.consentGiven = true;
                    window.sby.feeds[ index ].afterConsentToggled();
                });
            }
        });

        $(document).on('cmplzFireCategories', function (event) {
            if ( event.detail.category==='marketing' ) {
                $.each(window.sby.feeds,function(index){
                    window.sby.feeds[ index ].settings.consentGiven = true;
                    window.sby.feeds[ index ].afterConsentToggled();
                });
            }
        });

        // Borlabs Cookie by Borlabs
        $(document).on('borlabs-cookie-consent-saved', function (event) {
            $.each(window.sby.feeds,function(index){
                window.sby.feeds[ index ].settings.consentGiven = false;
                window.sby.feeds[ index ].afterConsentToggled();
            });
        });

        if (typeof window.consentApi !== 'undefined') {
            window.consentApi?.consent("feeds-for-youtube").then(() => {
                try {
                    // applies full features to feed
                    $.each(window.sby.feeds,function(index){
                        window.sby.feeds[ index ].settings.consentGiven = true;
                        window.sby.feeds[ index ].afterConsentToggled();
                    });
                }
                catch (error) {
                    // do nothing
                }
            });
        }

        $('.moove-gdpr-infobar-allow-all').on('click',function() {
            setTimeout(function() {
                $.each(window.sby.feeds,function(index){
                    window.sby.feeds[ index ].afterConsentToggled();
                });
            },1000);
        });

        // WPConsent
        window.addEventListener('wpconsent_consent_saved', function(event) {
            setTimeout(function() {
                $.each(window.sby.feeds,function(index){
                    window.sby.feeds[ index ].afterConsentToggled();
                });
            },1000);
        });

        window.addEventListener('wpconsent_consent_updated', function(event) {
            setTimeout(function() {
                $.each(window.sby.feeds,function(index){
                    window.sby.feeds[ index ].afterConsentToggled();
                });
            },1000);
        });

        // hide notice on click and send ajax request to backend
        $('#sby-frce-hide-license-error').on('click',function() {
            $('#sby-fr-ce-license-error').slideUp();
            jQuery.ajax({
                url: sbyOptions.adminAjaxUrl,
                type: 'post',
                data: {
                    action: 'sby_hide_frontend_license_error',
                    nonce: sbyOptions.nonce
                },
                success: function(msg){
                    console.log(msg);
                }
            });
        })
    });

} // if sby_js_exists

if (window.sbySemiEagerLoading) {
    var sbyYScriptId = "sby-youtube-api";
    var sbyYScript = document.getElementById(sbyYScriptId);

    if (sbyYScript === null) {
        var tag = document.createElement("script");
        var firstScript = document.getElementsByTagName("script")[0];

        tag.src = "https://www.youtube.com/iframe_api";
        tag.id = sbyYScriptId;
        firstScript.parentNode.insertBefore(tag, firstScript);

    }
}

window.onYouTubeIframeAPIReady = function() {
    var numFeeds = document.getElementsByClassName('sb_youtube').length;
    if (numFeeds > 0) {
        if (window.sbySemiEagerLoading) {
            if (typeof window.sby !== 'undefined') {
                for (var i = 0; i < numFeeds; i++) {
                    window.sby.feeds[i].playerAPIReady = true;
                }
            } else {
                window.sbyAPIReady = true;
            }
            sby_init();
        } else {

            if (window.sbyEagerLoading) {
                var flagLightbox = false,
                  autoplay = false;

                jQuery('.sb_youtube').each(function(index) {
                    var $self = jQuery(this);

                    if ($self.hasClass('sby_layout_list')) {
                        jQuery(this).addClass('sby_player_loaded');

                        $self.find('.sby_item').each(function() {
                            videoID = jQuery(this).attr('data-video-id');
                            //this.createPlayer(,videoID,0);
                            player = new YT.Player('sby_player_'+videoID, {
                                height: '100',
                                width: '100',
                                videoId: videoID,
                                playerVars: {
                                    modestbranding: 1,
                                    rel: 0,
                                    autoplay: autoplay
                                },
                                events: {
                                    'onStateChange': function(data) {
                                        var videoID = data.target.getVideoData()['video_id'];
                                        if (data.data !== 1) return;
                                        $self.find('.sby_item').each(function() {
                                            var itemVidID = jQuery(this).attr('data-video-id');

                                            if (jQuery(this).find('iframe').length && jQuery(data.target.a).attr('id') !== jQuery(this).find('iframe').attr('id')) {
                                                YT.get('sby_player_'+itemVidID).pauseVideo();
                                            }
                                        });
                                    }
                                }
                            });
                        });

                    } else if ($self.hasClass('sby_layout_gallery')) {
                        jQuery(this).addClass('sby_player_loaded');

                        player = new YT.Player('sby_player'+index, {
                            height: '100',
                            width: '100',
                            videoId: jQuery(this).find('.sby_item').first().attr('data-video-id'),
                            playerVars: {
                                modestbranding: 1,
                                rel: 0,
                                autoplay: autoplay
                            },
                            events: {
                                'onStateChange': function(data) {
                                    var videoID = data.target.getVideoData()['video_id'];
                                    if (data.data !== 1) return;
                                    $self.find('.sby_item').each(function() {
                                        var itemVidID = jQuery(this).attr('data-video-id');

                                        if (jQuery(this).find('iframe').length && jQuery(data.target.a).attr('id') !== jQuery(this).find('iframe').attr('id')) {
                                            YT.get('sby_player_'+itemVidID).pauseVideo();
                                        }
                                    });
                                }
                            }
                        });
                    } else {
                        flagLightbox = true;
                    }
                });

            } else if (typeof window.sby !== 'undefined') {
                for (var i = 0; i < numFeeds; i++) {
                    window.sby.feeds[i].playerAPIReady = true;
                }
            } else {
                window.sbyAPIReady = true;
            }
        }

        jQuery('.sb_youtube').each(function(index) {
            var $self = jQuery(this);
            if ($self.find('.sby_live_player').length) {
                player = new YT.Player($self.find('.sby_live_player').attr('id'), {
                    events: {
                        'onReady': function () {
                            $self.find('.sby_live_player').hide();
                            $self.find('.sby_item').remove();
                            var videoID = YT.get($self.find('.sby_live_player').attr('id')).getVideoData().video_id;
                            $self.find('.sby_player_video_thumbnail').attr('data-video-id',videoID).css('z-index',-1);
                            var itemOffset = $self.find('.sby_item').length,
                              submitData = {
                                  action: 'sby_live_retrieve',
                                  video_id: videoID,
                                  feed_id: $self.attr('data-feedid'),
                                  atts: $self.attr('data-shortcode-atts'),
                              };
                            var onSuccess = function (data) {
                                if (data.trim().indexOf('{') === 0) {
                                    var feed = window.sby.feeds[index],
                                      response = JSON.parse(data),
                                      checkWPPosts = typeof response.feedStatus.checkWPPosts !== 'undefined' ? response.feedStatus.checkWPPosts : false;
                                    if (feed.settings.debugEnabled) {
                                        console.log(response);
                                    }
                                    if (checkWPPosts) {
                                        feed.settings.checkWPPosts = true;
                                    } else {
                                        feed.settings.checkWPPosts = false;
                                    }
                                    feed.appendNewPosts(response.html);
                                    feed.addResizedImages(response.resizedImages);

                                    feed.afterInitialImagesLoaded();

                                    if (!response.feedStatus.shouldPaginate) {
                                        feed.outOfPages = true;
                                        $self.find('.sby_load_btn').hide();
                                    } else {
                                        feed.outOfPages = false;
                                    }

                                    jQuery('.sby_no_js').removeClass('sby_no_js');
                                    $self.find('.sby_live_player').remove();
                                    if ($self.hasClass('sby_layout_gallery')) {
                                        feed.createPlayer('sby_player'+feed.index);
                                    }
                                    $self.find('.sby_player_item').css('opacity',1);
                                    $self.find('.sby_item').css('opacity',1);
                                    $self.find('.sby_player_loading').removeClass('sby_player_loading');
                                    if ($self.hasClass('sby_layout_list')) {
                                        $self.find('.sby_item_video_thumbnail').on('mouseenter',function() {
                                            jQuery(this).css('z-index',-1);
                                        })
                                    }

                                }

                            };
                            jQuery.ajax({
                                url: sbyOptions.adminAjaxUrl,
                                type: 'post',
                                data: submitData,
                                success: onSuccess
                            });
                        }
                    }
                });
            }
        });

        if (flagLightbox) {
            if (!jQuery('#sby_lb-player').length) {
                jQuery('.sb_youtube').first().append('<div class="sby_lb-player-loaded sby_lb-player" id="sby_lb-player" style="display: none;"></div>');
            }
            player = new YT.Player('sby_lb-player', {
                height: '100',
                width: '100',
                videoId: jQuery(this).find('.sby_item').first().attr('data-video-id'),
                playerVars: {
                    modestbranding: 1, 
                    rel: 0,
                    autoplay: autoplay
                }
            });
            window.sbyLightboxPlayer = player;
        }

    }

    if (typeof window.sby !== 'undefined') {
        var evt = jQuery.Event('sbyfeedandytready');
        jQuery(window).trigger(evt);
    }

};

/**
 * Retrieves a specific attribute value from the given API data object.
 * 
 * @param {Object} rootPath
 * @param {string} attrName
 * 
 * @returns {string|boolean}
 */

function getSingleApiData(rootPath, attrName) {
    switch(attrName) {
        case 'authorProfileImageUrl':
            return rootPath?.snippet?.authorProfileImageUrl ? rootPath.snippet.authorProfileImageUrl : '';
        case 'authorDisplayName':
            return rootPath?.snippet?.authorDisplayName ? rootPath.snippet.authorDisplayName : '';
        case 'authorChannelUrl':
            return rootPath?.snippet?.authorChannelUrl ? rootPath.snippet.authorChannelUrl : '';
        case 'textDisplay':
            return rootPath?.snippet?.textDisplay ? rootPath.snippet.textDisplay : '';
        case 'likeCount':
            return rootPath?.snippet?.likeCount ? rootPath.snippet.likeCount : '';
        case 'publishedAt':
            return rootPath?.snippet?.publishedAt ? rootPath.snippet.publishedAt : '';
        case 'totalReplyCount':
            return rootPath?.totalReplyCount ? rootPath.totalReplyCount : '';
        default:
            return false;
    }
}

/**
 * Retrieves a static SVG image based on the provided name.
 * @param {string} name 
 * @returns {string|boolean} 
 */
function getStaticSVG(name) {

    switch(name) {
        case 'profile-picture':
            return '<svg fill="currentColor" width="800px" height="800px" viewBox="0 0 512 512" id="_x30_1" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M256,0C114.615,0,0,114.615,0,256s114.615,256,256,256s256-114.615,256-256S397.385,0,256,0z M256,90  c37.02,0,67.031,35.468,67.031,79.219S293.02,248.438,256,248.438s-67.031-35.468-67.031-79.219S218.98,90,256,90z M369.46,402  H142.54c-11.378,0-20.602-9.224-20.602-20.602C121.938,328.159,181.959,285,256,285s134.062,43.159,134.062,96.398  C390.062,392.776,380.839,402,369.46,402z"/></svg>';
        case 'thumbs-up':
            return '<svg width="15" height="13" viewBox="0 0 15 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.4159 4.18027C13.761 4.18027 14.0778 4.32177 14.3664 4.60477C14.6549 4.88777 14.7992 5.20738 14.7992 5.5636V6.2706C14.7992 6.36471 14.7902 6.45188 14.7722 6.5321C14.7542 6.61232 14.7272 6.69266 14.6912 6.7731L12.684 11.4908C12.5845 11.7449 12.4181 11.9486 12.1849 12.1019C11.9517 12.2552 11.69 12.3318 11.3999 12.3318H5.15938C4.77282 12.3318 4.44566 12.2006 4.17788 11.9383C3.90999 11.6759 3.77604 11.346 3.77604 10.9484V4.7561C3.77604 4.56277 3.81332 4.38049 3.88788 4.20927C3.96254 4.03804 4.06477 3.88754 4.19454 3.75777L7.28938 0.662932C7.5186 0.431043 7.79427 0.281321 8.11638 0.213765C8.43849 0.146321 8.71416 0.178988 8.94338 0.311765C9.22549 0.46421 9.40932 0.695932 9.49488 1.00693C9.58032 1.31793 9.58999 1.62804 9.52388 1.93727L9.09554 4.18027H13.4159ZM1.34404 12.3318C1.01393 12.3318 0.726767 12.2097 0.482544 11.9654C0.238322 11.7212 0.116211 11.434 0.116211 11.1039V5.40827C0.116211 5.07804 0.236989 4.79082 0.478544 4.5466C0.7201 4.30238 1.00466 4.18027 1.33221 4.18027H1.34804C1.67827 4.18027 1.96549 4.30238 2.20971 4.5466C2.45393 4.79082 2.57604 5.07804 2.57604 5.40827V11.1039C2.57604 11.434 2.45393 11.7212 2.20971 11.9654C1.96549 12.2097 1.67827 12.3318 1.34804 12.3318H1.34404Z" fill="currentColor"/></svg>';
        case 'angle-down':
            return '<svg width="8" height="6" viewBox="0 0 8 6" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.94 0.726654L4 3.77999L7.06 0.726654L8 1.66665L4 5.66665L0 1.66665L0.94 0.726654Z" fill="currentColor"/></svg>';
        case 'youtube':
            return '<svg width="14" height="11" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.66671 7.5L9.12671 5.5L5.66671 3.5V7.5ZM13.3734 2.28C13.46 2.59334 13.52 3.01334 13.56 3.54667C13.6067 4.08 13.6267 4.54 13.6267 4.94L13.6667 5.5C13.6667 6.96 13.56 8.03334 13.3734 8.72C13.2067 9.32 12.82 9.70667 12.22 9.87334C11.9067 9.96 11.3334 10.02 10.4534 10.06C9.58671 10.1067 8.79337 10.1267 8.06004 10.1267L7.00004 10.1667C4.20671 10.1667 2.46671 10.06 1.78004 9.87334C1.18004 9.70667 0.793374 9.32 0.626707 8.72C0.540041 8.40667 0.480041 7.98667 0.440041 7.45334C0.393374 6.92 0.373374 6.46 0.373374 6.06L0.333374 5.5C0.333374 4.04 0.440041 2.96667 0.626707 2.28C0.793374 1.68 1.18004 1.29334 1.78004 1.12667C2.09337 1.04 2.66671 0.980002 3.54671 0.940002C4.41337 0.893336 5.20671 0.873336 5.94004 0.873336L7.00004 0.833336C9.79337 0.833336 11.5334 0.940003 12.22 1.12667C12.82 1.29334 13.2067 1.68 13.3734 2.28Z" fill="currentColor"/></svg>';
        case 'cross':
            return '<svg width="15" height="14" viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.25 1.41L12.84 0L7.25 5.59L1.66 0L0.25 1.41L5.84 7L0.25 12.59L1.66 14L7.25 8.41L12.84 14L14.25 12.59L8.66 7L14.25 1.41Z" fill="currentColor"/></svg>';
        case 'message':
            return '<svg width="28" height="26" viewBox="0 0 28 26" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.33341 22C2.60008 22 1.9723 21.7389 1.45008 21.2167C0.927859 20.6944 0.666748 20.0667 0.666748 19.3333V3.33334C0.666748 2.6 0.927859 1.97223 1.45008 1.45001C1.9723 0.927783 2.60008 0.666672 3.33341 0.666672H24.6667C25.4001 0.666672 26.0279 0.927783 26.5501 1.45001C27.0723 1.97223 27.3334 2.6 27.3334 3.33334V24.1C27.3334 24.7 27.0612 25.1167 26.5167 25.35C25.9723 25.5833 25.489 25.4889 25.0667 25.0667L22.0001 22H3.33341ZM23.1334 19.3333L24.6667 20.8333V3.33334H3.33341V19.3333H23.1334Z" fill="currentColor"/></svg>';
        default:
          return false;
      }
}


/**
 * Generates the HTML template for a single comment.
 * 
 * @param {string} authorProfileImageUrl
 * @param {string} authorDisplayName
 * @param {string} authorChannelUrl
 * @param {string} textDisplay
 * @param {number} likeCount
 * @param {string} publishedAt
 * @param {number} totalReplyCount
 * 
 * @returns {string}
 */
function commentSingleTemplate(authorProfileImageUrl, authorDisplayName, authorChannelUrl, textDisplay, likeCount, publishedAt, totalReplyCount) {

    const dummyProfilePic = authorProfileImageUrl ? `<img src=${authorProfileImageUrl} loading="lazy" referrerPolicy="no-referrer"/>` : getStaticSVG('profile-picture');
    const replies = totalReplyCount ? `<button class="sby-replies">${totalReplyCount ? totalReplyCount : 0 } Replies ${getStaticSVG('angle-down')}</button>` : '';

    return `
            <div class="sby-comment-profile-pic">
                ${dummyProfilePic}
            </div>
            <div class="sby-comment-heading">
                <a href="${authorChannelUrl}" target="_blank" class="sby-comment-user-name">${authorDisplayName}</a>
                <span>${timeAgo(publishedAt)}</span>
            </div>
                <div class="sby-comment-text">
                <p class="sby-read-more-target">${textDisplay}</p>
                <div class="sby-read-more-trigger">
                    <button class="sby-read-more-text">Read More</button>
                    <button class="sby-read-less-text">Read Less</button>
                </div>
             </div>
            <div class="sby-comment-bottom">
                <span class="sby-comment-likes">
                    ${getStaticSVG('thumbs-up')} ${likeCount ? formatLargeNumber(likeCount) : 0 }
                </span>
                ${replies}
            </div>
    `;
}

/**
 * Generates the HTML template when no comments are found.
 *
 * @returns {string}
 */

function noCommentsTemplate() {
    return `
        <h4 class="sby-comments-sub-heading">Comments</h4>
        <div class="sby-no-comments">
            ${getStaticSVG('message')}
            <p>There are no comments to display</p>
        </div>`;
}

/**
 * Generates the HTML template when there is an error retriving comments.
 *
 * @returns {string}
 */
function errorCommentTemplate(error) {
    return `
        <h4 class="sby-comments-sub-heading">Comments</h4>
        <div class="sby-no-comments">
            <p>${error}</p>
        </div>`;
}

/**
 * Format date and time for ISO 8601
 * 
 * @param timestamp
 * 
 * @returns {string}
 */
function timeAgo(timestamp) {
    const now = new Date();
    const past = new Date(timestamp);
    const diffMs = now - past;

    // Helper functions to get time units
    const seconds = Math.floor(diffMs / 1000);
    const minutes = Math.floor(seconds / 60);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);
    const months = Math.floor(days / 30);
    const years = Math.floor(months / 12);

    // Determine the largest unit of time that applies
    if (years > 0) {
        return `${years} year${years > 1 ? 's' : ''} ago`;
    }
    if (months > 0) {
        return `${months} month${months > 1 ? 's' : ''} ago`;
    }
    if (days > 0) {
        return `${days} day${days > 1 ? 's' : ''} ago`;
    }
    if (hours > 0) {
        return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    }
    if (minutes > 0) {
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    }
    if (seconds > 0) {
        return `${seconds} second${seconds > 1 ? 's' : ''} ago`;
    }

    return 'just now';
}

/**
 * Converts plain text into HTML with clickable links.
 * 
 * @param {string} text
 * 
 * @returns {string} 
 */
function addLinksTotext(text) {
    //Add links to the caption
    if(!text) {
        return '';
    }

    text = text.replace(/(>#)/g,'> #');

    return sbyLinkify(text);
}

/**
 * Convert Unix timestamp to milliseconds
 * @param timestamp
 * @returns {string}
 */

function convertUnixToMs(timestamp) {

    if( ! timestamp ) {
        return '';
    }

    return new Date(parseInt(timestamp) * 1000);

}

/**
 * Toggles the visibility of accordion sections based on the trigger element.
 * 
 * @param {string} className
 * @param {string} target
 * @param {string} parent
 * @param {string} trigger 
 * 
 * @returns {void} 
 */
function toggleAccordion(className, target, parent ,trigger  ) {
    jQuery(trigger).css('display', 'none');
    jQuery(target).unbind('click');
    jQuery(target).click(function(){
        jQuery(this).toggleClass(className + '-trigger');
        jQuery(this).closest(parent).find(trigger).toggle();
    });
}
/**
 * Toggles the visibility of "Read More" buttons
 * 
 * @returns {void}
 */
function toggleReadMore() {
    const target = jQuery('.sby-read-more-target');
    const triggerClassName = '.sby-read-more-trigger';

    target.each(function(e) {
        const currentTarget = jQuery(this)[0];

        const paragraphHeight = currentTarget.scrollHeight;
        const clientHeight = currentTarget.offsetHeight;
        const hasMoreThanFourLines = paragraphHeight > clientHeight && paragraphHeight > clientHeight + 1; // clientHeight + 1 to fix firefox clientHeight calculate issue.

        if (hasMoreThanFourLines) {
            const trigger = jQuery(this).parent().find(triggerClassName);

            trigger.unbind('click');
            trigger.click(function() {
                jQuery(this).toggleClass('sby-read-more-trigger-active');
                jQuery(this).parent().find('.sby-read-more-target').toggleClass('sby-read-more-target-active');
            });
        } else {
            jQuery(this).parent().find(triggerClassName).hide();
        }

    });
}

/**
 * Applies a color scheme class to the lightbox based on the flag provided.
 * 
 * @param {string} colorScheme 
 * @param {boolean} flag
 */
function LightboxColorScheme(colorScheme, flag) {

    const commentWrap = jQuery('.sby_lb-caption');
    const colorSchemeClassName = 'sby-lb-dark-scheme';

    if( false === flag) {
        commentWrap.removeClass(colorSchemeClassName);
        return false;
    }

    if( 'dark' === colorScheme && true === flag) {
        commentWrap.addClass(colorSchemeClassName);
        return false;
    }
}

/**
 * Resets the body's overflow style and the lightbox color scheme when the lightbox is closed.
 *
 * @returns {void}
 */
function lightboxOnClose() {
    jQuery('body').css('overflow', 'auto');
    LightboxColorScheme('', false);
    jQuery('.sby_gdpr_notice').remove();
}

/**
 * Retrieves the layout type of the closest ancestor element with a specific layout class.
 *
 * @param {jQuery|HTMLElement} target
 * @returns {string|boolean}
 */
function getLayout(target) {
    const currentTarget = target.closest('.sb_youtube');

    if( currentTarget.hasClass('sby_layout_list') ) {
        return 'list'
    }

    if( currentTarget.hasClass('sby_layout_grid') ) {
        return 'grid'
    }

    if( currentTarget.hasClass('sby_layout_carousel') ) {
        return 'carousel'
    }

    if( currentTarget.hasClass('sby_layout_gallery') ) {
        return 'gallery'
    }

    return false;
}

/**
 * Opens and displays the comments section on the page.
 * 
 * @returns {void}
 */
function openComments() {

    if( ! window.sbyOptions.isPro ) {
        return false;
    }
    const openCommentTrigger = jQuery('.sby-comments-trigger');
    openCommentTrigger.unbind('click');
    openCommentTrigger.click(function() {
        const commentWrapClass = '.sby-comments-wrap';
        const commentSecionWrap = jQuery(this).closest('.sby-comment-container');
        const commentSection =  commentSecionWrap.find(commentWrapClass);
        const currentLayout =  getLayout(jQuery(this));

        if( commentSection.text().length <= 0) {
            let commentCount;
            let videoId;
            let target;

            if( 'gallery' === currentLayout ) {
                const targetParent = jQuery(this).closest('.sb_youtube');
                const currentTarget = targetParent.find('.sby_item.sby_current');

                if( targetParent && currentTarget ) {
                    videoId = checkValue(currentTarget.attr('data-video-id'));
                    commentCount = checkValue(currentTarget.find('a').attr('data-comment-count'));
                    target = targetParent.find(commentWrapClass);
                }
            }

            if( 'list' === currentLayout ) {
                const currentTarget = jQuery(this).closest('.sby_item');

                if( currentTarget ) {
                    videoId = checkValue(currentTarget.attr('data-video-id'));
                    commentCount = checkValue(currentTarget.find('a').attr('data-comment-count'));
                    target = currentTarget.find(commentWrapClass);
                }

                // Reset all other comments opened 
                resetComments(jQuery(this).closest('.sb_youtube'));
            }

            const atts = checkValue(jQuery(this).closest('.sb_youtube').attr('data-shortcode-atts'));

            generateCommentSection(videoId, atts, target, commentCount);
            commentSection.addClass('sby-comments-active');

        } else {
            commentSection.toggle();
            commentSection.toggleClass('sby-comments-active');
        }

        const currentTextState = commentSecionWrap.find('.sby-comments-trigger p');

        if( currentTextState ) {
            changeTextOnToggle(currentTextState, 'Show Comments', 'Hide Comments');
        }

    });
}

/**
 * Returns a valid value or an empty string based on the input.
 * @param {*} element 
 * @returns {string} 
 */
function checkValue(element) {
    return element ? element : '';
}

/**
 * Sends an AJAX request with the specified data and handles the response.
 * @param {Object} submitData
 * @param {Function} onSuccess 
 * 
 * @returns {void} 
 */
function sbyAjax(submitData,onSuccess) {
    jQuery.ajax({
        url: sbyOptions.adminAjaxUrl,
        type: 'post',
        data: submitData,
        success: onSuccess
    });
}

/**
 * Fetches and generates a comment section for a given video.
 * 
 * @param {string} videoId 
 * @param {Object} atts
 * @param {jQuery} target
 * 
 * @returns {void}
 */

function generateCommentSection(videoId, atts, target, commentCount) {

    submitData = {
        action: 'sby_get_comments',
        video_id: videoId,
        atts: atts
    };

    let onSuccess = function (data) {

        if( ! data ) {
            return false;
        }

        if( false === data.success ) {
            target.html(errorCommentTemplate(data.data));
            return false;
        }

        const commentJson = JSON.parse(data);

        if( ! commentJson ) {
            return false;
        }

        if( commentJson?.success && false === commentJson.success) {
            target.html(errorCommentTemplate(commentJson.data));
            return false;
        }

        if( commentJson?.error && commentJson?.error?.message) {
            let errorMessage = commentJson.error.message;
            if( errorMessage.includes('disabled comments') ) {
                errorMessage = 'Comments are turned off'
            }
            target.html(errorCommentTemplate(errorMessage));
            return false;
        }

        const noOfItems = commentJson?.items && commentJson?.items.length ? commentJson.items.length : '';
        const videoLink = videoId ? `https://www.youtube.com/watch?v=${videoId}` : '';

        if(! noOfItems ) {
            target.html(noCommentsTemplate());
            toggleReadMore();
            return false;
        }

        currentCommentCount = commentCount ? `( ${commentCount} )` : '';

        let commentHtml = `<h4 class="sby-comments-sub-heading">Comments ${currentCommentCount}</h4><ul class="sby-comments">`;
        jQuery.each(commentJson.items, function(index, comment) {

            const topLevelCommentPath = comment?.snippet?.topLevelComment;
            const topLevelCommentSnippet = comment?.snippet;

            // Generate the HTML for each comment
            commentHtml += `<li class="sby-comment">${commentSingleTemplate( getSingleApiData(topLevelCommentPath, 'authorProfileImageUrl'), getSingleApiData(topLevelCommentPath, 'authorDisplayName'), getSingleApiData(topLevelCommentPath, 'authorChannelUrl'), getSingleApiData(topLevelCommentPath, 'textDisplay'), getSingleApiData(topLevelCommentPath, 'likeCount'), getSingleApiData(topLevelCommentPath, 'publishedAt'), getSingleApiData(topLevelCommentSnippet, 'totalReplyCount'))}
            <ul class="sby-reply-comments">`;

            if( comment?.replies?.comments ) {
                // Use $.each to loop through replies
                jQuery.each(comment.replies.comments, function(replyIndex, reply) {

                    commentHtml += `<li class="sby-reply-comment" >${commentSingleTemplate(getSingleApiData(reply, 'authorProfileImageUrl'), getSingleApiData(reply, 'authorDisplayName'), getSingleApiData(reply, 'authorChannelUrl'), getSingleApiData(reply, 'textDisplay'), getSingleApiData(reply, 'likeCount'), getSingleApiData(reply, 'publishedAt'))}</li>`;
                });
            }
            // Close the comment container
            commentHtml += `</ul></li>`;
        });
        commentHtml += `</ul>`;
        commentHtml += `<a href="${videoLink}" target="_blank" class="sby-view-all-button ">View all comments on YouTube</a>`;

        target.html(commentHtml).fadeIn("fast"); 

        toggleReadMore();
        toggleAccordion('sby-active','.sby-replies','.sby-comment','.sby-reply-comments');
    }

    toggleReadMore();
    sbyAjax(submitData,onSuccess)

}

/**
 * Toggles the text of an element based on its current content.
 *
 * This function updates the text of an element if the element's current text
 * matches the specified `currentText`. If it matches, the text is replaced with
 * the provided `replacementText`. If it does not match, the text remains as `currentText`.
 *
 * @param {Object} currentState
 * @param {string} currentText
 * @param {string} replacementText
 * 
 * @returns {void} 
 */

function changeTextOnToggle(currentState, currentText, replacementText ) {

    if( currentState && currentText && replacementText ) {
        const currentStateText = currentText === currentState.text() ? replacementText : currentText;
        currentState.text(currentStateText);
    }
}

/**
 * Resets the comments of a specified parent element.
 *
 * @param {jQuery} parent
 *
 * @returns {void} 
 */

function resetComments(parent) {
    if( 'gallery' === getLayout(parent) || 'list' === getLayout(parent) ) {
        const trigger = parent.find('.sby-comments-trigger');
        trigger.find('p').text('Show Comments');
        parent.find('.sby-comments-wrap').html('');
    }
}

/**
 * Formats a large number into a more readable string with a suffix.
 * The function converts large numbers into a string with a suffix to denote the scale of the number.
 *
 * @param {number} num
 * 
 * @returns {string}
 */

function formatLargeNumber(num) {
    if (num >= 1e9) return (num / 1e9).toFixed(1) + 'B';
    if (num >= 1e6) return (num / 1e6).toFixed(1) + 'M';
    if (num >= 1e3) return (num / 1e3).toFixed(1) + 'K';

    return num;
}

/**
 * Applies a set of colors to the channel header.
 * 
 * @param {Object} colorArray - An object containing color properties for the channel header.
 * @param {string} colorArray.channelName - The text color for the channel name element.
 * @param {string} colorArray.subscribeCount - The text color for the subscribe count element.
 * @param {string} colorArray.buttonBackground - The background color for the button element.
 * @param {string} colorArray.buttonText - The text color for the button element.
 * 
 * @returns {void}
 */
function setColorsToChannelHeader(colorArray) {
    const {channelName, subscribeCount, buttonBackground, buttonText} = colorArray;
    const parent = jQuery('.sby_lb-dataContainer .sby-lb-channel-header');
    
    if( ! parent ) {
        return false;
    }

    if( channelName ) {
        parent.find('.sby-lb-channel-name-with-subs span:first-child').css('color', channelName);
    }

    if( subscribeCount ) {
        parent.find('.sby-lb-channel-name-with-subs span:nth-child(2)').css('color', subscribeCount);
    }

    if( buttonBackground ) {
        parent.find('.sby-lb-subscribe-btn').css('background', buttonBackground);
    }

    if( buttonText ) {
        parent.find('.sby-lb-subscribe-btn').css('color', buttonText);
    }
}
