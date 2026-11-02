document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-forumimages-carousel]').forEach((carousel) => {
        const viewport = carousel.querySelector('[data-forumimages-strip]');
        const track = carousel.querySelector('[data-forumimages-track]');

        if (!viewport || !track) {
            return;
        }

        const originals = Array.from(track.children);
        if (originals.length < 2) {
            return;
        }

        let offset = 0;
        let lastTime = null;
        let sequenceWidth = 0;
        const speed = 32;

        const appendCloneSet = () => {
            originals.forEach((item) => {
                const clone = item.cloneNode(true);
                clone.setAttribute('aria-hidden', 'true');
                clone.querySelectorAll('a').forEach((link) => link.setAttribute('tabindex', '-1'));
                if (clone.matches('a')) {
                    clone.setAttribute('tabindex', '-1');
                }
                track.appendChild(clone);
            });
        };

        const rebuildClones = () => {
            Array.from(track.children).slice(originals.length).forEach((item) => item.remove());

            const style = getComputedStyle(track);
            const gap = parseFloat(style.columnGap || style.gap) || 0;
            sequenceWidth = originals.reduce((total, item) => total + item.getBoundingClientRect().width, 0)
                + gap * originals.length;

            if (sequenceWidth <= 0) {
                return;
            }

            const setsNeeded = Math.max(2, Math.ceil(viewport.clientWidth / sequenceWidth) + 2);
            for (let set = 1; set < setsNeeded; set += 1) {
                appendCloneSet();
            }

            offset = -((-offset) % sequenceWidth);
        };

        const animate = (time) => {
            if (lastTime === null) {
                lastTime = time;
            }

            const delta = Math.min((time - lastTime) / 1000, 0.1);
            lastTime = time;

            if (sequenceWidth > 0) {
                offset -= speed * delta;

                if (-offset >= sequenceWidth) {
                    offset += sequenceWidth;
                }

                track.style.transform = `translate3d(${offset}px, 0, 0)`;
            }

            window.requestAnimationFrame(animate);
        };

        rebuildClones();
        window.addEventListener('resize', rebuildClones, { passive: true });
        window.requestAnimationFrame(animate);
    });
});
