<?php include(ROOT . '/views/layouts/header.php'); ?>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-lg-offset-3
                        col-md-6 col-md-offset-3
                        col-sm-8 col-sm-offset-2
                ">
                    <div class="signup-form">
                        <h2>Вход на сайт</h2>
                        <?php if (!empty($errors)) : ?>
                            <ul class="my-ul">
                                <?php foreach ($errors as $error) : ?>
                                    <li class="my-red-color"><?= htmlentities($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <form action="" method="post" class="my-form">
                            <input type="email" name="email" value="<?= htmlentities($email); ?>" placeholder="/Email">
                            <input type="password" name="password" value="<?= htmlentities($password); ?>" placeholder="/Пароль">
                            <label>
                                <input type="checkbox" name="remember" value="true"> Запомнить меня
                            </label>
                            <button name="submit" class="btn btn-default my-btn">Войти</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include(ROOT . '/views/layouts/footer.php'); ?>