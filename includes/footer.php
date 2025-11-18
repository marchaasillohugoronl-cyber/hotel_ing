</main>

<footer>
    <div class="container">
        <p>&copy; <?= date('Y') ?> <?= NOMBRE_HOSTAL ?> — Todos los derechos reservados.</p>
    </div>
</footer>

<style>
/* Estilo para el footer */
footer {
    background-color: #00264d;
    color: #fff;
    padding: 20px 0;
    text-align: center;
    box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
    margin-top: 40px;
}
footer .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}
footer p {
    margin: 0;
    font-size: 0.9rem;
}
footer a {
    color: #ffcc00;
    text-decoration: none;
    font-weight: bold;
    transition: color 0.3s;
}
footer a:hover {
    color: #fff;
}
</style>

<script src="<?= URL_BASE ?>assets/js/main.js"></script>
</body>
</html>
