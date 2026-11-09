const TOAST_CONTAINER_ID = "toast-container";
const TOAST_STYLE_ID = "toast-style";
const TOAST_LIMIT = 5;

function ensureToastStyles() {
    if (document.getElementById(TOAST_STYLE_ID)) {
        return;
    }

    const style = document.createElement("style");
    style.id = TOAST_STYLE_ID;
    style.textContent = `
        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateX(28px) scale(0.96);
            }
            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes toastOut {
            from {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
            to {
                opacity: 0;
                transform: translateX(28px) scale(0.96);
            }
        }

        #toast-container {
            position: fixed;
            right: 16px;
            bottom: 16px;
            z-index: 2147483647;
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: min(360px, calc(100vw - 24px));
            pointer-events: none;
        }

        .toast-item {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            min-height: 56px;
            padding: 12px 14px;
            border-radius: 12px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.22);
            color: #fff;
            opacity: 0;
            transform: translateX(28px) scale(0.96);
            pointer-events: auto;
            animation: toastIn 0.28s ease forwards;
            border-left: 4px solid rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            position: relative;
        }

        .toast-item.hide {
            animation: toastOut 0.24s ease forwards;
        }

        .toast-message {
            position: absolute;
            left: 0;
            right: 30px;
            top: 0;
            bottom: 0;
            font-size: 0.92rem;
            line-height: 1.4;
            word-break: break-word;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 12px;
        }

        .toast-close {
            border: none;
            background: transparent;
            color: rgba(255, 255, 255, 0.9);
            cursor: pointer;
            font-size: 1.1rem;
            line-height: 1;
            padding: 2px 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            min-height: 20px;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 480px) {
            #toast-container {
                right: 10px;
                bottom: 10px;
                width: min(280px, calc(100vw - 16px));
            }

            .toast-item {
                min-height: 48px;
                padding: 10px 12px;
            }

            .toast-message {
                font-size: 0.8rem;
                right: 26px;
                padding: 0 8px;
            }

            .toast-close {
                font-size: 0.95rem;
                min-width: 18px;
                min-height: 18px;
            }
        }
    `;

    document.head.appendChild(style);
}

function ensureToastContainer() {
    ensureToastStyles();

    let container = document.getElementById(TOAST_CONTAINER_ID);

    if (!container) {
        container = document.createElement("div");
        container.id = TOAST_CONTAINER_ID;
        document.body.appendChild(container);
    }

    return container;
}

export async function request(url, method = "GET", options = {}) {
    try {
        const headers = {
            "Content-Type": "application/json",
            ...(options.headers || {})
        };

        const response = await fetch(url, {
            method,
            credentials: "include",
            body: options.body,
            ...options,
            headers,
        });

        const text = await response.text();
        let data;

        try {
            data = JSON.parse(text);
        } 
        
        catch {
            if (/Fatal error|Parse error|Warning|Notice/i.test(text)) {
                const match = text.match(/(?:Fatal error|Parse error|Warning|Notice):\s*([^\n<]+)/i);
                const reason = match ? match[1].trim() : "Erro inesperado.";

                console.log("Erro inesperado: ", reason);
                console.log("Resposta: ", text);

                return { status: "error", message: `Erro inesperado: ${reason}` };
            }
            
            console.log("Resposta inválida do servidor:", text);
            return { status: "error", message: "Resposta inválida." };
        }

        if (!response.ok) {
            return data && typeof data === "object"
                ? { ...data, success: false }
                : { status: "error", message: "Erro na requisição." };
        }

        return data;
    } 
    
    catch (error) {
        return { status: "error", message: `Erro de conexão com o servidor: ${error}` };
    }
}

export function showToast(message, options = {}) {
    const text = String(message ?? "").trim();

    if (!text) {
        return null;
    }

    const container = ensureToastContainer();
    const current = container.querySelectorAll(".toast-item");

    if (current.length >= TOAST_LIMIT) {
        current[0].remove();
    }

    const type = options.type || "info";
    const duration = options.duration ?? 4200;
    const palette = {
        info: "#1f2937",
        success: "#16a34a",
        error: "#dc2626",
        warning: "#d97706",
    };

    const toast = document.createElement("div");
    toast.className = "toast-item";
    toast.style.background = palette[type] || palette.info;

    const content = document.createElement("div");
    content.className = "toast-message";
    content.textContent = text;

    const closeBtn = document.createElement("button");
    closeBtn.type = "button";
    closeBtn.className = "toast-close";
    closeBtn.setAttribute("aria-label", "Fechar notificação");
    closeBtn.textContent = "×";

    const dismiss = () => {
        toast.classList.add("hide");
        setTimeout(() => toast.remove(), 220);
    };

    closeBtn.addEventListener("click", dismiss);
    toast.appendChild(content);
    toast.appendChild(closeBtn);
    container.appendChild(toast);

    setTimeout(dismiss, duration);
    return toast;
}

export const show_toast = showToast;

if (typeof window !== "undefined") {
    window.showToast = showToast;
    window.show_toast = showToast;
}