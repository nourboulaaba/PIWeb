CREATE TABLE reset_password_request (
    id INT AUTO_INCREMENT NOT NULL,
    user_id INT NOT NULL,
    selector VARCHAR(20) NOT NULL,
    hashed_token VARCHAR(100) NOT NULL,
    requested_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    expires_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    PRIMARY KEY(id),
    INDEX IDX_7CE748AA76ED395 (user_id),
    CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
