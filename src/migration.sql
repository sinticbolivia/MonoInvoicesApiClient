alter table mb_invoice_products add column extern_id bigint unsigned after id;
alter table mb_invoices add column extern_id bigint unsigned after invoice_id;