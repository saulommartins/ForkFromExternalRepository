/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - SoluÃ§Ãµes em GestÃ£o PÃºblica                                *
    * @copyright (c) 2013 ConfederaÃ§Ã£o Nacional de MunicÃ­pos                         *
    * @author ConfederaÃ§Ã£o Nacional de MunicÃ­pios                                    *
    *                                                                                *
    * O URBEM CNM Ã© um software livre; vocÃª pode redistribuÃ­-lo e/ou modificÃ¡-lo sob *
    * os  termos  da LicenÃ§a PÃºblica Geral GNU conforme  publicada  pela FundaÃ§Ã£o do *
    * Software Livre (FSF - Free Software Foundation); na versÃ£o 2 da LicenÃ§a.       *
    *                                                                                *
    * Este  programa  Ã©  distribuÃ­do  na  expectativa  de  que  seja  Ãºtil,   porÃ©m, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implÃ­cita  de  COMERCIABILIDADE  OU *
    * ADEQUAÃÃO A UMA FINALIDADE ESPECÃFICA. Consulte a LicenÃ§a PÃºblica Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * VocÃª deve ter recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral do GNU "LICENCA.txt" *
    * com  este  programa; se nÃ£o, escreva para  a  Free  Software Foundation  Inc., *
    * no endereÃ§o 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/*
*
* Script de DDL e DML
*
* Versao 2.04.3
*
* Fabio Bertoldi - 20150915
*
*/

----------------
-- Ticket #23255
----------------

ALTER TABLE sw_nome_logradouro ADD COLUMN dt_inicio DATE;
ALTER TABLE sw_nome_logradouro ADD COLUMN dt_fim    DATE;

ALTER TABLE sw_nome_logradouro ADD   COLUMN cod_norma INTEGER;
UPDATE      sw_nome_logradouro SET          cod_norma = 0;
ALTER TABLE sw_nome_logradouro ALTER COLUMN cod_norma SET NOT NULL;
ALTER TABLE sw_nome_logradouro ADD CONSTRAINT fk_nome_logradouro_3 FOREIGN KEY             (cod_norma)
                                                                   REFERENCES normas.norma (cod_norma);
ALTER TABLE sw_nome_logradouro DROP CONSTRAINT pk_nome_logradouro;
ALTER TABLE sw_nome_logradouro ADD  CONSTRAINT pk_nome_logradouro PRIMARY KEY (cod_logradouro, timestamp, nom_logradouro);
