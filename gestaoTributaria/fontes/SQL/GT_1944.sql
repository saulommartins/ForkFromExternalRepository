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
* $Id: GT_1944.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.94.4
*/

----------------
-- Ticket #14051
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stAux   VARCHAR;
BEGIN

    SELECT valor
      INTO stAux
      FROM administracao.configuracao
     WHERE cod_modulo = 2
       AND exercicio  = '2008'
       AND parametro  = 'cnpj'
       AND valor      = '13805528000180';

    IF FOUND THEN
        INSERT INTO arrecadacao.calculo_cgm (cod_calculo, numcgm) values( 500517,3186);
        INSERT INTO arrecadacao.calculo_cgm (cod_calculo, numcgm) values( 500518,3186);
        INSERT INTO arrecadacao.calculo_cgm (cod_calculo, numcgm) values( 500519,3186);
        INSERT INTO arrecadacao.calculo_cgm (cod_calculo, numcgm) values( 500520,3186);
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION Manutencao();


CREATE OR REPLACE FUNCTION manutencao_doc() RETURNS VOID AS $$
DECLARE
    stAux           VARCHAR;
    inCodDOcumento  INTEGER;
BEGIN

    SELECT valor
      INTO stAux
      FROM administracao.configuracao
     WHERE cod_modulo = 2
       AND exercicio  = '2008'
       AND parametro  = 'cnpj'
       AND valor      <> '08924078000104';

    IF FOUND THEN

        DELETE
          FROM administracao.modelo_arquivos_documento
         WHERE cod_arquivo   = (
                                    SELECT cod_arquivo
                                      FROM administracao.arquivos_documento
                                     WHERE nome_arquivo_swx = 'notificacaoCobrancaUirauna.odt'
                               )
           AND cod_documento = (
                                    SELECT cod_documento
                                      FROM administracao.modelo_documento
                                     WHERE nome_documento   = 'Notificação Cobrança'
                                       AND nome_arquivo_agt = 'notificacaoAcordo.agt'
                               );

        DELETE
          FROM administracao.arquivos_documento
         WHERE nome_arquivo_swx = 'notificacaoCobrancaUirauna.odt';

        DELETE
          FROM administracao.modelo_documento
         WHERE nome_documento   = 'Notificação Cobrança'
           AND nome_arquivo_agt = 'notificacaoAcordo.agt';

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao_doc();
DROP FUNCTION manutencao_doc();
