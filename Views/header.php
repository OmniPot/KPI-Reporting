<div>
    <h2>Welcome to Medieval, <a href="/profiles/me"><?= $model->getUsername(); ?></a></h2>

    <?php if ( $model->getUsername() != 'guest' ): ?>
        <form action="/user/logout" method="POST">
            <input type="submit" name="logout" value="logout">
        </form>
    <?php endif; ?>
</div>