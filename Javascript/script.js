// Track mouse movement
document.addEventListener('mousemove', (e) => {
    const eyes = document.querySelectorAll('.eye');

    eyes.forEach(eye => {
        const pupil = eye.querySelector('.pupil');
        const rect = eye.getBoundingClientRect();

        // Get eye center
        const eyeX = rect.left + rect.width / 2;
        const eyeY = rect.top + rect.height / 2;

        // Get mouse offset
        const mouseX = e.clientX - eyeX;
        const mouseY = e.clientY - eyeY;

        // Calculate view angle
        const angle = Math.atan2(mouseY, mouseX);

        // Constrain pupil movement
        const radius = (rect.width / 2) - (pupil.offsetWidth / 2) - 2;

        // Limit pupil distance
        const distance = Math.min(radius, Math.hypot(mouseX, mouseY));

        // Calculate new position
        const pupilX = Math.cos(angle) * distance;
        const pupilY = Math.sin(angle) * distance;

        // Apply pupil transform
        pupil.style.transform = `translate(calc(-50% + ${pupilX}px), calc(-50% + ${pupilY}px))`;
    });
});

// Handle password focus
const passwordFields = document.querySelectorAll('input[type="password"]');
passwordFields.forEach(field => {
    field.addEventListener('focus', () => {
        document.querySelectorAll('.eye').forEach(eye => eye.classList.add('closed'));
    });

    field.addEventListener('blur', () => {
        document.querySelectorAll('.eye').forEach(eye => eye.classList.remove('closed'));
    });
});

// Prevent form resubmission
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}
