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
* Versao 2.01.0
*
* Fabio Bertoldi - 20120713
*
*/

----------------
-- Ticket #
----------------

CREATE OR REPLACE FUNCTION verifica_orcamento() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM orcamento.despesa
      WHERE exercicio = '2012'
          ;
    IF FOUND THEN
        PERFORM 1
           FROM contabilidade.tipo_transferencia
          WHERE exercicio = '2013'
              ;
        IF NOT FOUND THEN
            RAISE EXCEPTION 'É necessário gerar o exercício seguinte na elaboração do orçamento para a aplicação da versão 2.01.2!';
        END IF;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        verifica_orcamento();
DROP FUNCTION verifica_orcamento();

----------------
-- Ticket #
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio  = '2012'
        AND cod_modulo = 2
        AND parametro  = 'cnpj'
        AND valor      = '13805528000180'
          ;
    IF FOUND THEN

        INSERT
          INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao
          , ativo
          )
          VALUES
          ( 2859
          , 231
          , 'FLEmitirCarneIPTUDesoneradoMata.php'
          , 'CarneDesonerado'
          , 7
          , ''
          , 'Emissão de carnê / IPTU Desonerado'
          , TRUE
          );

        INSERT
          INTO arrecadacao.acao_modelo_carne
        SELECT cod_modelo
             , 2859 AS cod_acao
          FROM arrecadacao.acao_modelo_carne
         WHERE cod_acao = 2806
             ;

        INSERT
          INTO administracao.permissao
        SELECT numcgm
             , 2859 AS cod_acao
             , ano_exercicio
          FROM administracao.permissao
         WHERE cod_acao = 2806
             ;

        INSERT
          INTO administracao.auditoria
        SELECT numcgm
             , 2859 AS cod_acao
             , timestamp
             , transacao
             , objeto
          FROM administracao.auditoria
         WHERE cod_acao = 2806
             ;

        DELETE FROM arrecadacao.acao_modelo_carne WHERE cod_acao = 2806;
        DELETE FROM administracao.permissao       WHERE cod_acao = 2806;
        DELETE FROM administracao.auditoria       WHERE cod_acao = 2806;
        DELETE FROM administracao.acao            WHERE cod_acao = 2806;

    END IF;
END;
$$ LANGUAGE 'plpgsql';
SELECT manutencao();
DROP FUNCTION manutencao();
