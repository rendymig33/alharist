    </main>
</div>
<script>
    (function () {
        const sidebar = document.getElementById('app-sidebar');
        const toggle = document.getElementById('sidebar-toggle');

        if (!sidebar || !toggle) {
            return;
        }

        function closeSidebar() {
            if (window.innerWidth <= 920) {
                sidebar.classList.remove('open');
            }
        }

        toggle.addEventListener('click', function () {
            if (window.innerWidth <= 920) {
                sidebar.classList.toggle('open');
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 920) {
                sidebar.classList.remove('open');
            }
        });

        sidebar.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeSidebar);
        });

        document.addEventListener('click', function (event) {
            if (window.innerWidth > 920) {
                return;
            }

            if (!sidebar.classList.contains('open')) {
                return;
            }

            if (sidebar.contains(event.target) || toggle.contains(event.target)) {
                return;
            }

            sidebar.classList.remove('open');
        });
    }());
</script>
</body>
</html>
