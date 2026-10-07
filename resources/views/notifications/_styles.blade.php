<style>
    .notification-bell { position: relative; z-index: 1300; flex: 0 0 auto; }
    .notification-bell__trigger {
        position: relative;
        display: inline-grid;
        place-items: center;
        width: 42px;
        height: 42px;
        color: #6B3E1F;
        background: #fff;
        border: 1px solid #D8C7B0;
        border-radius: 9px;
        cursor: pointer;
        transition: background .18s ease, border-color .18s ease;
    }
    .notification-bell__trigger:hover,
    .notification-bell__trigger[aria-expanded="true"] { background: #F5EDE6; border-color: #6B3E1F; }
    .notification-bell__trigger > i { font-size: 1.2rem; }
    .notification-bell__count {
        position: absolute;
        top: -5px;
        right: -5px;
        display: grid;
        place-items: center;
        min-width: 19px;
        height: 19px;
        padding: 0 5px;
        color: #fff;
        background: #9D3F32;
        border: 2px solid #fff;
        border-radius: 999px;
        font: 700 10px/1 Inter, sans-serif;
    }
    .notification-bell__count[hidden] { display: none !important; }
    .notification-menu-panel {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        left: auto;
        display: block;
        width: min(360px, calc(100vw - 24px));
        max-width: calc(100vw - 24px);
        padding: 0;
        overflow: hidden;
        color: #2A1C12;
        background: #fff;
        border: 1px solid #D8C7B0;
        border-radius: 10px;
        box-shadow: 0 14px 34px rgba(61, 43, 31, .16);
        z-index: 1400;
    }
    .notification-menu-panel:not(.show) { display: none; }
    .notification-menu-panel.show { display: block; }
    .notification-menu-heading { display: grid; grid-template-columns: minmax(0, 1fr) auto; align-items: center; gap: 12px; padding: 13px 15px; border-bottom: 1px solid #E8DCCF; }
    .notification-menu-heading-copy { display: flex; align-items: baseline; gap: 9px; min-width: 0; }
    .notification-menu-heading strong { color: #2A1C12; font-size: .92rem; }
    .notification-menu-heading span { color: #8A7060; font-size: .72rem; white-space: nowrap; }
    .notification-menu-heading a { color: #6B3E1F; font-size: .78rem; font-weight: 700; white-space: nowrap; }
    .notification-menu-footer { display: flex; align-items: center; justify-content: center; min-height: 48px; padding: 11px 15px; border-top: 1px solid #E8DCCF; }
    .notification-menu-footer a { color: #6B3E1F; font-size: .78rem; font-weight: 700; white-space: nowrap; }
    .notification-menu-list { max-height: 390px; margin: 0; padding: 0; overflow-y: auto; list-style: none; }
    .notification-menu-item {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        padding: 12px 15px;
        color: #2A1C12;
        border-bottom: 1px solid #F0E8E0;
        text-decoration: none;
    }
    .notification-menu-item:hover,
    .notification-menu-item.is-unread { background: #FBF7F3; }
    .notification-menu-icon {
        display: grid;
        place-items: center;
        flex: 0 0 34px;
        width: 34px;
        height: 34px;
        color: #6B3E1F;
        background: #F5EDE6;
        border-radius: 8px;
        font-size: 1rem;
    }
    .notification-menu-copy { display: block; min-width: 0; flex: 1; }
    .notification-menu-copy strong,
    .notification-menu-copy span,
    .notification-menu-copy time { display: block; overflow: hidden; text-overflow: ellipsis; word-break: normal; overflow-wrap: break-word; }
    .notification-menu-copy strong { color: #2A1C12; font-size: .78rem; font-weight: 700; white-space: nowrap; }
    .notification-menu-copy span { margin-top: 2px; color: #5C4030; font-size: .73rem; line-height: 1.4; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; white-space: normal; }
    .notification-menu-copy time { margin-top: 5px; color: #8A7060; font-size: .66rem; white-space: nowrap; }
    .notification-menu-empty { padding: 24px 16px; color: #8A7060; font-size: .8rem; text-align: center; }
    .notification-menu-footer { justify-content: center; border-top: 1px solid #E8DCCF; }

    .notification-page { width: min(1080px, 100%); margin: 0 auto; color: #2A1C12; }
    .notification-page__heading { display: flex; align-items: flex-end; justify-content: space-between; gap: 18px; margin-bottom: 22px; }
    .notification-page__heading h1 { margin: 0; color: #2A1C12; font: 700 1.7rem/1.2 'Playfair Display', Georgia, serif; }
    .notification-page__heading p { margin: 5px 0 0; color: #8A7060; font-size: .85rem; }
    .notification-page__actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .notification-page__button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 39px;
        padding: 8px 12px;
        color: #6B3E1F;
        background: #fff;
        border: 1px solid #D8C7B0;
        border-radius: 7px;
        font: 600 .78rem/1.2 Inter, sans-serif;
        text-decoration: none;
        cursor: pointer;
    }
    .notification-page__button:hover { background: #F5EDE6; border-color: #6B3E1F; }
    .notification-page__button--primary { color: #fff; background: #6B3E1F; border-color: #6B3E1F; }
    .notification-page__button--primary:hover { color: #fff; background: #3D2B1F; }
    .notification-filterbar { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; }
    .notification-filterbar select {
        width: auto;
        min-width: 150px;
        min-height: 40px;
        padding: 8px 32px 8px 11px;
        color: #3D2B1F;
        background-color: #fff;
        border: 1px solid #D8C7B0;
        border-radius: 7px;
        font: 500 .8rem Inter, sans-serif;
    }
    .notification-feed { overflow: hidden; background: #fff; border: 1px solid #D8C7B0; border-radius: 9px; }
    .notification-feed-row { display: flex; align-items: flex-start; gap: 13px; padding: 16px; border-bottom: 1px solid #EFE6DD; }
    .notification-feed-row:last-child { border-bottom: 0; }
    .notification-feed-row.is-unread { background: #FCF8F4; }
    .notification-feed-row__icon { display: grid; place-items: center; flex: 0 0 40px; width: 40px; height: 40px; color: #6B3E1F; background: #F5EDE6; border-radius: 8px; font-size: 1.1rem; }
    .notification-feed-row__body { min-width: 0; flex: 1; }
    .notification-feed-row__title { display: inline-block; color: #2A1C12; font-size: .9rem; font-weight: 700; text-decoration: none; word-break: normal; overflow-wrap: break-word; }
    .notification-feed-row__title:hover { color: #6B3E1F; text-decoration: underline; }
    .notification-feed-row__message { margin: 4px 0 7px; color: #5C4030; font-size: .82rem; line-height: 1.5; overflow-wrap: break-word; }
    .notification-feed-row__meta { display: flex; align-items: center; gap: 9px; color: #8A7060; font-size: .7rem; flex-wrap: wrap; }
    .notification-feed-row__type { padding: 3px 7px; background: #F5EDE6; border-radius: 999px; }
    .notification-feed-row__controls { display: flex; align-items: center; gap: 4px; flex: 0 0 auto; }
    .notification-icon-button { display: grid; place-items: center; width: 36px; height: 36px; color: #6B3E1F; background: transparent; border: 0; border-radius: 7px; cursor: pointer; }
    .notification-icon-button:hover { background: #F5EDE6; }
    .notification-empty-state { padding: 48px 18px; color: #8A7060; text-align: center; }
    .notification-empty-state i { display: block; margin-bottom: 9px; color: #B8A090; font-size: 2rem; }
    .notification-empty-state strong { display: block; color: #5C4030; font-size: .9rem; }
    .notification-pagination { padding: 14px; border-top: 1px solid #EFE6DD; }
    .notification-preferences { margin-top: 22px; background: #fff; border: 1px solid #D8C7B0; border-radius: 9px; }
    .notification-preferences summary { padding: 15px 17px; color: #3D2B1F; font-size: .9rem; font-weight: 700; cursor: pointer; }
    .notification-preferences__content { padding: 0 17px 17px; }
    .notification-preferences__hint { margin: 0 0 13px; color: #8A7060; font-size: .77rem; }
    .notification-preferences__row { display: grid; grid-template-columns: minmax(0, 1fr) 100px 100px; align-items: center; gap: 10px; padding: 10px 5px; border-top: 1px solid #F0E8E0; }
    .notification-preferences__row strong { min-width: 0; color: #3D2B1F; font-size: .8rem; font-weight: 600; overflow-wrap: break-word; }
    .notification-preferences__head { color: #8A7060; font-size: .7rem; font-weight: 700; }
    .notification-preferences__toggle { display: flex; justify-content: center; }
    .notification-preferences__toggle input { width: 17px; height: 17px; accent-color: #6B3E1F; cursor: pointer; }
    .notification-action-toast { position: fixed; z-index: 1500; top: 82px; right: 18px; width: min(420px, calc(100vw - 28px)); margin: 0 !important; box-shadow: 0 12px 30px rgba(61, 43, 31, .16); transition: opacity .25s ease, transform .25s ease; }
    .notification-action-toast.is-dismissing { opacity: 0; transform: translateY(-7px); }

    @media (max-width: 700px) {
        .notification-menu-panel {
            position: fixed !important;
            top: 72px !important;
            right: 12px !important;
            left: 12px !important;
            width: auto;
            max-width: none;
            margin: 0 !important;
            transform: none !important;
        }
        .notification-page__heading { align-items: flex-start; flex-direction: column; }
        .notification-page__heading h1 { font-size: 1.45rem; }
        .notification-filterbar { align-items: stretch; flex-direction: column; }
        .notification-filterbar select { width: 100%; }
        .notification-feed-row { gap: 10px; padding: 13px 11px; }
        .notification-feed-row__icon { flex-basis: 34px; width: 34px; height: 34px; }
        .notification-feed-row__controls { flex-direction: column; }
        .notification-preferences__row { grid-template-columns: minmax(0, 1fr) 56px 56px; gap: 5px; }
        .notification-preferences__head { font-size: .63rem; text-align: center; }
        .notification-action-toast { top: 70px; right: 12px; }
    }
</style>