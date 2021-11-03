CREATE TABLE `blog`  (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` int UNSIGNED NOT NULL DEFAULT 0,
    `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
    `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `created_at` int UNSIGNED NOT NULL DEFAULT 0,
    `updated_at` int UNSIGNED NOT NULL DEFAULT 0,
    `deleted_at` int UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

CREATE TABLE `user`  (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
    `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
    `created_at` int UNSIGNED NOT NULL DEFAULT 0,
    `updated_at` int UNSIGNED NOT NULL DEFAULT 0,
    `deleted_at` int UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;