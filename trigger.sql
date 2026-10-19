DELIMITER $$

CREATE TRIGGER trg_demande_essai_after_update
AFTER UPDATE ON demande_essai
FOR EACH ROW
BEGIN
    IF NOT (OLD.statut <=> NEW.statut) THEN
        INSERT INTO demande_essai_logs
            (id_demande, id_admin, action, ancienne_valeur, nouvelle_valeur, date_action)
        VALUES (
            NEW.id_demande, COALESCE(@admin_id, NULL),
            CONCAT('statut_', NEW.statut),
            OLD.statut, NEW.statut, NOW()
        );
    END IF;

    IF NOT (OLD.date_souhaitee <=> NEW.date_souhaitee)
       OR NOT (OLD.heure_souhaitee <=> NEW.heure_souhaitee) THEN
        INSERT INTO demande_essai_logs
            (id_demande, id_admin, action, ancienne_valeur, nouvelle_valeur, date_action)
        VALUES (
            NEW.id_demande, COALESCE(@admin_id, NULL),
            'modifier_date',
            CONCAT(COALESCE(OLD.date_souhaitee,'—'), ' ', COALESCE(OLD.heure_souhaitee,'')),
            CONCAT(COALESCE(NEW.date_souhaitee,'—'), ' ', COALESCE(NEW.heure_souhaitee,'')),
            NOW()
        );
    END IF;
END$$

DELIMITER ;