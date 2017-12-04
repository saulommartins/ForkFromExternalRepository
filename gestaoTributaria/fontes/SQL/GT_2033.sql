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
* Versao 2.03.3
*
* Fabio Bertoldi - 20141125
*
*/

----------------
-- Ticket #22008
----------------

UPDATE administracao.acao SET ativo = FALSE where cod_acao = 1086;



CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio  = '2014'
        AND cod_modulo = 2
        AND parametro  = 'cnpj'
        AND valor      = '13805528000180'
          ;
    IF FOUND THEN

        ----------------
        -- Ticket #22485
        ----------------
        INSERT INTO arrecadacao.modelo_carne        VALUES ((SELECT MAX(cod_modelo)+1 FROM arrecadacao.modelo_carne), 'Carnê T.F.F. 2015', 'RCarneTFFMataSaoJoao2015.class.php', 14, FALSE);
        INSERT INTO arrecadacao.acao_modelo_carne   VALUES ((SELECT MAX(cod_modelo)   FROM arrecadacao.modelo_carne), 963 ); 
        INSERT INTO arrecadacao.acao_modelo_carne   VALUES ((SELECT MAX(cod_modelo)   FROM arrecadacao.modelo_carne), 964 ); 
        INSERT INTO arrecadacao.acao_modelo_carne   VALUES ((SELECT MAX(cod_modelo)   FROM arrecadacao.modelo_carne), 978 ); 
        INSERT INTO arrecadacao.acao_modelo_carne   VALUES ((SELECT MAX(cod_modelo)   FROM arrecadacao.modelo_carne), 979 ); 
        
        ----------------
        -- Ticket #22485
        ----------------
        INSERT INTO arrecadacao.modelo_carne        VALUES ((SELECT MAX(cod_modelo)+1 FROM arrecadacao.modelo_carne), 'Carne I.P.T.U. 2015', 'RCarneIPTUMataSaoJoao2015.class.php', 12, FALSE);
        INSERT INTO arrecadacao.acao_modelo_carne   VALUES ((SELECT MAX(cod_modelo)   FROM arrecadacao.modelo_carne), 963 ); 
        INSERT INTO arrecadacao.acao_modelo_carne   VALUES ((SELECT MAX(cod_modelo)   FROM arrecadacao.modelo_carne), 964 ); 
        INSERT INTO arrecadacao.acao_modelo_carne   VALUES ((SELECT MAX(cod_modelo)   FROM arrecadacao.modelo_carne), 978 ); 
        INSERT INTO arrecadacao.acao_modelo_carne   VALUES ((SELECT MAX(cod_modelo)   FROM arrecadacao.modelo_carne), 979 ); 

    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

