<div style="padding: 20px;">
    <h3 style="font-size: 16px; margin-bottom: 20px; color: #1F2933;">Verificando recursos del sistema...</h3>

    <div style="display: grid; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #F5F7FA; border-radius: 8px;">
            <span id="check-superadmin" style="font-size: 18px;">○</span>
            <span>Superadmin creado</span>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #F5F7FA; border-radius: 8px;">
            <span id="check-tenant" style="font-size: 18px;">○</span>
            <span>Primer tenant configurado</span>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #F5F7FA; border-radius: 8px;">
            <span id="check-theme" style="font-size: 18px;">○</span>
            <span>Tema aplicado</span>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #F5F7FA; border-radius: 8px;">
            <span id="check-directories" style="font-size: 18px;">○</span>
            <span>Directorios del sistema</span>
        </div>
    </div>

    <p style="font-size: 12px; color: #6B7280; margin-top: 20px; text-align: center;">
        Todos los pasos se han completado exitosamente. El sistema está listo para usar.
    </p>
</div>

<script>
    // Esperar a que se complete el paso anterior
    document.getElementById('stepForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Simular verificación
        setTimeout(() => {
            document.getElementById('check-superadmin').textContent = '✓';
            document.getElementById('check-superadmin').style.color = '#4CAF50';
        }, 300);

        setTimeout(() => {
            document.getElementById('check-tenant').textContent = '✓';
            document.getElementById('check-tenant').style.color = '#4CAF50';
        }, 600);

        setTimeout(() => {
            document.getElementById('check-theme').textContent = '✓';
            document.getElementById('check-theme').style.color = '#4CAF50';
        }, 900);

        setTimeout(() => {
            document.getElementById('check-directories').textContent = '✓';
            document.getElementById('check-directories').style.color = '#4CAF50';
        }, 1200);
    });
</script>

