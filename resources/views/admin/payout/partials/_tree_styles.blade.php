<style>
.payout-tree-wrapper {
    position: relative;
    padding-left: 0;
}
.payout-tree-wrapper ul {
    position: relative;
    padding-left: 1.75rem;
    list-style: none;
    margin-bottom: 0;
}
.payout-tree-wrapper ul::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 1.25rem;
    left: 0.85rem;
    width: 2px;
    background-color: #cbd5e1;
}
.payout-tree-wrapper li {
    position: relative;
    margin-top: 0.75rem;
    margin-bottom: 0.75rem;
}
.payout-tree-wrapper ul > li::before {
    content: '';
    position: absolute;
    top: 1.5rem;
    left: -0.9rem;
    width: 0.9rem;
    height: 2px;
    background-color: #cbd5e1;
}
.payout-tree-card {
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 0.875rem 1.125rem;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.payout-tree-card:hover {
    border-color: #3b82f6;
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.12);
    transform: translateY(-1px);
}
.payout-chevron {
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.payout-expand[aria-expanded="true"] .payout-chevron {
    transform: rotate(90deg);
}
.payout-tree-node.is-hidden {
    display: none !important;
}
.payout-tree-node.is-matched > .payout-tree-card {
    border-color: #f59e0b !important;
    background-color: #fffbeb !important;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2) !important;
}
.stat-pill {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.625rem;
    border-radius: 9999px;
    font-size: 0.8125rem;
    font-weight: 500;
    border: 1px solid #e2e8f0;
    background-color: #f8fafc;
    color: #334155;
}
.stat-pill-primary {
    background-color: #eff6ff;
    border-color: #bfdbfe;
    color: #1d4ed8;
}
.stat-pill-info {
    background-color: #ecfeff;
    border-color: #a5f3fc;
    color: #0e7490;
}
.stat-pill-success {
    background-color: #f0fdf4;
    border-color: #bbf7d0;
    color: #15803d;
}
.stat-pill-warning {
    background-color: #fffbeb;
    border-color: #fde68a;
    color: #b45309;
}
@media (max-width: 768px) {
    .payout-tree-wrapper ul {
        padding-left: 1rem;
    }
    .payout-tree-wrapper ul::before {
        left: 0.5rem;
    }
    .payout-tree-wrapper ul > li::before {
        left: -0.5rem;
        width: 0.5rem;
    }
}
</style>
