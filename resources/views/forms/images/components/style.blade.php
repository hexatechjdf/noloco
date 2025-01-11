<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
        margin: 30px;
    }

    .container-fluid {
        border: 1px solid #ddd;
        background-color: #fff;
        border-radius: 10px;
        overflow-y: auto;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        padding: 20px;
        /* height: 90vh; */
    }


    .featured-label {
        position: absolute;
        top: 6px;
        left: 6px;
        background: #18bd5b;
        padding: 4px 5px;
        font-size: 14px;
        font-weight: 600;
        color: #fff;
    }

    #submitImagesButton,
    .saveChanging {
        background-color: #18bd5b;
        border: none;
        font-weight: 600;
        transition: background-color 0.3s;
    }

    #submitImagesButton:hover,
    .saveChanging:hover {
        background-color: #45a049;

    }

    .image-box .card {
        transition: all 0.3s ease-in-out;
    }

    .image-box .card img {
        transition: all 0.3s ease-in-out;
        cursor: move;
    }



    .image-box .card:hover img {
        transform: scale(1.1);
    }

    .feature-checkbox-label {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        color: #fff;
        padding: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        transition: all 0.3s ease-in-out;
        transform: translateY(100%);
    }

    .feature-checkbox-label input[type="checkbox"] {
        margin-right: 8px;
    }

    .card:hover .feature-checkbox-label {
        transform: translateY(0);
    }

    .feature-checkbox {
        margin-right: 5px;
    }

    .featured {
        border-color: #4caf50;
    }

    .image-preview {
        padding: 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        background-color: #f9f9f9;
        border-radius: 4px;
        overflow-y: auto;
        min-height: 300px;
        margin: 20px 0;
    }

    .image-box {

        position: relative;
        overflow: hidden;
    }

    .image-box img {
        width: 100%;
        height: 255px;
    }

    .button-container button {
        padding: 4px;
        background-color: rgba(255, 255, 255, 0.8);
        border: none;
        color: #fff;
        border-radius: 4px;
        position: absolute;
        top: 6px;
        right: 6px;
    }

    .add-title-button {
        background-color: #18bd5b;
        color: #fff;
        border: none;
        padding: 10px;
        width: 100%;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .add-title-button:hover {
        background-color: #45a049;
    }

    button.btn.btn-dark.button-element {
        display: none;
    }
</style>
