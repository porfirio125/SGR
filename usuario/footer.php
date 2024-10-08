<footer>
    <div class="footer-container">
        <p>&copy; 2024 Computación. Todos los derechos reservados.</p>
        <ul>
            <li><a href="politicas.html">Políticas de Privacidad</a></li>
            <li><a href="terminos.html">Términos y Condiciones</a></li>
            <li><a href="contacto.html">Contacto</a></li>
        </ul>
    </div>
</footer>
<style>
* {
    box-sizing: border-box;
}

footer {
    background-color: red;
    padding: 20px;
    border-radius: 15px;
    position: fixed;
    bottom: 0;
    width: 100%;
    left: 0;
    z-index: 1000;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
}

.footer-container {
    max-width: 1200px;
    margin: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

footer ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

footer ul li {
    display: inline-block;
    margin-right: 20px;
}

footer ul li:last-child {
    margin-right: 0;
}

footer ul li a {
    text-decoration: none;
    color: #007bff;
}

footer ul li a:hover {
    text-decoration: underline;
}


.content {
    padding-bottom: 100px;
}
</style>