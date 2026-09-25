/**
 * DULCE GRACIA — SISTEMA GLOBAL DE ALERTAS CON SONIDO
 * Soporta Toasts, Alerts, Notificaciones Flotantes y Audio Web sintetizado.
 */

const DGAlert = (function () {
    // Configuración inicial de sonido desde localStorage (por defecto '1')
    let soundEnabled = localStorage.getItem('dg_sound_enabled') !== '0';

    // Generador de audio sintetizado Web Audio API (Sin archivos externos de audio)
    function playAudioTone(type) {
        if (!soundEnabled) return;

        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();

            // Si el contexto está suspendido (política del navegador), intentar reanudar
            if (ctx.state === 'suspended') {
                ctx.resume();
            }

            const playNote = (freq, startTime, duration, typeWave = 'sine') => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = typeWave;
                osc.frequency.setValueAtTime(freq, ctx.currentTime + startTime);

                // Envolvente suave para evitar clicks de audio
                gain.gain.setValueAtTime(0.001, ctx.currentTime + startTime);
                gain.gain.exponentialRampToValueAtTime(0.15, ctx.currentTime + startTime + 0.05);
                gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + startTime + duration);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(ctx.currentTime + startTime);
                osc.stop(ctx.currentTime + startTime + duration + 0.05);
            };

            switch (type) {
                case 'success':
                    playNote(523.25, 0, 0.15); // C5
                    playNote(659.25, 0.12, 0.25); // E5
                    break;
                case 'point_earned':
                    playNote(659.25, 0, 0.12); // E5
                    playNote(987.77, 0.1, 0.3);  // B5 (sonido tipo moneda)
                    break;
                case 'purchase':
                    playNote(523.25, 0, 0.1);    // C5
                    playNote(659.25, 0.1, 0.1);  // E5
                    playNote(783.99, 0.2, 0.1);  // G5
                    playNote(1046.50, 0.3, 0.35); // C6
                    break;
                case 'order_received':
                    playNote(587.33, 0, 0.15); // D5
                    playNote(880.00, 0.15, 0.3); // A5
                    break;
                case 'order_updated':
                    playNote(440.00, 0, 0.12); // A4
                    playNote(659.25, 0.12, 0.25); // E5
                    break;
                case 'warning':
                    playNote(440.00, 0, 0.15, 'triangle'); // A4
                    playNote(440.00, 0.2, 0.15, 'triangle');
                    break;
                case 'error':
                    playNote(311.13, 0, 0.2, 'sawtooth'); // Eb4
                    playNote(261.63, 0.18, 0.3, 'sawtooth'); // C4
                    break;
                default:
                    playNote(523.25, 0, 0.2); // C5 (info)
            }
        } catch (e) {
            console.warn('Audio feedback error or restricted by browser:', e);
        }
    }

    // Retorna icono según el tipo
    function getIconHtml(type, customIcon) {
        if (customIcon) return `<i class="${customIcon} me-2 fs-5"></i>`;
        switch (type) {
            case 'success':
            case 'purchase':
                return '<i class="fas fa-check-circle me-2 fs-5 text-success"></i>';
            case 'point_earned':
                return '<i class="fas fa-coins me-2 fs-5 text-warning"></i>';
            case 'order_received':
                return '<i class="fas fa-box me-2 fs-5 text-info"></i>';
            case 'order_updated':
                return '<i class="fas fa-sync-alt me-2 fs-5 text-primary"></i>';
            case 'warning':
                return '<i class="fas fa-exclamation-triangle me-2 fs-5 text-warning"></i>';
            case 'error':
                return '<i class="fas fa-times-circle me-2 fs-5 text-danger"></i>';
            default:
                return '<i class="fas fa-info-circle me-2 fs-5 text-info"></i>';
        }
    }

    // Mostrar notificación Toast flotante
    function show(options) {
        const type = options.type || 'info';
        const title = options.title || '';
        const message = options.message || '';
        const duration = options.duration || 4000;
        const playSound = options.playSound !== false;

        if (playSound) {
            playAudioTone(type);
        }

        if (typeof Toastify === 'function') {
            let borderColor = 'var(--dg-primary)';
            if (type === 'error') borderColor = '#dc3545';
            if (type === 'warning') borderColor = '#ffc107';
            if (type === 'point_earned') borderColor = '#ffb703';

            Toastify({
                text: `${title ? title + ': ' : ''}${message}`,
                duration: duration,
                close: true,
                gravity: 'bottom',
                position: 'right',
                stopOnFocus: true,
                style: {
                    background: '#ffffff',
                    color: '#2b1810',
                    borderRadius: '14px',
                    borderLeft: `5px solid ${borderColor}`,
                    boxShadow: '0 8px 24px rgba(0, 0, 0, 0.12)',
                    fontFamily: 'var(--font-body, sans-serif)',
                    padding: '12px 18px'
                }
            }).showToast();
        } else {
            console.log(`[ALERT ${type.toUpperCase()}] ${title}: ${message}`);
        }
    }

    return {
        isSoundEnabled: function () {
            return soundEnabled;
        },
        setSoundEnabled: function (enabled) {
            soundEnabled = !!enabled;
            localStorage.setItem('dg_sound_enabled', soundEnabled ? '1' : '0');
        },
        show: show,
        success: function (title, message) {
            show({ type: 'success', title: title, message: message });
        },
        info: function (title, message) {
            show({ type: 'info', title: title, message: message });
        },
        warning: function (title, message) {
            show({ type: 'warning', title: title, message: message });
        },
        error: function (title, message) {
            show({ type: 'error', title: title, message: message });
        },
        orderReceived: function (orderNumber) {
            show({
                type: 'order_received',
                title: '✓ Pedido Recibido',
                message: `Tu pedido #${orderNumber} fue registrado correctamente.`,
                duration: 5000
            });
        },
        orderUpdated: function (orderNumber, statusLabel) {
            show({
                type: 'order_updated',
                title: 'Pedido Actualizado',
                message: `El pedido #${orderNumber} ha cambiado a: ${statusLabel}`,
                duration: 5000
            });
        },
        purchaseSuccess: function (orderNumber) {
            show({
                type: 'purchase',
                title: '¡Compra Realizada con Éxito!',
                message: `Pedido #${orderNumber} confirmado. Gracias por elegir Dulce Gracia.`,
                duration: 6000
            });
        },
        pointEarned: function (points) {
            show({
                type: 'point_earned',
                title: '✨ ¡Puntos Ganados!',
                message: `Acabas de ganar ${points} puntos en tu Billetera Dulce Gracia.`,
                duration: 5000
            });
        }
    };
})();
