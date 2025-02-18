<!DOCTYPE html>
<html>

    <head>
        <title>Login</title>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link
            href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap"
            rel="stylesheet">
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        } 

        .container {
            width: 100vw;
            height: 100vh;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-gap: 7rem;
            padding: 0 2rem;
        } 

        .login-content {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            text-align: center;
        } 

        form {
            width: 360px;
        } 

        .login-content h2 {
            margin: 15px 0;
            color: black;
            text-transform: uppercase;
            font-size: 1.7rem;
        }

        .login-content .input-div {
            position: relative;
            display: grid;
            grid-template-columns: 7% 93%;
            margin: 25px 0;
            padding: 5px 0;
            border-bottom: 2px solid black;
        }

        .login-content .input-div.one {
            margin-top: 0;
        }

        .i {
            color: black;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .i i {
            transition: .3s;
        }

        .input-div>div {
            position: relative;
            height: 45px;
        }

        .input-div>div>h5 {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: black;
            font-size: 16px;
            transition: .3s;
        }

        .input-div:before,
        .input-div:after {
            content: '';
            position: absolute;
            bottom: -2px;
            width: 0%;
            height: 2px;
            background-color: #D3D3D3;
            transition: .4s;
        }

        .input-div:before {
            right: 50%;
        }

        .input-div:after {
            left: 50%;
        }

        .input-div.focus:before,
        .input-div.focus:after {
            width: 50%;
        }

        .input-div.focus>div>h5 {
            top: -5px;
            font-size: 15px;
        }

        .input-div.focus>.i>i {
            color: black;
        }

        .input-div>div>input {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            border: none;
            outline: none;
            background: none;
            padding: 0.5rem 0.7rem;
            font-size: 1.2rem;
            color: #555;
            font-family: 'poppins', sans-serif;
        }

        .input-div.pass {
            margin-bottom: 4px;
        }

        a {
            display: block;
            text-align: right;
            text-decoration: none;
            color: #999;
            font-size: 0.9rem;
            transition: .3s;
        }

        a:hover {
            color: black;
        }

        .btn {
            display: block;
            width: 100%;
            height: 50px;
            border-radius: 25px;
            outline: none;
            border: none;
            /* background-image: linear-gradient(to right, #32be8f, #38d39f, #32be8f); */
            background-color: black;
            background-size: 200%;
            font-size: 1.2rem;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            text-transform: uppercase;
            margin: 1rem 0;
            cursor: pointer;
            transition: .5s;
        }

        .btn:hover {
            background-position: right;
        }

        .container {
            grid-template-columns: 1fr;
        } 
        

        .login-content {
            justify-content: center;
        }


        @media screen and (max-width: 1050px) {
            .container {
                grid-gap: 5rem;
            }
        }

        @media screen and (max-width: 1000px) {
            form {
                width: 290px;
            }

            .login-content h2 {
                font-size: 2.4rem;
                margin: 8px 0;
            } 
        }

        @media screen and (max-width: 900px) {
            .container {
                grid-template-columns: 1fr;
            } 
            

            .login-content {
                justify-content: center;
            }
        }

        .text-danger {
            color: red;
            font-size: 0.9rem;
        }


        /* Animation css */

        #preloader {
            position: fixed;
            width: 100%;
            height: 100%;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        #logo svg {
            width: 200px;
            height: 200px;
            animation: fadeInScale 1s ease-in-out forwards;
        }

        @keyframes fadeInScale {
            0% {
                opacity: 0;
                transform: scale(0.5);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .fade-out {
            animation: fadeOut 0.8s ease-out forwards;
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
                visibility: hidden;
            }
        }
    </style>
    </head>

    <body>

        <!-- Preloader -->
        <div id="preloader">
            <div id="logo">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_27_397)">
                        <path
                            d="M48.0959 37.8242C48.0959 41.2461 48.0959 44.668 48.0959 48.0931C32.1078 48.0931 16.1196 48.0931 0.0959473 48.0931C0.0959473 44.7022 0.0959473 41.308 0.140269 37.8682C0.540792 37.8179 0.896992 37.8132 1.29751 37.8142C3.26006 37.8134 5.1783 37.8069 7.12789 37.7934C7.27247 37.6154 7.39548 37.4499 7.49745 37.2724C8.77745 35.0439 10.0506 32.8114 11.3332 30.5844C12.8699 27.916 14.4133 25.2513 15.9556 22.586C16.4739 21.6902 16.9979 20.7977 17.5404 19.8674C17.623 19.9922 17.6788 20.0675 17.7252 20.1482C18.5 21.4963 19.2717 22.8462 20.0478 24.1936C21.6934 27.0508 23.3405 29.9071 24.9884 32.7631C25.8766 34.3025 26.783 35.8318 27.6464 37.385C27.863 37.7747 28.1131 37.8998 28.5614 37.811C30.3643 37.8143 32.1229 37.8113 33.9255 37.8146C34.3106 37.821 34.6518 37.821 35.0637 37.821C29.2085 27.6616 23.4029 17.5886 17.5661 7.46115C17.46 7.61098 17.3818 7.70401 17.3226 7.8079C16.6627 8.96589 16.0136 10.1301 15.3463 11.2838C13.981 13.6447 12.6017 15.9974 11.2368 18.3585C9.40944 21.5197 7.58808 24.6843 5.76834 27.8499C4.00539 30.9166 2.2496 33.9875 0.482329 37.0517C0.394136 37.2046 0.22656 37.3117 0.0959473 37.4402C0.0959473 24.9964 0.0959473 12.5527 0.0959473 0.102539C16.0863 0.102539 32.0766 0.102539 48.0959 0.102539C48.0959 12.5143 48.0959 24.9324 48.0703 37.4265C47.5953 36.7434 47.1424 35.9864 46.6974 35.2248C45.6634 33.455 44.6277 31.6861 43.6033 29.9107C42.7103 28.3632 41.8374 26.8042 40.9458 25.2559C39.8074 23.2791 38.6552 21.3104 37.5164 19.3339C36.4857 17.5449 35.4699 15.7472 34.4385 13.9585C33.368 12.1022 32.2866 10.2522 31.2101 8.39943C31.0289 8.08761 30.8483 7.77547 30.6334 7.4047C29.5561 9.28381 28.5337 11.0781 27.5004 12.8661C26.739 14.1837 25.9728 15.4987 25.1884 16.8025C24.9418 17.2123 24.9469 17.5596 25.2014 17.9642C25.6549 18.6851 26.0687 19.4312 26.4969 20.1679C27.1444 21.2818 27.7901 22.3967 28.4829 23.5911C29.2254 22.3165 29.9342 21.0996 30.6535 19.8649C30.9014 20.3099 31.127 20.7286 31.3653 21.14C32.2677 22.6977 33.1785 24.2506 34.0778 25.8101C34.7443 26.9661 35.3925 28.1325 36.0584 29.2888C36.8649 30.6891 37.6851 32.0813 38.4923 33.4811C39.2528 34.7999 40.0116 36.1198 40.7539 37.4489C40.924 37.7535 41.1326 37.8364 41.4733 37.8337C43.6808 37.8162 45.8884 37.8244 48.0959 37.8242Z"
                            fill="#ffff" />
                        <path
                            d="M0.0959473 37.4784C0.22656 37.3115 0.394136 37.2044 0.482329 37.0515C2.2496 33.9872 4.00539 30.9164 5.76834 27.8496C7.58808 24.6841 9.40944 21.5194 11.2368 18.3583C12.6017 15.9972 13.981 13.6444 15.3463 11.2836C16.0136 10.1299 16.6627 8.96567 17.3226 7.80768C17.3818 7.70379 17.46 7.61076 17.5661 7.46094C23.4029 17.5883 29.2085 27.6614 35.0637 37.8208C34.6518 37.8208 34.3106 37.8208 33.8927 37.7934C32.0927 37.7585 30.3695 37.7506 28.6463 37.7461C28.6033 37.746 28.5602 37.784 28.5171 37.8043C28.1131 37.8996 27.863 37.7745 27.6464 37.3848C26.783 35.8316 25.8766 34.3023 24.9884 32.7628C23.3405 29.9069 21.6934 27.0506 20.0478 24.1934C19.2717 22.846 18.5 21.4961 17.7252 20.148C17.6788 20.0672 17.623 19.9919 17.5404 19.8671C16.9979 20.7974 16.4739 21.69 15.9556 22.5858C14.4133 25.251 12.8699 27.9157 11.3332 30.5842C10.0506 32.8112 8.77745 35.0437 7.49745 37.2722C7.39548 37.4497 7.27247 37.6151 7.09429 37.7737C5.16222 37.7529 3.2951 37.744 1.42797 37.7395C1.36977 37.7394 1.31146 37.7844 1.25319 37.8084C0.896992 37.813 0.540792 37.8177 0.140269 37.8232C0.0959473 37.7216 0.0959473 37.6192 0.0959473 37.4784Z"
                            fill="#008CFF" />
                        <path
                            d="M48.0961 37.7854C45.8886 37.824 43.6809 37.8158 41.4735 37.8333C41.1328 37.836 40.9242 37.7531 40.7541 37.4485C40.0118 36.1194 39.253 34.7995 38.4925 33.4807C37.6853 32.0809 36.865 30.6887 36.0586 29.2884C35.3927 28.1321 34.7445 26.9656 34.0779 25.8097C33.1787 24.2502 32.2679 22.6973 31.3655 21.1396C31.1272 20.7282 30.9016 20.3094 30.6536 19.8645C29.9344 21.0992 29.2255 22.3161 28.483 23.5907C27.7903 22.3963 27.1446 21.2814 26.4971 20.1675C26.0688 19.4308 25.6551 18.6847 25.2015 17.9638C24.947 17.5592 24.942 17.2119 25.1885 16.8021C25.973 15.4983 26.7391 14.1832 27.5005 12.8657C28.5339 11.0777 29.5563 9.28341 30.6335 7.4043C30.8485 7.77506 31.0291 8.0872 31.2103 8.39903C32.2868 10.2518 33.3682 12.1018 34.4387 13.9581C35.4701 15.7468 36.4858 17.5445 37.5166 19.3335C38.6553 21.31 39.8076 23.2787 40.9459 25.2555C41.8375 26.8038 42.7105 28.3628 43.6034 29.9103C44.6279 31.6857 45.6636 33.4546 46.6976 35.2244C47.1425 35.986 47.5955 36.743 48.0705 37.471C48.0961 37.5422 48.0961 37.6446 48.0961 37.7854Z"
                            fill="#B9CCDD" />
                        <path
                            d="M1.29761 37.8142C1.31155 37.7846 1.36987 37.7396 1.42807 37.7397C3.2952 37.7443 5.16232 37.7531 7.06304 37.7809C5.1784 37.8069 3.26016 37.8134 1.29761 37.8142Z"
                            fill="#008CFF" />
                        <path
                            d="M28.5613 37.8108C28.5601 37.7841 28.6033 37.746 28.6463 37.7461C30.3695 37.7506 32.0926 37.7586 33.8487 37.7871C32.1229 37.8111 30.3642 37.8141 28.5613 37.8108Z"
                            fill="#008CFF" />
                    </g>
                    <defs>
                        <clippath id="clip0_27_397">
                            <rect width="48" height="48" fill="white" />
                        </clippath>
                    </defs>
                </svg>
            </div>
        </div>

        <div id="main-content" style="display: none;">
            <div class="container">
                <div class="login-content">
                    <form method="POST" action="{{ route('login') }}" class="m-2"> 
                        <svg width="100" height="100" viewBox="0 0 48 48" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_27_397)">
                                <path
                                    d="M48.0959 37.8242C48.0959 41.2461 48.0959 44.668 48.0959 48.0931C32.1078 48.0931 16.1196 48.0931 0.0959473 48.0931C0.0959473 44.7022 0.0959473 41.308 0.140269 37.8682C0.540792 37.8179 0.896992 37.8132 1.29751 37.8142C3.26006 37.8134 5.1783 37.8069 7.12789 37.7934C7.27247 37.6154 7.39548 37.4499 7.49745 37.2724C8.77745 35.0439 10.0506 32.8114 11.3332 30.5844C12.8699 27.916 14.4133 25.2513 15.9556 22.586C16.4739 21.6902 16.9979 20.7977 17.5404 19.8674C17.623 19.9922 17.6788 20.0675 17.7252 20.1482C18.5 21.4963 19.2717 22.8462 20.0478 24.1936C21.6934 27.0508 23.3405 29.9071 24.9884 32.7631C25.8766 34.3025 26.783 35.8318 27.6464 37.385C27.863 37.7747 28.1131 37.8998 28.5614 37.811C30.3643 37.8143 32.1229 37.8113 33.9255 37.8146C34.3106 37.821 34.6518 37.821 35.0637 37.821C29.2085 27.6616 23.4029 17.5886 17.5661 7.46115C17.46 7.61098 17.3818 7.70401 17.3226 7.8079C16.6627 8.96589 16.0136 10.1301 15.3463 11.2838C13.981 13.6447 12.6017 15.9974 11.2368 18.3585C9.40944 21.5197 7.58808 24.6843 5.76834 27.8499C4.00539 30.9166 2.2496 33.9875 0.482329 37.0517C0.394136 37.2046 0.22656 37.3117 0.0959473 37.4402C0.0959473 24.9964 0.0959473 12.5527 0.0959473 0.102539C16.0863 0.102539 32.0766 0.102539 48.0959 0.102539C48.0959 12.5143 48.0959 24.9324 48.0703 37.4265C47.5953 36.7434 47.1424 35.9864 46.6974 35.2248C45.6634 33.455 44.6277 31.6861 43.6033 29.9107C42.7103 28.3632 41.8374 26.8042 40.9458 25.2559C39.8074 23.2791 38.6552 21.3104 37.5164 19.3339C36.4857 17.5449 35.4699 15.7472 34.4385 13.9585C33.368 12.1022 32.2866 10.2522 31.2101 8.39943C31.0289 8.08761 30.8483 7.77547 30.6334 7.4047C29.5561 9.28381 28.5337 11.0781 27.5004 12.8661C26.739 14.1837 25.9728 15.4987 25.1884 16.8025C24.9418 17.2123 24.9469 17.5596 25.2014 17.9642C25.6549 18.6851 26.0687 19.4312 26.4969 20.1679C27.1444 21.2818 27.7901 22.3967 28.4829 23.5911C29.2254 22.3165 29.9342 21.0996 30.6535 19.8649C30.9014 20.3099 31.127 20.7286 31.3653 21.14C32.2677 22.6977 33.1785 24.2506 34.0778 25.8101C34.7443 26.9661 35.3925 28.1325 36.0584 29.2888C36.8649 30.6891 37.6851 32.0813 38.4923 33.4811C39.2528 34.7999 40.0116 36.1198 40.7539 37.4489C40.924 37.7535 41.1326 37.8364 41.4733 37.8337C43.6808 37.8162 45.8884 37.8244 48.0959 37.8242Z"
                                    fill="#ffff" />
                                <path
                                    d="M0.0959473 37.4784C0.22656 37.3115 0.394136 37.2044 0.482329 37.0515C2.2496 33.9872 4.00539 30.9164 5.76834 27.8496C7.58808 24.6841 9.40944 21.5194 11.2368 18.3583C12.6017 15.9972 13.981 13.6444 15.3463 11.2836C16.0136 10.1299 16.6627 8.96567 17.3226 7.80768C17.3818 7.70379 17.46 7.61076 17.5661 7.46094C23.4029 17.5883 29.2085 27.6614 35.0637 37.8208C34.6518 37.8208 34.3106 37.8208 33.8927 37.7934C32.0927 37.7585 30.3695 37.7506 28.6463 37.7461C28.6033 37.746 28.5602 37.784 28.5171 37.8043C28.1131 37.8996 27.863 37.7745 27.6464 37.3848C26.783 35.8316 25.8766 34.3023 24.9884 32.7628C23.3405 29.9069 21.6934 27.0506 20.0478 24.1934C19.2717 22.846 18.5 21.4961 17.7252 20.148C17.6788 20.0672 17.623 19.9919 17.5404 19.8671C16.9979 20.7974 16.4739 21.69 15.9556 22.5858C14.4133 25.251 12.8699 27.9157 11.3332 30.5842C10.0506 32.8112 8.77745 35.0437 7.49745 37.2722C7.39548 37.4497 7.27247 37.6151 7.09429 37.7737C5.16222 37.7529 3.2951 37.744 1.42797 37.7395C1.36977 37.7394 1.31146 37.7844 1.25319 37.8084C0.896992 37.813 0.540792 37.8177 0.140269 37.8232C0.0959473 37.7216 0.0959473 37.6192 0.0959473 37.4784Z"
                                    fill="#008CFF" />
                                <path
                                    d="M48.0961 37.7854C45.8886 37.824 43.6809 37.8158 41.4735 37.8333C41.1328 37.836 40.9242 37.7531 40.7541 37.4485C40.0118 36.1194 39.253 34.7995 38.4925 33.4807C37.6853 32.0809 36.865 30.6887 36.0586 29.2884C35.3927 28.1321 34.7445 26.9656 34.0779 25.8097C33.1787 24.2502 32.2679 22.6973 31.3655 21.1396C31.1272 20.7282 30.9016 20.3094 30.6536 19.8645C29.9344 21.0992 29.2255 22.3161 28.483 23.5907C27.7903 22.3963 27.1446 21.2814 26.4971 20.1675C26.0688 19.4308 25.6551 18.6847 25.2015 17.9638C24.947 17.5592 24.942 17.2119 25.1885 16.8021C25.973 15.4983 26.7391 14.1832 27.5005 12.8657C28.5339 11.0777 29.5563 9.28341 30.6335 7.4043C30.8485 7.77506 31.0291 8.0872 31.2103 8.39903C32.2868 10.2518 33.3682 12.1018 34.4387 13.9581C35.4701 15.7468 36.4858 17.5445 37.5166 19.3335C38.6553 21.31 39.8076 23.2787 40.9459 25.2555C41.8375 26.8038 42.7105 28.3628 43.6034 29.9103C44.6279 31.6857 45.6636 33.4546 46.6976 35.2244C47.1425 35.986 47.5955 36.743 48.0705 37.471C48.0961 37.5422 48.0961 37.6446 48.0961 37.7854Z"
                                    fill="#B9CCDD" />
                                <path
                                    d="M1.29761 37.8142C1.31155 37.7846 1.36987 37.7396 1.42807 37.7397C3.2952 37.7443 5.16232 37.7531 7.06304 37.7809C5.1784 37.8069 3.26016 37.8134 1.29761 37.8142Z"
                                    fill="#008CFF" />
                                <path
                                    d="M28.5613 37.8108C28.5601 37.7841 28.6033 37.746 28.6463 37.7461C30.3695 37.7506 32.0926 37.7586 33.8487 37.7871C32.1229 37.8111 30.3642 37.8141 28.5613 37.8108Z"
                                    fill="#008CFF" />
                            </g>
                            <defs>
                                <clippath id="clip0_27_397">
                                    <rect width="48" height="48" fill="white" />
                                </clippath>
                            </defs>
                        </svg>
                        <h2 class="title">Welcome</h2>
                        <div class="input-div one">
                            <div class="i">
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="div">
                                <h5>Email</h5>
                                <input id="email" class="input block mt-1 w-full" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                            </div>
                        </div>
                        @error('email')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <div class="input-div pass">
                            <div class="i">
                                <i class="fa fa-lock"></i>
                            </div>
                            <div class="div">
                                <h5>Password</h5>
                                <input id="password" class="input block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />                                
                            </div>
                        </div>
                        @error('password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        
                        <a href="{{ route('password.request') }}">Forgot Password?</a>
                        <input type="submit" class="btn" value="Login">
                    </form>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function () {
                const inputs = document.querySelectorAll(".input");

                function addFocus() {
                    let parent = this.parentNode.parentNode;
                    parent.classList.add("focus");

                    // Remove error message when input is focused
                    const errorDiv = parent.nextElementSibling;
                    if (errorDiv && errorDiv.classList.contains("text-danger")) {
                        errorDiv.remove();
                    }
                }

                function removeFocus() {
                    let parent = this.parentNode.parentNode;
                    if (this.value.trim() === "") {
                        parent.classList.remove("focus");
                    }
                }

                // Keep focus on inputs with error values
                inputs.forEach(input => {
                    if (input.value.trim() !== "") {
                        input.parentNode.parentNode.classList.add("focus");
                    }
                    input.addEventListener("focus", addFocus);
                    input.addEventListener("blur", removeFocus);
                });
            });
        </script>


        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const preloader = document.getElementById("preloader");
                const mainContent = document.getElementById("main-content");
                const errorMessages = document.querySelectorAll(".text-danger");

                // Check if there are any error messages
                if (errorMessages.length > 0) {
                    // If errors exist, skip preloader
                    preloader.style.display = "none";
                    mainContent.style.display = "block";
                } else {
                    // Otherwise, show preloader with fade effect
                    setTimeout(function () {
                        preloader.classList.add("fade-out");
                        setTimeout(function () {
                            preloader.style.display = "none";
                            mainContent.style.display = "block";
                        }, 800);
                    }, 1300);
                }
            });
        </script>


        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Select input fields
                const emailInput = document.getElementById("email");
                const passwordInput = document.getElementById("password");

                // Function to remove error messages when input is focused
                function removeError(input) {
                    input.addEventListener("focus", function () {
                        const errorDiv = this.parentElement.parentElement.nextElementSibling;
                        if (errorDiv && errorDiv.classList.contains("text-danger")) {
                            errorDiv.remove();
                        }
                    });
                }

                removeError(emailInput);
                removeError(passwordInput);
            });
        </script>

    </body>

</html>