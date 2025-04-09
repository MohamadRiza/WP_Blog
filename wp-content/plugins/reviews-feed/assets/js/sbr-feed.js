var sbr_js_exists = (typeof sbr_js_exists !== 'undefined') ? true : false;
if(!sbr_js_exists) {
	/*!
         * Isotope PACKAGED v3.0.6
         *
         * Licensed GPLv3 for open source use
         * or Isotope Commercial License for commercial use
         *
         * https://isotope.metafizzy.co
         * Copyright 2010-2018 Metafizzy
         */

	!function (t, e) {
		"function" == typeof define && define.amd ? define("jquery-bridget/jquery-bridget", ["jquery"], function (i) {
			return e(t, i)
		}) : "object" == typeof module && module.exports ? module.exports = e(t, require("jquery")) : t.jQueryBridget = e(t, t.jQuery)
	}(window, function (t, e) {
		"use strict";

		function i(i, s, a) {
			function u(t, e, o) {
				var n, s = "$()." + i + '("' + e + '")';
				return t.each(function (t, u) {
					var h = a.data(u, i);
					if (!h) return void r(i + " not initialized. Cannot call methods, i.e. " + s);
					var d = h[e];
					if (!d || "_" == e.charAt(0)) return void r(s + " is not a valid method");
					var l = d.apply(h, o);
					n = void 0 === n ? l : n
				}), void 0 !== n ? n : t
			}

			function h(t, e) {
				t.each(function (t, o) {
					var n = a.data(o, i);
					n ? (n.option(e), n._init()) : (n = new s(o, e), a.data(o, i, n))
				})
			}

			a = a || e || t.jQuery, a && (s.prototype.option || (s.prototype.option = function (t) {
				a.isPlainObject(t) && (this.options = a.extend(!0, this.options, t))
			}), a.fn[i] = function (t) {
				if ("string" == typeof t) {
					var e = n.call(arguments, 1);
					return u(this, t, e)
				}
				return h(this, t), this
			}, o(a))
		}

		function o(t) {
			!t || t && t.bridget || (t.bridget = i)
		}

		var n = Array.prototype.slice, s = t.console, r = "undefined" == typeof s ? function () {
		} : function (t) {
			s.error(t)
		};
		return o(e || t.jQuery), i
	}), function (t, e) {
		"function" == typeof define && define.amd ? define("ev-emitter/ev-emitter", e) : "object" == typeof module && module.exports ? module.exports = e() : t.EvEmitter = e()
	}("undefined" != typeof window ? window : this, function () {
		function t() {
		}

		var e = t.prototype;
		return e.on = function (t, e) {
			if (t && e) {
				var i = this._events = this._events || {}, o = i[t] = i[t] || [];
				return o.indexOf(e) == -1 && o.push(e), this
			}
		}, e.once = function (t, e) {
			if (t && e) {
				this.on(t, e);
				var i = this._onceEvents = this._onceEvents || {}, o = i[t] = i[t] || {};
				return o[e] = !0, this
			}
		}, e.off = function (t, e) {
			var i = this._events && this._events[t];
			if (i && i.length) {
				var o = i.indexOf(e);
				return o != -1 && i.splice(o, 1), this
			}
		}, e.emitEvent = function (t, e) {
			var i = this._events && this._events[t];
			if (i && i.length) {
				i = i.slice(0), e = e || [];
				for (var o = this._onceEvents && this._onceEvents[t], n = 0; n < i.length; n++) {
					var s = i[n], r = o && o[s];
					r && (this.off(t, s), delete o[s]), s.apply(this, e)
				}
				return this
			}
		}, e.allOff = function () {
			delete this._events, delete this._onceEvents
		}, t
	}), function (t, e) {
		"function" == typeof define && define.amd ? define("get-size/get-size", e) : "object" == typeof module && module.exports ? module.exports = e() : t.getSize = e()
	}(window, function () {
		"use strict";

		function t(t) {
			var e = parseFloat(t), i = t.indexOf("%") == -1 && !isNaN(e);
			return i && e
		}

		function e() {
		}

		function i() {
			for (var t = {
				width: 0,
				height: 0,
				innerWidth: 0,
				innerHeight: 0,
				outerWidth: 0,
				outerHeight: 0
			}, e = 0; e < h; e++) {
				var i = u[e];
				t[i] = 0
			}
			return t
		}

		function o(t) {
			var e = getComputedStyle(t);
			return e || a("Style returned " + e + ". Are you running this code in a hidden iframe on Firefox? See https://bit.ly/getsizebug1"), e
		}

		function n() {
			if (!d) {
				d = !0;
				var e = document.createElement("div");
				e.style.width = "200px", e.style.padding = "1px 2px 3px 4px", e.style.borderStyle = "solid", e.style.borderWidth = "1px 2px 3px 4px", e.style.boxSizing = "border-box";
				var i = document.body || document.documentElement;
				i.appendChild(e);
				var n = o(e);
				r = 200 == Math.round(t(n.width)), s.isBoxSizeOuter = r, i.removeChild(e)
			}
		}

		function s(e) {
			if (n(), "string" == typeof e && (e = document.querySelector(e)), e && "object" == typeof e && e.nodeType) {
				var s = o(e);
				if ("none" == s.display) return i();
				var a = {};
				a.width = e.offsetWidth, a.height = e.offsetHeight;
				for (var d = a.isBorderBox = "border-box" == s.boxSizing, l = 0; l < h; l++) {
					var f = u[l], c = s[f], m = parseFloat(c);
					a[f] = isNaN(m) ? 0 : m
				}
				var p = a.paddingLeft + a.paddingRight, y = a.paddingTop + a.paddingBottom,
					g = a.marginLeft + a.marginRight, v = a.marginTop + a.marginBottom,
					_ = a.borderLeftWidth + a.borderRightWidth, z = a.borderTopWidth + a.borderBottomWidth, I = d && r,
					x = t(s.width);
				x !== !1 && (a.width = x + (I ? 0 : p + _));
				var S = t(s.height);
				return S !== !1 && (a.height = S + (I ? 0 : y + z)), a.innerWidth = a.width - (p + _), a.innerHeight = a.height - (y + z), a.outerWidth = a.width + g, a.outerHeight = a.height + v, a
			}
		}

		var r, a = "undefined" == typeof console ? e : function (t) {
				console.error(t)
			},
			u = ["paddingLeft", "paddingRight", "paddingTop", "paddingBottom", "marginLeft", "marginRight", "marginTop", "marginBottom", "borderLeftWidth", "borderRightWidth", "borderTopWidth", "borderBottomWidth"],
			h = u.length, d = !1;
		return s
	}), function (t, e) {
		"use strict";
		"function" == typeof define && define.amd ? define("desandro-matches-selector/matches-selector", e) : "object" == typeof module && module.exports ? module.exports = e() : t.matchesSelector = e()
	}(window, function () {
		"use strict";
		var t = function () {
			var t = window.Element.prototype;
			if (t.matches) return "matches";
			if (t.matchesSelector) return "matchesSelector";
			for (var e = ["webkit", "moz", "ms", "o"], i = 0; i < e.length; i++) {
				var o = e[i], n = o + "MatchesSelector";
				if (t[n]) return n
			}
		}();
		return function (e, i) {
			return e[t](i)
		}
	}), function (t, e) {
		"function" == typeof define && define.amd ? define("fizzy-ui-utils/utils", ["desandro-matches-selector/matches-selector"], function (i) {
			return e(t, i)
		}) : "object" == typeof module && module.exports ? module.exports = e(t, require("desandro-matches-selector")) : t.fizzyUIUtils = e(t, t.matchesSelector)
	}(window, function (t, e) {
		var i = {};
		i.extend = function (t, e) {
			for (var i in e) t[i] = e[i];
			return t
		}, i.modulo = function (t, e) {
			return (t % e + e) % e
		};
		var o = Array.prototype.slice;
		i.makeArray = function (t) {
			if (Array.isArray(t)) return t;
			if (null === t || void 0 === t) return [];
			var e = "object" == typeof t && "number" == typeof t.length;
			return e ? o.call(t) : [t]
		}, i.removeFrom = function (t, e) {
			var i = t.indexOf(e);
			i != -1 && t.splice(i, 1)
		}, i.getParent = function (t, i) {
			for (; t.parentNode && t != document.body;) if (t = t.parentNode, e(t, i)) return t
		}, i.getQueryElement = function (t) {
			return "string" == typeof t ? document.querySelector(t) : t
		}, i.handleEvent = function (t) {
			var e = "on" + t.type;
			this[e] && this[e](t)
		}, i.filterFindElements = function (t, o) {
			t = i.makeArray(t);
			var n = [];
			return t.forEach(function (t) {
				if (t instanceof HTMLElement) {
					if (!o) return void n.push(t);
					e(t, o) && n.push(t);
					for (var i = t.querySelectorAll(o), s = 0; s < i.length; s++) n.push(i[s])
				}
			}), n
		}, i.debounceMethod = function (t, e, i) {
			i = i || 100;
			var o = t.prototype[e], n = e + "Timeout";
			t.prototype[e] = function () {
				var t = this[n];
				clearTimeout(t);
				var e = arguments, s = this;
				this[n] = setTimeout(function () {
					o.apply(s, e), delete s[n]
				}, i)
			}
		}, i.docReady = function (t) {
			var e = document.readyState;
			"complete" == e || "interactive" == e ? setTimeout(t) : document.addEventListener("DOMContentLoaded", t)
		}, i.toDashed = function (t) {
			return t.replace(/(.)([A-Z])/g, function (t, e, i) {
				return e + "-" + i
			}).toLowerCase()
		};
		var n = t.console;
		return i.htmlInit = function (e, o) {
			i.docReady(function () {
				var s = i.toDashed(o), r = "data-" + s, a = document.querySelectorAll("[" + r + "]"),
					u = document.querySelectorAll(".js-" + s), h = i.makeArray(a).concat(i.makeArray(u)),
					d = r + "-options", l = t.jQuery;
				h.forEach(function (t) {
					var i, s = t.getAttribute(r) || t.getAttribute(d);
					try {
						i = s && JSON.parse(s)
					} catch (a) {
						return void (n && n.error("Error parsing " + r + " on " + t.className + ": " + a))
					}
					var u = new e(t, i);
					l && l.data(t, o, u)
				})
			})
		}, i
	}), function (t, e) {
		"function" == typeof define && define.amd ? define("outlayer/item", ["ev-emitter/ev-emitter", "get-size/get-size"], e) : "object" == typeof module && module.exports ? module.exports = e(require("ev-emitter"), require("get-size")) : (t.Outlayer = {}, t.Outlayer.Item = e(t.EvEmitter, t.getSize))
	}(window, function (t, e) {
		"use strict";

		function i(t) {
			for (var e in t) return !1;
			return e = null, !0
		}

		function o(t, e) {
			t && (this.element = t, this.layout = e, this.position = {x: 0, y: 0}, this._create())
		}

		function n(t) {
			return t.replace(/([A-Z])/g, function (t) {
				return "-" + t.toLowerCase()
			})
		}

		var s = document.documentElement.style, r = "string" == typeof s.transition ? "transition" : "WebkitTransition",
			a = "string" == typeof s.transform ? "transform" : "WebkitTransform",
			u = {WebkitTransition: "webkitTransitionEnd", transition: "transitionend"}[r], h = {
				transform: a,
				transition: r,
				transitionDuration: r + "Duration",
				transitionProperty: r + "Property",
				transitionDelay: r + "Delay"
			}, d = o.prototype = Object.create(t.prototype);
		d.constructor = o, d._create = function () {
			this._transn = {ingProperties: {}, clean: {}, onEnd: {}}, this.css({position: "absolute"})
		}, d.handleEvent = function (t) {
			var e = "on" + t.type;
			this[e] && this[e](t)
		}, d.getSize = function () {
			this.size = e(this.element)
		}, d.css = function (t) {
			var e = this.element.style;
			for (var i in t) {
				var o = h[i] || i;
				e[o] = t[i]
			}
		}, d.getPosition = function () {
			var t = getComputedStyle(this.element), e = this.layout._getOption("originLeft"),
				i = this.layout._getOption("originTop"), o = t[e ? "left" : "right"], n = t[i ? "top" : "bottom"],
				s = parseFloat(o), r = parseFloat(n), a = this.layout.size;
			o.indexOf("%") != -1 && (s = s / 100 * a.width), n.indexOf("%") != -1 && (r = r / 100 * a.height), s = isNaN(s) ? 0 : s, r = isNaN(r) ? 0 : r, s -= e ? a.paddingLeft : a.paddingRight, r -= i ? a.paddingTop : a.paddingBottom, this.position.x = s, this.position.y = r
		}, d.layoutPosition = function () {
			var t = this.layout.size, e = {}, i = this.layout._getOption("originLeft"),
				o = this.layout._getOption("originTop"), n = i ? "paddingLeft" : "paddingRight",
				s = i ? "left" : "right", r = i ? "right" : "left", a = this.position.x + t[n];
			e[s] = this.getXValue(a), e[r] = "";
			var u = o ? "paddingTop" : "paddingBottom", h = o ? "top" : "bottom", d = o ? "bottom" : "top",
				l = this.position.y + t[u];
			e[h] = this.getYValue(l), e[d] = "", this.css(e), this.emitEvent("layout", [this])
		}, d.getXValue = function (t) {
			var e = this.layout._getOption("horizontal");
			return this.layout.options.percentPosition && !e ? t / this.layout.size.width * 100 + "%" : t + "px"
		}, d.getYValue = function (t) {
			var e = this.layout._getOption("horizontal");
			return this.layout.options.percentPosition && e ? t / this.layout.size.height * 100 + "%" : t + "px"
		}, d._transitionTo = function (t, e) {
			this.getPosition();
			var i = this.position.x, o = this.position.y, n = t == this.position.x && e == this.position.y;
			if (this.setPosition(t, e), n && !this.isTransitioning) return void this.layoutPosition();
			var s = t - i, r = e - o, a = {};
			a.transform = this.getTranslate(s, r), this.transition({
				to: a,
				onTransitionEnd: {transform: this.layoutPosition},
				isCleaning: !0
			})
		}, d.getTranslate = function (t, e) {
			var i = this.layout._getOption("originLeft"), o = this.layout._getOption("originTop");
			return t = i ? t : -t, e = o ? e : -e, "translate3d(" + t + "px, " + e + "px, 0)"
		}, d.goTo = function (t, e) {
			this.setPosition(t, e), this.layoutPosition()
		}, d.moveTo = d._transitionTo, d.setPosition = function (t, e) {
			this.position.x = parseFloat(t), this.position.y = parseFloat(e)
		}, d._nonTransition = function (t) {
			this.css(t.to), t.isCleaning && this._removeStyles(t.to);
			for (var e in t.onTransitionEnd) t.onTransitionEnd[e].call(this)
		}, d.transition = function (t) {
			if (!parseFloat(this.layout.options.transitionDuration)) return void this._nonTransition(t);
			var e = this._transn;
			for (var i in t.onTransitionEnd) e.onEnd[i] = t.onTransitionEnd[i];
			for (i in t.to) e.ingProperties[i] = !0, t.isCleaning && (e.clean[i] = !0);
			if (t.from) {
				this.css(t.from);
				var o = this.element.offsetHeight;
				o = null
			}
			this.enableTransition(t.to), this.css(t.to), this.isTransitioning = !0
		};
		var l = "opacity," + n(a);
		d.enableTransition = function () {
			if (!this.isTransitioning) {
				var t = this.layout.options.transitionDuration;
				t = "number" == typeof t ? t + "ms" : t, this.css({
					transitionProperty: l,
					transitionDuration: t,
					transitionDelay: this.staggerDelay || 0
				}), this.element.addEventListener(u, this, !1)
			}
		}, d.onwebkitTransitionEnd = function (t) {
			this.ontransitionend(t)
		}, d.onotransitionend = function (t) {
			this.ontransitionend(t)
		};
		var f = {"-webkit-transform": "transform"};
		d.ontransitionend = function (t) {
			if (t.target === this.element) {
				var e = this._transn, o = f[t.propertyName] || t.propertyName;
				if (delete e.ingProperties[o], i(e.ingProperties) && this.disableTransition(), o in e.clean && (this.element.style[t.propertyName] = "", delete e.clean[o]), o in e.onEnd) {
					var n = e.onEnd[o];
					n.call(this), delete e.onEnd[o]
				}
				this.emitEvent("transitionEnd", [this])
			}
		}, d.disableTransition = function () {
			this.removeTransitionStyles(), this.element.removeEventListener(u, this, !1), this.isTransitioning = !1
		}, d._removeStyles = function (t) {
			var e = {};
			for (var i in t) e[i] = "";
			this.css(e)
		};
		var c = {transitionProperty: "", transitionDuration: "", transitionDelay: ""};
		return d.removeTransitionStyles = function () {
			this.css(c)
		}, d.stagger = function (t) {
			t = isNaN(t) ? 0 : t, this.staggerDelay = t + "ms"
		}, d.removeElem = function () {
			this.element.parentNode.removeChild(this.element), this.css({display: ""}), this.emitEvent("remove", [this])
		}, d.remove = function () {
			return r && parseFloat(this.layout.options.transitionDuration) ? (this.once("transitionEnd", function () {
				this.removeElem()
			}), void this.hide()) : void this.removeElem()
		}, d.reveal = function () {
			delete this.isHidden, this.css({display: ""});
			var t = this.layout.options, e = {}, i = this.getHideRevealTransitionEndProperty("visibleStyle");
			e[i] = this.onRevealTransitionEnd, this.transition({
				from: t.hiddenStyle,
				to: t.visibleStyle,
				isCleaning: !0,
				onTransitionEnd: e
			})
		}, d.onRevealTransitionEnd = function () {
			this.isHidden || this.emitEvent("reveal")
		}, d.getHideRevealTransitionEndProperty = function (t) {
			var e = this.layout.options[t];
			if (e.opacity) return "opacity";
			for (var i in e) return i
		}, d.hide = function () {
			this.isHidden = !0, this.css({display: ""});
			var t = this.layout.options, e = {}, i = this.getHideRevealTransitionEndProperty("hiddenStyle");
			e[i] = this.onHideTransitionEnd, this.transition({
				from: t.visibleStyle,
				to: t.hiddenStyle,
				isCleaning: !0,
				onTransitionEnd: e
			})
		}, d.onHideTransitionEnd = function () {
			this.isHidden && (this.css({display: "none"}), this.emitEvent("hide"))
		}, d.destroy = function () {
			this.css({position: "", left: "", right: "", top: "", bottom: "", transition: "", transform: ""})
		}, o
	}), function (t, e) {
		"use strict";
		"function" == typeof define && define.amd ? define("outlayer/outlayer", ["ev-emitter/ev-emitter", "get-size/get-size", "fizzy-ui-utils/utils", "./item"], function (i, o, n, s) {
			return e(t, i, o, n, s)
		}) : "object" == typeof module && module.exports ? module.exports = e(t, require("ev-emitter"), require("get-size"), require("fizzy-ui-utils"), require("./item")) : t.Outlayer = e(t, t.EvEmitter, t.getSize, t.fizzyUIUtils, t.Outlayer.Item)
	}(window, function (t, e, i, o, n) {
		"use strict";

		function s(t, e) {
			var i = o.getQueryElement(t);
			if (!i) return void (u && u.error("Bad element for " + this.constructor.namespace + ": " + (i || t)));
			this.element = i, h && (this.$element = h(this.element)), this.options = o.extend({}, this.constructor.defaults), this.option(e);
			var n = ++l;
			this.element.outlayerGUID = n, f[n] = this, this._create();
			var s = this._getOption("initLayout");
			s && this.layout()
		}

		function r(t) {
			function e() {
				t.apply(this, arguments)
			}

			return e.prototype = Object.create(t.prototype), e.prototype.constructor = e, e
		}

		function a(t) {
			if ("number" == typeof t) return t;
			var e = t.match(/(^\d*\.?\d*)(\w*)/), i = e && e[1], o = e && e[2];
			if (!i.length) return 0;
			i = parseFloat(i);
			var n = m[o] || 1;
			return i * n
		}

		var u = t.console, h = t.jQuery, d = function () {
		}, l = 0, f = {};
		s.namespace = "outlayer", s.Item = n, s.defaults = {
			containerStyle: {position: "relative"},
			initLayout: !0,
			originLeft: !0,
			originTop: !0,
			resize: !0,
			resizeContainer: !0,
			transitionDuration: "0.4s",
			hiddenStyle: {opacity: 0, transform: "scale(0.001)"},
			visibleStyle: {opacity: 1, transform: "scale(1)"}
		};
		var c = s.prototype;
		o.extend(c, e.prototype), c.option = function (t) {
			o.extend(this.options, t)
		}, c._getOption = function (t) {
			var e = this.constructor.compatOptions[t];
			return e && void 0 !== this.options[e] ? this.options[e] : this.options[t]
		}, s.compatOptions = {
			initLayout: "isInitLayout",
			horizontal: "isHorizontal",
			layoutInstant: "isLayoutInstant",
			originLeft: "isOriginLeft",
			originTop: "isOriginTop",
			resize: "isResizeBound",
			resizeContainer: "isResizingContainer"
		}, c._create = function () {
			this.reloadItems(), this.stamps = [], this.stamp(this.options.stamp), o.extend(this.element.style, this.options.containerStyle);
			var t = this._getOption("resize");
			t && this.bindResize()
		}, c.reloadItems = function () {
			this.items = this._itemize(this.element.children)
		}, c._itemize = function (t) {
			for (var e = this._filterFindItemElements(t), i = this.constructor.Item, o = [], n = 0; n < e.length; n++) {
				var s = e[n], r = new i(s, this);
				o.push(r)
			}
			return o
		}, c._filterFindItemElements = function (t) {
			return o.filterFindElements(t, this.options.itemSelector)
		}, c.getItemElements = function () {
			return this.items.map(function (t) {
				return t.element
			})
		}, c.layout = function () {
			this._resetLayout(), this._manageStamps();
			var t = this._getOption("layoutInstant"), e = void 0 !== t ? t : !this._isLayoutInited;
			this.layoutItems(this.items, e), this._isLayoutInited = !0
		}, c._init = c.layout, c._resetLayout = function () {
			this.getSize()
		}, c.getSize = function () {
			this.size = i(this.element)
		}, c._getMeasurement = function (t, e) {
			var o, n = this.options[t];
			n ? ("string" == typeof n ? o = this.element.querySelector(n) : n instanceof HTMLElement && (o = n), this[t] = o ? i(o)[e] : n) : this[t] = 0
		}, c.layoutItems = function (t, e) {
			t = this._getItemsForLayout(t), this._layoutItems(t, e), this._postLayout()
		}, c._getItemsForLayout = function (t) {
			return t.filter(function (t) {
				return !t.isIgnored
			})
		}, c._layoutItems = function (t, e) {
			if (this._emitCompleteOnItems("layout", t), t && t.length) {
				var i = [];
				t.forEach(function (t) {
					var o = this._getItemLayoutPosition(t);
					o.item = t, o.isInstant = e || t.isLayoutInstant, i.push(o)
				}, this), this._processLayoutQueue(i)
			}
		}, c._getItemLayoutPosition = function () {
			return {x: 0, y: 0}
		}, c._processLayoutQueue = function (t) {
			this.updateStagger(), t.forEach(function (t, e) {
				this._positionItem(t.item, t.x, t.y, t.isInstant, e)
			}, this)
		}, c.updateStagger = function () {
			var t = this.options.stagger;
			return null === t || void 0 === t ? void (this.stagger = 0) : (this.stagger = a(t), this.stagger)
		}, c._positionItem = function (t, e, i, o, n) {
			o ? t.goTo(e, i) : (t.stagger(n * this.stagger), t.moveTo(e, i))
		}, c._postLayout = function () {
			this.resizeContainer()
		}, c.resizeContainer = function () {
			var t = this._getOption("resizeContainer");
			if (t) {
				var e = this._getContainerSize();
				e && (this._setContainerMeasure(e.width, !0), this._setContainerMeasure(e.height, !1))
			}
		}, c._getContainerSize = d, c._setContainerMeasure = function (t, e) {
			if (void 0 !== t) {
				var i = this.size;
				i.isBorderBox && (t += e ? i.paddingLeft + i.paddingRight + i.borderLeftWidth + i.borderRightWidth : i.paddingBottom + i.paddingTop + i.borderTopWidth + i.borderBottomWidth), t = Math.max(t, 0), this.element.style[e ? "width" : "height"] = t + "px"
			}
		}, c._emitCompleteOnItems = function (t, e) {
			function i() {
				n.dispatchEvent(t + "Complete", null, [e])
			}

			function o() {
				r++, r == s && i()
			}

			var n = this, s = e.length;
			if (!e || !s) return void i();
			var r = 0;
			e.forEach(function (e) {
				e.once(t, o)
			})
		}, c.dispatchEvent = function (t, e, i) {
			var o = e ? [e].concat(i) : i;
			if (this.emitEvent(t, o), h) if (this.$element = this.$element || h(this.element), e) {
				var n = h.Event(e);
				n.type = t, this.$element.trigger(n, i)
			} else this.$element.trigger(t, i)
		}, c.ignore = function (t) {
			var e = this.getItem(t);
			e && (e.isIgnored = !0)
		}, c.unignore = function (t) {
			var e = this.getItem(t);
			e && delete e.isIgnored
		}, c.stamp = function (t) {
			t = this._find(t), t && (this.stamps = this.stamps.concat(t), t.forEach(this.ignore, this))
		}, c.unstamp = function (t) {
			t = this._find(t), t && t.forEach(function (t) {
				o.removeFrom(this.stamps, t), this.unignore(t)
			}, this)
		}, c._find = function (t) {
			if (t) return "string" == typeof t && (t = this.element.querySelectorAll(t)), t = o.makeArray(t)
		}, c._manageStamps = function () {
			this.stamps && this.stamps.length && (this._getBoundingRect(), this.stamps.forEach(this._manageStamp, this))
		}, c._getBoundingRect = function () {
			var t = this.element.getBoundingClientRect(), e = this.size;
			this._boundingRect = {
				left: t.left + e.paddingLeft + e.borderLeftWidth,
				top: t.top + e.paddingTop + e.borderTopWidth,
				right: t.right - (e.paddingRight + e.borderRightWidth),
				bottom: t.bottom - (e.paddingBottom + e.borderBottomWidth)
			}
		}, c._manageStamp = d, c._getElementOffset = function (t) {
			var e = t.getBoundingClientRect(), o = this._boundingRect, n = i(t), s = {
				left: e.left - o.left - n.marginLeft,
				top: e.top - o.top - n.marginTop,
				right: o.right - e.right - n.marginRight,
				bottom: o.bottom - e.bottom - n.marginBottom
			};
			return s
		}, c.handleEvent = o.handleEvent, c.bindResize = function () {
			t.addEventListener("resize", this), this.isResizeBound = !0
		}, c.unbindResize = function () {
			t.removeEventListener("resize", this), this.isResizeBound = !1
		}, c.onresize = function () {
			this.resize()
		}, o.debounceMethod(s, "onresize", 100), c.resize = function () {
			this.isResizeBound && this.needsResizeLayout() && this.layout()
		}, c.needsResizeLayout = function () {
			var t = i(this.element), e = this.size && t;
			return e && t.innerWidth !== this.size.innerWidth
		}, c.addItems = function (t) {
			var e = this._itemize(t);
			return e.length && (this.items = this.items.concat(e)), e
		}, c.appended = function (t) {
			var e = this.addItems(t);
			e.length && (this.layoutItems(e, !0), this.reveal(e))
		}, c.prepended = function (t) {
			var e = this._itemize(t);
			if (e.length) {
				var i = this.items.slice(0);
				this.items = e.concat(i), this._resetLayout(), this._manageStamps(), this.layoutItems(e, !0), this.reveal(e), this.layoutItems(i)
			}
		}, c.reveal = function (t) {
			if (this._emitCompleteOnItems("reveal", t), t && t.length) {
				var e = this.updateStagger();
				t.forEach(function (t, i) {
					t.stagger(i * e), t.reveal()
				})
			}
		}, c.hide = function (t) {
			if (this._emitCompleteOnItems("hide", t), t && t.length) {
				var e = this.updateStagger();
				t.forEach(function (t, i) {
					t.stagger(i * e), t.hide()
				})
			}
		}, c.revealItemElements = function (t) {
			var e = this.getItems(t);
			this.reveal(e)
		}, c.hideItemElements = function (t) {
			var e = this.getItems(t);
			this.hide(e)
		}, c.getItem = function (t) {
			for (var e = 0; e < this.items.length; e++) {
				var i = this.items[e];
				if (i.element == t) return i
			}
		}, c.getItems = function (t) {
			t = o.makeArray(t);
			var e = [];
			return t.forEach(function (t) {
				var i = this.getItem(t);
				i && e.push(i)
			}, this), e
		}, c.remove = function (t) {
			var e = this.getItems(t);
			this._emitCompleteOnItems("remove", e), e && e.length && e.forEach(function (t) {
				t.remove(), o.removeFrom(this.items, t)
			}, this)
		}, c.destroy = function () {
			var t = this.element.style;
			t.height = "", t.position = "", t.width = "", this.items.forEach(function (t) {
				t.destroy()
			}), this.unbindResize();
			var e = this.element.outlayerGUID;
			delete f[e], delete this.element.outlayerGUID, h && h.removeData(this.element, this.constructor.namespace)
		}, s.data = function (t) {
			t = o.getQueryElement(t);
			var e = t && t.outlayerGUID;
			return e && f[e]
		}, s.create = function (t, e) {
			var i = r(s);
			return i.defaults = o.extend({}, s.defaults), o.extend(i.defaults, e), i.compatOptions = o.extend({}, s.compatOptions), i.namespace = t, i.data = s.data, i.Item = r(n), o.htmlInit(i, t), h && h.bridget && h.bridget(t, i), i
		};
		var m = {ms: 1, s: 1e3};
		return s.Item = n, s
	}), function (t, e) {
		"function" == typeof define && define.amd ? define("isotope-layout/js/item", ["outlayer/outlayer"], e) : "object" == typeof module && module.exports ? module.exports = e(require("outlayer")) : (t.Smashotope = t.Smashotope || {}, t.Smashotope.Item = e(t.Outlayer))
	}(window, function (t) {
		"use strict";

		function e() {
			t.Item.apply(this, arguments)
		}

		var i = e.prototype = Object.create(t.Item.prototype), o = i._create;
		i._create = function () {
			this.id = this.layout.itemGUID++, o.call(this), this.sortData = {}
		}, i.updateSortData = function () {
			if (!this.isIgnored) {
				this.sortData.id = this.id, this.sortData["original-order"] = this.id, this.sortData.random = Math.random();
				var t = this.layout.options.getSortData, e = this.layout._sorters;
				for (var i in t) {
					var o = e[i];
					this.sortData[i] = o(this.element, this)
				}
			}
		};
		var n = i.destroy;
		return i.destroy = function () {
			n.apply(this, arguments), this.css({display: ""})
		}, e
	}), function (t, e) {
		"function" == typeof define && define.amd ? define("isotope-layout/js/layout-mode", ["get-size/get-size", "outlayer/outlayer"], e) : "object" == typeof module && module.exports ? module.exports = e(require("get-size"), require("outlayer")) : (t.Smashotope = t.Smashotope || {}, t.Smashotope.LayoutMode = e(t.getSize, t.Outlayer))
	}(window, function (t, e) {
		"use strict";

		function i(t) {
			this.smashotope = t, t && (this.options = t.options[this.namespace], this.element = t.element, this.items = t.filteredItems, this.size = t.size)
		}

		var o = i.prototype,
			n = ["_resetLayout", "_getItemLayoutPosition", "_manageStamp", "_getContainerSize", "_getElementOffset", "needsResizeLayout", "_getOption"];
		return n.forEach(function (t) {
			o[t] = function () {
				return e.prototype[t].apply(this.smashotope, arguments)
			}
		}), o.needsVerticalResizeLayout = function () {
			var e = t(this.smashotope.element), i = this.smashotope.size && e;
			return i && e.innerHeight != this.smashotope.size.innerHeight
		}, o._getMeasurement = function () {
			this.smashotope._getMeasurement.apply(this, arguments)
		}, o.getColumnWidth = function () {
			this.getSegmentSize("column", "Width")
		}, o.getRowHeight = function () {
			this.getSegmentSize("row", "Height")
		}, o.getSegmentSize = function (t, e) {
			var i = t + e, o = "outer" + e;
			if (this._getMeasurement(i, o), !this[i]) {
				var n = this.getFirstItemSize();
				this[i] = n && n[o] || this.smashotope.size["inner" + e]
			}
		}, o.getFirstItemSize = function () {
			var e = this.smashotope.filteredItems[0];
			return e && e.element && t(e.element)
		}, o.layout = function () {
			this.smashotope.layout.apply(this.smashotope, arguments)
		}, o.getSize = function () {
			this.smashotope.getSize(), this.size = this.smashotope.size
		}, i.modes = {}, i.create = function (t, e) {
			function n() {
				i.apply(this, arguments)
			}

			return n.prototype = Object.create(o), n.prototype.constructor = n, e && (n.options = e), n.prototype.namespace = t, i.modes[t] = n, n
		}, i
	}), function (t, e) {
		"function" == typeof define && define.amd ? define("masonry-layout/masonry", ["outlayer/outlayer", "get-size/get-size"], e) : "object" == typeof module && module.exports ? module.exports = e(require("outlayer"), require("get-size")) : t.Masonry = e(t.Outlayer, t.getSize)
	}(window, function (t, e) {
		var i = t.create("masonry");
		i.compatOptions.fitWidth = "isFitWidth";
		var o = i.prototype;
		return o._resetLayout = function () {
			this.getSize(), this._getMeasurement("columnWidth", "outerWidth"), this._getMeasurement("gutter", "outerWidth"), this.measureColumns(), this.colYs = [];
			for (var t = 0; t < this.cols; t++) this.colYs.push(0);
			this.maxY = 0, this.horizontalColIndex = 0
		}, o.measureColumns = function () {
			if (this.getContainerWidth(), !this.columnWidth) {
				var t = this.items[0], i = t && t.element;
				this.columnWidth = i && e(i).outerWidth || this.containerWidth
			}
			var o = this.columnWidth += this.gutter, n = this.containerWidth + this.gutter, s = n / o, r = o - n % o,
				a = r && r < 1 ? "round" : "floor";
			s = Math[a](s), this.cols = Math.max(s, 1)
		}, o.getContainerWidth = function () {
			var t = this._getOption("fitWidth"), i = t ? this.element.parentNode : this.element, o = e(i);
			this.containerWidth = o && o.innerWidth
		}, o._getItemLayoutPosition = function (t) {
			t.getSize();
			var e = t.size.outerWidth % this.columnWidth, i = e && e < 1 ? "round" : "ceil",
				o = Math[i](t.size.outerWidth / this.columnWidth);
			o = Math.min(o, this.cols);
			for (var n = this.options.horizontalOrder ? "_getHorizontalColPosition" : "_getTopColPosition", s = this[n](o, t), r = {
				x: this.columnWidth * s.col,
				y: s.y
			}, a = s.y + t.size.outerHeight, u = o + s.col, h = s.col; h < u; h++) this.colYs[h] = a;
			return r
		}, o._getTopColPosition = function (t) {
			var e = this._getTopColGroup(t), i = Math.min.apply(Math, e);
			return {col: e.indexOf(i), y: i}
		}, o._getTopColGroup = function (t) {
			if (t < 2) return this.colYs;
			for (var e = [], i = this.cols + 1 - t, o = 0; o < i; o++) e[o] = this._getColGroupY(o, t);
			return e
		}, o._getColGroupY = function (t, e) {
			if (e < 2) return this.colYs[t];
			var i = this.colYs.slice(t, t + e);
			return Math.max.apply(Math, i)
		}, o._getHorizontalColPosition = function (t, e) {
			var i = this.horizontalColIndex % this.cols, o = t > 1 && i + t > this.cols;
			i = o ? 0 : i;
			var n = e.size.outerWidth && e.size.outerHeight;
			return this.horizontalColIndex = n ? i + t : this.horizontalColIndex, {col: i, y: this._getColGroupY(i, t)}
		}, o._manageStamp = function (t) {
			var i = e(t), o = this._getElementOffset(t), n = this._getOption("originLeft"), s = n ? o.left : o.right,
				r = s + i.outerWidth, a = Math.floor(s / this.columnWidth);
			a = Math.max(0, a);
			var u = Math.floor(r / this.columnWidth);
			u -= r % this.columnWidth ? 0 : 1, u = Math.min(this.cols - 1, u);
			for (var h = this._getOption("originTop"), d = (h ? o.top : o.bottom) + i.outerHeight, l = a; l <= u; l++) this.colYs[l] = Math.max(d, this.colYs[l])
		}, o._getContainerSize = function () {
			this.maxY = Math.max.apply(Math, this.colYs);
			var t = {height: this.maxY};
			return this._getOption("fitWidth") && (t.width = this._getContainerFitWidth()), t
		}, o._getContainerFitWidth = function () {
			for (var t = 0, e = this.cols; --e && 0 === this.colYs[e];) t++;
			return (this.cols - t) * this.columnWidth - this.gutter
		}, o.needsResizeLayout = function () {
			var t = this.containerWidth;
			return this.getContainerWidth(), t != this.containerWidth
		}, i
	}), function (t, e) {
		"function" == typeof define && define.amd ? define("isotope-layout/js/layout-modes/masonry", ["../layout-mode", "masonry-layout/masonry"], e) : "object" == typeof module && module.exports ? module.exports = e(require("../layout-mode"), require("masonry-layout")) : e(t.Smashotope.LayoutMode, t.Masonry)
	}(window, function (t, e) {
		"use strict";
		var i = t.create("masonry"), o = i.prototype, n = {_getElementOffset: !0, layout: !0, _getMeasurement: !0};
		for (var s in e.prototype) n[s] || (o[s] = e.prototype[s]);
		var r = o.measureColumns;
		o.measureColumns = function () {
			this.items = this.smashotope.filteredItems, r.call(this)
		};
		var a = o._getOption;
		return o._getOption = function (t) {
			return "fitWidth" == t ? void 0 !== this.options.isFitWidth ? this.options.isFitWidth : this.options.fitWidth : a.apply(this.smashotope, arguments)
		}, i
	}), function (t, e) {
		"function" == typeof define && define.amd ? define("isotope-layout/js/layout-modes/fit-rows", ["../layout-mode"], e) : "object" == typeof exports ? module.exports = e(require("../layout-mode")) : e(t.Smashotope.LayoutMode)
	}(window, function (t) {
		"use strict";
		var e = t.create("fitRows"), i = e.prototype;
		return i._resetLayout = function () {
			this.x = 0, this.y = 0, this.maxY = 0, this._getMeasurement("gutter", "outerWidth")
		}, i._getItemLayoutPosition = function (t) {
			t.getSize();
			var e = t.size.outerWidth + this.gutter, i = this.smashotope.size.innerWidth + this.gutter;
			0 !== this.x && e + this.x > i && (this.x = 0, this.y = this.maxY);
			var o = {x: this.x, y: this.y};
			return this.maxY = Math.max(this.maxY, this.y + t.size.outerHeight), this.x += e, o
		}, i._getContainerSize = function () {
			return {height: this.maxY}
		}, e
	}), function (t, e) {
		"function" == typeof define && define.amd ? define("isotope-layout/js/layout-modes/vertical", ["../layout-mode"], e) : "object" == typeof module && module.exports ? module.exports = e(require("../layout-mode")) : e(t.Smashotope.LayoutMode)
	}(window, function (t) {
		"use strict";
		var e = t.create("vertical", {horizontalAlignment: 0}), i = e.prototype;
		return i._resetLayout = function () {
			this.y = 0
		}, i._getItemLayoutPosition = function (t) {
			t.getSize();
			var e = (this.smashotope.size.innerWidth - t.size.outerWidth) * this.options.horizontalAlignment,
				i = this.y;
			return this.y += t.size.outerHeight, {x: e, y: i}
		}, i._getContainerSize = function () {
			return {height: this.y}
		}, e
	}), function (t, e) {
		"function" == typeof define && define.amd ? define(["outlayer/outlayer", "get-size/get-size", "desandro-matches-selector/matches-selector", "fizzy-ui-utils/utils", "isotope-layout/js/item", "isotope-layout/js/layout-mode", "isotope-layout/js/layout-modes/masonry", "isotope-layout/js/layout-modes/fit-rows", "isotope-layout/js/layout-modes/vertical"], function (i, o, n, s, r, a) {
			return e(t, i, o, n, s, r, a)
		}) : "object" == typeof module && module.exports ? module.exports = e(t, require("outlayer"), require("get-size"), require("desandro-matches-selector"), require("fizzy-ui-utils"), require("isotope-layout/js/item"), require("isotope-layout/js/layout-mode"), require("isotope-layout/js/layout-modes/masonry"), require("isotope-layout/js/layout-modes/fit-rows"), require("isotope-layout/js/layout-modes/vertical")) : t.Smashotope = e(t, t.Outlayer, t.getSize, t.matchesSelector, t.fizzyUIUtils, t.Smashotope.Item, t.Smashotope.LayoutMode)
	}(window, function (t, e, i, o, n, s, r) {
		function a(t, e) {
			return function (i, o) {
				for (var n = 0; n < t.length; n++) {
					var s = t[n], r = i.sortData[s], a = o.sortData[s];
					if (r > a || r < a) {
						var u = void 0 !== e[s] ? e[s] : e, h = u ? 1 : -1;
						return (r > a ? 1 : -1) * h
					}
				}
				return 0
			}
		}

		var u = t.jQuery, h = String.prototype.trim ? function (t) {
			return t.trim()
		} : function (t) {
			return t.replace(/^\s+|\s+$/g, "")
		}, d = e.create("smashotope", {layoutMode: "masonry", isJQueryFiltering: !0, sortAscending: !0});
		d.Item = s, d.LayoutMode = r;
		var l = d.prototype;
		l._create = function () {
			this.itemGUID = 0, this._sorters = {}, this._getSorters(), e.prototype._create.call(this), this.modes = {}, this.filteredItems = this.items, this.sortHistory = ["original-order"];
			for (var t in r.modes) this._initLayoutMode(t)
		}, l.reloadItems = function () {
			this.itemGUID = 0, e.prototype.reloadItems.call(this)
		}, l._itemize = function () {
			for (var t = e.prototype._itemize.apply(this, arguments), i = 0; i < t.length; i++) {
				var o = t[i];
				o.id = this.itemGUID++
			}
			return this._updateItemsSortData(t), t
		}, l._initLayoutMode = function (t) {
			var e = r.modes[t], i = this.options[t] || {};
			this.options[t] = e.options ? n.extend(e.options, i) : i, this.modes[t] = new e(this)
		}, l.layout = function () {
			return !this._isLayoutInited && this._getOption("initLayout") ? void this.arrange() : void this._layout()
		}, l._layout = function () {
			var t = this._getIsInstant();
			this._resetLayout(), this._manageStamps(), this.layoutItems(this.filteredItems, t), this._isLayoutInited = !0
		}, l.arrange = function (t) {
			this.option(t), this._getIsInstant();
			var e = this._filter(this.items);
			this.filteredItems = e.matches, this._bindArrangeComplete(), this._isInstant ? this._noTransition(this._hideReveal, [e]) : this._hideReveal(e), this._sort(), this._layout()
		}, l._init = l.arrange, l._hideReveal = function (t) {
			this.reveal(t.needReveal), this.hide(t.needHide)
		}, l._getIsInstant = function () {
			var t = this._getOption("layoutInstant"), e = void 0 !== t ? t : !this._isLayoutInited;
			return this._isInstant = e, e
		}, l._bindArrangeComplete = function () {
			function t() {
				e && i && o && n.dispatchEvent("arrangeComplete", null, [n.filteredItems])
			}

			var e, i, o, n = this;
			this.once("layoutComplete", function () {
				e = !0, t()
			}), this.once("hideComplete", function () {
				i = !0, t()
			}), this.once("revealComplete", function () {
				o = !0, t()
			})
		}, l._filter = function (t) {
			var e = this.options.filter;
			e = e || "*";
			for (var i = [], o = [], n = [], s = this._getFilterTest(e), r = 0; r < t.length; r++) {
				var a = t[r];
				if (!a.isIgnored) {
					var u = s(a);
					u && i.push(a), u && a.isHidden ? o.push(a) : u || a.isHidden || n.push(a)
				}
			}
			return {matches: i, needReveal: o, needHide: n}
		}, l._getFilterTest = function (t) {
			return u && this.options.isJQueryFiltering ? function (e) {
				return u(e.element).is(t);
			} : "function" == typeof t ? function (e) {
				return t(e.element)
			} : function (e) {
				return o(e.element, t)
			}
		}, l.updateSortData = function (t) {
			var e;
			t ? (t = n.makeArray(t), e = this.getItems(t)) : e = this.items, this._getSorters(), this._updateItemsSortData(e)
		}, l._getSorters = function () {
			var t = this.options.getSortData;
			for (var e in t) {
				var i = t[e];
				this._sorters[e] = f(i)
			}
		}, l._updateItemsSortData = function (t) {
			for (var e = t && t.length, i = 0; e && i < e; i++) {
				var o = t[i];
				o.updateSortData()
			}
		};
		var f = function () {
			function t(t) {
				if ("string" != typeof t) return t;
				var i = h(t).split(" "), o = i[0], n = o.match(/^\[(.+)\]$/), s = n && n[1], r = e(s, o),
					a = d.sortDataParsers[i[1]];
				return t = a ? function (t) {
					return t && a(r(t))
				} : function (t) {
					return t && r(t)
				}
			}

			function e(t, e) {
				return t ? function (e) {
					return e.getAttribute(t)
				} : function (t) {
					var i = t.querySelector(e);
					return i && i.textContent
				}
			}

			return t
		}();
		d.sortDataParsers = {
			parseInt: function (t) {
				return parseInt(t, 10)
			}, parseFloat: function (t) {
				return parseFloat(t)
			}
		}, l._sort = function () {
			if (this.options.sortBy) {
				var t = n.makeArray(this.options.sortBy);
				this._getIsSameSortBy(t) || (this.sortHistory = t.concat(this.sortHistory));
				var e = a(this.sortHistory, this.options.sortAscending);
				this.filteredItems.sort(e)
			}
		}, l._getIsSameSortBy = function (t) {
			for (var e = 0; e < t.length; e++) if (t[e] != this.sortHistory[e]) return !1;
			return !0
		}, l._mode = function () {
			var t = this.options.layoutMode, e = this.modes[t];
			if (!e) throw new Error("No layout mode: " + t);
			return e.options = this.options[t], e
		}, l._resetLayout = function () {
			e.prototype._resetLayout.call(this), this._mode()._resetLayout()
		}, l._getItemLayoutPosition = function (t) {
			return this._mode()._getItemLayoutPosition(t)
		}, l._manageStamp = function (t) {
			this._mode()._manageStamp(t)
		}, l._getContainerSize = function () {
			return this._mode()._getContainerSize()
		}, l.needsResizeLayout = function () {
			return this._mode().needsResizeLayout()
		}, l.appended = function (t) {
			var e = this.addItems(t);
			if (e.length) {
				var i = this._filterRevealAdded(e);
				this.filteredItems = this.filteredItems.concat(i)
			}
		}, l.prepended = function (t) {
			var e = this._itemize(t);
			if (e.length) {
				this._resetLayout(), this._manageStamps();
				var i = this._filterRevealAdded(e);
				this.layoutItems(this.filteredItems), this.filteredItems = i.concat(this.filteredItems), this.items = e.concat(this.items)
			}
		}, l._filterRevealAdded = function (t) {
			var e = this._filter(t);
			return this.hide(e.needHide), this.reveal(e.matches), this.layoutItems(e.matches, !0), e.matches
		}, l.insert = function (t) {
			var e = this.addItems(t);
			if (e.length) {
				var i, o, n = e.length;
				for (i = 0; i < n; i++) o = e[i], this.element.appendChild(o.element);
				var s = this._filter(e).matches;
				for (i = 0; i < n; i++) e[i].isLayoutInstant = !0;
				for (this.arrange(), i = 0; i < n; i++) delete e[i].isLayoutInstant;
				this.reveal(s)
			}
		};
		var c = l.remove;
		return l.remove = function (t) {
			t = n.makeArray(t);
			var e = this.getItems(t);
			c.call(this, t);
			for (var i = e && e.length, o = 0; i && o < i; o++) {
				var s = e[o];
				n.removeFrom(this.filteredItems, s)
			}
		}, l.shuffle = function () {
			for (var t = 0; t < this.items.length; t++) {
				var e = this.items[t];
				e.sortData.random = Math.random()
			}
			this.options.sortBy = "random", this._sort(), this._layout()
		}, l._noTransition = function (t, e) {
			var i = this.options.transitionDuration;
			this.options.transitionDuration = 0;
			var o = t.apply(this, e);
			return this.options.transitionDuration = i, o
		}, l.getFilteredItemElements = function () {
			return this.filteredItems.map(function (t) {
				return t.element
			})
		}, d
	});


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
			refreshClass: "sbr-owl-refresh",
			loadedClass: "sbr-owl-loaded",
			loadingClass: "sbr-owl-loading",
			rtlClass: "sbr-owl-rtl",
			responsiveClass: "sbr-owl-responsive",
			dragClass: "sbr-owl-drag",
			itemClass: "sbr-owl-item",
			stageClass: "sbr-owl-stage",
			stageOuterClass: "sbr-owl-stage-outer",
			grabClass: "sbr-owl-grab"
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
			return "string" === typeof b && (this._invalidated[b] = !0, this.is("valid") && this.leave("valid")), a.map(this._invalidated, function (a, b) {
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
		}, a.fn.sbrOwlCarousel = function (b) {
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
		}, a.fn.sbrOwlCarousel.Constructor = e
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
			this._core.$element.is(":visible") !== this._visible && (this._visible = !this._visible, this._core.$element.toggleClass("sbr-owl-hidden", !this._visible), this._visible && this._core.invalidate("width") && this._core.refresh())
		}, e.prototype.destroy = function () {
			var a, c;
			b.clearInterval(this._interval);
			for (a in this._handlers) this._core.$element.off(a, this._handlers[a]);
			for (c in Object.getOwnPropertyNames(this)) "function" != typeof this[c] && (this[c] = null)
		}, a.fn.sbrOwlCarousel.Constructor.Plugins.AutoRefresh = e
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
			var d = this._core.$stage.children().eq(c), e = d && d.find(".sbr-owl-lazy");
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
		}, a.fn.sbrOwlCarousel.Constructor.Plugins.Lazy = e
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
		e.Defaults = {autoHeight: !1, autoHeightClass: "sbr-owl-height"}, e.prototype.update = function () {
			var b = this._core._current, c = b + this._core.settings.items,
				d = this._core.$stage.children().toArray().slice(b, c), e = [], f = 0;
			a.each(d, function (b, c) {
				e.push(a(c).height())
			}), f = Math.max.apply(null, e), this._core.$stage.parent().height(f).addClass(this._core.settings.autoHeightClass)
		}, e.prototype.destroy = function () {
			var a, b;
			for (a in this._handlers) this._core.$element.off(a, this._handlers[a]);
			for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null)
		}, a.fn.sbrOwlCarousel.Constructor.Plugins.AutoHeight = e
	}(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
		var e = function (b) {
			this._core = b, this._videos = {}, this._playing = null, this._handlers = {
				"initialized.owl.carousel": a.proxy(function (a) {
					a.namespace && this._core.register({type: "state", name: "playing", tags: ["interacting"]})
				}, this), "resize.owl.carousel": a.proxy(function (a) {
					a.namespace && this._core.settings.video && this.isInFullScreen() && a.preventDefault()
				}, this), "refreshed.owl.carousel": a.proxy(function (a) {
					a.namespace && this._core.is("resizing") && this._core.$stage.find(".cloned .sbr-owl-video-frame").remove()
				}, this), "changed.owl.carousel": a.proxy(function (a) {
					a.namespace && "position" === a.property.name && this._playing && this.stop()
				}, this), "prepared.owl.carousel": a.proxy(function (b) {
					if (b.namespace) {
						var c = a(b.content).find(".sbr-owl-video");
						c.length && (c.css("display", "none"), this.fetch(c, a(b.content)))
					}
				}, this)
			}, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers), this._core.$element.on("click.owl.video", ".sbr-owl-video-play-icon", a.proxy(function (a) {
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
					e = '<div class="sbr-owl-video-play-icon"></div>', d = k.lazyLoad ? '<div class="sbr-owl-video-tn ' + j + '" ' + i + '="' + a + '"></div>' : '<div class="sbr-owl-video-tn" style="opacity:1;background-image:url(' + a + ')"></div>', b.after(d), b.after(e)
				};
			if (b.wrap('<div class="sbr-owl-video-wrapper"' + g + "></div>"), this._core.settings.lazyLoad && (i = "data-src", j = "sbr-owl-lazy"), h.length) return l(h.attr(i)), h.remove(), !1;
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
			this._core.trigger("stop", null, "video"), this._playing.find(".sbr-owl-video-frame").remove(), this._playing.removeClass("sbr-owl-video-playing"), this._playing = null, this._core.leave("playing"), this._core.trigger("stopped", null, "video")
		}, e.prototype.play = function (b) {
			var c, d = a(b.target), e = d.closest("." + this._core.settings.itemClass),
				f = this._videos[e.attr("data-video")], g = f.width || "100%",
				h = f.height || this._core.$stage.height();
			this._playing || (this._core.enter("playing"), this._core.trigger("play", null, "video"), e = this._core.items(this._core.relative(e.index())), this._core.reset(e.index()), "youtube" === f.type ? c = '<iframe width="' + g + '" height="' + h + '" src="//www.youtube.com/embed/' + f.id + "?autoplay=1&rel=0&v=" + f.id + '" frameborder="0" allowfullscreen></iframe>' : "vimeo" === f.type ? c = '<iframe src="//player.vimeo.com/video/' + f.id + '?autoplay=1" width="' + g + '" height="' + h + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>' : "vzaar" === f.type && (c = '<iframe frameborder="0"height="' + h + '"width="' + g + '" allowfullscreen mozallowfullscreen webkitAllowFullScreen src="//view.vzaar.com/' + f.id + '/player?autoplay=true"></iframe>'), a('<div class="sbr-owl-video-frame">' + c + "</div>").insertAfter(e.find(".sbr-owl-video")), this._playing = e.addClass("sbr-owl-video-playing"))
		}, e.prototype.isInFullScreen = function () {
			var b = c.fullscreenElement || c.mozFullScreenElement || c.webkitFullscreenElement;
			return b && a(b).parent().hasClass("sbr-owl-video-frame")
		}, e.prototype.destroy = function () {
			var a, b;
			this._core.$element.off("click.owl.video");
			for (a in this._handlers) this._core.$element.off(a, this._handlers[a]);
			for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null)
		}, a.fn.sbrOwlCarousel.Constructor.Plugins.Video = e
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
				this.core.current() !== this.previous && (g && (b = this.core.coordinates(this.previous) - this.core.coordinates(this.next), d.one(a.support.animation.end, c).css({left: b + "px"}).addClass("animated sbr-owl-animated-out").addClass(g)), f && e.one(a.support.animation.end, c).addClass("animated sbr-owl-animated-in").addClass(f))
			}
		}, e.prototype.clear = function (b) {
			a(b.target).css({left: ""}).removeClass("animated sbr-owl-animated-out sbr-owl-animated-in").removeClass(this.core.settings.animateIn).removeClass(this.core.settings.animateOut), this.core.onTransitionEnd()
		}, e.prototype.destroy = function () {
			var a, b;
			for (a in this.handlers) this.core.$element.off(a, this.handlers[a]);
			for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null)
		},
			a.fn.sbrOwlCarousel.Constructor.Plugins.Animate = e
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
		}, a.fn.sbrOwlCarousel.Constructor.Plugins.autoplay = e
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
			navContainerClass: "sbr-owl-nav",
			navClass: ["sbr-owl-prev", "sbr-owl-next"],
			slideBy: 1,
			dotClass: "sbr-owl-dot",
			dotsClass: "sbr-owl-dots",
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
		}, a.fn.sbrOwlCarousel.Constructor.Plugins.Navigation = e
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
		}, a.fn.sbrOwlCarousel.Constructor.Plugins.Hash = e
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

	!function (a, b) {
		"function" == typeof define && define.amd ? define("packery/js/rect", b) : "object" == typeof module && module.exports ? module.exports = b() : (a.Packery = a.Packery || {}, a.Packery.Rect = b())
	}(window, function () {
		function a(b) {
			for (var c in a.defaults) this[c] = a.defaults[c];
			for (c in b) this[c] = b[c]
		}

		a.defaults = {x: 0, y: 0, width: 0, height: 0};
		var b = a.prototype;
		return b.contains = function (a) {
			var b = a.width || 0, c = a.height || 0;
			return this.x <= a.x && this.y <= a.y && this.x + this.width >= a.x + b && this.y + this.height >= a.y + c
		}, b.overlaps = function (a) {
			var b = this.x + this.width, c = this.y + this.height, d = a.x + a.width, e = a.y + a.height;
			return this.x < d && b > a.x && this.y < e && c > a.y
		}, b.getMaximalFreeRects = function (b) {
			if (!this.overlaps(b)) return !1;
			var c, d = [], e = this.x + this.width, f = this.y + this.height, g = b.x + b.width, h = b.y + b.height;
			return this.y < b.y && (c = new a({
				x: this.x,
				y: this.y,
				width: this.width,
				height: b.y - this.y
			}), d.push(c)), e > g && (c = new a({
				x: g,
				y: this.y,
				width: e - g,
				height: this.height
			}), d.push(c)), f > h && (c = new a({
				x: this.x,
				y: h,
				width: this.width,
				height: f - h
			}), d.push(c)), this.x < b.x && (c = new a({
				x: this.x,
				y: this.y,
				width: b.x - this.x,
				height: this.height
			}), d.push(c)), d
		}, b.canFit = function (a) {
			return this.width >= a.width && this.height >= a.height
		}, a
	}), function (a, b) {
		if ("function" == typeof define && define.amd) define("packery/js/packer", ["./rect"], b); else if ("object" == typeof module && module.exports) module.exports = b(require("./rect")); else {
			var c = a.Packery = a.Packery || {};
			c.Packer = b(c.Rect)
		}
	}(window, function (a) {
		function b(a, b, c) {
			this.width = a || 0, this.height = b || 0, this.sortDirection = c || "downwardLeftToRight", this.reset()
		}

		var c = b.prototype;
		c.reset = function () {
			this.spaces = [];
			var b = new a({x: 0, y: 0, width: this.width, height: this.height});
			this.spaces.push(b), this.sorter = d[this.sortDirection] || d.downwardLeftToRight
		}, c.pack = function (a) {
			for (var b = 0; b < this.spaces.length; b++) {
				var c = this.spaces[b];
				if (c.canFit(a)) {
					this.placeInSpace(a, c);
					break
				}
			}
		}, c.columnPack = function (a) {
			for (var b = 0; b < this.spaces.length; b++) {
				var c = this.spaces[b], d = c.x <= a.x && c.x + c.width >= a.x + a.width && c.height >= a.height - .01;
				if (d) {
					a.y = c.y, this.placed(a);
					break
				}
			}
		}, c.rowPack = function (a) {
			for (var b = 0; b < this.spaces.length; b++) {
				var c = this.spaces[b], d = c.y <= a.y && c.y + c.height >= a.y + a.height && c.width >= a.width - .01;
				if (d) {
					a.x = c.x, this.placed(a);
					break
				}
			}
		}, c.placeInSpace = function (a, b) {
			a.x = b.x, a.y = b.y, this.placed(a)
		}, c.placed = function (a) {
			for (var b = [], c = 0; c < this.spaces.length; c++) {
				var d = this.spaces[c], e = d.getMaximalFreeRects(a);
				e ? b.push.apply(b, e) : b.push(d)
			}
			this.spaces = b, this.mergeSortSpaces()
		}, c.mergeSortSpaces = function () {
			b.mergeRects(this.spaces), this.spaces.sort(this.sorter)
		}, c.addSpace = function (a) {
			this.spaces.push(a), this.mergeSortSpaces()
		}, b.mergeRects = function (a) {
			var b = 0, c = a[b];
			a:for (; c;) {
				for (var d = 0, e = a[b + d]; e;) {
					if (e == c) d++; else {
						if (e.contains(c)) {
							a.splice(b, 1), c = a[b];
							continue a
						}
						c.contains(e) ? a.splice(b + d, 1) : d++
					}
					e = a[b + d]
				}
				b++, c = a[b]
			}
			return a
		};
		var d = {
			downwardLeftToRight: function (a, b) {
				return a.y - b.y || a.x - b.x
			}, rightwardTopToBottom: function (a, b) {
				return a.x - b.x || a.y - b.y
			}
		};
		return b
	}), function (a, b) {
		"function" == typeof define && define.amd ? define("packery/js/item", ["outlayer/outlayer", "./rect"], b) : "object" == typeof module && module.exports ? module.exports = b(require("outlayer"), require("./rect")) : a.Packery.Item = b(a.Outlayer, a.Packery.Rect)
	}(window, function (a, b) {
		var c = document.documentElement.style, d = "string" == typeof c.transform ? "transform" : "WebkitTransform",
			e = function () {
				a.Item.apply(this, arguments)
			}, f = e.prototype = Object.create(a.Item.prototype), g = f._create;
		f._create = function () {
			g.call(this), this.rect = new b
		};
		var h = f.moveTo;
		return f.moveTo = function (a, b) {
			var c = Math.abs(this.position.x - a), d = Math.abs(this.position.y - b),
				e = this.layout.dragItemCount && !this.isPlacing && !this.isTransitioning && 1 > c && 1 > d;
			return e ? void this.goTo(a, b) : void h.apply(this, arguments)
		}, f.enablePlacing = function () {
			this.removeTransitionStyles(), this.isTransitioning && d && (this.element.style[d] = "none"), this.isTransitioning = !1, this.getSize(), this.layout._setRectSize(this.element, this.rect), this.isPlacing = !0
		}, f.disablePlacing = function () {
			this.isPlacing = !1
		}, f.removeElem = function () {
			this.element.parentNode.removeChild(this.element), this.layout.packer.addSpace(this.rect), this.emitEvent("remove", [this])
		}, f.showDropPlaceholder = function () {
			var a = this.dropPlaceholder;
			a || (a = this.dropPlaceholder = document.createElement("div"), a.className = "packery-drop-placeholder", a.style.position = "absolute"), a.style.width = this.size.width + "px", a.style.height = this.size.height + "px", this.positionDropPlaceholder(), this.layout.element.appendChild(a)
		}, f.positionDropPlaceholder = function () {
			this.dropPlaceholder.style[d] = "translate(" + this.rect.x + "px, " + this.rect.y + "px)"
		}, f.hideDropPlaceholder = function () {
			this.layout.element.removeChild(this.dropPlaceholder)
		}, e
	}), function (a, b) {
		"function" == typeof define && define.amd ? define("packery/js/packery", ["get-size/get-size", "outlayer/outlayer", "./rect", "./packer", "./item"], b) : "object" == typeof module && module.exports ? module.exports = b(require("get-size"), require("outlayer"), require("./rect"), require("./packer"), require("./item")) : a.Packery = b(a.getSize, a.Outlayer, a.Packery.Rect, a.Packery.Packer, a.Packery.Item)
	}(window, function (a, b, c, d, e) {
		function f(a, b) {
			return a.position.y - b.position.y || a.position.x - b.position.x
		}

		function g(a, b) {
			return a.position.x - b.position.x || a.position.y - b.position.y
		}

		function h(a, b) {
			var c = b.x - a.x, d = b.y - a.y;
			return Math.sqrt(c * c + d * d)
		}

		c.prototype.canFit = function (a) {
			return this.width >= a.width - 1 && this.height >= a.height - 1
		};
		var i = b.create("packery");
		i.Item = e;
		var j = i.prototype;
		j._create = function () {
			b.prototype._create.call(this), this.packer = new d, this.shiftPacker = new d, this.isEnabled = !0, this.dragItemCount = 0;
			var a = this;
			this.handleDraggabilly = {
				dragStart: function () {
					a.itemDragStart(this.element)
				}, dragMove: function () {
					a.itemDragMove(this.element, this.position.x, this.position.y)
				}, dragEnd: function () {
					a.itemDragEnd(this.element)
				}
			}, this.handleUIDraggable = {
				start: function (b, c) {
					c && a.itemDragStart(b.currentTarget)
				}, drag: function (b, c) {
					c && a.itemDragMove(b.currentTarget, c.position.left, c.position.top)
				}, stop: function (b, c) {
					c && a.itemDragEnd(b.currentTarget)
				}
			}
		}, j._resetLayout = function () {
			this.getSize(), this._getMeasurements();
			var a, b, c;
			this._getOption("horizontal") ? (a = 1 / 0, b = this.size.innerHeight + this.gutter, c = "rightwardTopToBottom") : (a = this.size.innerWidth + this.gutter, b = 1 / 0, c = "downwardLeftToRight"), this.packer.width = this.shiftPacker.width = a, this.packer.height = this.shiftPacker.height = b, this.packer.sortDirection = this.shiftPacker.sortDirection = c, this.packer.reset(), this.maxY = 0, this.maxX = 0
		}, j._getMeasurements = function () {
			this._getMeasurement("columnWidth", "width"), this._getMeasurement("rowHeight", "height"), this._getMeasurement("gutter", "width")
		}, j._getItemLayoutPosition = function (a) {
			if (this._setRectSize(a.element, a.rect), this.isShifting || this.dragItemCount > 0) {
				var b = this._getPackMethod();
				this.packer[b](a.rect)
			} else this.packer.pack(a.rect);
			return this._setMaxXY(a.rect), a.rect
		}, j.shiftLayout = function () {
			this.isShifting = !0, this.layout(), delete this.isShifting
		}, j._getPackMethod = function () {
			return this._getOption("horizontal") ? "rowPack" : "columnPack"
		}, j._setMaxXY = function (a) {
			this.maxX = Math.max(a.x + a.width, this.maxX), this.maxY = Math.max(a.y + a.height, this.maxY)
		}, j._setRectSize = function (b, c) {
			var d = a(b), e = d.outerWidth, f = d.outerHeight;
			(e || f) && (e = this._applyGridGutter(e, this.columnWidth), f = this._applyGridGutter(f, this.rowHeight)), c.width = Math.min(e, this.packer.width), c.height = Math.min(f, this.packer.height)
		}, j._applyGridGutter = function (a, b) {
			if (!b) return a + this.gutter;
			b += this.gutter;
			var c = a % b, d = c && 1 > c ? "round" : "ceil";
			return a = Math[d](a / b) * b
		}, j._getContainerSize = function () {
			return this._getOption("horizontal") ? {width: this.maxX - this.gutter} : {height: this.maxY - this.gutter}
		}, j._manageStamp = function (a) {
			var b, d = this.getItem(a);
			if (d && d.isPlacing) b = d.rect; else {
				var e = this._getElementOffset(a);
				b = new c({
					x: this._getOption("originLeft") ? e.left : e.right,
					y: this._getOption("originTop") ? e.top : e.bottom
				})
			}
			this._setRectSize(a, b), this.packer.placed(b), this._setMaxXY(b)
		}, j.sortItemsByPosition = function () {
			var a = this._getOption("horizontal") ? g : f;
			this.items.sort(a)
		}, j.fit = function (a, b, c) {
			var d = this.getItem(a);
			d && (this.stamp(d.element), d.enablePlacing(), this.updateShiftTargets(d), b = void 0 === b ? d.rect.x : b, c = void 0 === c ? d.rect.y : c, this.shift(d, b, c), this._bindFitEvents(d), d.moveTo(d.rect.x, d.rect.y), this.shiftLayout(), this.unstamp(d.element), this.sortItemsByPosition(), d.disablePlacing())
		}, j._bindFitEvents = function (a) {
			function b() {
				d++, 2 == d && c.dispatchEvent("fitComplete", null, [a])
			}

			var c = this, d = 0;
			a.once("layout", b), this.once("layoutComplete", b)
		}, j.resize = function () {
			this.isResizeBound && this.needsResizeLayout() && (this.options.shiftPercentResize ? this.resizeShiftPercentLayout() : this.layout())
		}, j.needsResizeLayout = function () {
			var b = a(this.element), c = this._getOption("horizontal") ? "innerHeight" : "innerWidth";
			return b[c] != this.size[c]
		}, j.resizeShiftPercentLayout = function () {
			var b = this._getItemsForLayout(this.items), c = this._getOption("horizontal"), d = c ? "y" : "x",
				e = c ? "height" : "width", f = c ? "rowHeight" : "columnWidth", g = c ? "innerHeight" : "innerWidth",
				h = this[f];
			if (h = h && h + this.gutter) {
				this._getMeasurements();
				var i = this[f] + this.gutter;
				b.forEach(function (a) {
					var b = Math.round(a.rect[d] / h);
					a.rect[d] = b * i
				})
			} else {
				var j = a(this.element)[g] + this.gutter, k = this.packer[e];
				b.forEach(function (a) {
					a.rect[d] = a.rect[d] / k * j
				})
			}
			this.shiftLayout()
		}, j.itemDragStart = function (a) {
			if (this.isEnabled) {
				this.stamp(a);
				var b = this.getItem(a);
				b && (b.enablePlacing(), b.showDropPlaceholder(), this.dragItemCount++, this.updateShiftTargets(b))
			}
		}, j.updateShiftTargets = function (a) {
			this.shiftPacker.reset(), this._getBoundingRect();
			var b = this._getOption("originLeft"), d = this._getOption("originTop");
			this.stamps.forEach(function (a) {
				var e = this.getItem(a);
				if (!e || !e.isPlacing) {
					var f = this._getElementOffset(a), g = new c({x: b ? f.left : f.right, y: d ? f.top : f.bottom});
					this._setRectSize(a, g), this.shiftPacker.placed(g)
				}
			}, this);
			var e = this._getOption("horizontal"), f = e ? "rowHeight" : "columnWidth", g = e ? "height" : "width";
			this.shiftTargetKeys = [], this.shiftTargets = [];
			var h, i = this[f];
			if (i = i && i + this.gutter) {
				var j = Math.ceil(a.rect[g] / i), k = Math.floor((this.shiftPacker[g] + this.gutter) / i);
				h = (k - j) * i;
				for (var l = 0; k > l; l++) this._addShiftTarget(l * i, 0, h)
			} else h = this.shiftPacker[g] + this.gutter - a.rect[g], this._addShiftTarget(0, 0, h);
			var m = this._getItemsForLayout(this.items), n = this._getPackMethod();
			m.forEach(function (a) {
				var b = a.rect;
				this._setRectSize(a.element, b), this.shiftPacker[n](b), this._addShiftTarget(b.x, b.y, h);
				var c = e ? b.x + b.width : b.x, d = e ? b.y : b.y + b.height;
				if (this._addShiftTarget(c, d, h), i) for (var f = Math.round(b[g] / i), j = 1; f > j; j++) {
					var k = e ? c : b.x + i * j, l = e ? b.y + i * j : d;
					this._addShiftTarget(k, l, h)
				}
			}, this)
		}, j._addShiftTarget = function (a, b, c) {
			var d = this._getOption("horizontal") ? b : a;
			if (!(0 !== d && d > c)) {
				var e = a + "," + b, f = -1 != this.shiftTargetKeys.indexOf(e);
				f || (this.shiftTargetKeys.push(e), this.shiftTargets.push({x: a, y: b}))
			}
		}, j.shift = function (a, b, c) {
			var d, e = 1 / 0, f = {x: b, y: c};
			this.shiftTargets.forEach(function (a) {
				var b = h(a, f);
				e > b && (d = a, e = b)
			}), a.rect.x = d.x, a.rect.y = d.y
		};
		var k = 120;
		j.itemDragMove = function (a, b, c) {
			function d() {
				f.shift(e, b, c), e.positionDropPlaceholder(), f.layout()
			}

			var e = this.isEnabled && this.getItem(a);
			if (e) {
				b -= this.size.paddingLeft, c -= this.size.paddingTop;
				var f = this, g = new Date;
				this._itemDragTime && g - this._itemDragTime < k ? (clearTimeout(this.dragTimeout), this.dragTimeout = setTimeout(d, k)) : (d(), this._itemDragTime = g)
			}
		}, j.itemDragEnd = function (a) {
			function b() {
				d++, 2 == d && (c.element.classList.remove("is-positioning-post-drag"), c.hideDropPlaceholder(), e.dispatchEvent("dragItemPositioned", null, [c]))
			}

			var c = this.isEnabled && this.getItem(a);
			if (c) {
				clearTimeout(this.dragTimeout), c.element.classList.add("is-positioning-post-drag");
				var d = 0, e = this;
				c.once("layout", b), this.once("layoutComplete", b), c.moveTo(c.rect.x, c.rect.y), this.layout(), this.dragItemCount = Math.max(0, this.dragItemCount - 1), this.sortItemsByPosition(), c.disablePlacing(), this.unstamp(c.element)
			}
		}, j.bindDraggabillyEvents = function (a) {
			this._bindDraggabillyEvents(a, "on")
		}, j.unbindDraggabillyEvents = function (a) {
			this._bindDraggabillyEvents(a, "off")
		}, j._bindDraggabillyEvents = function (a, b) {
			var c = this.handleDraggabilly;
			a[b]("dragStart", c.dragStart), a[b]("dragMove", c.dragMove), a[b]("dragEnd", c.dragEnd)
		}, j.bindUIDraggableEvents = function (a) {
			this._bindUIDraggableEvents(a, "on")
		}, j.unbindUIDraggableEvents = function (a) {
			this._bindUIDraggableEvents(a, "off")
		}, j._bindUIDraggableEvents = function (a, b) {
			var c = this.handleUIDraggable;
			a[b]("dragstart", c.start)[b]("drag", c.drag)[b]("dragstop", c.stop)
		};
		var l = j.destroy;
		return j.destroy = function () {
			l.apply(this, arguments), this.isEnabled = !1
		}, i.Rect = c, i.Packer = d, i
	}), function (a, b) {
		"function" == typeof define && define.amd ? define(["isotope-layout/js/layout-mode", "packery/js/packery"], b) : "object" == typeof module && module.exports ? module.exports = b(require("isotope-layout/js/layout-mode"), require("packery")) : b(a.Smashotope.LayoutMode, a.Packery)
	}(window, function (a, b) {
		var c = a.create("packery"), d = c.prototype, e = {_getElementOffset: !0, _getMeasurement: !0};
		for (var f in b.prototype) e[f] || (d[f] = b.prototype[f]);
		var g = d._resetLayout;
		d._resetLayout = function () {
			this.packer = this.packer || new b.Packer, this.shiftPacker = this.shiftPacker || new b.Packer, g.apply(this, arguments)
		};
		var h = d._getItemLayoutPosition;
		d._getItemLayoutPosition = function (a) {
			return a.rect = a.rect || new b.Rect, h.call(this, a)
		};
		var i = d.needsResizeLayout;
		d.needsResizeLayout = function () {
			return this._getOption("horizontal") ? this.needsVerticalResizeLayout() : i.call(this)
		};
		var j = d._getOption;
		return d._getOption = function (a) {
			return "horizontal" == a ? void 0 !== this.options.isHorizontal ? this.options.isHorizontal : this.options.horizontal : j.apply(this.smashotope, arguments)
		}, c
	});

	// Two Row Carousel
	;(function ($, window, document, undefined) {
		Owl2row = function (scope) {
			this.owl = scope;
			this.owl.options = $.extend({}, Owl2row.Defaults, this.owl.options);
			var self = this;
			//link callback events with owl carousel here
			this.handlers = {
				'initialize.owl.carousel': $.proxy(function (e) {
					if (this.owl.settings.owl2row === true && !$("#sb_instagram").hasClass("2rows")) {
						this.build2row(this);
						$("#sb_instagram").addClass("2rows");
					}else{
						$("#sb_instagram").removeClass("2rows");
					}
				}, this)
			};

			this.owl.$element.on(this.handlers);

		};

		Owl2row.Defaults = {
			owl2row: false,
			owl2rowTarget: 'sb-post-item-wrap',
			owl2rowContainer: 'sbr_owl2row-item',
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

		$.fn.sbrOwlCarousel.Constructor.Plugins['owl2row'] = Owl2row;
	})(window.Zepto || window.jQuery, window, document);


	(function($){

		function sbrAddImgLiquid() {
			/*! imgLiquid v0.9.944 / 03-05-2013 https://github.com/karacas/imgLiquid */
			var sbr_imgLiquid = sbr_imgLiquid || {VER: "0.9.944"};
			sbr_imgLiquid.bgs_Available = !1, sbr_imgLiquid.bgs_CheckRunned = !1, function (i) {
				function t() {
					if (!sbr_imgLiquid.bgs_CheckRunned) {
						sbr_imgLiquid.bgs_CheckRunned = !0;
						var t = i('<span style="background-size:cover" />');
						i("body").append(t), !function () {
							var i = t[0];
							if (i && window.getComputedStyle) {
								var e = window.getComputedStyle(i, null);
								e && e.backgroundSize && (sbr_imgLiquid.bgs_Available = "cover" === e.backgroundSize)
							}
						}(), t.remove()
					}
				}

				i.fn.extend({
					sbr_imgLiquid: function (e) {
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
								}), i("img", u).css({display: "none"}), g.onItemFinish && g.onItemFinish(t, u, c), u.addClass("sbr_imgLiquid_bgSize"), u.addClass("sbr_imgLiquid_ready"), l()
							}

							function o() {
								function e() {
									c.data("sbr_imgLiquid_error") || c.data("sbr_imgLiquid_loaded") || c.data("sbr_imgLiquid_oldProcessed") || (u.is(":visible") && c[0].complete && c[0].width > 0 && c[0].height > 0 ? (c.data("sbr_imgLiquid_loaded", !0), setTimeout(r, t * g.delay)) : setTimeout(e, g.timecheckvisibility))
								}

								if (c.data("oldSrc") && c.data("oldSrc") !== c.attr("src")) {
									var a = c.clone().removeAttr("style");
									return a.data("sbr_imgLiquid_settings", c.data("sbr_imgLiquid_settings")), c.parent().prepend(a), c.remove(), c = a, c[0].width = 0, void setTimeout(o, 10)
								}
								return c.data("sbr_imgLiquid_oldProcessed") ? void r() : (c.data("sbr_imgLiquid_oldProcessed", !1), c.data("oldSrc", c.attr("src")), i("img:not(:first)", u).css("display", "none"), u.css({overflow: "hidden"}), c.fadeTo(0, 0).removeAttr("width").removeAttr("height").css({
									visibility: "visible",
									"max-width": "none",
									"max-height": "none",
									width: "auto",
									height: "auto",
									display: "block"
								}), c.on("error", n), c[0].onerror = n, e(), void d())
							}

							function d() {
								(g.responsive || c.data("sbr_imgLiquid_oldProcessed")) && c.data("sbr_imgLiquid_settings") && (g = c.data("sbr_imgLiquid_settings"), u.actualSize = u.get(0).offsetWidth + u.get(0).offsetHeight / 1e4, u.sizeOld && u.actualSize !== u.sizeOld && r(), u.sizeOld = u.actualSize, setTimeout(d, g.responsiveCheckTime))
							}

							function n() {
								c.data("sbr_imgLiquid_error", !0), u.addClass("sbr_imgLiquid_error"), g.onItemError && g.onItemError(t, u, c), l()
							}

							function s() {
								var i = {};
								if (a.settings.useDataHtmlAttr) {
									var t = u.attr("data-sbr_imgLiquid-fill"),
										e = u.attr("data-sbr_imgLiquid-horizontalAlign"),
										o = u.attr("data-sbr_imgLiquid-verticalAlign");
									("true" === t || "false" === t) && (i.fill = Boolean("true" === t)), void 0 === e || "left" !== e && "center" !== e && "right" !== e && -1 === e.indexOf("%") || (i.horizontalAlign = e), void 0 === o || "top" !== o && "bottom" !== o && "center" !== o && -1 === o.indexOf("%") || (i.verticalAlign = o)
								}
								return sbr_imgLiquid.isIE && a.settings.ieFadeInDisabled && (i.fadeInTime = 0), i
							}

							function r() {
								var i, e, a, o, d, n, s, r, m = 0, h = 0, f = u.width(), v = u.height();
								void 0 === c.data("owidth") && c.data("owidth", c[0].width), void 0 === c.data("oheight") && c.data("oheight", c[0].height), g.fill === f / v >= c.data("owidth") / c.data("oheight") ? (i = "100%", e = "auto", a = Math.floor(f), o = Math.floor(f * (c.data("oheight") / c.data("owidth")))) : (i = "auto", e = "100%", a = Math.floor(v * (c.data("owidth") / c.data("oheight"))), o = Math.floor(v)), d = g.horizontalAlign.toLowerCase(), s = f - a, "left" === d && (h = 0), "center" === d && (h = .5 * s), "right" === d && (h = s), -1 !== d.indexOf("%") && (d = parseInt(d.replace("%", ""), 10), d > 0 && (h = s * d * .01)), n = g.verticalAlign.toLowerCase(), r = v - o, "left" === n && (m = 0), "center" === n && (m = .5 * r), "bottom" === n && (m = r), -1 !== n.indexOf("%") && (n = parseInt(n.replace("%", ""), 10), n > 0 && (m = r * n * .01)), g.hardPixels && (i = a, e = o), c.css({
									width: i,
									height: e,
									"margin-left": Math.floor(h),
									"margin-top": Math.floor(m)
								}), c.data("sbr_imgLiquid_oldProcessed") || (c.fadeTo(g.fadeInTime, 1), c.data("sbr_imgLiquid_oldProcessed", !0), g.removeBoxBackground && u.css("background-image", "none"), u.addClass("sbr_imgLiquid_nobgSize"), u.addClass("sbr_imgLiquid_ready")), g.onItemFinish && g.onItemFinish(t, u, c), l()
							}

							function l() {
								t === a.length - 1 && a.settings.onFinish && a.settings.onFinish()
							}

							var g = a.settings, u = i(this), c = i("img:first", u);
							return c.length ? (c.data("sbr_imgLiquid_settings") ? (u.removeClass("sbr_imgLiquid_error").removeClass("sbr_imgLiquid_ready"), g = i.extend({}, c.data("sbr_imgLiquid_settings"), a.options)) : g = i.extend({}, a.settings, s()), c.data("sbr_imgLiquid_settings", g), g.onItemStart && g.onItemStart(t, u, c), void (sbr_imgLiquid.bgs_Available && g.useBackgroundSize ? e() : o())) : void n()
						})
					}
				})
			}(jQuery);

			// Use imagefill to set the images as backgrounds so they can be square
			!function () {
				var css = sbr_imgLiquid.injectCss,
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

		function sbrAddVisibilityListener() {
			/* Detect when element becomes visible. Used for when the feed is initially hidden, in a tab for example. https://github.com/shaunbowe/jquery.visibilityChanged */
			!function (i) {
				var n = {
					callback: function () {
					}, runOnLoad: !0, frequency: 100, sbrPreviousVisibility: null
				}, c = {};
				c.sbrCheckVisibility = function (i, n) {
					if (jQuery.contains(document, i[0])) {
						var e = n.sbrPreviousVisibility, t = i.is(":visible");
						n.sbrPreviousVisibility = t, null == e ? n.runOnLoad && n.callback(i, t) : e !== t && n.callback(i, t), setTimeout(function () {
							c.sbrCheckVisibility(i, n)
						}, n.frequency)
					}
				}, i.fn.sbrVisibilityChanged = function (e) {
					var t = i.extend({}, n, e);
					return this.each(function () {
						c.sbrCheckVisibility(i(this), t)
					})
				}
			}(jQuery);
		}

		function Sbr() {
			this.feeds = {};
			this.options = sbrOptions;
			this.isTouch = sbrIsTouch();
		}

		Sbr.prototype = {
			createPage: function (createFeeds, createFeedsArgs) {
				if (typeof window.sbrOptions.adminAjaxUrl === 'undefined' || window.sbrOptions.adminAjaxUrl.indexOf(window.location.hostname) === -1) {
					window.sbrOptions.adminAjaxUrl = window.location.hostname + '/wp-admin/admin-ajax.php';
				}
				this.createFeeds(createFeedsArgs);
			},
			createLightbox: function() {
				var lbBuilder = sbrGetlightboxBuilder();
				var sbr_lb_delay = (function () {
					var sbr_timer = 0;
					return function (sbr_callback, sbr_ms) {
						clearTimeout(sbr_timer);
						sbr_timer = setTimeout(sbr_callback, sbr_ms);
					};
				})();
				jQuery(window).on('resize',function () {
					sbr_lb_delay(function () {
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
								a("body").on("click", "a[data-sbr-lightbox]", function(c) {
									return b.start(a(c.currentTarget)), !1
								})
							}, b.prototype.build = function() {
								var b = this;
								a(""+
									lbBuilder.template()).appendTo(a("body")), this.$lightbox = a("#sbr_lightbox"), this.$overlay = a("#sbr_lightboxOverlay"), this.$outerContainer = this.$lightbox.find(".sbr_lb-outerContainer"), this.$container = this.$lightbox.find(".sbr_lb-container"), this.containerTopPadding = parseInt(this.$container.css("padding-top"), 10), this.containerRightPadding = parseInt(this.$container.css("padding-right"), 10), this.containerBottomPadding = parseInt(this.$container.css("padding-bottom"), 10), this.containerLeftPadding = parseInt(this.$container.css("padding-left"), 10), this.$overlay.hide().on("click", function() {
									return b.end(), !1
								}), jQuery(document).on('click', function(event, b, c) {
									//Fade out the lightbox if click anywhere outside of the two elements defined below
									if (!jQuery(event.target).closest('.sbr_lb-outerContainer').length) {
										if (!jQuery(event.target).closest('.sbr_lb-dataContainer').length) {
											//Fade out lightbox
											if (typeof window.sbrLightboxPlayer !== 'undefined') { YT.get('sbr_lb-player').pauseVideo(); }

											jQuery('#sbr_lightboxOverlay, #sbr_lightbox').fadeOut();
										}
									}
								}), this.$lightbox.hide(),
									jQuery('#sbr_lightboxOverlay').on("click", function(c) {
										if (typeof window.sbrLightboxPlayer !== 'undefined') { YT.get('sbr_lb-player').pauseVideo(); }

										return "sbr_lightbox" === a(c.target).attr("id") && b.end(), !1
									}), this.$lightbox.find(".sbr_lb-prev").on("click", function() {

									if (typeof window.sbrLightboxPlayer !== 'undefined') { YT.get('sbr_lb-player').pauseVideo(); }

									return b.changeImage(0 === b.currentImageIndex ? b.album.length - 1 : b.currentImageIndex - 1), !1
								}), this.$lightbox.find(".sbr_lb-container").on("swiperight", function() {

									if (typeof window.sbrLightboxPlayer !== 'undefined') { YT.get('sbr_lb-player').pauseVideo(); }

									return b.changeImage(0 === b.currentImageIndex ? b.album.length - 1 : b.currentImageIndex - 1), !1
								}), this.$lightbox.find(".sbr_lb-next").on("click", function() {

									if (typeof window.sbrLightboxPlayer !== 'undefined') { YT.get('sbr_lb-player').pauseVideo(); }

									return b.changeImage(b.currentImageIndex === b.album.length - 1 ? 0 : b.currentImageIndex + 1), !1
								}), this.$lightbox.find(".sbr_lb-container").on("swipeleft", function() {

									if (typeof window.sbrLightboxPlayer !== 'undefined') { YT.get('sbr_lb-player').pauseVideo(); }

									return b.changeImage(b.currentImageIndex === b.album.length - 1 ? 0 : b.currentImageIndex + 1), !1
								}), this.$lightbox.find(".sbr_lb-loader, .sbr_lb-close").on("click", function() {

									if (typeof window.sbrLightboxPlayer !== 'undefined') { YT.get('sbr_lb-player').pauseVideo(); }

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
									h = b.attr("data-sbr-lightbox");
								if (h) {
									f = a(b.prop("tagName") + '[data-sbr-lightbox="' + h + '"]');
									for (var i = 0; i < f.length; i = ++i) c(a(f[i])), f[i] === b[0] && (g = i)
								} else if ("lightbox" === b.attr("rel")) c(b);
								else {
									f = a(b.prop("tagName") + '[rel="' + b.attr("rel") + '"]');
									for (var j = 0; j < f.length; j = ++j) c(a(f[j])), f[j] === b[0] && (g = j)
								}
								var k = e.scrollTop() + this.options.positionFromTop,
									l = e.scrollLeft();
								this.$lightbox.css({
									top: k + "px",
									left: l + "px"
								}).fadeIn(this.options.fadeDuration), this.changeImage(g)
							}, b.prototype.changeImage = function(b) {
								var c = this;
								this.disableKeyboardNav();
								var d = this.$lightbox.find(".sbr_lb-image");
								this.$overlay.fadeIn(this.options.fadeDuration), a(".sbr_lb-loader").fadeIn("slow"), this.$lightbox.find(".sbr_lb-image, .sbr_lb-nav, .sbr_lb-prev, .sbr_lb-next, .sbr_lb-dataContainer, .sbr_lb-numbers, .sbr_lb-caption").hide(), this.$outerContainer.addClass("animating");
								var e = new Image;
								e.onload = function() {
									var f, g, h, i, j, k, l;
									var sbrArrowWidth = 100;
									d.attr("src", c.album[b].link), f = a(e), d.width(e.width), d.height(e.height), c.options.fitImagesInViewport && (l = a(window).width(), k = a(window).height(), j = l - c.containerLeftPadding - c.containerRightPadding - 20 - sbrArrowWidth, i = k - c.containerTopPadding - c.containerBottomPadding - 150, (e.width > j || e.height > i) && (e.width / j > e.height / i ? (h = j, g = parseInt(e.height / (e.width / h), 10), d.width(h), d.height(g)) : (g = i, h = parseInt(e.width / (e.height / g), 10), d.width(h), d.height(g)))), c.sizeContainer(d.width(), d.height())
								}, e.src = this.album[b].link, this.currentImageIndex = b
							}, b.prototype.sizeOverlay = function() {
								this.$overlay.width(a(window).width()).height(a(document).height())
							}, b.prototype.sizeContainer = function(a, b) {
								function c() {
									d.$lightbox.find(".sbr_lb-dataContainer").width(g), d.$lightbox.find(".sbr_lb-prevLink").height(h), d.$lightbox.find(".sbr_lb-nextLink").height(h), d.showImage()
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
								this.$lightbox.find(".sbr_lb-loader").hide(), this.$lightbox.find(".sbr_lb-image").fadeIn("slow"), this.updateNav(), this.updateDetails(), this.preloadNeighboringImages(), this.enableKeyboardNav()
							}, b.prototype.updateNav = function() {
								var a = !1;
								try {
									document.createEvent("TouchEvent"), a = this.options.alwaysShowNavOnTouchDevices ? !0 : !1
								} catch (b) {}
								this.$lightbox.find(".sbr_lb-nav").show(), this.album.length > 1 && (this.options.wrapAround ? (a && this.$lightbox.find(".sbr_lb-prev, .sbr_lb-next").css("opacity", "1"), this.$lightbox.find(".sbr_lb-prev, .sbr_lb-next").show()) : (this.currentImageIndex > 0 && (this.$lightbox.find(".sbr_lb-prev").show(), a && this.$lightbox.find(".sbr_lb-prev").css("opacity", "1")), this.currentImageIndex < this.album.length - 1 && (this.$lightbox.find(".sbr_lb-next").show(), a && this.$lightbox.find(".sbr_lb-next").css("opacity", "1"))))
							}, b.prototype.updateDetails = function() {
								var b = this;

								/** NEW PHOTO ACTION **/
								if(jQuery('iframe.sbr_lb-player-loaded').length) {
									jQuery('.sbr_lb-player-placeholder').replaceWith(jQuery('iframe.sbr_lb-player-loaded'));
									jQuery('iframe.sbr_lb-player-loaded').removeClass('sbr_lb-player-loaded').show();
								}
								//Switch video when either a new popup or navigating to new one
								var feed = window.sbr.feeds[this.album[this.currentImageIndex].feedIndex];
								lbBuilder.beforePlayerSetup(this.$lightbox,this.album[this.currentImageIndex],this.currentImageIndex,this.album,feed);
								var preconditions = true;

								if( preconditions ){
									jQuery('#sbr_lightbox').removeClass('sbr-image-lightbox');

									var fullImage = this.album[this.currentImageIndex].link;
									$('.sbr_lb-image').attr('src',fullImage);
									this.$outerContainer.removeClass("animating");
									this.$lightbox.find(".sbr_lb-dataContainer").fadeIn(this.options.resizeDuration, function() {
										return b.sizeOverlay()
									});

									if (this.album.length > 1 && this.options.showImageNumberLabel) {
										this.$lightbox.find(".sbr_lb-number").text(this.options.albumLabel(this.currentImageIndex + 1, this.album.length)).fadeIn("fast");
									} else {
										this.$lightbox.find(".sbr_lb-number").hide();
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
									if( sbr_supports_video() ) $('#sbr_lightbox video.sbr_video')[0].pause();
									$('#sbr_lightbox iframe').attr('src', '');
									this.end();
								} else if (key === 'p' || keycode === KEYCODE_LEFTARROW) {
									if (this.currentImageIndex !== 0) {
										this.changeImage(this.currentImageIndex - 1);
									} else if (this.options.wrapAround && this.album.length > 1) {
										this.changeImage(this.album.length - 1);
									}
								} else if (key === 'n' || keycode === KEYCODE_RIGHTARROW) {
									if (this.currentImageIndex !== this.album.length - 1) {
										this.changeImage(this.currentImageIndex + 1);
									} else if (this.options.wrapAround && this.album.length > 1) {
										this.changeImage(0);
									}
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
							new c(a)

							//Lightbox hide photo function
							$('.sbr_lightbox_action a').off().on('click', function(){
								$(this).parent().find('.sbr_lightbox_tooltip').toggle();
							});
						}
					})
				}).call(this);
			},
			createFeeds: function (args) {
				window.sbr.createLightbox();
				args.whenFeedsCreated(
					$('.sb-feed-container').each(function (index) {
						$(this).attr('data-sbr-index', index + 1);
						var $self = $(this),
							flags = typeof $self.attr('data-sbr-flags') !== 'undefined' ? $self.attr('data-sbr-flags').split(',') : [],
							general = typeof $self.attr('data-options') !== 'undefined' ? JSON.parse($self.attr('data-options')) : {};
						var feedOptions = {
							postID : typeof $self.attr( 'data-postid' ) !== 'undefined' ? $self.attr( 'data-postid' ) : 'unknown',
							feedID : typeof $self.attr( 'data-feed-id' ) !== 'undefined' ? $self.attr( 'data-feed-id' ) : 'unknown',
							nextPage : 2,
							layout : typeof $self.attr( 'data-layout' ) !== 'undefined' ? $self.attr( 'data-layout' ) : 'list',
							cols : typeof $self.attr( 'data-cols' ) !== 'undefined' ? $self.attr( 'data-cols' ) : 3,
							colstablet : typeof $self.attr( 'data-colstablet' ) !== 'undefined' ? $self.attr( 'data-colstablet' ) : 2,
							colsmobile : typeof $self.attr( 'data-colsmobile' ) !== 'undefined' ? $self.attr( 'data-colsmobile' ) : 1,
							shortCodeAtts : typeof $self.attr('data-shortcode-atts') !== 'undefined' ? $self.attr('data-shortcode-atts') : '',
							misc : typeof $self.attr('data-misc') !== 'undefined' ? JSON.parse($self.attr('data-misc')) : {},
							debugEnabled : (flags.indexOf('debug') > -1),
							gdpr : (flags.indexOf('gdpr') > -1),
							consentGiven : (flags.indexOf('gdpr') === -1),
							noCDN : (flags.indexOf('disablecdn') > -1),
							lightboxEnabled : true,
							locator : (flags.indexOf('locator') > -1),
							checkMediaNonce : typeof $self.attr( 'data-check-media' ) !== 'undefined' ? $self.attr( 'data-check-media' ) : false,
							autoMinRes : 1,
							general : general
						};
						if ( typeof feedOptions.misc.carousel !== 'undefined' ) {
							feedOptions.carousel = feedOptions.misc.carousel;
						}
						window.sbr.feeds[index] = sbrGetNewFeed(this, index, feedOptions);
						window.sbr.feeds[index].init();

						var evt = jQuery.Event('sbrafterfeedcreate');
						evt.feed = window.sbr.feeds[index];
						jQuery(window).trigger(evt);
					})
				);
			},
			afterFeedsCreated: function () {
				// enable header hover action
				$('.sb_instagram_header').each(function () {
					var $thisHeader = $(this);
					$thisHeader.find('.sbr_header_link').on('mouseenter mouseleave', function(e) {
						switch(e.type) {
							case 'mouseenter':
								$thisHeader.find('.sbr_header_img_hover').addClass('sbr_fade_in');
								break;
							case 'mouseleave':
								$thisHeader.find('.sbr_header_img_hover').removeClass('sbr_fade_in');
								break;
						}
					});
				});

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
			}
		};

		function SbrFeed(el, index, settings) {
			this.el = el;
			this.index = index;
			this.settings = settings;
			this.placeholderURL = window.sbr.options.placeholder;
			this.consentGiven = settings.consentGiven;
			this.minImageWidth = 0;
			this.imageResolution = 150;
			this.outOfPages = false;
		}

		SbrFeed.prototype = {
			init: function() {
				var feed = this;
				feed.settings.consentGiven = feed.checkConsent();

				if ($(this.el).find('#sbr_mod_error').length) {
					$(this.el).prepend($(this.el).find('#sbr_mod_error'));
				}
				feed.afterInitialImagesLoaded();
				var sbr_delay = (function () {
					var sbr_timer = 0;
					return function (sbr_callback, sbr_ms) {
						clearTimeout(sbr_timer);
						sbr_timer = setTimeout(sbr_callback, sbr_ms);
					};
				})();
				jQuery(window).on('resize',function () {
					sbr_delay(function () {
						feed.afterResize();
					}, 500);
				});

				if ( feed.settings.checkMediaNonce ) {
					var submitData = {
						action: 'sbr_check_media',
						nonce: feed.settings.checkMediaNonce,
						feed_id: feed.settings.feedID
					};
					var onSuccess = function (data) {
						if ( typeof data === 'object' ) {
							$.each(data.data,function(index){
								if ($('.sbr-item-' + index ).length) {
									$('.sbr-item-' + index ).find('.sb-media-placeholder').replaceWith(this);
									feed.applyLightbox($('.sbr-item-' + index ));
								}
							});
						}
					};
					sbrAjax(submitData, onSuccess);
				}
			},
			initLayout: function() {
				var self = this;
				$(this.el).find('.sb-new').removeClass('sb-new');
				if ( this.settings.layout === 'masonry' ) {
					$(this.el).addClass('sbr-masonry');

					if ($(this.el).find('.sb-feed-posts').data('smashotope')) {
						$(this.el).find('.sb-feed-posts').smashotope('layout');
					}

					this.isotopeArgs = {
						itemSelector: '.sb-post-item-wrap',
						layoutMode: 'packery',
						transitionDuration: 0,
						percentPosition: true,
						// options...
						resizable: false // disable normal resizing
					}

					this.smashotopeInit();
				} else if ( this.settings.layout === 'carousel' ) {
					this.initCarousel();
				}
				setTimeout(function() {
					self.setItemSizeClass();
				},100)

			},
			initCarousel: function() {
				var $self = $(this.el),
					feed = this;
				//$self.find('.sb-feed-posts').addClass('sbr_carousel').attr('role','carousel');
				$self.find('.sb-feed-posts').addClass('sbr_carousel');
				$self.find('.sb-load-button-ctn').remove();
				var arrows = this.settings.carousel[0],
					pagination = this.settings.carousel[1],
					autoplay = this.settings.carousel[2],
					time = this.settings.carousel[3],
					loop = this.settings.carousel[4] === 'infinity',
					rows = this.settings.carousel[5];
				//Initiate carousel
				if( !autoplay ) time = false;

				//Set defaults for responsive breakpoints
				var itemsTabletSmall = this.settings.colstablet,
					itemsMobile = this.settings.colsmobile,
					autoplay = time !== false,
					has2rows = (rows == 2),
					loop = loop,
					onChange = function() {
						setTimeout(function(){
							feed.afterResize();
						}, 250);
					},
					afterInit = function() {
						var $self = jQuery(feed.el);
						$self.find('.sb-feed-posts.sbr_carousel').fadeIn();
						setTimeout(function(){
							$self.find('.sbr_owl2row-item, .sbr_carousel').fadeIn();
						}, 50);

						setTimeout(function(){

							var $navElementsWrapper = $self.find('.sbr-owl-nav');
							if (arrows === 'onhover') {

							} else if (arrows === 'below') {
								var $dots = $self.find('.sbr-owl-dots'),
									$prev = $self.find('.sbr-owl-prev'),
									$next = $self.find('.sbr-owl-next'),
									$nav = $self.find('.sbr-owl-nav'),
									$dot = $self.find('.sbr-owl-dot'),
									widthDots = $dot.length * $dot.innerWidth(),
									maxWidth = $self.innerWidth();

								$prev.after($dots);

								$nav.css('position', 'relative');
								$next.css('position', 'absolute').css('top', '-6px').css('right', Math.max((.5 * $nav.innerWidth() - .5 * (widthDots) - $next.innerWidth() - 6), 0));
								$prev.css('position', 'absolute').css('top', '-6px').css('left', Math.max((.5 * $nav.innerWidth() - .5 * (widthDots) - $prev.innerWidth() - 6), 0));
							} else if (arrows === 'hide') {
								$navElementsWrapper.addClass('hide').hide();
							}

						}, 100);
					};

				this.carouselArgs = {
					items: this.settings.cols,
					loop: loop,
					rewind: !loop,
					autoplay: autoplay,
					autoplayTimeout: Math.max(time,2000),
					autoplayHoverPause: true,
					nav: arrows,
					navText: ['<svg class="svg-inline--fa fa-chevron-left fa-w-10" aria-hidden="true" data-fa-processed="" data-prefix="fa" data-icon="chevron-left" role="presentation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M34.52 239.03L228.87 44.69c9.37-9.37 24.57-9.37 33.94 0l22.67 22.67c9.36 9.36 9.37 24.52.04 33.9L131.49 256l154.02 154.75c9.34 9.38 9.32 24.54-.04 33.9l-22.67 22.67c-9.37 9.37-24.57 9.37-33.94 0L34.52 272.97c-9.37-9.37-9.37-24.57 0-33.94z"></path></svg>', '<svg class="svg-inline--fa fa-chevron-right fa-w-10" aria-hidden="true" data-fa-processed="" data-prefix="fa" data-icon="chevron-right" role="presentation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path></svg>'],
					dots: pagination,
					owl2row: has2rows,
					responsive: {
						0: {
							items: this.settings.colsmobile
						},
						480: {
							items: this.settings.colstablet
						},
						640: {
							items: this.settings.cols
						}
					},
					onChange: onChange,
					onInitialize: afterInit
				};
				$self.find('.sbr_carousel').sbrOwlCarousel(this.carouselArgs);
				//if (parseInt(settings.general.carousel[5]) === 2) {
				//	$sbi.addClass('sbi_carousel_2_row');
				//}
			},
			smashotopeInit: function() {
				var gutter = parseInt( $(this.el).attr( 'data-gutter' ) );
				this.isotopeArgs.packery = {
					columnWidth: this.isotopeArgs.itemSelector,
					gutter : Number.isInteger( gutter ) ? gutter : 0
				};
				$(this.el).find('.sb-feed-posts').smashotope(this.isotopeArgs);
			},
			appendSmashotope: function() {
				$self = $(this.el);
				if ($self.find('.sb-feed-posts').data('smashotope')) {
					$self.find('.sb-feed-posts').smashotope('appended', jQuery('.sb-new'));
					setTimeout(() => {
						$self.find('.sb-feed-posts').smashotope('layout');
					}, 10);
				}
			},
			afterInitialImagesLoaded: function() {
				this.initNewItems();
				this.initLayout();
				this.hideExtraImagesForWidth();
				this.loadMoreButtonInit();
				this.hideExtraItemsForWidth();
				if (this.settings.consentGiven) {
					this.applyFullFeatures();
				} else {
					this.removeFeatures();
				}
			},
			initNewItems: function() {
				var feed = this,
					$self = $(this.el);

				$self.find('.sb-new').each(function (index) {
					var $item = jQuery(this);

					if ($item.find('.sb-item-text').length && ! $item.find('.sb-item-text').hasClass('sb-item-full-text')) {
						//Expand post
						var $caption = $item.find('.sb-item-text'),
							text_limit = typeof feed.settings.misc.contentLength !== 'undefined' ? parseInt(feed.settings.misc.contentLength) : 280;
						if (text_limit < 1) text_limit = 99999;
						//Set the full text to be the caption (used in the image alt)
						var captionText = feed.stripEmojihtml($item.find('.sb-item-text').first());
						/*
						var captionText = feed.stripEmojihtml($item.find('.sb-item-text').first()),
							brCount = (captionText.match(/<br>/g) || []).length,
							brAdjust = true;
						// replace emoji with alt for more accurate shortening
						if (brAdjust && brCount > 0 && captionText.indexOf('<br>') < text_limit) {
							var $sizingCaption = $item.find('.sb-item-text').first(),
								captionWidth = $sizingCaption.width() > 20 ? $sizingCaption.width() : $item.width(),
								fontSize = $sizingCaption.css('font-size'),
								charactersPerLine = captionWidth / parseInt(fontSize) * 1.85,
								maxCharsPerLine = Math.floor(charactersPerLine),
								projectedMaxLines = Math.ceil(text_limit / charactersPerLine);

							var splitCaption = captionText.split('<br>'),
								linesConsumed = 0,
								adjustedTextLimit = 0;
							jQuery.each(splitCaption, function () {

								var linesLeft = projectedMaxLines - linesConsumed;
								if (linesLeft > 0) {
									var thisLinesConsumed = Math.max(1, Math.ceil(this.length / charactersPerLine));

									adjustedTextLimit += Math.min(this.length + 4, linesLeft * maxCharsPerLine);
									linesConsumed += thisLinesConsumed;
								}
							});

							text_limit = adjustedTextLimit;
						}
						*/
						var short_text = captionText.substring(0, text_limit);
						short_text = captionText.length > text_limit ? short_text.substr(0, Math.min(short_text.length, short_text.lastIndexOf(" "))) : short_text;

						//Cut the text based on limits set
						if ($caption.length) {
							//if (short_text === captionText && $item.find('.sb-expand a').attr('data-link') === '') {
							if (short_text === captionText) {
								$caption.next('.sb-expand').remove();
							} else {

								$item.attr('data-text',$item.find('.sb-item-text').html());
								if (short_text.length > 1) {
									$item.find('.sb-item-text').html(short_text);
								}
								$item.find('.sb-item-text').append($item.find('.sb-expand'));
								/*
								if ( $item.find('.sb-expand a').attr('data-link') !== '' ) {
									$item.find('.sb-expand a').attr('href',$item.find('.sb-expand a').attr('data-link'))
									$item.find('.sb-expand a').attr('target','blank').attr('rel','noopener noreferrer');
								} else {
									$item.find('.sb-expand a').addClass('sb-expand-on-click');
								}
								*/
								$item.find('.sb-expand a').addClass('sb-expand-on-click');

							}


						}

						//Show the 'See More' link if needed
						if (captionText.length > text_limit) {
							$item.find('.sb-expand').show();
						}
						//Click function
						$item.find('.sb-expand a.sb-expand-on-click').off('click').on('click', function (e) {
							e.preventDefault();
							var $expand = jQuery(this);
							$expand.closest('.sbr-expand').remove();
							$caption.html($item.attr('data-text'));
							$item.addClass('sb-item-full-text');
							feed.afterResize();
						});
					}
				}); //End .sby_item each
			},
			stripEmojihtml: function ($el) {
				$el.find('.emoji').each(function() {
					$(this).replaceWith($(this).attr('alt'));
				});

				return $el.html();
			},
			hideExtraImagesForWidth: function() {
				if (this.settings.layout === 'carousel') {
					return;
				}
				var $self = $(this.el),
					diff = 0;
				if ($(window).width() < 480 || window.sb_preview_device === 'mobile') {
					if (this.settings.misc.num.mobile < $self.find('.sb-post-item-wrap').length) {
						$self.find('.sb-post-item-wrap').slice(this.settings.misc.num.mobile - $self.find('.sb-post-item-wrap').length).addClass('sb-num-diff-hide');
					}
				} else if ($(window).width() < 640 || window.sb_preview_device === 'tablet') {
					if (this.settings.misc.num.tablet < $self.find('.sb-post-item-wrap').length) {
						$self.find('.sb-post-item-wrap').slice(this.settings.misc.num.tablet - $self.find('.sb-post-item-wrap').length).addClass('sb-num-diff-hide');
					}
				} else {
					if (this.settings.misc.num.desktop < $self.find('.sb-post-item-wrap').length) {
						$self.find('.sb-post-item-wrap').slice(this.settings.misc.num.desktop - $self.find('.sb-post-item-wrap').length).addClass('sb-num-diff-hide');
					}
				}

				if ($self.find('.sb-feed-posts').data('smashotope')) {
					$self.find('.sb-feed-posts').smashotope('layout');
				}

			},
			afterResize: function() {
				var $self = $(this.el);
				this.setImageHeight();
				this.setImageResolution();
				this.maybeRaiseImageResolution();
				if ($self.find('.sb-feed-posts').data('smashotope')) {
					$self.find('.sb-feed-posts').smashotope('layout');
				}
				this.setItemSizeClass();
			},
			setItemSizeClass: function () {
				var $self = $(this.el);

				var itemWidth = $self.find('.sb-post-item').first().width();
				if (itemWidth <= 320) $self.addClass('sbr-narrow');
				if (itemWidth <= 250) $self.addClass('sbr-super-narrow');
				if (itemWidth > 320) $self.removeClass('sbr-super-narrow sbr-narrow');
			},
			afterLoadMoreClicked: function($button) {
				var $wrap = $button.closest('.sb-load-button-ctn'),
					areDiffHiding = $wrap.closest('.sb-feed-container').find('.sb-num-diff-hide').length;
				$wrap.find('.sbr-loader').removeClass('sbr-hidden');
				$wrap.find('.sb-load-button span').addClass('sbr-hidden');
				$wrap.closest('.sb-feed-container').find('.sb-num-diff-hide').addClass('sbr_transition').removeClass('sb-num-diff-hide');
				if (areDiffHiding && $(this.el).find('.sb-feed-posts').data('smashotope')) {
					$(this.el).find('.sb-feed-posts').smashotope('layout');
				}
			},
			loadMoreButtonInit: function () {
				var $self = $(this.el),
					feed = this;
				if (this.settings.misc.flagLastPage && ! $self.find( '.sb-num-diff-hide').length ) {
					$self.find('.sb-load-button-ctn .sb-load-button').remove();
				}
				$self.find('.sb-load-button-ctn .sb-load-button').off().on('click', function () {
					feed.afterLoadMoreClicked(jQuery(this));
					feed.getNewPostSet();
				}); //End click event
			},
			getNewPostSet: function () {
				var $self = $(this.el),
					feed = this;
				var submitData = {
					action: 'sbr_load_more_clicked',
					next_page: feed.settings.nextPage,
					feed_id: feed.settings.feedID,
					location: feed.locationGuess(),
					post_id: feed.settings.postID,
					atts: feed.settings.shortCodeAtts,
					current_resolution: feed.imageResolution
				};
				var onSuccess = function (data) {
					feed.appendNewPosts(data.data.html);
					feed.settings.nextPage++;
					feed.afterNewImagesLoaded();
					if (data.data.is_last_page) {
						feed.outOfPages = true;
						$self.find('.sb-load-button-ctn').remove();
					}

				};
				sbrAjax(submitData, onSuccess);
			},
			afterNewImagesLoaded: function() {
				var $self = $(this.el),
					feed = this;
				this.initNewItems();
				setTimeout(function () {
					//Hide the loader in the load more button
					$self.find('.sbr-loader').addClass('sbr-hidden');
					$self.find('.sb-load-button span').removeClass('sbr-hidden');

					feed.maybeRaiseImageResolution();
				}, 500);
				if (this.settings.consentGiven) {
					this.applyFullFeatures();
				} else {
					this.removeFeatures();
				}

				$self.find('.sb-new').removeClass('sb-new');
			},
			appendNewPosts: function (newPostsHtml) {
				var $self = $(this.el);
				if ($self.find('.sb-feed-posts .sb-post-item-wrap').length) {
					$self.find('.sb-feed-posts .sb-post-item-wrap').last().after(newPostsHtml);
				} else {
					$self.find('.sb-feed-posts').append(newPostsHtml);
				}
				if (this.settings.layout === 'masonry') {
					this.appendSmashotope();
				}
			},
			addResizedImages: function (resizedImagesToAdd) {
				for (var imageID in resizedImagesToAdd) {
					this.resizedImages[imageID] = resizedImagesToAdd[imageID];
				}
			},
			setImageHeight: function() {
			},
			maybeRaiseSingleImageResolution: function ($item, forceChange) {
				var feed = this,
					imgSrcSet = feed.getImageUrls($item),
					currentUrl = $item.find('.sbr_video_thumbnail > img').attr('src'),
					currentRes = 150,
					aspectRatio = 1, // all thumbnails are oriented the same so the best calculation uses 1
					forceChange = typeof forceChange !== 'undefined' ? forceChange : false;

				if ($item.hasClass('sbr_no_resraise')   ||
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
						var minImageWidth = Math.max(feed.settings.autoMinRes,$item.find('.sbr_video_thumbnail').innerWidth()),
							thisImageReplace = feed.getBestResolutionForAuto(minImageWidth, aspectRatio, $(this.el).find('sbr_item').first());
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
					$item.find('.sbr_video_thumbnail > img').attr('src', newUrl);
					if ($item.find('.sbr_video_thumbnail').hasClass('sbr_imgLiquid_ready')) {
						$item.find('.sbr_video_thumbnail').css('background-image', 'url("' + newUrl + '")');
					}
				}

				$item.find('img').on('error', function () {
					if (!$(this).hasClass('sbr_img_error')) {
						$(this).addClass('sbr_img_error');
						var sourceFromAPI = ($(this).attr('src').indexOf('i.ytimg.com') > -1);

						if (!sourceFromAPI) {
							if (typeof $(this).closest('.sbr_video_thumbnail').attr('data-full-res') !== 'undefined') {
								//$(this).attr('src', $(this).closest('.sbr_video_thumbnail').attr('data-full-res'));
								//$(this).closest('.sbr_video_thumbnail').css('background-image', 'url(' + $(this).closest('.sbr_video_thumbnail').attr('data-full-res') + ')');
							} else if ($(this).closest('.sbr_video_thumbnail').attr('href') !== 'undefined') {
								//$(this).attr('src', $(this).closest('.sbr_video_thumbnail').attr('href') + 'media?size=l');
								//$(this).closest('.sbr_video_thumbnail').css('background-image', 'url(' + $(this).closest('.sbr_video_thumbnail').attr('href') + 'media?size=l)');
							}
						} else {
							feed.settings.favorLocal = true;
							var srcSet = feed.getImageUrls($(this).closest('.sbr_item'));
							if (typeof srcSet[640] !== 'undefined') {
								//$(this).attr('src', srcSet[640]);
								//$(this).closest('.sbr_video_thumbnail').css('background-image', 'url(' + srcSet[640] + ')');
							}
						}
						setTimeout(function() {
							feed.afterResize();
						}, 1500)
					} else {
						console.log('unfixed error ' + $(this).attr('src'));
					}
				});
			},
			maybeRaiseImageResolution: function (justNew) {
				var feed = this,
					itemsSelector = typeof justNew !== 'undefined' && justNew === true ? '.sbr_item.sbr_new' : '.sbr_item',
					forceChange = !feed.isInitialized ? true : false;
				$(feed.el).find(itemsSelector).each(function () {
					if (!$(this).hasClass(' sb-num-diff-hide')
						&& $(this).find('.sbr_video_thumbnail').length
						&& typeof $(this).find('.sbr_video_thumbnail').attr('data-img-src-set') !== 'undefined') {
						feed.maybeRaiseSingleImageResolution($(this),forceChange);
					}
				}); //End .sbr_item each
				feed.isInitialized = true;
			},
			getBestResolutionForAuto: function(colWidth, aspectRatio, $item) {
				if (isNaN(aspectRatio) || aspectRatio < 1) {
					aspectRatio = 1;
				}
				var bestWidth = colWidth * aspectRatio,
					bestWidthRounded = Math.ceil(bestWidth / 10) * 10,
					customSizes = [120, 320, 480, 640];

				if ($item.hasClass('sbr_highlighted')) {
					bestWidthRounded = bestWidthRounded *2;
				}

				if (customSizes.indexOf(parseInt(bestWidthRounded)) === -1) {
					var done = false;
					$.each(customSizes, function (_index, item) {
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

				if (!$self.hasClass('.sbr_layout_carousel')) {
					if ($(window).width() < 480) {
						if (nummobile < $self.find('.sbr_item').length) {
							$self.find('.sbr_item').slice(nummobile - $self.find('.sbr_item').length).addClass(' sb-num-diff-hide');
						}
					} else {
						if (num < $self.find('.sbr_item').length) {
							$self.find('.sbr_item').slice(num - $self.find('.sbr_item').length).addClass(' sb-num-diff-hide');
						}
					}
				}

			},
			setImageSizeClass: function () {
				var $self = $(this.el);
				$self.removeClass('sbr_small sbr_medium');
				var feedWidth = $self.innerWidth(),
					photoPadding = parseInt(($self.find('.sbr_items_wrap').outerWidth() - $self.find('.sbr_items_wrap').width())) / 2,
					cols = this.getColumnCount(),
					feedWidthSansPadding = feedWidth - (photoPadding * (cols+2)),
					colWidth = (feedWidthSansPadding / cols);

				if (colWidth > 140 && colWidth < 240) {
					$self.addClass('sbr_medium');
				} else if (colWidth <= 140) {
					$self.addClass('sbr_small');
				}
			},
			setMinImageWidth: function () {
				if ($(this.el).find('.sbr_item .sbr_video_thumbnail').first().length) {
					this.minImageWidth = $(this.el).find('.sbr_item .sbr_video_thumbnail').first().innerWidth();
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
				var srcSet = JSON.parse($item.find('.sbr_video_thumbnail').attr('data-img-src-set').replace(/\\\//g, '/')),
					id = $item.attr('id').replace('sbr_', '').replace('player_','');
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
							srcSet[640] = sbrOptions.resized_url + this.resizedImages[id].id + 'full.jpg';
							$item.find('.sbr_link_area').attr( 'href', sbrOptions.resized_url + this.resizedImages[id].id + 'full.jpg' );
							$item.find('.sbr_video_thumbnail').attr( 'data-full-res', sbrOptions.resized_url + this.resizedImages[id].id + 'full.jpg' );
						}
						if (typeof this.resizedImages[id]['sizes']['low'] !== 'undefined') {
							foundSizes.push(320);
							srcSet[320] = sbrOptions.resized_url + this.resizedImages[id].id + 'low.jpg';
							if (this.settings.favorLocal && typeof this.resizedImages[id]['sizes']['full'] === 'undefined') {
								$item.find('.sbr_link_area').attr( 'href', sbrOptions.resized_url + this.resizedImages[id].id + 'low.jpg' );
								$item.find('.sbr_video_thumbnail').attr( 'data-full-res', sbrOptions.resized_url + this.resizedImages[id].id + 'low.jpg' );
							}
						}
						if (typeof this.resizedImages[id]['sizes']['thumb'] !== 'undefined') {
							foundSizes.push(150);
							srcSet[150] = sbrOptions.resized_url + this.resizedImages[id].id + 'thumb.jpg';
						}
						if (this.settings.favorLocal) {
							if (foundSizes.indexOf(640) === -1) {
								if (foundSizes.indexOf(320) > -1) {
									srcSet[640] = sbrOptions.resized_url + this.resizedImages[id].id + 'low.jpg';
								}
							}
							if (foundSizes.indexOf(320) === -1) {
								if (foundSizes.indexOf(640) > -1) {
									srcSet[320] = sbrOptions.resized_url + this.resizedImages[id].id + 'full.jpg';
								} else if (foundSizes.indexOf(150) > -1) {
									srcSet[320] = sbrOptions.resized_url + this.resizedImages[id].id + 'thumb.jpg';
								}
							}
							if (foundSizes.indexOf(150) === -1) {
								if (foundSizes.indexOf(320) > -1) {
									srcSet[150] = sbrOptions.resized_url + this.resizedImages[id].id + 'low.jpg';
								} else if (foundSizes.indexOf(640) > -1) {
									srcSet[150] = sbrOptions.resized_url + this.resizedImages[id].id + 'full.jpg';
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
				if ($el.hasClass('sbr_item') || $el.hasClass('sbr_player_item')) {
					if (typeof $el.find('.sbr_video_thumbnail').attr('data-video-id') !== 'undefined') {
						return $el.find('.sbr_video_thumbnail').attr('data-video-id');
					}
				} else if ($el.closest('sbr_item').length || $el.closest('sbr_player_item').length) {
					var $targeEl = $el.closest('sbr_item').length ? $el.closest('sbr_item') : $el.closest('sbr_player_item');
					if (typeof $targeEl.find('.sbr_video_thumbnail').attr('data-video-id') !== 'undefined') {
						return $targeEl.find('.sbr_video_thumbnail').attr('data-video-id');
					}
				} else if ($el.hasClass('sb-feed-container')) {
					return $el.find('.sbr_item').first().find('.sbr_video_thumbnail').attr('data-video-id');
				} else if ($(this.el).find('.sbr_video_thumbnail').first().length && typeof $(this.el).find('.sbr_video_thumbnail').first().attr('data-video-id') !== 'undefined'){
					return $(this.el).find('.sbr_video_thumbnail').first().attr('data-video-id');
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
						return sbrOptions.resized_url + username + '.jpg';
					} else if (typeof availableAvatars[username] !== 'undefined') {
						return availableAvatars[username];
					} else {
						return '';
					}
				} else {
					if (typeof availableAvatars[username] !== 'undefined') {
						return availableAvatars[username];
					} else if (typeof availableAvatars['LCL'+username] !== 'undefined' && parseInt(availableAvatars['LCL'+username]) === 1)  {
						return sbrOptions.resized_url + username + '.jpg';
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
				sbrAddImgLiquid();
				if (typeof $self.find(".sbr_player_item").sbr_imgLiquid == 'function') {
					if ($self.find('.sbr_player_item').length) {
						$self.find(".sbr_player_item .sbr_player_video_thumbnail").sbr_imgLiquid({fill: true});
					}
					$self.find(".sbr_item .sbr_item_video_thumbnail").sbr_imgLiquid({fill: true});
				}
			},
			listenForVisibilityChange: function() {
				var feed = this;
				sbrAddVisibilityListener();
				if (typeof $(this.el).filter(':hidden').sbrVisibilityChanged == 'function') {
					//If the feed is initially hidden (in a tab for example) then check for when it becomes visible and set then set the height
					$(this.el).filter(':hidden').sbrVisibilityChanged({
						callback: function () {
							feed.afterResize();
						},
						runOnLoad: false
					});
				}
			},
			getColumnCount: function() {
				var cols = this.settings.cols,
					colstablet = this.settings.colstablet,
					colsmobile = this.settings.colsmobile;

				sbrWindowWidth = window.innerWidth;
				if (sbrWindowWidth <= 480) {
					parseInt(colsmobile);
				} else if (sbrWindowWidth <= 640) {
					parseInt(colstablet);
				}

				return parseInt(cols);
			},
			onThumbnailClick: function($clicked,isPlayer,videoID) {
				if (!this.canCreatePlayer()) {
					return;
				}
				var $self = $(this.el);
				if ($self.hasClass('sbr_layout_gallery')) {
					$self.find('.sbr_current').removeClass('sbr_current');
					$clicked.closest('.sbr_item').addClass('sbr_current');

					$clicked.closest('.sbr_item').addClass('sbr_current');
					$self.addClass('sbr_player_added').find('.sbr_player_outer_wrap').addClass('sbr_player_loading');
					$self.find('.sbr_player_outer_wrap .sbr_video_thumbnail').find('.sbr_loader').show().removeClass('sbr_hidden');
					if (!$self.find('.sbr_player_outer_wrap iframe').length) {
						if (isPlayer) {
							this.createPlayer('sbr_player'+this.index);
						} else {
							var videoID = typeof videoID === 'undefined' ? this.getVideoID($clicked.closest('.sbr_item')) : videoID;
							this.createPlayer('sbr_player'+this.index,videoID);
						}
					} else {
						if (isPlayer) {
							var videoID = typeof videoID === 'undefined' ? this.getVideoID($self.find('.sbr_item').first()) : videoID;

							this.playVideoInPlayer(videoID);
						} else {
							var videoID = typeof videoID === 'undefined' ? this.getVideoID($clicked.closest('.sbr_item')) : videoID;

							this.changePlayerInfo($clicked.closest('.sbr_item'));
							this.playVideoInPlayer(videoID);
							this.afterVideoChanged();
						}
					}

				} else if ($(this.el).hasClass('sbr_layout_grid') || $(this.el).hasClass('sbr_layout_carousel')) {
					var $sbrItem = $clicked.closest('.sbr_item'),
						videoID = typeof videoID === 'undefined' ? this.getVideoID($sbrItem) : videoID;
					this.playVideoInPlayer(videoID);
					this.afterVideoChanged();
				} else if ($(this.el).hasClass('sbr_layout_list')) {
					var $sbrItem = $clicked.closest('.sbr_item'),
						videoID = typeof videoID === 'undefined' ? this.getVideoID($sbrItem) : videoID;
					if ($sbrItem.length && !$sbrItem.find('iframe').length) {
						$sbrItem.find('.sbr_loader').show().removeClass('sbr_hidden');
						$sbrItem.addClass('sbr_player_loading sbr_player_loaded');
						this.createPlayer('sbr_player_'+videoID,videoID);
					} else {
						this.playVideoInPlayer(videoID,$sbrItem.attr('data-video-id'));
						this.afterVideoChanged();
					}
				}
			},
			onThumbnailEnter: function($hovered) {
				if (!this.canCreatePlayer()) {
					return;
				}
				var $self = $(this.el);
				if ($self.hasClass('sbr_layout_list')) {
					var $sbrItem = $hovered.closest('.sbr_item'),
						videoID = this.getVideoID($sbrItem);
					if (!$sbrItem.find('iframe').length) {
						$sbrItem.find('.sbr_loader').show().removeClass('sbr_hidden');
						$sbrItem.addClass('sbr_player_loading sbr_player_loaded');
						this.createPlayer('sbr_player_'+videoID,videoID,0);
					}
				}
			},
			onThumbnailLeave: function() {
			},
			changePlayerInfo: function() {

			},
			playerEagerLoaded: function() {
				if (typeof this.player !== 'undefined' || $(this.el).hasClass('sbr_player_loaded')) {
					return true;
				}
			},
			initGrid: function() {
				if (window.sbrSemiEagerLoading && jQuery('#sbr_lightbox').length) {
					var feed = this;
					playerID = 'sbr_lb-player';
					jQuery('#sbr_lightbox').addClass('sbr_video_lightbox');

					var videoID = $(this.el).find('sbr_item').first().attr('data-video-id'),
						autoplay = sbrOptions.autoplay;
					if (typeof window.sbrLightboxPlayer === 'undefined') {
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
									feed.afterStateChange(playerID, videoID, data, $('#' + playerID).closest('.sbr_video_thumbnail_wrap'));
								}
							}
						};
						feed.maybeAddCTA(playerID);

						window.sbrLightboxPlayer = new window.YT.Player(playerID, args);
					}
				}
			},
			maybeAddCTA: function() {},
			canCreatePlayer: function() {
				if ($(this.el).find('#sbr_blank').length) {
					return false;
				}
				return this.playerEagerLoaded() || (this.playerAPIReady && this.settings.consentGiven) || (window.sbrAPIReady && this.settings.consentGiven);
			},
			playVideoInPlayer: function(videoID,playerID) {
				if (typeof this.player !== 'undefined' && typeof this.player.loadVideoById !== 'undefined') {
					this.player.loadVideoById(videoID);
				} else if (typeof window.sbrLightboxPlayer !== 'undefined'
					&& typeof window.sbrLightboxPlayer.loadVideoById !== 'undefined') {
					window.sbrLightboxPlayer.loadVideoById(videoID);
				} else if (typeof playerID !== 'undefined'
					&& typeof this.players !== 'undefined'
					&& typeof this.players[playerID] !== 'undefined'
					&& typeof this.players[playerID].loadVideoById !== 'undefined') {
					this.players[playerID].loadVideoById(videoID);
				}
			},
			afterVideoChanged: function() {
				if ($(this.el).hasClass('sbr_layout_gallery')) {
					$(this.el).find('.sbr_player_outer_wrap').removeClass('sbr_player_loading');
					$(this.el).find('.sbr_player_outer_wrap .sbr_video_thumbnail').find('.sbr_loader').hide().addClass('sbr_hidden');

					if ($(window).width() < 480) {
						$('html, body').animate({
							scrollTop: $(this.el).find('.sbr_player_outer_wrap').offset().top
						}, 300);
					}

				}
			},
			checkConsent: function() {
				if (this.settings.consentGiven || !this.settings.gdpr) {
					this.settings.noCDN = false;
					return true;
				}
				if (typeof CLI_Cookie !== "undefined") { // GDPR Cookie Consent by WebToffee
					if (CLI_Cookie.read(CLI_ACCEPT_COOKIE_NAME) !== null)  {

						// WebToffee no longer uses this cookie but being left here to maintain backwards compatibility
						if (CLI_Cookie.read('cookielawinfo-checkbox-non-necessary') !== null) {
							this.settings.consentGiven = CLI_Cookie.read('cookielawinfo-checkbox-non-necessary') === 'yes';
						}

						if (CLI_Cookie.read('cookielawinfo-checkbox-necessary') !== null) {
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
				} else if (typeof window.cookieconsent !== 'undefined') { // Complianz by Really Simple Plugins
					this.settings.consentGiven = ( sbrCmplzGetCookie('cmplz_consent_status') === 'allow' || jQuery('body').hasClass('cmplz-status-marketing') );
				} else if (typeof window.Cookiebot !== "undefined") { // Cookiebot by Cybot A/S
					this.settings.consentGiven = Cookiebot.consented;
				} else if (typeof window.BorlabsCookie !== 'undefined') { // Borlabs Cookie by Borlabs
					this.settings.consentGiven = window.BorlabsCookie.checkCookieConsent('youtube');
				}

				var evt = jQuery.Event('sbrcheckconsent');
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
					$(feed.el).find('.sbr_video_thumbnail').each(function() {
						$(this).removeAttr('data-sbr-lightbox');
					});
				}
			},
			addLightboxAtt: function() {
				var feed = this;

				if (typeof $(feed.el).find('.sb-single-image').last().attr('data-sbr-lightbox') === 'undefined'
					&& feed.settings.lightboxEnabled) {
					$(feed.el).find('.sb-single-image').each(function() {
						$(this).attr('data-sbr-lightbox',feed.index);
					});
				}
			},
			applyLightbox: function($item) {
				var feed = this;

				$item.find('.sb-single-image').each(function() {
					$(this).attr('data-sbr-lightbox',feed.index);
				});
			},
			applyFullFeatures: function() {
				var feed = this;
				if (feed.settings.lightboxEnabled) {
					feed.addLightboxAtt();
				}
				var $self = $(feed.el);
				$self.find('.sbr_no_consent').removeClass('sbr_no_consent');
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

		function SbrLightboxBuilder() {}

		SbrLightboxBuilder.prototype = {
			getData: function(a){
				var closestFeedIndex = parseInt(a.closest('.sb-feed-container').attr('data-sbr-index')-1);
				return {
					feedIndex : closestFeedIndex,
					link: a.attr("href")
					//videoTitle: typeof a.attr("data-video-title") !== 'undefined' ? a.attr("data-video-title") : 'YouTube Video',
					//video: a.attr("data-video-id")
				}
			},
			template: function () {
				return "<div id='sbr_lightboxOverlay' class='sbr_lightboxOverlay'></div>"+
					"<div id='sbr_lightbox' class='sbr_lightbox'>"+
					"<div class='sbr_lb-outerContainer'>"+
					"<div class='sbr_lb-container'>"+
					"<img class='sbr_lb-image' alt='Lightbox image placeholder' src='' />"+
					"<div class='sbr_lb-player sbr_lb-player-placeholder' id='sbr_lb-player'></div>" +
					"<div class='sbr_lb-nav'><a class='sbr_lb-prev' href='#' ><p class='sbr-screenreader'>Previous Slide</p><span></span></a><a class='sbr_lb-next' href='#' ><p class='sbr-screenreader'>Next Slide</p><span></span></a></div>"+
					"<div class='sbr_lb-loader'><a class='sbr_lb-cancel'></a></div>"+
					"</div>"+
					"</div>"+
					"<div class='sbr_lb-dataContainer'>"+
					"<div class='sbr_lb-data'>"+
					"<div class='sbr_lb-details'>"+
					"<div class='sbr_lb-caption'></div>"+
					"<div class='sbr_lb-info'>"+
					"<div class='sbr_lb-number'></div>"+
					"</div>"+
					"</div>"+
					"<div class='sbr_lb-closeContainer'><a class='sbr_lb-close'></a></div>"+
					"</div>"+
					"</div>"+
					"</div>";
			},
			beforePlayerSetup: function(){

			},
			afterPlayerSetup: function () {
			},
			afterResize: function(){
				var playerHeight = $('#sbr_lightbox .sbr_lb-player').height();

				if (playerHeight > 100) {
					var heightDif = $('#sbr_lightbox .sbr_lb-outerContainer').height() - playerHeight;
					if (heightDif > 10) {
						$('#sbr_lightbox .sbr_lb-player').css('top',heightDif/2);
					}
				}
			},
			pausePlayer: function () {
				if (typeof YT.get('sbr_lb-player') !== 'undefined' && typeof YT.get('sbr_lb-player').pauseVideo === 'function') {
					YT.get('sbr_lb-player').pauseVideo()
				} else if (typeof window.sbrLightboxPlayer !== 'undefined' && typeof window.sbrLightboxPlayer.pauseVideo === 'function') {
					window.sbrLightboxPlayer.pauseVideo();
				}

			}
		};

		window.sbr_init = function() {
			window.sbr = new Sbr();
			window.sbr.createPage( window.sbr.createFeeds, {whenFeedsCreated: window.sbr.afterFeedsCreated});
		};

		function sbrGetNewFeed(feed,index,feedOptions) {
			return new SbrFeed(feed,index,feedOptions);
		}

		function sbrGetlightboxBuilder() {
			return new SbrLightboxBuilder();
		}

		function sbrAjax(submitData,onSuccess) {
			$.ajax({
				url: sbrOptions.adminAjaxUrl,
				type: 'post',
				data: submitData,
				success: onSuccess
			});
		}

		function sbrIsTouch() {
			return "ontouchstart" in document.documentElement;
		}

		function sbrCmplzGetCookie(cname) {
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

	jQuery(document).ready(function($) {
		sbr_init();

		// Cookie Notice by dFactory
		$('#cookie-notice a').on('click',function() {
			setTimeout(function() {
				$.each(window.sbr.feeds,function(index){
					window.sbr.feeds[ index ].afterConsentToggled();
				});
			},1000);
		});

		// Cookie Notice by dFactory
		$('#cookie-law-info-bar a').on('click',function() {
			setTimeout(function() {
				$.each(window.sbr.feeds,function(index){
					window.sbr.feeds[ index ].afterConsentToggled();
				});
			},1000);
		});

		// GDPR Cookie Consent by WebToffee
		$('.cli-user-preference-checkbox').on('click',function(){
			setTimeout(function() {
				$.each(window.sbr.feeds,function(index){
					window.sbr.feeds[ index ].settings.consentGiven = false;
					window.sbr.feeds[ index ].afterConsentToggled();
				});
			},1000);
		});

		// Cookiebot, Complianz by Really Simple Plugins, Complianz by Really Simple Plugins Borlabs Cookie by Borlabs
		$(window).on('CookiebotOnAccept cmplzAcceptAll cmplzRevoke borlabs-cookie-consent-saved', function () {
			$.each(window.sbr.feeds,function(index){
				window.sbr.feeds[ index ].settings.consentGiven = true;
				window.sbr.feeds[ index ].afterConsentToggled();
			});
		});
	});

} // if sbr_js_exists
