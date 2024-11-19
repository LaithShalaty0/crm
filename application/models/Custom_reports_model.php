<?php
class Custom_reports_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_report_data($from_date, $to_date)
    {
        $sql = "
            SELECT
                COUNT(DISTINCT CASE WHEN cfv.fieldid = 25 AND cfv.value IS NOT NULL THEN l.id END) AS leads_logged_in,
                COUNT(DISTINCT CASE WHEN ls.name = 'Exhibition' THEN l.id END) AS leads_from_exhibition,
                COUNT(DISTINCT CASE WHEN ls2.name = 'Canceled' THEN l.id END) AS leads_canceled,
                COUNT(DISTINCT CASE WHEN ls2.name = 'Under Processing' THEN l.id END) AS leads_under_processing,
                COUNT(DISTINCT CASE WHEN cfv2.fieldid = 27 AND cfv2.value IS NOT NULL THEN l.id END) AS leads_with_lack_of_interest,
                SUM(CASE WHEN cfv2.value = 'The price is not suitable' THEN 1 ELSE 0 END) AS lack_of_interest_price,
                SUM(CASE WHEN cfv2.value = 'City is not suitable' THEN 1 ELSE 0 END) AS lack_of_interest_city,
                SUM(CASE WHEN cfv2.value = 'Area is not suitable' THEN 1 ELSE 0 END) AS lack_of_interest_area,
                SUM(CASE WHEN cfv2.value = 'Are not interested in this area' THEN 1 ELSE 0 END) AS lack_of_interest_not_interested_in_area,
                SUM(CASE WHEN cfv2.value = 'Do not like the design' THEN 1 ELSE 0 END) AS lack_of_interest_design,
                SUM(CASE WHEN cfv2.value = 'Other' THEN 1 ELSE 0 END) AS lack_of_interest_other,
                COUNT(DISTINCT CASE WHEN cfv3.fieldid = 30 AND cfv3.value IS NOT NULL THEN l.id END) AS not_qualified_leads,
                SUM(CASE WHEN cfv3.value = 'There is no financial capacity' THEN 1 ELSE 0 END) AS not_qualified_financial_capacity,
                SUM(CASE WHEN cfv3.value = 'Not eligible' THEN 1 ELSE 0 END) AS not_qualified_not_eligible,
                SUM(CASE WHEN cfv3.value = 'Has benefited from support' THEN 1 ELSE 0 END) AS not_qualified_benefited_support
            FROM tblleads l
            LEFT JOIN tblleads_sources ls ON l.source = ls.id
            LEFT JOIN tblleads_status ls2 ON l.status = ls2.id
            LEFT JOIN tblcustomfieldsvalues cfv ON l.id = cfv.relid AND cfv.fieldto = 'leads' AND cfv.fieldid = 25
            LEFT JOIN tblcustomfieldsvalues cfv2 ON l.id = cfv2.relid AND cfv2.fieldto = 'leads' AND cfv2.fieldid = 27
            LEFT JOIN tblcustomfieldsvalues cfv3 ON l.id = cfv3.relid AND cfv3.fieldto = 'leads' AND cfv3.fieldid = 30
            WHERE l.dateadded BETWEEN ? AND ?
        ";

        $query = $this->db->query($sql, array($from_date, $to_date));
        return $query->row_array();
    }

    public function get_cumulative_report_data()
    {
        $sql = "
            SELECT
                COUNT(DISTINCT CASE WHEN cfv.fieldid = 25 AND cfv.value IS NOT NULL THEN l.id END) AS leads_logged_in,
                COUNT(DISTINCT CASE WHEN ls.name = 'Exhibition' THEN l.id END) AS leads_from_exhibition,
                COUNT(DISTINCT CASE WHEN ls2.name = 'Canceled' THEN l.id END) AS leads_canceled,
                COUNT(DISTINCT CASE WHEN ls2.name = 'Under Processing' THEN l.id END) AS leads_under_processing,
                COUNT(DISTINCT CASE WHEN cfv2.fieldid = 27 AND cfv2.value IS NOT NULL THEN l.id END) AS leads_with_lack_of_interest,
                SUM(CASE WHEN cfv2.value = 'The price is not suitable' THEN 1 ELSE 0 END) AS lack_of_interest_price,
                SUM(CASE WHEN cfv2.value = 'City is not suitable' THEN 1 ELSE 0 END) AS lack_of_interest_city,
                SUM(CASE WHEN cfv2.value = 'Area is not suitable' THEN 1 ELSE 0 END) AS lack_of_interest_area,
                SUM(CASE WHEN cfv2.value = 'Are not interested in this area' THEN 1 ELSE 0 END) AS lack_of_interest_not_interested_in_area,
                SUM(CASE WHEN cfv2.value = 'Do not like the design' THEN 1 ELSE 0 END) AS lack_of_interest_design,
                SUM(CASE WHEN cfv2.value = 'Other' THEN 1 ELSE 0 END) AS lack_of_interest_other,
                COUNT(DISTINCT CASE WHEN cfv3.fieldid = 30 AND cfv3.value IS NOT NULL THEN l.id END) AS not_qualified_leads,
                SUM(CASE WHEN cfv3.value = 'There is no financial capacity' THEN 1 ELSE 0 END) AS not_qualified_financial_capacity,
                SUM(CASE WHEN cfv3.value = 'Not eligible' THEN 1 ELSE 0 END) AS not_qualified_not_eligible,
                SUM(CASE WHEN cfv3.value = 'Has benefited from support' THEN 1 ELSE 0 END) AS not_qualified_benefited_support
            FROM tblleads l
            LEFT JOIN tblleads_sources ls ON l.source = ls.id
            LEFT JOIN tblleads_status ls2 ON l.status = ls2.id
            LEFT JOIN tblcustomfieldsvalues cfv ON l.id = cfv.relid AND cfv.fieldto = 'leads' AND cfv.fieldid = 25
            LEFT JOIN tblcustomfieldsvalues cfv2 ON l.id = cfv2.relid AND cfv2.fieldto = 'leads' AND cfv2.fieldid = 27
            LEFT JOIN tblcustomfieldsvalues cfv3 ON l.id = cfv3.relid AND cfv3.fieldto = 'leads' AND cfv3.fieldid = 30
        ";

        $query = $this->db->query($sql);
        return $query->row_array();
    }

    // public function get_stuff_report($from_date, $to_date)
    // {
    //     $sql = "
    //         SELECT s.staffid, s.staff_name,
    //             COALESCE(signin.signin_count, 0) AS signin_count,
    //             COALESCE(not_eligible.not_eligible_count, 0) AS not_eligible_count,
    //             COALESCE(no_financial_capacity.no_financial_capacity_count, 0) AS no_financial_capacity_count,
    //             '-' AS signin_capacity,
    //             COALESCE(payment_without_cancellations.payment_without_cancellations, 0) AS payment_without_cancellations,
    //             COALESCE(cancellations_after_payment.cancellations_after_payment, 0) AS cancellations_after_payment,
    //             COALESCE(contracts.contracts, 0) AS contracts,
    //             COALESCE(proposals.proposal_count, 0) AS proposal_count,
    //             COALESCE(added_contracts.contract_count, 0) AS contract_count
    //         FROM (
    //             SELECT staffid, CONCAT(firstname, ' ', lastname) AS staff_name 
    //             FROM tblstaff 
    //             WHERE active = 1
    //         ) s
    //         LEFT JOIN (
    //             SELECT assigned, COUNT(*) AS signin_count 
    //             FROM tblleads 
    //             LEFT JOIN tblcustomfieldsvalues cfv ON tblleads.id = cfv.relid 
    //             LEFT JOIN tblcustomfields cf ON cfv.fieldid = cf.id 
    //             WHERE cf.slug = 'leads_signing_in_date_time' AND cfv.value IS NOT NULL 
    //                 AND DATE(tblleads.dateadded) BETWEEN ? AND ?
    //             GROUP BY assigned
    //         ) signin ON s.staffid = signin.assigned
    //         LEFT JOIN (
    //             SELECT assigned, COUNT(*) AS not_eligible_count 
    //             FROM tblleads 
    //             LEFT JOIN tblcustomfieldsvalues cfv ON tblleads.id = cfv.relid 
    //             LEFT JOIN tblcustomfields cf ON cfv.fieldid = cf.id 
    //             WHERE cf.slug = 'leads_reason_why_not_qualified' AND cfv.value = 'Not eligible' 
    //                 AND DATE(tblleads.dateadded) BETWEEN ? AND ?
    //             GROUP BY assigned
    //         ) not_eligible ON s.staffid = not_eligible.assigned
    //         LEFT JOIN (
    //             SELECT assigned, COUNT(*) AS no_financial_capacity_count 
    //             FROM tblleads 
    //             LEFT JOIN tblcustomfieldsvalues cfv ON tblleads.id = cfv.relid 
    //             LEFT JOIN tblcustomfields cf ON cfv.fieldid = cf.id 
    //             WHERE cf.slug = 'leads_reason_why_not_qualified' AND cfv.value = 'There is no financial capacity' 
    //                 AND DATE(tblleads.dateadded) BETWEEN ? AND ?
    //             GROUP BY assigned
    //         ) no_financial_capacity ON s.staffid = no_financial_capacity.assigned
    //         LEFT JOIN (
    //             SELECT assigned, COUNT(*) AS payment_without_cancellations 
    //             FROM tblinvoices 
    //             WHERE paymentmethod != 'credit' 
    //                 AND DATE(date) BETWEEN ? AND ?
    //             GROUP BY assigned
    //         ) payment_without_cancellations ON s.staffid = payment_without_cancellations.assigned
    //         LEFT JOIN (
    //             SELECT assigned, COUNT(*) AS cancellations_after_payment 
    //             FROM tblinvoices 
    //             WHERE creditnote IS NOT NULL 
    //                 AND DATE(date) BETWEEN ? AND ?
    //             GROUP BY assigned
    //         ) cancellations_after_payment ON s.staffid = cancellations_after_payment.assigned
    //         LEFT JOIN (
    //             SELECT staffid, COUNT(*) AS contracts 
    //             FROM tblcontracts 
    //             WHERE DATE(datecreated) BETWEEN ? AND ?
    //             GROUP BY staffid
    //         ) contracts ON s.staffid = contracts.staffid
    //         LEFT JOIN (
    //             SELECT assigned, COUNT(*) AS proposal_count 
    //             FROM tblproposals 
    //             WHERE DATE(datecreated) BETWEEN ? AND ?
    //             GROUP BY assigned
    //         ) proposals ON s.staffid = proposals.assigned
    //         LEFT JOIN (
    //             SELECT staff_id AS assigned, COUNT(*) AS contract_count 
    //             FROM tblcontracts 
    //             WHERE DATE(datecreated) BETWEEN ? AND ?
    //             GROUP BY staff_id
    //         ) added_contracts ON s.staffid = added_contracts.assigned
    //     ";
    
    //     // Parameters for the query
    //     $params = array(
    //         $from_date, $to_date, // for signin_count
    //         $from_date, $to_date, // for not_eligible_count
    //         $from_date, $to_date, // for no_financial_capacity_count
    //         $from_date, $to_date, // for payment_without_cancellations
    //         $from_date, $to_date, // for cancellations_after_payment
    //         $from_date, $to_date, // for contracts
    //         $from_date, $to_date, // for proposal_count
    //         $from_date, $to_date  // for contract_count
    //     );
    
    //     // Execute the query with parameters
    //     $query = $this->db->query($sql, $params);
    
    //     // Return the result set
    //     return $query->result_array();
    // }
    

    public function get_cumulative_stuff_report()
    {
        $sql = "
            SELECT s.staffid, s.staff_name,
                COALESCE(signin.signin_count, 0) AS signin_count,
                COALESCE(not_eligible.not_eligible_count, 0) AS not_eligible_count,
                COALESCE(no_financial_capacity.no_financial_capacity_count, 0) AS no_financial_capacity_count,
                '-' AS signin_capacity,
                COALESCE(payment_without_cancellations.payment_without_cancellations, 0) AS payment_without_cancellations,
                COALESCE(cancellations_after_payment.cancellations_after_payment, 0) AS cancellations_after_payment,
                COALESCE(contracts.contracts, 0) AS contracts,
                COALESCE(proposals.proposal_count, 0) AS proposal_count,
                COALESCE(added_contracts.contract_count, 0) AS contract_count
            FROM (
                SELECT staffid, CONCAT(firstname, ' ', lastname) AS staff_name 
                FROM tblstaff 
                WHERE active = 1
            ) s
            LEFT JOIN (
                SELECT assigned, COUNT(*) AS signin_count 
                FROM tblleads 
                LEFT JOIN tblcustomfieldsvalues cfv ON tblleads.id = cfv.relid 
                LEFT JOIN tblcustomfields cf ON cfv.fieldid = cf.id 
                WHERE cf.slug = 'leads_signing_in_date_time' AND cfv.value IS NOT NULL 
                GROUP BY assigned
            ) signin ON s.staffid = signin.assigned
            LEFT JOIN (
                SELECT assigned, COUNT(*) AS not_eligible_count 
                FROM tblleads 
                LEFT JOIN tblcustomfieldsvalues cfv ON tblleads.id = cfv.relid 
                LEFT JOIN tblcustomfields cf ON cfv.fieldid = cf.id 
                WHERE cf.slug = 'leads_reason_why_not_qualified' AND cfv.value = 'Not eligible' 
                GROUP BY assigned
            ) not_eligible ON s.staffid = not_eligible.assigned
            LEFT JOIN (
                SELECT assigned, COUNT(*) AS no_financial_capacity_count 
                FROM tblleads 
                LEFT JOIN tblcustomfieldsvalues cfv ON tblleads.id = cfv.relid 
                LEFT JOIN tblcustomfields cf ON cfv.fieldid = cf.id 
                WHERE cf.slug = 'leads_reason_why_not_qualified' AND cfv.value = 'There is no financial capacity' 
                GROUP BY assigned
            ) no_financial_capacity ON s.staffid = no_financial_capacity.assigned
            LEFT JOIN (
                SELECT assigned, COUNT(*) AS payment_without_cancellations 
                FROM tblleads l
                LEFT JOIN tblinvoices i ON l.id = i.clientid 
                LEFT JOIN tblcustomfieldsvalues cfv ON l.id = cfv.relid 
                LEFT JOIN tblcustomfields cf ON cfv.fieldid = cf.id 
                WHERE cf.slug = 'leads_reason_for_logging_out' AND cfv.value IS NULL 
                GROUP BY l.assigned
            ) payment_without_cancellations ON s.staffid = payment_without_cancellations.assigned
            LEFT JOIN (
                SELECT assigned, COUNT(*) AS cancellations_after_payment 
                FROM tblleads l
                LEFT JOIN tblinvoices i ON l.id = i.clientid 
                LEFT JOIN tblcustomfieldsvalues cfv ON l.id = cfv.relid 
                LEFT JOIN tblcustomfields cf ON cfv.fieldid = cf.id 
                WHERE cf.slug = 'leads_reason_for_logging_out' AND cfv.value IS NOT NULL 
                GROUP BY l.assigned
            ) cancellations_after_payment ON s.staffid = cancellations_after_payment.assigned
            LEFT JOIN (
                SELECT addedfrom, COUNT(*) AS contracts 
                FROM tblcontracts 
                GROUP BY addedfrom
            ) contracts ON s.staffid = contracts.addedfrom
            LEFT JOIN (
                SELECT assigned, COUNT(*) AS proposal_count 
                FROM tblproposals 
                GROUP BY assigned
            ) proposals ON s.staffid = proposals.assigned
            LEFT JOIN (
                SELECT addedfrom, COUNT(*) AS contract_count 
                FROM tblcontracts 
                GROUP BY addedfrom
            ) added_contracts ON s.staffid = added_contracts.addedfrom;
        ";

        $query = $this->db->query($sql);
        return $query->result_array();
    }
}
