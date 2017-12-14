/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/*
*
* Script de DDL e DML
*
* Versao 2.03.2
*
* Fabio Bertoldi - 20141001
*
*/

----------------
-- Ticket #22166
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSqlEnt    VARCHAR;
    stSQL       VARCHAR;
    stDelEnt    VARCHAR;
    reRecord    RECORD;
    reRecordEnt RECORD;
BEGIN
    PERFORM 1
      FROM administracao.configuracao
     WHERE exercicio  = '2014'
       AND cod_modulo = 2
       AND parametro  = 'cnpj'
       AND valor      = '13805528000180'
         ;
    IF FOUND THEN
            DELETE FROM pessoal.atributo_contrato_servidor_valor;
            DELETE FROM pessoal_3.atributo_contrato_servidor_valor;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

SELECT atualizarbanco('ALTER TABLE pessoal.atributo_contrato_servidor_valor ALTER COLUMN valor TYPE text;');
SELECT atualizarbanco('ALTER TABLE pessoal.atributo_contrato_servidor_valor DROP CONSTRAINT pk_atributo_contrato_servidor_valor;');
SELECT atualizarbanco('ALTER TABLE pessoal.atributo_contrato_servidor_valor ADD  CONSTRAINT pk_atributo_contrato_servidor_valor PRIMARY KEY (cod_contrato, cod_modulo, cod_cadastro, cod_atributo, timestamp);');

