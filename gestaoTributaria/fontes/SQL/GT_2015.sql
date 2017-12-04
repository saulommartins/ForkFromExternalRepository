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
* Versao 2.01.5
*
* Fabio Bertoldi - 20130427
*
*/

----------------
-- Ticket #20092
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2013'
        AND parametro  = 'cnpj'
        AND valor      = '13805528000180'
          ;
    IF FOUND THEN
        INSERT INTO arrecadacao.modelo_carne (cod_modelo, nom_modelo, nom_arquivo, cod_modulo, capa_primeira_folha) VALUES ((SELECT COALESCE(MAX(cod_modelo)+1,1) FROM arrecadacao.modelo_carne), 'Carne Divida 2013', 'RCarneDividaMataSaoJoao2013.class.php' ,33 , FALSE);
        INSERT INTO arrecadacao.acao_modelo_carne (cod_modelo, cod_acao) VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne), 1849);
        INSERT INTO arrecadacao.acao_modelo_carne (cod_modelo, cod_acao) VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne), 1755 );
        INSERT INTO arrecadacao.acao_modelo_carne (cod_modelo, cod_acao) VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne), 1648 );
        
        INSERT INTO arrecadacao.modelo_carne (cod_modelo, nom_modelo, nom_arquivo, cod_modulo, capa_primeira_folha) VALUES ((SELECT COALESCE(MAX(cod_modelo)+1,1) FROM arrecadacao.modelo_carne), 'Carne Divida Ativa Refis 2013', 'RCarneDividaRefis2013MataSaoJoao.class.php' ,33 , FALSE);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne), 1849);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne), 1755 );
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne), 1648 );
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();
