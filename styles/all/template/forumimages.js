document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-forumimages-carousel]').forEach((carousel) => {
        const viewport = carousel.querySelector('[data-forumimages-strip]');
        const track = carousel.querySelector('[data-forumimages-track]');
        const previousButton = carousel.querySelector('[data-forumimages-prev]');
        const nextButton = carousel.querySelector('[data-forumimages-next]');

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
        let manualAnimation = false;
        const speed = 32;
        const manualDuration = 280;

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

        const getStep = () => {
            const style = getComputedStyle(track);
            const gap = parseFloat(style.columnGap || style.gap) || 0;
            return originals[0].getBoundingClientRect().width + gap;
        };

        const normaliseOffset = () => {
            if (sequenceWidth <= 0) {
                return;
            }

            while (offset > 0) {
                offset -= sequenceWidth;
            }

            while (-offset >= sequenceWidth) {
                offset += sequenceWidth;
            }
        };

        const applyOffset = () => {
            track.style.transform = `translate3d(${offset}px, 0, 0)`;
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
            normaliseOffset();
            applyOffset();
        };

        const easeInOutCubic = (progress) => (
            progress < 0.5
                ? 4 * progress * progress * progress
                : 1 - Math.pow(-2 * progress + 2, 3) / 2
        );

        const moveBy = (distance) => {
            if (sequenceWidth <= 0 || manualAnimation) {
                return;
            }

            const step = Math.abs(distance);
            if (distance > 0 && offset + step > 0) {
                offset -= sequenceWidth;
                applyOffset();
            }

            const startOffset = offset;
            const targetOffset = offset + distance;
            const startTime = performance.now();
            manualAnimation = true;

            const animateManual = (time) => {
                const progress = Math.min((time - startTime) / manualDuration, 1);
                const eased = easeInOutCubic(progress);
                offset = startOffset + (targetOffset - startOffset) * eased;
                applyOffset();

                if (progress < 1) {
                    window.requestAnimationFrame(animateManual);
                    return;
                }

                offset = targetOffset;
                normaliseOffset();
                applyOffset();
                manualAnimation = false;
                lastTime = null;
            };

            window.requestAnimationFrame(animateManual);
        };

        previousButton?.addEventListener('click', () => moveBy(getStep()));
        nextButton?.addEventListener('click', () => moveBy(-getStep()));

        const animate = (time) => {
            if (lastTime === null) {
                lastTime = time;
            }

            const delta = Math.min((time - lastTime) / 1000, 0.1);
            lastTime = time;

            if (sequenceWidth > 0 && !manualAnimation) {
                offset -= speed * delta;
                normaliseOffset();
                applyOffset();
            }

            window.requestAnimationFrame(animate);
        };

        rebuildClones();
        window.addEventListener('resize', rebuildClones, { passive: true });
        window.requestAnimationFrame(animate);
    });
});
