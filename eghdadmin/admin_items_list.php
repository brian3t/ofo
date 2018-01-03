<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_items_list.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/shopping_cart.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("products_categories");

	$permissions = get_permissions();
	$products_settings = get_setting_value($permissions, "products_settings", 0);
	$product_types = get_setting_value($permissions, "product_types", 0);
	$manufacturers = get_setting_value($permissions, "manufacturers", 0);
	$products_reviews = get_setting_value($permissions, "products_reviews", 0);
	$shipping_methods = get_setting_value($permissions, "shipping_methods", 0);
	$shipping_times = get_setting_value($permissions, "shipping_times", 0);
	$shipping_rules = get_setting_value($permissions, "shipping_rules", 0);
	$downloadable_products = get_setting_value($permissions, "downloadable_products", 0);
	$coupons = get_setting_value($permissions, "coupons", 0);
	$advanced_search = get_setting_value($permissions, "advanced_search", 0);
	$products_report = get_setting_value($permissions, "products_report", 0);
	$product_prices = get_setting_value($permissions, "product_prices", 0);
	$product_images = get_setting_value($permissions, "product_images", 0);
	$product_properties = get_setting_value($permissions, "product_properties", 0);
	$product_features = get_setting_value($permissions, "product_features", 0);
	$product_related = get_setting_value($permissions, "product_related", 0);
	$product_categories = get_setting_value($permissions, "product_categories", 0);
	$product_accessories = get_setting_value($permissions, "product_accessories", 0);
	$product_releases = get_setting_value($permissions, "product_releases", 0);
	$products_order = get_setting_value($permissions, "products_order", 0);
	$products_export = get_setting_value($permissions, "products_export", 0);
	$products_import = get_setting_value($permissions, "products_import", 0);
	$products_export_google_base = get_setting_value($permissions, "products_export_google_base", 0);
	$features_groups = get_setting_value($permissions, "features_groups", 0);
	$tell_friend = get_setting_value($permissions, "tell_friend", 0);
	$categories_export = get_setting_value($permissions, "categories_export", 0);
	$categories_import = get_setting_value($permissions, "categories_import", 0);
	$categories_order = get_setting_value($permissions, "categories_order", 0);
	$view_categories = get_setting_value($permissions, "view_categories", 0);
	$view_products = get_setting_value($permissions, "view_products", 0);
	$add_categories = get_setting_value($permissions, "add_categories", 0);
	$update_categories = get_setting_value($permissions, "update_categories", 0);
	$remove_categories = get_setting_value($permissions, "remove_categories", 0);
	$add_products = get_setting_value($permissions, "add_products", 0);
	$update_products = get_setting_value($permissions, "update_products", 0);
	$remove_products = get_setting_value($permissions, "remove_products", 0);
	$approve_products = get_setting_value($permissions, "approve_products", 0);
	$view_only_products = !$update_products && $view_products;
	$read_only_products = !$update_products && !$view_products;
	$view_only_categories = !$update_categories && !$remove_categories && $view_categories;
	$read_only_categories = !$update_categories && !$remove_categories && !$view_categories;
	$remove_checkbox_column = !$update_products && !$remove_products && !$approve_products;
	$empty_select_block = !$add_products && !$update_products && !$products_order;
	$empty_export_block = !$products_export && !$products_import && !$products_export_google_base;
	$empty_export_approve_block = $empty_export_block && !$approve_products;
	$empty_first_category_block = !$add_categories && !$categories_order;
	$empty_second_category_block = !$categories_export && !$categories_import;

	$rp = new VA_URL("admin_items_list.php", false);
	$rp->add_parameter("category_id", REQUEST, "category_id");
	$rp->add_parameter("sc", GET, "sc");
	$rp->add_parameter("sl", GET, "sl");
	$rp->add_parameter("sa", GET, "sa");
	$rp->add_parameter("ss", GET, "ss");
	$rp->add_parameter("ap", GET, "ap");
	$rp->add_parameter("s", GET, "s");
	if ($sitelist) {
		$rp->add_parameter("param_site_id", GET, "param_site_id");		
	}	
	
	$operation = get_param("operation");
	$items_ids = get_param("items_ids");
//brian3t here delete all items that have no AAIA app linked to it
//$items_ids = '10460,10482,3731,2100,2101,2102,2103,2104,2105,2106,2107,2108,2109,2110,2111,2112,2113,2114,2115,2116,3732,2117,2118,2119,2120,2121,2122,2123,2124,2125,2126,2127,2128,2129,2130,2131,2132,2133,2134,2135,2136,2137,2138,2139,2140,2141,2142,2143,2144,2145,2146,3733,3734,2147,2148,2149,2150,2151,2152,2153,2154,2155,2156,2157,2158,2159,2160,2161,2162,2163,2164,2165,2166,2167,2168,2169,2170,2171,2172,2173,2174,2175,2176,2177,2178,2179,2180,2181,2182,2183,2184,2185,2186,2187,2188,2189,2190,2191,2192,2193,2194,2195,2196,2197,2198,2199,2200,2201,2202,2203,2204,2205,2206,2207,2208,2209,2210,2211,2212,2213,2214,2215,2216,2217,2218,2219,2220,2221,2222,2223,2224,2225,2226,2227,2228,2229,2230,2231,2232,2233,2234,2235,2236,2237,2238,2239,2240,2241,2242,2243,2244,2245,2246,2247,2248,2249,2250,2251,2252,2253,2254,2255,2256,2257,2258,2259,2260,2261,2262,2263,2264,2265,2266,2267,2268,2269,2270,2271,2272,2273,2274,2275,2276,2277,2278,2279,2280,2281,2282,2283,2284,2285,2286,2287,2288,2289,2290,2291,2292,2293,2294,2295,2296,2297,2298,2299,2300,2301,2302,2303,2304,2305,2306,2307,2308,2309,2310,2311,2312,2313,2314,2315,2316,2317,2318,2319,2320,2321,2322,2323,2324,2325,2326,2327,2328,2329,2330,2331,2332,2333,2334,2335,2336,2337,2338,2339,2340,2341,2342,2343,2344,2345,2346,2347,2348,2349,2350,2351,2352,2353,2354,2355,2356,2357,2358,2359,2360,2361,2362,2363,2364,2365,2366,2367,2368,2369,2370,2371,2372,2373,2374,2375,2376,2377,2378,2379,2380,2381,2382,2383,2384,2385,2386,2387,2388,2389,2390,2391,2392,2393,2394,2395,2396,2397,2398,2399,2400,2401,2402,2403,2404,2405,2406,2407,2408,2409,2410,2411,2412,2413,2414,2415,2416,2417,2418,2419,2420,2421,2422,2423,2424,2425,2426,2427,2428,2429,2430,2431,2432,2433,2434,2435,2436,2437,2438,2439,2440,2441,2442,2443,2444,2445,2446,2447,2448,2449,2450,2451,2452,2453,2454,2455,2456,2457,2458,2459,2460,2461,2462,2463,2464,2465,2466,2467,2468,2469,2470,2471,2472,2473,2474,2475,2476,2477,2478,2479,2480,2481,2482,2483,2484,2485,2486,2487,2488,2489,2490,2491,2492,2493,2494,2495,2496,2497,2498,2499,2500,2501,2502,2503,2504,2505,2506,2507,2508,2509,2510,2511,2512,2513,2514,2515,2516,2517,2518,2519,2520,2521,2522,2523,2524,2525,2526,2527,2528,2529,2530,2531,2532,2533,2534,2535,2536,2537,2538,2539,2540,2541,2542,2543,2544,2545,2546,2547,2548,2549,2550,2551,2552,2553,2554,2555,2556,2557,2558,2559,2560,2561,2562,2563,2564,2565,2566,2567,2568,2569,2570,2571,2572,2573,2574,2575,2576,2577,2578,2579,2580,2581,2582,2583,2584,2585,2586,2587,2588,2589,2590,2591,2592,2593,2594,2595,2596,2597,2598,2599,2600,2601,2602,2603,2604,2605,2606,2607,2608,2609,2610,2611,2612,2613,2614,2615,2616,2617,2618,2619,2620,2621,2622,2623,2624,2625,2626,2627,2628,2629,2630,2631,2632,2633,2634,2635,2636,2637,2638,2639,2640,2641,2642,2643,2644,2645,2646,2647,2648,2649,2650,2651,2652,2653,2654,2655,2656,2657,2658,2659,2660,2661,2662,2663,2664,2665,2666,2667,2668,2669,2670,2671,2672,2673,2674,2675,2676,2677,2678,2679,2680,2681,2682,2683,2684,2685,2686,2687,2688,2689,2690,2691,2692,2693,2694,2695,2696,2697,2698,2699,2700,2701,2702,2703,2704,2705,2706,2707,2708,2709,2710,2711,2712,2713,2714,2715,2716,2717,2718,2719,2720,2721,2722,2723,2724,2725,2726,2727,2728,2729,2730,2731,2732,2733,2734,2735,2736,2737,2738,2739,2740,2741,11233,11172,2742,2743,2744,2745,2746,2747,2748,2749,2750,2751,2752,2753,2754,2755,2756,2757,2758,2759,2760,2761,2762,2763,2764,2765,2766,2767,2768,2769,2770,2771,2772,2773,2774,2775,2776,2777,2778,2779,2780,2781,2782,2783,2784,2785,2786,2787,2788,2789,2790,2791,2792,2793,2794,2795,2796,2797,2798,2799,2800,2801,2802,2803,2804,2805,2806,2807,2808,2809,2810,2811,2812,2813,2814,2815,2816,2817,2818,2819,2820,2821,2822,2823,2824,2825,2826,2827,2828,2829,2830,2831,2832,2833,2834,2835,2836,2837,2838,2839,2840,2841,2842,2843,2844,2845,2846,2847,2848,2849,2850,2851,2852,2853,2854,2855,2856,2857,2858,2859,2860,2861,2862,2863,2864,2865,2866,2867,2868,2869,2870,2871,2872,2873,2874,2875,2876,2877,2878,2879,2880,2881,2882,2883,2884,2885,2886,2887,2888,2889,2890,2891,2892,2893,2894,2895,2896,2897,2898,2899,2900,2901,2902,2903,2904,2905,2906,2907,2908,2909,2910,2911,2912,2913,2914,2915,2916,2917,2918,2919,2920,2921,2922,2923,2924,2925,2926,2927,2928,2929,2930,2931,2932,2933,2934,2935,2936,2937,2938,2939,2940,2941,2942,2943,2944,2945,2946,2947,2948,2949,2950,2951,2952,2953,2954,2955,2956,2957,2958,2959,2960,2961,2962,2963,2964,2965,2966,2967,2968,2969,2970,2971,2972,2973,2974,2975,2976,2977,2978,2979,2980,2981,2982,2983,2984,2985,2986,2987,2988,2989,2990,2991,2992,2993,2994,2995,2996,2997,2998,2999,3000,3001,3002,3003,3004,3005,3006,3007,3008,3009,3010,3011,3012,3013,3014,3015,3016,3017,3018,3019,3020,3021,3022,3023,3024,3025,3026,3027,3028,3029,3030,3031,3032,3033,3034,3035,3036,3037,3038,3039,3040,3041,3042,3043,3044,3045,3046,3047,3048,3049,3050,3051,3052,3053,3054,3055,3056,3057,3058,3059,3060,3061,3062,3063,3064,3065,3066,3067,3068,3069,3070,3071,3072,3073,3074,3075,3076,3077,3078,3079,3080,3081,3082,3083,3084,3085,3086,3087,3088,3089,3090,3091,3092,3093,3094,3095,3096,3097,3098,3099,3100,3101,3102,3103,3104,3105,3106,3107,3108,3109,3110,3111,3112,3113,3114,3115,3116,3117,3118,3119,3120,3121,3122,3123,3124,3125,3126,3127,3128,3129,3130,3131,3132,3133,3134,3135,3136,3137,3138,3139,3140,3141,3142,3143,3144,3145,3146,3147,3148,3149,3150,3151,3152,3153,3154,3155,3156,3157,3158,3159,3160,3161,3162,3163,3164,3165,3166,3167,3168,3169,3170,3171,3172,3173,3174,3175,3176,3177,3178,3179,3180,3181,3182,3183,3184,3185,3186,3187,3188,3189,3190,3191,3192,3193,3194,3195,3196,3197,3198,3199,3200,3201,3202,3203,3204,3205,3206,3207,3208,3209,3210,3211,3212,3213,3214,3215,3216,3217,3218,3219,3220,3221,3222,3223,3224,3225,3226,3227,3228,3229,3230,3231,3232,3233,3234,3235,3236,3237,3238,3239,3240,3241,3242,3243,3244,3245,3246,3247,3248,3249,3250,3251,3252,3253,3254,3255,3256,3257,3258,3259,3260,3261,3262,3263,3264,3265,3266,3267,3268,3269,3270,3271,3272,3273,3274,3275,3276,3277,3278,3279,3280,3281,3282,3283,3284,3285,3286,3287,3288,3289,3290,3291,3292,3293,3294,3295,3296,3297,3298,3299,3300,3301,3302,3303,3304,3305,3306,3307,3308,3309,3310,3311,3312,3313,3314,3315,3316,3317,3318,3735,3736,3737,3738,3739,3740,3741,3742,3743,3744,3745,3746,3747,3748,3319,3320,3321,3322,3323,3324,3325,3326,3327,3328,3329,3330,3331,3332,3333,3334,3335,3336,3337,3338,3339,3340,3341,3342,3343,3344,3345,3346,3347,3348,3349,3350,3351,3352,3353,3354,3355,3356,3357,3358,3359,3360,3361,3362,3363,3364,3365,3366,3367,3368,3369,3370,3371,3372,3373,3374,3375,3376,3377,3378,3379,3380,3381,3382,3383,3384,3385,3386,3387,3388,3389,3390,3391,3392,3393,3394,3395,3396,3397,3398,3399,3400,3401,3402,3403,3404,3405,3406,3407,3408,3409,3410,3411,3412,3413,3414,3415,3416,3417,3418,3419,3420,3421,3422,3423,3424,3425,3426,3427,3428,3429,3430,3431,3432,3433,11234,11173,3434,3435,3436,3437,3438,3439,3440,3441,3442,3443,3444,3445,3446,3447,3448,3449,3450,3451,3452,3453,3454,3455,3456,3457,3458,3459,3460,3461,3462,3463,3464,3465,3466,3467,3468,3469,3470,3471,3472,3473,3474,3475,3476,3477,3478,3479,3480,3481,3482,3483,3484,3485,3486,3487,3488,3489,3490,3491,3492,3493,3494,3495,3496,3497,3498,3499,3500,3501,3502,3503,3504,3505,3506,3507,3508,3509,3510,3511,3512,3513,3514,3515,3516,3517,3518,3519,3520,3521,3522,3523,3524,3525,3526,3527,3528,3529,3530,3531,3532,3533,3534,3535,3536,3537,3538,3539,3540,3541,3542,3543,3544,3545,3546,3547,3548,3549,3550,3551,3552,3553,3554,3555,3556,3557,3558,3559,3560,3561,3562,3563,3564,3565,3566,3567,3568,3569,3570,3571,3572,3573,3574,3575,3576,3577,3578,3579,3580,3581,3582,3583,3584,3585,3586,3587,3588,3589,3590,3591,3592,3593,3594,3595,3596,3597,3598,3599,11235,11174,3600,3601,3602,3603,3604,3605,3606,3607,3608,3609,3610,3611,3612,3613,3614,3615,3616,3617,3618,3619,3620,3621,3622,3623,3624,3625,3626,3627,3628,3629,3630,3631,3632,3633,3634,3635,3636,3637,3638,3639,3640,3641,3642,3643,3644,3645,3646,3647,3648,3649,3650,3651,3652,3653,3654,3655,3656,3657,3658,3659,3660,3661,3662,3663,3664,3665,3666,3667,3668,3669,3670,3671,3672,3673,3674,3675,3676,3677,3678,3679,3680,3681,3682,3683,3684,3685,3686,3687,3688,3689,3690,3691,3692,3693,3694,3695,3696,3697,3698,3699,3700,3701,3702,3703,3704,3705,3706,3707,3708,3709,3710,3711,3712,3713,3714,3715,3716,3717,3718,3719,3720,3721,3722,3723,3724,3725,3726,3727,3728,3729,3730,11236,11237,11175,11238,11239,11176,11240,11241,11177,11242,11243,11244,11178,11245,11246,11247,11179,11248,11249,11250,11251,11180,11252,11253,3800,4722,3801,3802,3803,3804,3805,3806,3807,3808,3809,3810,3811,3812,3813,3814,3815,3816,3817,3818,3819,3820,3821,3822,3823,3824,3825,3826,3827,3828,3829,3830,3831,3832,3833,3834,3835,3836,4723,3837,4724,3838,3839,3840,3841,3842,3843,3844,3845,3846,3847,3848,3849,3850,3851,3852,3853,4725,3854,3855,3856,3857,3858,3859,3860,3861,3862,3863,3864,3865,3866,4726,4727,3867,3868,3869,3870,3871,3872,3873,3874,3875,3876,3877,3878,3879,3880,3881,3882,3883,3884,3885,3886,3887,3888,3889,3890,3891,3892,3893,3894,3895,4728,3896,3897,3898,3899,3900,3901,3902,3903,3904,3905,3906,3907,3908,3909,3910,3911,3912,3913,3914,3915,3916,4729,3917,3918,3919,3920,3921,3922,3923,3924,3925,3926,3927,3928,3929,3930,3931,3932,3933,3934,3935,3936,3937,3938,3939,3940,3941,3942,3943,3944,3945,3946,3947,3948,3949,3950,3951,3952,3953,3954,3955,3956,3957,3958,3959,3960,3961,3962,3963,3964,3965,3966,4730,3967,3968,3969,3970,3971,3972,3973,3974,3975,3976,3977,3978,3979,3980,3981,3982,3983,3984,3985,3986,3987,3988,3989,3990,3991,3992,3993,3994,3995,3996,3997,3998,3999,4000,4001,4002,4003,4004,4005,4006,4007,4008,4009,4010,4011,4012,4013,4731,4014,4015,4016,4017,4018,4019,4020,4021,4022,4023,4024,4025,4026,4027,4028,4029,4030,4031,4032,4033,4034,4035,4036,4037,4038,4039,4040,4041,4042,4043,4044,4045,4046,4047,4048,4049,4050,4051,4052,4053,4054,4055,4056,4057,4058,4059,4060,4061,4062,4063,4064,4065,4066,4067,4068,4069,4070,4071,4072,4073,4074,4075,4076,4077,4078,4079,4080,4081,4082,4083,4084,4085,4086,4087,4088,4089,4090,4091,4092,4093,4094,4095,4096,4097,4098,4099,4100,4101,4102,4103,4104,4105,4106,4107,4108,4109,4110,4111,4112,4113,4114,4115,4116,4117,4118,4119,4120,4121,4122,4123,4124,4125,4126,4127,4128,4129,4130,4131,4132,4133,4134,4135,4136,4137,4138,4139,4140,4141,4142,4143,4732,4733,4144,4145,4146,4734,4735,4736,4737,4738,4147,4739,4148,4149,4150,4151,4152,4153,4154,4155,4156,4157,4158,4159,4160,4161,4162,4163,4164,4165,4166,4167,4168,4169,4170,4171,4172,4173,4174,4175,4176,4177,4178,4179,4180,4181,4182,4183,4184,4185,4186,4187,4188,4189,4190,4191,4192,4193,4194,4195,4196,4197,4198,4199,4200,4201,4202,4203,4204,4205,4206,4207,4208,4209,4210,4211,4212,4213,4214,4215,4216,4217,4218,4219,4220,4221,4222,4223,4224,4225,4226,4227,4228,4229,4230,4231,4232,4233,4234,4235,4236,4237,4238,4239,4240,4241,4242,4243,4244,4245,4246,4247,4248,4249,4250,4251,4252,4253,4254,4255,4256,4257,4258,4259,4260,4261,4262,4263,4264,4265,4266,4267,4268,4269,4270,4271,4272,4273,4274,4275,4276,4277,4278,4279,4280,4281,4282,4283,4284,4285,4286,4287,4288,4289,4290,4291,4292,4293,4294,4295,4296,4297,4298,4299,4300,4301,4302,4303,4304,4305,4306,4307,4308,4309,4310,4311,4312,4313,4314,4315,4316,4317,4318,4319,4320,4321,4322,4323,4324,4325,4326,4327,4328,4329,4330,4331,4332,4333,4334,4335,4336,4337,4338,4339,4340,4341,4342,4343,4344,4345,4346,4347,4348,4349,4350,4351,4352,4353,4354,4355,4356,4357,4358,4359,4360,4361,4362,4363,4364,4365,4366,4367,4368,4369,4370,4371,4372,4373,4374,4375,4376,4377,4378,4379,4380,4381,4382,4383,4384,4385,4386,4387,4388,4389,4390,4391,4392,4393,4394,4395,4396,4397,4398,4399,4400,4401,4402,4403,4404,4405,4406,4407,4408,4409,4410,4411,4412,4413,4414,4415,4416,4417,4418,4419,4420,4421,4422,4423,4424,4425,4426,4427,4428,4429,4430,4431,4432,4433,4434,4435,4436,4437,4438,4439,4440,4441,4442,4443,4444,4445,4446,4447,4448,4449,4450,4451,4452,4453,4454,4455,4456,4457,4458,4459,4460,4461,4462,4463,4464,4465,4466,4467,4740,4468,4469,4470,4471,4472,4473,4474,4475,4476,4477,4478,4479,4480,4481,4482,4483,4484,4485,4486,4487,4488,4489,4490,4491,4492,4493,4494,4495,4496,4497,4498,4499,4500,4501,4502,4503,4504,4505,4506,4507,4508,4509,4510,4511,4512,4513,4514,4515,4516,4517,4518,4519,4520,4521,4522,4523,4524,4525,4526,4527,4528,4529,4530,4531,4532,4533,4534,4535,4536,4537,4538,4539,4540,4541,4542,4543,4544,4545,4546,4547,4548,4549,4550,4551,4552,4553,4554,4555,4556,4557,4558,4559,4560,4561,4562,4563,4564,4565,4566,4567,4568,4569,4570,4571,4572,4573,4574,4575,4576,4577,4578,4579,4580,4581,4582,4583,4584,4585,4586,4587,4588,4589,4590,4591,4592,4593,4594,4595,4596,4597,4598,4599,4600,4601,4602,4603,4604,4605,4606,4607,4608,4609,4610,4611,4612,4613,4614,4615,4616,4617,4618,4619,4620,4621,4622,4623,4624,4625,4626,4627,4628,4629,4630,4631,4632,4633,4634,4635,4636,4637,4638,4639,4640,4641,4642,4643,4644,4645,4646,4647,4648,4649,4650,4651,4652,4653,4654,4655,4656,4657,4658,4659,4660,4661,4662,4663,4664,4665,4666,4667,4668,4669,4670,4671,4672,4673,4674,4675,4676,4677,4678,4679,4680,4681,4682,4683,4684,4685,4686,4687,4688,4689,4690,4691,4692,4693,4694,4695,4696,4697,4698,4699,4700,4701,4702,4703,4704,4705,4706,4707,4708,4709,4710,4711,4712,4713,4714,4715,4716,4717,4718,4719,4720,4721,11254,11181,11255,11256,11257,11182,11258,11259,11260,11183,11261,11220,11191,11170,11198,11217,11219,11223,11169,11200,11201,11168,11167,11166,11207,11206,11208,11229,11231,11225,11232,11215,11189,11165,11222,11164,11197,11224,11163,11162,11161,11160,11159,11158,11157,11156,11155,11154,11153,11218,11152,11151,11196,11199,11192,11216,11194,11187,11188,11186,11195,11202,11203,11150,11149,11148,11204,11205,11147,11193,11146,11171,11209,11221,11190,11145,11210,11211,11212,11144,11230,11213,11214,11227,11228,11143,11226,11278,11279,11280,11281,11282,11283,11284,11285,11286,11287,11288,11289,11290,11291,11292,11293,11294,11295,11296,11297,11298,11299,11300,11301,11302,11303,11304,11305,11306,11307,11308,11309,11310,11311,11312,11313,11314,5968,11184,11315,11316,11317,11318,11319,11320,11321,11322,11323,11324,11325,11326,11327,11264,11185,11328,11329,11330,11331,11332,11265,11266,11267,11268,11269,11270,11271,11272,11273,11274,11275,11276,11277,5962,9091,1450,1452,1453,1454,1455,1456,1457,1458,1459,1460,1461,1462,1463,1464,1465,1466,500,10002,10000,501,1467,1468,1469,1470,1471,1472,1473,1474,1475,1476,1477,1478,1479,1480,10003,1481,1482,1483,1484,1485,1486,10004,1487,1488,1489,1490,1491,10174,10352,982,10232,10353,10166,10233,10167,10168,10169,10170,10358,10329,10171,10359,215,216,217,218,219,318,307,220,306,314,221,10392,10393,10387,10354,10388,10355,10356,10396,320,10389,10328,222,223,224,327,11000,11002,11001,11003,11004,11005,11006,11007,11008,11009,11010,11011,11012,11013,11014,11015,11016,11017,11018,11019,11020,11021,11022,11023,11024,11025,11026,11028,11029,11030,11031,11033,11034,11035,11036,11037,11038,11039,11040,11041,11042,11043,11044,10357,10172,9088,225,10173,10480,2022,10397,2021,10400,10391,10402,10024,10025,10026,10021,10051,10052,10301,10053,10054,10055,10027,10056,10057,10058,10302,10059,10060,5979,10061,10062,10029,10030,10291,10031,10032,10033,10292,9085,10293,10294,10303,641,10304,10295,10305,10306,10063,10064,10065,10066,10136,10067,10034,557,10307,10035,10068,10290,10296,10036,10022,10321,10324,10037,10308,10309,10038,10069,10070,10039,10071,10137,10138,10040,10041,10072,2018,2019,10073,10074,10075,10260,10076,10077,10042,10078,10297,10043,10044,10045,10046,10298,10299,10300,10047,10048,10049,10050,10079,10139,10080,10081,9084,10082,10310,10083,5978,10322,10140,10141,10142,10084,10143,10085,10086,5971,10087,10088,10089,10090,10286,10091,10092,10093,10094,10095,10313,10311,10096,10097,10023,10287,10312,10279,10098,10280,10282,984,10099,10100,10101,10281,10102,10314,5961,10103,10105,10106,2017,10323,10107,10288,10289,10108,10315,10316,10109,10317,10110,10318,10283,10284,10111,10112,10113,10474,5974,10463,10285,10114,986,10319,10117,10270,10271,10272,10273,9093,10263,10261,9094,10264,10278,2034,10274,10275,10265,10266,10267,10268,10269,10276,10277,10262,9071,10246,9069,10243,226,227,228,301,229,323,230,339,231,331,232,233,235,236,237,321,238,239,240,241,242,342,243,305,310,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,10330,319,316,244,322,333,325,245,246,74,247,340,248,9070,10176,10177,10178,10234,10360,10179,10180,10247,5966,5964,5967,5965,1448,10453,10454,1730,1605,10181,1065,10182,10258,10349,10350,10346,250,326,10331,10347,10332,10333,10467,10334,10351,11863,11864,11865,11866,11867,11868,11869,11870,11871,11872,11873,11874,11875,11876,11877,11878,11879,11880,11881,11882,11883,11884,11885,11886,11887,11888,11889,11890,11891,11892,11893,11894,11895,11896,11897,11898,11899,11900,11901,11902,11903,11904,11905,11906,11907,11908,11909,11910,11911,11912,11913,11914,11915,11916,11917,11918,11919,11920,11921,11922,11923,11924,11925,11926,177,179,178,176,251,10235,10184,1447,10465,10185,10236,10186,10237,10238,10239,10240,10241,10187,10188,5973,10361,9090,10483,10189,9087,5975,5970,5972,10394,336,10362,252,10190,10403,10404,10405,10406,10191,10410,10192,10335,10193,10363,10194,11045,5980,5981,10195,11046,11048,11049,11050,10407,11052,11053,11054,11055,10196,11056,11057,11058,11059,11060,11061,11062,11063,11064,11065,11066,11067,11068,11069,11070,11071,11072,11073,11074,11075,11076,11077,11078,11079,11080,11081,11082,11083,11084,11085,11086,11087,11088,11089,11090,11091,11092,11093,11094,11095,11096,11097,11098,11099,11100,11101,11102,11103,11104,11105,11106,11107,11108,10411,10197,11109,10412,11110,11111,11112,11113,11114,11115,11116,11117,10162,10198,11118,11119,11120,11121,11122,11123,10199,11124,11125,11126,11127,11128,11129,11130,11131,11132,11133,11134,11135,11136,11137,11138,11139,11140,11141,11142,10200,10201,10364,1270,10202,11927,10408,10413,10203,253,10204,10466,10205,10409,10206,2016,10208,10365,10366,10367,10414,10395,10163,10368,10209,10210,10211,2037,10369,10212,10370,10336,10213,10371,10415,10216,10217,10218,2023,2020,10372,10373,10375,10450,10469,10451,10337,10376,10338,254,10219,334,255,10220,10339,10377,10245,256,257,311,258,259,313,299,260,300,261,117,10340,262,263,264,10341,265,266,267,268,269,270,271,272,273,10342,10452,274,275,276,277,144,302,278,279,280,312,281,204,205,206,207,208,209,210,211,212,213,282,283,335,284,303,308,285,286,287,332,288,289,330,329,10343,315,290,291,309,324,304,293,10325,294,328,10326,10344,295,10345,10327,337,296,170,297,298,10011,10012,10013,10015,10016,5977,10014,10017,10018,10010,10019,10221,10242,10222,9086,10259,1449,10253,10254,10223,10378,10225,10252,10226,10380,5976,10384,10386,10227,10248,10164,10255,10228,10249,10229,10250,10230,10251,10231,11928,11933,11934,11929,11930,11931,11932,11990,11991,11947,11948,11944,11937,11950,11951,11938,11952,11953,11939,11954,11955,11956,11957,11958,11945,11940,11959,11949,11935,11936,11960,11961,11962,11963,11941,11942,11965,11966,11967,11968,11969,11992,11970,11971,11946,11972,11973,11974,11975,11976,11977,11978,11979,11980,11981,11982,11983,11984,11985,11986,11943,11987,11988,11989';

$categories_ids = get_param("categories_ids");
	$approved_status = get_param("approved_status");
	if ($operation == "delete_items") {
		if ($remove_products && strlen($items_ids)) {
			delete_products($items_ids);
		}
	} else if ($operation == "delete_categories") {
		if ($remove_categories && strlen($categories_ids)) {
			delete_categories($categories_ids);
		}
	} else if ($operation == "update_status") {
		if ($update_products && strlen($items_ids)) {
			$sql  = " UPDATE " . $table_prefix . "items SET is_approved=" . $db->tosql($approved_status, INTEGER); 
			$sql .= " WHERE item_id IN (" . $db->tosql($items_ids, TEXT, false) . ")";
			$db->query($sql);
		}
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_items_list.html");

	//BEGIN product privileges changes
	$set_delimiter = false;
	if ($product_prices) {
		$set_delimiter = true;
	}
	if ($product_images && $set_delimiter) {
		$t->set_var("product_images_delimiter", " | ");
	} elseif ($product_images) {
		$set_delimiter = true;
	}
	if ($product_properties && $set_delimiter) {
		$t->set_var("product_properties_delimiter", " | ");
	} elseif ($product_properties) {
		$set_delimiter = true;
	}
	if ($product_features && $set_delimiter) {
		$t->set_var("product_features_delimiter", " | ");
	} elseif ($product_features) {
		$set_delimiter = true;
	}
	if ($product_related && $set_delimiter) {
		$t->set_var("product_related_delimiter", " | ");
	} elseif ($product_related) {
		$set_delimiter = true;
	}
	if ($product_categories && $set_delimiter) {
		$t->set_var("product_categories_delimiter", " | ");
	} elseif ($product_categories) {
		$set_delimiter = true;
	}
	if ($product_accessories && $set_delimiter) {
		$t->set_var("product_accessories_delimiter", " | ");
	} elseif ($product_accessories) {
		$set_delimiter = true;
	}
	if ($product_releases && $set_delimiter) {
		$t->set_var("product_releases_delimiter", " | ");
	}
	//END product privileges changes

	// set files names
	$t->set_var("admin_items_list_href",       "admin_items_list.php");
	$t->set_var("admin_layout_page_href",      "admin_layout_page.php");
	$t->set_var("admin_reviews_href",          "admin_reviews.php");
	$t->set_var("admin_category_edit_href",    "admin_category_edit.php");
	$t->set_var("admin_product_href",          "admin_product.php");
	$t->set_var("admin_properties_href",       "admin_properties.php");
	$t->set_var("admin_releases_href",         "admin_releases.php");
	$t->set_var("admin_item_related_href",     "admin_item_related.php");
	$t->set_var("admin_item_categories_href",  "admin_item_categories.php");
	$t->set_var("admin_category_items_href",  "admin_category_items.php");
	$t->set_var("admin_categories_order_href", "admin_categories_order.php");
	$t->set_var("admin_products_order_href",   "admin_products_order.php");
	$t->set_var("admin_item_types_href",       "admin_item_types.php");
	$t->set_var("admin_features_groups_href",  "admin_features_groups.php");
	$t->set_var("admin_item_prices_href",      "admin_item_prices.php");
	$t->set_var("admin_item_features_href",    "admin_item_features.php");
	$t->set_var("admin_item_images_href",      "admin_item_images.php");
	$t->set_var("admin_item_accessories_href", "admin_item_accessories.php");
	$t->set_var("admin_export_google_base_href", "admin_export_google_base.php");
	$t->set_var("admin_search_href",           "admin_search.php");
	$t->set_var("admin_tell_friend_href",      "admin_tell_friend.php");
	$t->set_var("admin_products_edit_href",  "admin_products_edit.php");
	$t->set_var("rp_url", urlencode($rp->get_url()));



	$t->set_var("admin_import_href", "admin_import.php");
	$t->set_var("admin_export_href", "admin_export.php");

	$t->set_var("approved_status", $approved_status);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$category_id = get_param("category_id");
	if (!strlen($category_id))  { $category_id = "0"; }
	// get search parameters
	$s = trim(get_param("s"));
	$sc = get_param("sc");
	$sl = get_param("sl");
	$ss = get_param("ss");
	$ap = get_param("ap");
	$param_site_id = get_param("param_site_id");
	$search = (strlen($s) || strlen($sl) || strlen($ss) || strlen($ap) || strlen($param_site_id)) ? true : false;
	if ($sc) { $category_id = $sc; }
	$sa = "";

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
	$tree->show($category_id);

	$sql  = " SELECT full_description FROM " . $table_prefix . "categories WHERE category_id = " . $db->tosql($category_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$t->set_var("full_description", $db->f("full_description"));
	} else {
		$t->set_var("full_description", "");
	}

	$t->set_var("parent_category_id", $category_id);
	$sql  = " SELECT category_id,category_name ";
	$sql .= " FROM " . $table_prefix . "categories WHERE parent_category_id = " . $db->tosql($category_id, INTEGER);
	$sql .= " ORDER BY category_order ";
	$db->query($sql);

	// BEGIN product privileges changes
	$set_delimiter = false;
	if ($add_categories) {
		$t->parse("add_categories_priv", false);
		$set_delimiter = true;
	}
	//END product_privileges changes

	if ($db->next_record())
	{
		// BEGIN product privileges changes
		if ($categories_order) {
			if ($set_delimiter) {
				$t->set_var("categories_order_delimiter", "|");
			}
			$t->parse("categories_order_link", false);
		}
		if (!$empty_first_category_block) {
			$t->parse("categories_first_block", false);
		}
		//END product_privileges changes

		$t->set_var("no_categories", "");
		$category_index = 0;
		do {
			$category_index++;
			$row_category_id = $db->f("category_id");
			$row_category_name = $db->f("category_name");
			$row_category_name = get_translation($row_category_name, $language_code);
//delete_categories($category_id);

			$t->set_var("category_index", $category_index);
			$t->set_var("category_id", $row_category_id);
			$t->set_var("category_name", htmlspecialchars($row_category_name));
			if (!$read_only_categories) {
				if ($view_only_categories) {
					$t->set_var("category_edit_msg", VIEW_MSG);
				} else {
					$t->set_var("category_edit_msg", EDIT_MSG);
				}
				$t->parse("categories_edit_link", false);
			}
			
			if ($product_categories) {
				$t->parse("category_products_priv", false);
			} else {
				$t->set_var("category_products_priv", "");
			}

			$row_style = ($category_index % 2 == 0) ? "row1" : "row2";
			$t->set_var("row_style", $row_style);
			if ($remove_categories) {
				$t->parse("category_checkbox", false);
			} else {
				$t->set_var("category_checkbox", "");
			}

			$t->parse("categories");
		} while ($db->next_record());
		if ($remove_categories) {
			$t->parse("categories_all_checkbox", false);
			if ($add_categories || $update_categories) {
				$t->set_var("delete_categories_delimiter", "|");	
			}
			$t->parse("delete_categories_link", false);
			$t->set_var("categories_colspan", "2");
		} else {
			$t->set_var("categories_colspan", "1");
		}

		$t->set_var("categories_number", $category_index);
		$t->parse("categories_header", false);
	}
	else
	{
		$t->set_var("categories", "");
		$t->set_var("categories_order_link", "");
		$t->parse("no_categories");
	}

	// BEGIN product privileges changes
	if (!$empty_first_category_block) {
		$t->parse("categories_first_block", false);
	}
	//END product_privileges changes
	
	$group_by = "";
	
	$sorter = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_items_list.php");
	$sorter->set_parameters(false, true, true, false);
	$sorter->set_default_sorting(10, "asc");
	$sorter->set_sorter(PROD_TITLE_COLUMN, "sorter_item_name", 1, "i.item_name");
	$sorter->set_sorter(PROD_PRICE_COLUMN, "sorter_price", 2, "i.price");
	$sorter->set_sorter(PROD_QTY_COLUMN, "sorter_qty", 3, "i.stock_level");
	if ($search) {
		$sorter->set_sorter(ADMIN_ORDER_MSG, "sorter_order", 10, "i.item_order, i.item_id", "i.item_order, i.item_id", "i.item_order DESC, i.item_id");
		$group_by .= ", i.item_order";
	} else {
		$sorter->set_sorter(ADMIN_ORDER_MSG, "sorter_order", 10, "ic.item_order", "ic.item_order, i.item_order, i.item_id", "ic.item_order DESC, i.item_order, i.item_id");
		$group_by .= ", ic.item_order, i.item_order";
	}

	$where = "";
	$join  = "";
	$brackets = "";
	if ($search && $category_id != 0) {
		$brackets .= "((";
		$join  .= " LEFT JOIN " . $table_prefix . "items_categories ic ON i.item_id=ic.item_id) ";
		$join  .= " LEFT JOIN " . $table_prefix . "categories c ON c.category_id = ic.category_id) ";
		
		$where .= " AND (ic.category_id = " . $db->tosql($category_id, INTEGER);
		$where .= " OR c.category_path LIKE '" . $db->tosql($tree->get_path($category_id), TEXT, false) . "%')";
	} elseif (!$search) {
		$brackets .= "(";
		$join  .= " LEFT JOIN " . $table_prefix . "items_categories ic ON i.item_id=ic.item_id) ";
		$where .= " AND ic.category_id = " . $db->tosql($category_id, INTEGER);
	}
	if ($s) {
		$sa = split(" ", $s);
		for($si = 0; $si < sizeof($sa); $si++) {
			$sa[$si] = str_replace("%","\%",$sa[$si]);
			$where .= " AND (i.item_name LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%'";
			$where .= " OR i.item_code LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%' ";
			if (sizeof($sa) == 1 && preg_match("/^\d+$/", $sa[0])) {
				$where .= " OR i.item_id =" . $db->tosql($sa[0], INTEGER);
			}
			$where .= " OR i.manufacturer_code LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%')";
		}
	}
	if (strlen($sl)) {
		if ($sl == 1) {
			$where .= " AND (i.stock_level>0 OR i.stock_level IS NULL) ";
		} else {
			$where .= " AND i.stock_level<1 ";
		}
	}
	if (strlen($ss)) {
		if ($ss == 1) {
			$where .= " AND i.is_showing=1 ";
		} else {
			$where .= " AND i.is_showing=0 ";
		}
		$group_by .= ", i.is_showing";
	}
	if (strlen($ap)) {
		if ($ap == 1) {
			$where .= " AND i.is_approved=1 ";
		} else {
			$where .= " AND i.is_approved=0 ";
		}
		$group_by .= ", i.is_approved";
	}
	if (strlen($param_site_id)) {
		if ($param_site_id == "all") {
			$where .= " AND i.sites_all=1 ";
		} else {
			$brackets .= "(";
			$join  .= " LEFT JOIN " . $table_prefix . "items_sites s ON (s.item_id = i.item_id AND i.sites_all = 0 )) ";
			$where .= " AND (s.site_id=" . $db->tosql($param_site_id, INTEGER) . " OR i.sites_all=1) ";
		}
		$group_by .= ", i.sites_all";
	}

	
	$total_records = 0;
	if (strtolower($db_type) == "mysql" || !strlen($join)) {
		$sql  = " SELECT COUNT(DISTINCT i.item_id) ";
	} else {
		$sql  = " SELECT COUNT(*) ";
	}
	$sql .= " FROM " . $brackets . $table_prefix . "items i " . $join;
	$sql .= " WHERE 1=1 ";
	$sql .= $where;
	$total_records = 0;
	if (strtolower($db_type) == "mysql" || !strlen($join)) {
		$db->query($sql);
		$db->next_record();
		$total_records = $db->f(0);
	} else {
		$sql .= " GROUP BY i.item_id";
		$db->query($sql);
		while ($db->next_record()) {
			$total_records++;
		}
	}

	// set up variables for navigator
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_items_list.php");
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", MOVING, $pages_number, $records_per_page, $total_records, false);

	
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;              

	$item_index = 0;

	// 'Add new product' link
	$set_delimiter = false;
	if ($add_products) {
		$t->parse("add_products_priv", false);
		$set_delimiter = true;
	}

	if ($total_records > 0) {
		$sql  = " SELECT i.item_id, i.item_code, i.manufacturer_code, i.item_name, i.price, i.sales_price, i.is_sales, i.stock_level ";
		$sql .= " FROM " . $brackets . $table_prefix . "items i " . $join;
		$sql .= " WHERE 1=1 ";
		$sql .= $where;
		$sql .= " GROUP BY i.item_id, i.item_code, i.manufacturer_code, i.item_name, i.price, i.sales_price, i.is_sales, i.stock_level ";
		$sql .= $group_by;
	
		$sql .= $sorter->order_by;
		$db->query($sql);
		if ($db->next_record())
		{
			//BEGIN product privileges changes
			if ($update_products) {
				if ($set_delimiter) {
					$t->set_var("edit_items_delimiter", " | ");
				}
				$t->parse("edit_items_link", false);
				$set_delimiter = true;
			}
			if ($remove_products) {
				if ($set_delimiter) {
					$t->set_var("delete_items_delimiter", " | ");
				}
				$t->parse("delete_items_link", false);
				$set_delimiter = true;
			}
			if ($products_order) {
				if ($set_delimiter) {
					$t->set_var("products_order_delimiter", " | ");
				}
				$t->parse("products_order_link", false);
			}
			//END product privileges changes
			$t->set_var("category_id", $category_id);
			$t->set_var("no_items", "");
			do {
				$item_index++;
				$item_id = $db->f("item_id");
				$product_category_id = $db->f("category_id");
				$item_code = $db->f("item_code");
				$manufacturer_code = $db->f("manufacturer_code");
				$item_name = get_translation($db->f("item_name"));
				$price = $db->f("price");
				$is_sales = $db->f("is_sales");
				$sales_price = $db->f("sales_price");
				$stock_level = $db->f("stock_level");
				$item_codes = "";
				if ($item_code && $manufacturer_code) {
					$item_codes = "(" . $item_code . ", " . $manufacturer_code . ")";
				} elseif ($item_code) {
					$item_codes = "(" . $item_code . ")";
				} elseif ($manufacturer_code) {
					$item_codes = "(" . $manufacturer_code . ")";
				}

				$price = calculate_price($price, $is_sales, $sales_price);

				$t->set_var("item_id", $item_id);
				$t->set_var("item_index", $item_index);
				$t->set_var("product_category_id", $product_category_id);
				$t->set_var("item_code", htmlspecialchars($item_code));
				$t->set_var("manufacturer_code", htmlspecialchars($manufacturer_code));
				$t->set_var("item_codes", htmlspecialchars($item_codes));

				$item_name = htmlspecialchars($item_name);
				if (is_array($sa)) {
					for ($si = 0; $si < sizeof($sa); $si++) {
						$regexp = "";
						for ($si = 0; $si < sizeof($sa); $si++) {
							if (strlen($regexp)) $regexp .= "|";
							$regexp .= htmlspecialchars(str_replace(
								array( "/", "|",  "$", "^", "?", ".", "{", "}", "[", "]", "(", ")", "*"),
								array("\/","\|","\\$","\^","\?","\.","\{","\}","\[","\]","\(","\)","\*"),$sa[$si]));
						}
						if (strlen($regexp))
						{
							$item_name = preg_replace ("/(" . $regexp . ")/i", "<font color=\"blue\">\\1</font>", $item_name);
						}
					}
				}
				$t->set_var("item_name", $item_name);
				$t->set_var("price", currency_format($price));
				if ($stock_level < 0) {
					$stock_level = "<font color=red>" . $stock_level . "</font>";
				}
				$t->set_var("stock_level", $stock_level);

				// BEGIN product privileges changes
				if ($product_prices) {
					$t->parse("product_prices_priv", false);
				} else {
					$t->set_var("product_prices_priv", "");
				}
				if ($product_images) {
					$t->parse("product_images_priv", false);
				} else {
					$t->set_var("product_images_priv", "");
				}
				if ($product_properties) {
					$t->parse("product_properties_priv", false);
				} else {
					$t->set_var("product_properties_priv", "");
				}
				if ($product_features) {
					$t->parse("product_features_priv", false);
				} else {
					$t->set_var("product_features_priv", "");
				}
				if ($product_related) {
					$t->parse("product_related_priv", false);
				} else {
					$t->set_var("product_related_priv", "");
				}
				if ($product_categories) {
					$t->parse("product_categories_priv", false);
				} else {
					$t->set_var("product_categories_priv", "");
				}
				if ($product_accessories) {
					$t->parse("product_accessories_priv", false);
				} else {
					$t->set_var("product_accessories_priv", "");
				}
				if ($product_releases) {
					$t->parse("product_releases_priv", false);
				} else {
					$t->set_var("product_releases_priv", "");
				}
				if ($read_only_products) {
					$t->parse("read_only_products_priv", false);
					$t->set_var("update_products_priv", "");
				} elseif ($view_only_products) {
					$t->set_var("product_edit_msg", VIEW_MSG);
					$t->parse("update_products_priv", false);
					$t->set_var("read_only_products_priv", "");
				} else {
					$t->set_var("product_edit_msg", EDIT_MSG);
					$t->parse("update_products_priv", false);
					$t->set_var("read_only_products_priv", "");
				}
				if (!$remove_checkbox_column) {
					$t->parse("checkbox_list_priv", false);
				}
				
				$row_style = ($item_index % 2 == 0) ? "row1" : "row2";
				$t->set_var("row_style", $row_style);
				// END product privileges changes
				$t->parse("items_list");
			} while ($db->next_record());
			if (!$remove_checkbox_column) {
				$t->parse("checkbox_header_priv", false);
			}
			$t->parse("items_header", false);
		}
	}

	if ($item_index < 1) {
		$t->set_var("delete_items_link", "");
		$t->set_var("products_order_link", "");
		$t->set_var("items_list", "");
		$t->parse("no_items");
	}

	if ($total_records > 0) {
		$admin_google_base_filtered_url = new VA_URL("admin_export_google_base.php", false);
		if ($search) {
			$admin_google_base_filtered_url->add_parameter("sc", GET, "sc");
		} else {
			$admin_google_base_filtered_url->add_parameter("sc", CONSTANT, $category_id);
		}
		$admin_google_base_filtered_url->add_parameter("sl", GET, "sl");
		$admin_google_base_filtered_url->add_parameter("sa", GET, "sa");
		$admin_google_base_filtered_url->add_parameter("ss", GET, "ss");
		$admin_google_base_filtered_url->add_parameter("ap", GET, "ap");
		$admin_google_base_filtered_url->add_parameter("s", GET, "s");
		$admin_google_base_filtered_url->add_parameter("param_site_id", GET, "param_site_id");		

		$t->set_var("admin_google_base_filtered_url", $admin_google_base_filtered_url->get_url());
		$t->set_var("total_filtered", $total_records);
		$t->parse("google_base_filtered", false);
		
		$admin_export_filtered_url = new VA_URL("admin_export.php", true);
		$admin_export_filtered_url->add_parameter("table", CONSTANT, "items");
		if (!strlen(get_param("category_id")))
			$admin_export_filtered_url->add_parameter("category_id", CONSTANT, $category_id);

		$t->set_var("admin_export_filtered_url", $admin_export_filtered_url->get_url());
		$t->set_var("total_filtered", $total_records);
		$t->parse("export_filtered", false);	
  
		if ($approve_products) {
			if (!$empty_export_block) {
				$t->set_var("update_status_br", "<br><br>");
			}
			$approved_options = array(array("", ""), array("1", IS_APPROVED_MSG), array("0", NOT_APPROVED_MSG));
			for ($i = 0; $i < sizeof($approved_options); $i++) {
				if ($approved_options[$i][0] == $approved_status) {
					$t->set_var("status_id_selected", "selected");
				} else {
					$t->set_var("status_id_selected", "");
				}
				$t->set_var("status_id_value", $approved_options[$i][0]);
				$t->set_var("status_id_description", $approved_options[$i][1]);
				$t->parse("status_id", true);
			}
			$t->parse("update_status", false);
		}
	}

	// BEGIN product privileges changes
	$set_delimiter = false;
	if ($products_export) {
		$t->parse("products_export_priv", false);
		$set_delimiter = true;
	}
	if ($products_import) {
		if ($set_delimiter) {
			$t->set_var("products_import_delimiter", " | ");
		}
		$t->parse("products_import_priv", false);
	}
	if ($products_export_google_base) {
		if ($set_delimiter) {
			$t->set_var("products_export_google_base_delimiter", " | ");
		}
		$t->parse("products_export_google_base_priv", false);
	}
	// END product privileges changes


	// set up search form parameters
	$stock_levels =
		array(
			array("", ""), array(0, OUTOFSTOCK_PRODUCTS_MSG), array(1, INSTOCK_PRODUCTS_MSG)
		);
	$sales =
		array(
			array("", ""), array(0, NOT_FOR_SALES_MSG), array(1, FOR_SALES_MSG)
		);
	$aproved_values =
		array(
			array("", ""), array(0, NO_MSG), array(1, YES_MSG)
		);

	set_options($stock_levels, $sl, "sl");
	set_options($sales, $ss, "ss");
	set_options($aproved_values, $ap, "ap");
	$values_before[] = array("", SEARCH_IN_ALL_MSG);
	if ($category_id != 0) {
		$values_before[] = array($category_id, SEARCH_IN_CURRENT_MSG);
	}
	if ($sitelist) {
		$sites   = get_db_values("SELECT site_id,site_name FROM " . $table_prefix . "sites ORDER BY site_id ", 
			array(array("", ""), array("all",  SITES_ALL_MSG) ));
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse("sitelist");
	}

	$sql  = " SELECT category_id,category_name ";
	$sql .= " FROM " . $table_prefix . "categories WHERE parent_category_id = " . $db->tosql($category_id, INTEGER);
	$sql .= " ORDER BY category_order ";
	$sc_values = get_db_values($sql, $values_before);
	set_options($sc_values, $sc, "sc");
	$t->set_var("s", $s);
	if ($search) {
		$t->parse("s_d", false);
	}

	$hidden_params["s"] = get_param("s");
	$hidden_params["sl"] = get_param("sl");
	$hidden_params["sc"] = get_param("sc");
	$hidden_params["sort_ord"] = get_param("sort_ord");
	$hidden_params["sort_dir"] = get_param("sort_dir");
	get_query_string($hidden_params, "", "", true);

	if (!$empty_select_block) {
		$t->parse("products_select_block_priv", false);
	}
	if (!$empty_export_approve_block) {
		$t->parse("products_export_block_priv", false);
	}

	$set_delimiter = false;
	if ($categories_export) {
		$t->parse("categories_export_priv", false);
		$set_delimiter = true;
	}
	if ($categories_import) {
		if ($set_delimiter) {
			$t->set_var("categories_import_delimiter", " | ");
		}
		$t->parse("categories_import_priv", false);
	}
	if (!$empty_second_category_block) {
		$t->parse("categories_second_block", false);
	}

	$t->set_var("items_number", $item_index);
	$t->parse("items_block", false);

	$t->pparse("main");

?>