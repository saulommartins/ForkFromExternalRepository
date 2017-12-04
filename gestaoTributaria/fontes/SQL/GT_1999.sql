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
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id:$
*
* Versão 1.99.9
*/

----------------
-- Ticket #17074
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM administracao.configuracao
      WHERE parametro  = 'cnpj'
        AND exercicio  = '2011'
        AND cod_modulo = 2
        AND valor      = '13805528000180'
          ;

    IF FOUND THEN

        INSERT INTO arrecadacao.modelo_carne        VALUES ((SELECT MAX(cod_modelo) + 1 FROM arrecadacao.modelo_carne), 'Carne I.S.S. 2011', 'RCarneISSMataSaoJoao2011.class.php', 14, FALSE);
        INSERT INTO arrecadacao.acao_modelo_carne   VALUES ((SELECT MAX(cod_modelo)     FROM arrecadacao.modelo_carne), 963 );
        INSERT INTO arrecadacao.acao_modelo_carne   VALUES ((SELECT MAX(cod_modelo)     FROM arrecadacao.modelo_carne), 964 );
        INSERT INTO arrecadacao.acao_modelo_carne   VALUES ((SELECT MAX(cod_modelo)     FROM arrecadacao.modelo_carne), 978 );
        INSERT INTO arrecadacao.acao_modelo_carne   VALUES ((SELECT MAX(cod_modelo)     FROM arrecadacao.modelo_carne), 979 );
        INSERT INTO arrecadacao.acao_modelo_carne   VALUES ((SELECT MAX(cod_modelo)     FROM arrecadacao.modelo_carne), 1677);
        INSERT INTO arrecadacao.acao_modelo_carne   VALUES ((SELECT MAX(cod_modelo)     FROM arrecadacao.modelo_carne), 1678);

    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();

