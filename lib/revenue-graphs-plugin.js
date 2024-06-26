/*!
 * chartjs-plugin-zoom v1.0.0
 * undefined
 * (c) 2016-2021 chartjs-plugin-zoom Contributors
 * Released under the MIT License
 */
!(function (e, n) {
  "object" == typeof exports && "undefined" != typeof module
    ? (module.exports = n(
        require("chart.js"),
        require("hammerjs"),
        require("chart.js/helpers")
      ))
    : "function" == typeof define && define.amd
    ? define(["chart.js", "hammerjs", "chart.js/helpers"], n)
    : ((e =
        "undefined" != typeof globalThis ? globalThis : e || self).ChartZoom =
        n(e.Chart, e.Hammer, e.Chart.helpers));
})(this, function (e, n, t) {
  "use strict";
  function o(e) {
    return e && "object" == typeof e && "default" in e ? e : { default: e };
  }
  var a = o(n);
  function i(e, n, t) {
    return (
      void 0 === e ||
      ("string" == typeof e
        ? -1 !== e.indexOf(n)
        : "function" == typeof e && -1 !== e({ chart: t }).indexOf(n))
    );
  }
  function c(e, n, o) {
    const a = (function ({ x: e, y: n }, t) {
      const o = t.scales,
        a = Object.keys(o);
      for (let t = 0; t < a.length; t++) {
        const i = o[a[t]];
        if (n >= i.top && n <= i.bottom && e >= i.left && e <= i.right)
          return i;
      }
      return null;
    })(n, o);
    if (a && i(e, a.axis, o)) return [a];
    const c = [];
    return (
      t.each(o.scales, function (n) {
        i(e, n.axis, o) || c.push(n);
      }),
      c
    );
  }
  function r(e, n, t) {
    const o = e.max - e.min,
      a = o * (n - 1),
      i = e.isHorizontal() ? t.x : t.y,
      c = (e.getValueForPixel(i) - e.min) / o || 0;
    return { min: a * c, max: a * (1 - c) };
  }
  function l(e, { min: n, max: t }, o, a = !1) {
    const { axis: i, options: c } = e,
      { min: r = -1 / 0, max: l = 1 / 0, minRange: s = 0 } = (o && o[i]) || {},
      m = Math.max(n, r),
      u = Math.min(t, l),
      d = a ? Math.max(u - m, s) : e.max - e.min;
    if (u - m !== d)
      if (r > u - d) (n = m), (t = m + d);
      else if (l < m + d) (t = u), (n = u - d);
      else {
        const e = (d - u + m) / 2;
        (n = m - e), (t = u + e);
      }
    else (n = m), (t = u);
    return (
      (c.min = n), (c.max = t), e.parse(n) !== e.min || e.parse(t) !== e.max
    );
  }
  const s = (e) =>
    0 === e || isNaN(e)
      ? 0
      : e < 0
      ? Math.min(Math.round(e), -1)
      : Math.max(Math.round(e), 1);
  const m = {
    second: 500,
    minute: 3e4,
    hour: 18e5,
    day: 432e5,
    week: 3024e5,
    month: 1296e6,
    quarter: 5184e6,
    year: 157248e5,
  };
  function u(e, n, t, o = !1) {
    const { min: a, max: i, options: c } = e,
      r = c.time && c.time.round,
      s = m[r] || 0,
      u = e.getValueForPixel(e.getPixelForValue(a + s) - n),
      d = e.getValueForPixel(e.getPixelForValue(i + s) - n),
      { min: f = -1 / 0, max: h = 1 / 0 } = (o && t && t[e.axis]) || {};
    return u < f || d > h || l(e, { min: u, max: d }, t, o);
  }
  function d(e, n, t) {
    return u(e, n, t, !0);
  }
  const f = {
      category: function (e, n, t, o) {
        const a = r(e, n, t);
        return (
          e.min === e.max &&
            n < 1 &&
            (function (e) {
              const n = e.getLabels().length - 1;
              e.min > 0 && (e.min -= 1), e.max < n && (e.max += 1);
            })(e),
          l(e, { min: e.min + s(a.min), max: e.max - s(a.max) }, o, !0)
        );
      },
      default: function (e, n, t, o) {
        const a = r(e, n, t);
        return l(e, { min: e.min + a.min, max: e.max - a.max }, o, !0);
      },
    },
    h = {
      category: function (e, n, t) {
        const o = e.getLabels().length - 1;
        let { min: a, max: i } = e;
        const c = Math.max(i - a, 1),
          r = Math.round(
            (function (e) {
              return e.isHorizontal() ? e.width : e.height;
            })(e) / Math.max(c, 10)
          ),
          s = Math.round(Math.abs(n / r));
        let m;
        return (
          n < -r
            ? ((i = Math.min(i + s, o)),
              (a = 1 === c ? i : i - c),
              (m = i === o))
            : n > r &&
              ((a = Math.max(0, a - s)),
              (i = 1 === c ? a : a + c),
              (m = 0 === a)),
          l(e, { min: a, max: i }, t) || m
        );
      },
      default: u,
      logarithmic: d,
      timeseries: d,
    },
    p = new WeakMap();
  function x(e) {
    let n = p.get(e);
    return (
      n ||
        ((n = { originalScaleLimits: {}, handlers: {}, panDelta: {} }),
        p.set(e, n)),
      n
    );
  }
  function g(e) {
    const { originalScaleLimits: n } = x(e);
    return (
      t.each(e.scales, function (e) {
        n[e.id] || (n[e.id] = { min: e.options.min, max: e.options.max });
      }),
      t.each(n, function (t, o) {
        e.scales[o] || delete n[o];
      }),
      n
    );
  }
  function y(e, n, o, a) {
    const i = f[e.type] || f.default;
    t.callback(i, [e, n, o, a]);
  }
  function b(e) {
    const n = e.chartArea;
    return { x: (n.left + n.right) / 2, y: (n.top + n.bottom) / 2 };
  }
  function v(e, n, o = "none") {
    const {
        x: a = 1,
        y: r = 1,
        focalPoint: l = b(e),
      } = "number" == typeof n ? { x: n, y: n } : n,
      {
        options: { limits: s, zoom: m },
      } = x(e),
      { mode: u = "xy", overScaleMode: d } = m || {};
    g(e);
    const f = 1 !== a && i(u, "x", e),
      h = 1 !== r && i(u, "y", e),
      p = d && c(d, l, e);
    t.each(p || e.scales, function (e) {
      e.isHorizontal() && f
        ? y(e, a, l, s)
        : !e.isHorizontal() && h && y(e, r, l, s);
    }),
      e.update(o),
      t.callback(m.onZoom, [{ chart: e }]);
  }
  function z(e, n, t) {
    const o = e.getValueForPixel(n),
      a = e.getValueForPixel(t);
    return { min: Math.min(o, a), max: Math.max(o, a) };
  }
  function w(e, n, o) {
    const { panDelta: a } = x(e.chart),
      i = a[e.id] || 0;
    t.sign(i) === t.sign(n) && (n += i);
    const c = h[e.type] || h.default;
    t.callback(c, [e, n, o]) ? (a[e.id] = 0) : (a[e.id] = n);
  }
  function M(e, n, o, a = "none") {
    const { x: c = 0, y: r = 0 } = "number" == typeof n ? { x: n, y: n } : n,
      {
        options: { pan: l, limits: s },
      } = x(e),
      { mode: m = "xy", onPan: u } = l || {};
    g(e);
    const d = 0 !== c && i(m, "x", e),
      f = 0 !== r && i(m, "y", e);
    t.each(o || e.scales, function (e) {
      e.isHorizontal() && d ? w(e, c, s) : !e.isHorizontal() && f && w(e, r, s);
    }),
      e.update(a),
      t.callback(u, [{ chart: e }]);
  }
  function k(e, n, t) {
    const { handlers: o } = x(e),
      a = o[t];
    a && (n.removeEventListener(t, a), delete o[t]);
  }
  function P(e, n, t, o) {
    const { handlers: a, options: i } = x(e);
    k(e, n, t), (a[t] = (n) => o(e, n, i)), n.addEventListener(t, a[t]);
  }
  function C(e, n) {
    const t = x(e);
    t.dragStart && ((t.dragging = !0), (t.dragEnd = n), e.update("none"));
  }
  function S(e, n, o) {
    const { onZoomStart: a, onZoomRejected: i } = o;
    if (a) {
      const { left: o, top: c } = n.target.getBoundingClientRect(),
        r = { x: n.clientX - o, y: n.clientY - c };
      if (!1 === t.callback(a, [{ chart: e, event: n, point: r }]))
        return t.callback(i, [{ chart: e, event: n }]), !1;
    }
  }
  function j(e, n) {
    const o = x(e),
      { pan: a, zoom: i } = o.options,
      c = a && a.modifierKey;
    if (c && n[c + "Key"])
      return t.callback(i.onZoomRejected, [{ chart: e, event: n }]);
    !1 !== S(e, n, i) && ((o.dragStart = n), P(e, e.canvas, "mousemove", C));
  }
  function R(e, n, t, o) {
    const { left: a, top: c } = t.target.getBoundingClientRect(),
      r = i(n, "x", e),
      l = i(n, "y", e);
    let {
      top: s,
      left: m,
      right: u,
      bottom: d,
      width: f,
      height: h,
    } = e.chartArea;
    r &&
      ((m = Math.min(t.clientX, o.clientX) - a),
      (u = Math.max(t.clientX, o.clientX) - a)),
      l &&
        ((s = Math.min(t.clientY, o.clientY) - c),
        (d = Math.max(t.clientY, o.clientY) - c));
    const p = u - m,
      x = d - s;
    return {
      left: m,
      top: s,
      right: u,
      bottom: d,
      width: p,
      height: x,
      zoomX: r && p ? 1 + (f - p) / f : 1,
      zoomY: l && x ? 1 + (h - x) / h : 1,
    };
  }
  function Y(e, n) {
    const o = x(e);
    if (!o.dragStart) return;
    k(e.canvas, "mousemove", e);
    const {
        mode: a,
        onZoomComplete: c,
        drag: { threshold: r = 0 },
      } = o.options.zoom,
      s = R(e, a, o.dragStart, n),
      m = i(a, "x", e) ? s.width : 0,
      u = i(a, "y", e) ? s.height : 0,
      d = Math.sqrt(m * m + u * u);
    (o.dragStart = o.dragEnd = null),
      d <= r ||
        (!(function (e, n, o, a = "none") {
          const {
              options: { limits: c, zoom: r },
            } = x(e),
            { mode: s = "xy" } = r;
          g(e);
          const m = i(s, "x", e),
            u = i(s, "y", e);
          t.each(e.scales, function (e) {
            e.isHorizontal() && m
              ? l(e, z(e, n.x, o.x), c, !0)
              : !e.isHorizontal() && u && l(e, z(e, n.y, o.y), c, !0);
          }),
            e.update(a),
            t.callback(r.onZoom, [{ chart: e }]);
        })(e, { x: s.left, y: s.top }, { x: s.right, y: s.bottom }, "zoom"),
        setTimeout(() => (o.dragging = !1), 500),
        t.callback(c, [{ chart: e }]));
  }
  function Z(e, n) {
    const {
      handlers: { onZoomComplete: o },
      options: { zoom: a },
    } = x(e);
    if (
      !(function (e, n, o) {
        const { wheel: a, onZoomRejected: i } = o;
        if (!a.modifierKey || n[a.modifierKey + "Key"]) {
          if (
            !1 !== S(e, n, o) &&
            (n.cancelable && n.preventDefault(), void 0 !== n.deltaY)
          )
            return !0;
        } else t.callback(i, [{ chart: e, event: n }]);
      })(e, n, a)
    )
      return;
    const i = n.target.getBoundingClientRect(),
      c = 1 + (n.deltaY >= 0 ? -a.wheel.speed : a.wheel.speed);
    v(e, {
      x: c,
      y: c,
      focalPoint: { x: n.clientX - i.left, y: n.clientY - i.top },
    }),
      o && o();
  }
  function T(e, n, o, a) {
    o &&
      (x(e).handlers[n] = (function (e, n) {
        let t;
        return function () {
          return clearTimeout(t), (t = setTimeout(e, n)), n;
        };
      })(() => t.callback(o, [{ chart: e }]), a));
  }
  function X(e) {
    const n = x(e);
    return function (o, a) {
      const i = n.options.pan;
      if (!i || !i.enabled) return !1;
      if (!a || !a.srcEvent) return !0;
      const c = i.modifierKey;
      return (
        !(c && "mouse" === a.pointerType && !a.srcEvent[c + "Key"]) ||
        (t.callback(i.onPanRejected, [{ chart: e, event: a }]), !1)
      );
    };
  }
  function E(e, n, t) {
    if (n.scale) {
      const { center: o, pointers: a } = t,
        c = (1 / n.scale) * t.scale,
        r = t.target.getBoundingClientRect(),
        l = (function (e, n) {
          const t = Math.abs(e.clientX - n.clientX),
            o = Math.abs(e.clientY - n.clientY),
            a = t / o;
          let i, c;
          return (
            a > 0.3 && a < 1.7 ? (i = c = !0) : t > o ? (i = !0) : (c = !0),
            { x: i, y: c }
          );
        })(a[0], a[1]),
        s = n.options.zoom.mode;
      v(e, {
        x: l.x && i(s, "x", e) ? c : 1,
        y: l.y && i(s, "y", e) ? c : 1,
        focalPoint: { x: o.x - r.left, y: o.y - r.top },
      }),
        (n.scale = t.scale);
    }
  }
  function F(e, n, t) {
    const o = n.delta;
    o &&
      ((n.panning = !0),
      M(e, { x: t.deltaX - o.x, y: t.deltaY - o.y }, n.panScales),
      (n.delta = { x: t.deltaX, y: t.deltaY }));
  }
  const H = new WeakMap();
  function K(e, n) {
    const o = x(e),
      i = e.canvas,
      { pan: r, zoom: l } = n,
      s = new a.default.Manager(i);
    l &&
      l.pinch.enabled &&
      (s.add(new a.default.Pinch()),
      s.on("pinchstart", () =>
        (function (e, n) {
          n.options.zoom.pinch.enabled && (n.scale = 1);
        })(0, o)
      ),
      s.on("pinch", (n) => E(e, o, n)),
      s.on("pinchend", (n) =>
        (function (e, n, o) {
          n.scale &&
            (E(e, n, o),
            (n.scale = null),
            t.callback(n.options.zoom.onZoomComplete, [{ chart: e }]));
        })(e, o, n)
      )),
      r &&
        r.enabled &&
        (s.add(new a.default.Pan({ threshold: r.threshold, enable: X(e) })),
        s.on("panstart", (n) =>
          (function (e, n, o) {
            const {
              enabled: a,
              overScaleMode: i,
              onPanStart: r,
              onPanRejected: l,
            } = n.options.pan;
            if (!a) return;
            const s = o.target.getBoundingClientRect(),
              m = { x: o.center.x - s.left, y: o.center.y - s.top };
            if (!1 === t.callback(r, [{ chart: e, event: o, point: m }]))
              return t.callback(l, [{ chart: e, event: o }]);
            (n.panScales = i && c(i, m, e)),
              (n.delta = { x: 0, y: 0 }),
              clearTimeout(n.panEndTimeout),
              F(e, n, o);
          })(e, o, n)
        ),
        s.on("panmove", (n) => F(e, o, n)),
        s.on("panend", () =>
          (function (e, n) {
            (n.delta = null),
              n.panning &&
                ((n.panEndTimeout = setTimeout(() => (n.panning = !1), 500)),
                t.callback(n.options.pan.onPanComplete, [{ chart: e }]));
          })(e, o)
        )),
      H.set(e, s);
  }
  var D = {
    id: "zoom",
    version: "1.0.0",
    defaults: {
      pan: { enabled: !1, mode: "xy", threshold: 10, modifierKey: null },
      zoom: {
        wheel: { enabled: !1, speed: 0.1, modifierKey: null },
        drag: { enabled: !1 },
        pinch: { enabled: !1 },
        mode: "xy",
      },
    },
    start: function (e, n, o) {
      (x(e).options = o),
        Object.prototype.hasOwnProperty.call(o.zoom, "enabled") &&
          console.warn(
            "The option `zoom.enabled` is no longer supported. Please use `zoom.wheel.enabled`, `zoom.drag.enabled`, or `zoom.pinch.enabled`."
          ),
        a.default && K(e, o),
        (e.pan = (n, t, o) => M(e, n, t, o)),
        (e.zoom = (n, t) => v(e, n, t)),
        (e.zoomScale = (n, t, o) =>
          (function (e, n, t, o = "none") {
            g(e), l(e.scales[n], t, void 0, !0), e.update(o);
          })(e, n, t, o)),
        (e.resetZoom = (n) =>
          (function (e, n = "default") {
            const o = g(e);
            t.each(e.scales, function (e) {
              const n = e.options;
              o[e.id]
                ? ((n.min = o[e.id].min), (n.max = o[e.id].max))
                : (delete n.min, delete n.max);
            }),
              e.update(n);
          })(e, n));
    },
    beforeEvent(e) {
      const n = x(e);
      if (n.panning || n.dragging) return !1;
    },
    beforeUpdate: function (e, n, t) {
      (x(e).options = t),
        (function (e, n) {
          const t = e.canvas,
            { wheel: o, drag: a, onZoomComplete: i } = n.zoom;
          o.enabled
            ? (P(e, t, "wheel", Z), T(e, "onZoomComplete", i, 250))
            : k(e, t, "wheel"),
            a.enabled
              ? (P(e, t, "mousedown", j), P(e, t.ownerDocument, "mouseup", Y))
              : (k(e, t, "mousedown"),
                k(e, t, "mousemove"),
                k(e, t.ownerDocument, "mouseup"));
        })(e, t);
    },
    beforeDatasetsDraw: function (e, n, t) {
      const { dragStart: o, dragEnd: a } = x(e);
      if (a) {
        const {
            left: n,
            top: i,
            width: c,
            height: r,
          } = R(e, t.zoom.mode, o, a),
          l = t.zoom.drag,
          s = e.ctx;
        s.save(),
          s.beginPath(),
          (s.fillStyle = l.backgroundColor || "rgba(225,225,225,0.3)"),
          s.fillRect(n, i, c, r),
          l.borderWidth > 0 &&
            ((s.lineWidth = l.borderWidth),
            (s.strokeStyle = l.borderColor || "rgba(225,225,225)"),
            s.strokeRect(n, i, c, r)),
          s.restore();
      }
    },
    stop: function (e) {
      !(function (e) {
        const { canvas: n } = e;
        n &&
          (k(e, n, "mousedown"),
          k(e, n, "mousemove"),
          k(e, n.ownerDocument, "mouseup"),
          k(e, n, "wheel"),
          k(e, n, "click"));
      })(e),
        a.default &&
          (function (e) {
            const n = H.get(e);
            n &&
              (n.remove("pinchstart"),
              n.remove("pinch"),
              n.remove("pinchend"),
              n.remove("panstart"),
              n.remove("pan"),
              n.remove("panend"),
              n.destroy(),
              H.delete(e));
          })(e),
        (function (e) {
          p.delete(e);
        })(e);
    },
    panFunctions: h,
    zoomFunctions: f,
  };
  return e.Chart.register(D), D;
});
