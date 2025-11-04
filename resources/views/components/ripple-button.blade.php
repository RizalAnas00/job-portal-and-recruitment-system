<div>
    <button
        {{ $attributes->merge([
            'type' => 'button',
            'class' => 'bt relative overflow-hidden rounded-lg transition-all duration-300
                        focus:outline-none active:scale-[0.98]'
        ]) }}
    >
        {{ $buttonText ?? 'Button' }}
    </button>
</div>

<style>
    .ripple {
        position: absolute;
        border-radius: 50%;
        transform: scale(0);
        animation: ripple-animation 700ms ease-out;
        pointer-events: none;
        z-index: 0;
        opacity: 0.5;
    }

    @keyframes ripple-animation {
        to {
            transform: scale(8);
            opacity: 0;
        }
    }
</style>

<script>
    document.querySelectorAll('.bt').forEach((btn) => {
        btn.addEventListener('click', function (event) {
            const circle = document.createElement('span');
            const diameter = Math.max(btn.clientWidth, btn.clientHeight) * 2.5;
            const radius = diameter / 2;

            const rect = btn.getBoundingClientRect();
            circle.style.width = circle.style.height = `${diameter}px`;
            circle.style.left = `${event.clientX - rect.left - radius}px`;
            circle.style.top = `${event.clientY - rect.top - radius}px`;

            // 💡 Ambil warna ripple otomatis dari computed style (dinamis!)
            const bgColor = window.getComputedStyle(btn).backgroundColor;
            const textColor = window.getComputedStyle(btn).color;

            // Tentukan ripple color berdasarkan kontras
            const isLightBg = bgColor.includes('255, 255, 255') || bgColor.includes('rgba(255');
            circle.style.backgroundColor = isLightBg
                ? textColor.replace('rgb', 'rgba').replace(')', ', 0.3)')
                : 'rgba(255,255,255,0.35)';

            circle.classList.add('ripple');

            const existingRipple = btn.querySelector('.ripple');
            if (existingRipple) existingRipple.remove();

            btn.appendChild(circle);
        });
    });
</script>
