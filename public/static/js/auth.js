// Importações
import { request, showToast } from "./lib/utils.js";

// Elementos
const wrapper = document.querySelector(".wrapper");
let form = undefined;

// Estado da página
let page_state = "login";
let previous_states = [];

// Constantes
const BASE_URL = window.__APP__?.BASE_URL;

// HTML dos estados
const login_state = `
        <div id="left">
            <form class="formulario">
                <h1>Faça login</h1>

                <input type="email" name="email" placeholder="Email @escola" required>
                <input type="password" name="password" placeholder="Senha" required>

                <button type="submit" id="button">Entrar</button>

                <p class="form_link">Não tem uma conta? <a href="#" class="switch_link" data-target="signup">Cadastre-se</a></p>
            </form>
        </div>

        <div id="right">
            <h1>Não tem uma conta?</h1>
            <p>Cadastre-se para ter acesso a todas as notícias e novidades.</p>
            <button type="button" class="main_btn" data-target="signup">Cadastre-se</button>
        </div>    
`;

const signup_state = `
        <div id="left">
            <form class="formulario">
                <h1>Cadastre-se</h1>

                <input type="text" name="username" placeholder="Nome completo" required>
                <input type="email" name="email" placeholder="Email @escola" required>
                <input type="password" name="password" placeholder="Senha" required>

                <button type="submit" id="button">Cadastrar</button>

                <p class="form_link">Já tem uma conta? <a href="#" class="switch_link" data-target="login">Faça login</a></p>
            </form>
        </div>

        <div id="right">
            <h1>Já tem uma conta?</h1>
            <p>Entre para continuar acessando sua conta e acompanhar tudo de perto.</p>
            <button type="button" class="main_btn" data-target="login">Faça login</button>
        </div>    
`;

function authenticate(event) {
    event.preventDefault();
    
    switch (page_state) {
        case "login":
            login();
            break;
        case "signup":
            signup();
            break;
    }
}

async function login() {
    const formData = new FormData(form);
    const email = formData.get("email");
    const password = formData.get("password");

    const response = await request(`${BASE_URL}/login`, "POST", {
        body: JSON.stringify({ 
            email: email, 
            password: password
        })
    });

    console.log(response);

    if (response.success) {
        showToast("Login realizado com sucesso!", { type: "success", duration: 1900 });
        goto_index();
        return;
    }

    const message = response.message || response.detail || "Erro ao fazer login.";
    showToast(message, { type: "error", duration: 1900 });
}

async function signup() {
    const formData = new FormData(form);
    const username = formData.get("username");
    const email = formData.get("email");
    const password = formData.get("password");

    const response = await request(`${BASE_URL}/sign-up`, "POST", {
        body: JSON.stringify({ username, email, password })
    });

    if (response.success) {
        showToast("Cadastro realizado com sucesso!", { type: "success", duration: 1900 });
        goto_index();
        return;
    }

    const message = response.message || response.detail || "Erro ao cadastrar.";
    showToast(message, { type: "error", duration: 1900 });
}

function goto_index() {
    window.location.href = `${BASE_URL}/`;
}

function bindForm() {
    form = document.querySelector(".formulario");

    if (!form) {
        return;
    }

    form.addEventListener("submit", authenticate);
}

function bindStateButtons() {
    const stateButtons = document.querySelectorAll(".main_btn, .switch_link");

    if (!stateButtons.length) {
        return;
    }

    stateButtons.forEach((stateButton) => {
        stateButton.addEventListener("click", (event) => {
            event.preventDefault();

            const target = stateButton.dataset.target;

            if (target === "signup") {
                changeState("signup");
                return;
            }

            if (target === "login") {
                changeState("login");
            }
        });
    });
}

function changeState(new_state) {
    if (wrapper.classList.contains("animate-out")) {
        return;
    }

    if (new_state !== page_state) {
        previous_states.push(page_state);
    }

    wrapper.classList.remove("animate-in");
    wrapper.classList.add("animate-out");

    setTimeout(() => {
        page_state = new_state;
        render();

        requestAnimationFrame(() => {
            wrapper.classList.remove("animate-out");
            wrapper.classList.add("animate-in");

            setTimeout(() => {
                wrapper.classList.remove("animate-in");
            }, 250);
        });
    }, 180);
}

function previous() {
    if (previous_states.length === 0) {
        return;
    }

    const last_state = previous_states.pop();
    changeState(last_state);
}

function render() {
    switch (page_state) {
        case "login":
            wrapper.innerHTML = login_state;
            break;
        case "signup":
            wrapper.innerHTML = signup_state;
            break;
    }

    bindForm();
    bindStateButtons();
}

changeState(page_state);