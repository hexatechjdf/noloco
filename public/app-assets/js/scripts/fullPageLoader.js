import { axiosInstance } from './helper.js';

// window.onload = function () {


    const loaderOverlay = document.getElementById('dotLoaderOverlay');

    function showLoader() {
        loaderOverlay.style.display = 'flex';
        //loaderOverlay.fadeIn(); // Show the loader
    }

    function hideLoader() {
        setTimeout(() => {
            loaderOverlay.style.display = 'none';
        }, 300);
    }

    axiosInstance.interceptors.request.use(function (config) {
        if (!config.hasOwnProperty('hideLoader') || config.hideLoader === false) {
            showLoader();
        }
        return config;
    });

    axiosInstance.interceptors.response.use(function (response) {
        if (!response.config.hasOwnProperty('hideLoader') || response.config.hideLoader === false) {
            hideLoader();
        }
        return response;
    }, function (error) {
        if (!error.config || !error.config.hasOwnProperty('hideLoader') || error.config.hideLoader === false) {
            hideLoader();
        }
        return Promise.reject(error);
    });

    // };
