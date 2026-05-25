<style>
    .perfil-panel {
        background: #ffffff;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .08);
        border: 1px solid #eef0f4;
    }

    .perfil-section-header {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        margin-bottom: 1rem;
    }

    .perfil-section-header h4 {
        color: #a6192e;
        font-weight: 800;
        margin: 0;
    }

    .perfil-card-item {
        display: flex;
        gap: 1rem;
        border: 1px solid #dfe3ea;
        border-radius: 14px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        background: #ffffff;
    }

    .perfil-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 999px;
        background: #fff1f3;
        color: #a6192e;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex: 0 0 48px;
    }

    .perfil-card-body {
        flex: 1;
    }

    .perfil-card-title {
        font-weight: 800;
        font-size: 1.08rem;
        color: #111827;
        margin-bottom: .2rem;
    }

    .perfil-card-subtitle {
        color: #374151;
        margin-bottom: .35rem;
    }

    .perfil-card-meta {
        color: #6b7280;
        font-size: .92rem;
    }

    .perfil-form-wrapper {
        margin-bottom: 1.25rem;
    }

    .habilidad-panel {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 1.5rem;
        background: #fbfcfe;
        box-shadow: 0 4px 14px rgba(15, 23, 42, .04);
        margin-bottom: 1.25rem;
    }

    .habilidad-panel h4 {
        color: #a6192e;
        font-weight: 800;
    }

    .habilidad-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .75rem 1.25rem;
    }

    @media (max-width: 992px) {
        .habilidad-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 576px) {
        .perfil-section-header {
            flex-direction: column;
            align-items: stretch;
        }

        .perfil-card-item {
            flex-direction: column;
        }

        .habilidad-grid {
            grid-template-columns: 1fr;
        }
    }
</style>