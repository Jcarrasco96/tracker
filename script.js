(function () {
    const script = document.currentScript;
    const websiteId = script.getAttribute("data-website-id");

    if (!websiteId) {
        return;
    }

    function getDeviceInfo() {
        const ua = navigator.userAgent || "";

        let isMobile = /Mobi|Android|iPhone|iPad|iPod/i.test(ua);

        let browser = "unknown";

        if (ua.includes("Firefox")) browser = "Firefox";
        else if (ua.includes("Chrome") && !ua.includes("Edg")) browser = "Chrome";
        else if (ua.includes("Safari") && !ua.includes("Chrome")) browser = "Safari";
        else if (ua.includes("Edg")) browser = "Edge";

        let os = "unknown";

        if (ua.includes("Windows")) os = "Windows";
        else if (ua.includes("Mac OS")) os = "macOS";
        else if (ua.includes("Linux")) os = "Linux";
        else if (/Android/i.test(ua)) os = "Android";
        else if (/iPhone|iPad|iPod/i.test(ua)) os = "iOS";

        let platform = navigator.platform || "unknown";

        return {platform, is_mobile: isMobile, browser, os};
    }

    function track(eventType, data = {}) {
        const device = getDeviceInfo();

        const payload = {
            website_id: websiteId,
            event_type: eventType,
            referrer: document.referrer,
            url: location.href,

            device,

            ...data
        };

        fetch("https://mytk.jcarrasco96.com/api/e", {
            method: "POST",
            keepalive: true,
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(payload)
        }).catch(() => {
            console.log('Error!!!!!')
        });
    }

    track("pageview");

    let lastUrl = location.href;

    setInterval(() => {
        if (location.href !== lastUrl) {
            lastUrl = location.href;
            track("pageview");
        }
    }, 1000);

    document.addEventListener("click", function (e) {
        const el = e.target.closest("[data-track-event]");
        if (!el) {
            return;
        }

        track(el.getAttribute("data-track-event"), {
            label: el.getAttribute("data-track-label"),
            value: el.getAttribute("data-track-value")
        });
    });
})();