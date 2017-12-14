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
* Versão 1.99.1
*/

----------------
-- Ticket #16863
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    inMaxModelo     INTEGER;
BEGIN

    SELECT MAX(cod_modelo) + 1
      INTO inMaxModelo
      FROM arrecadacao.modelo_carne
         ;

    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio  = '2010'
        AND cod_modulo = 2
        AND parametro  = 'cnpj'
        AND valor      = '13805528000180'
          ;

    IF FOUND THEN

        INSERT INTO arrecadacao.modelo_carne      VALUES (inMaxModelo,'Carne I.P.T.U. 2011', 'RCarneIPTUMataSaoJoao2011.class.php', null, 'f');
        INSERT INTO arrecadacao.acao_modelo_carne VALUES (inMaxModelo,963);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES (inMaxModelo,964);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES (inMaxModelo,978);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES (inMaxModelo,979);

        inMaxModelo := inMaxModelo + 1;

        INSERT INTO arrecadacao.modelo_carne      VALUES (inMaxModelo, 'Carne T.F.F. 2011', 'RCarneTFFMataSaoJoao2011.class.php', null, 'f'); 
        INSERT INTO arrecadacao.acao_modelo_carne VALUES (inMaxModelo,963);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES (inMaxModelo,964);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES (inMaxModelo,978);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES (inMaxModelo,979);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES (inMaxModelo,980);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES (inMaxModelo,962);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES (inMaxModelo,1672);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES (inMaxModelo,1677);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES (inMaxModelo,1678);

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();

