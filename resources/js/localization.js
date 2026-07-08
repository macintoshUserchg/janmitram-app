import { createI18n } from "vue-i18n";
import axios from "axios";

const i18n = createI18n({
    locale: localStorage.getItem("locale"),
    messages: {},
});

function getBasePath() {
    const meta = document.querySelector('meta[name="base-url"]');
    return meta ? new URL(meta.getAttribute('content')).pathname.replace(/\/?$/, '/') : '/';
}

function fetchLocalizationData() {
    const lang = localStorage.getItem("locale") || "en";
    axios
        .get(getBasePath() + "lang/" + lang)
        .then((response) => {
            i18n.global.setLocaleMessage(lang, response.data);
            i18n.global.locale = lang;
        })
        .catch((error) => {
            console.error("Failed to load language file", error);
        });
}

export default { i18n, fetchLocalizationData };
