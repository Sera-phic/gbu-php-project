    </div><!-- End admin-main -->
    
    <script src="../assets/js/main.js"></script>
    <script>
        // Sidebar Toggle for Mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('adminSidebar').classList.toggle('show');
        });
        
        // DataTables-like search (simple version for now)
        function filterTable(inputId, tableId) {
            const input = document.getElementById(inputId);
            const table = document.getElementById(tableId);
            const rows = table.getElementsByTagName('tr');
            
            input.addEventListener('keyup', function() {
                const filter = input.value.toLowerCase();
                for (let i = 1; i < rows.length; i++) {
                    const row = rows[i];
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                }
            });
        }
    </script>
</body>
</html>
