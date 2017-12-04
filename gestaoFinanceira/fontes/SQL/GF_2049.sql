
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
* Versao 2.04.9
*
* Fabio Bertoldi - 20160415
*
*/

----------------
-- Ticket #23586
----------------

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
SELECT 3112
     , 209
     , 'FLRelatorioEmpenhoModalidade.php'
     , 'imprimir'
     , 17
     , ''
     , 'Empenhos por Modalidade'
     , TRUE
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.acao
              WHERE cod_acao = 3112
           );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
SELECT 2
     , 10
     , 13
     , 'Empenhos por Modalidade'
     , 'LHEmpenhoModalidade.php'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.relatorio
              WHERE cod_gestao    = 2
                AND cod_modulo    = 10
                AND cod_relatorio = 13
           );


----------------
-- Ticket #23548
----------------

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
SELECT 3100
     , 209
     , 'FLResumoExecucaoRP.php'
     , 'imprimir'
     , 16
     , ''
     , 'Resumo Execução de Restos a Pagar'
     , TRUE
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.acao
              WHERE cod_acao           = 3100
                AND cod_funcionalidade = 209
           );


----------------
-- Ticket #23662
----------------

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
     ( 3113
     , 434
     , 'FMManterMetasFisicas.php'
     , 'manter'
     , 5
     , ''
     , 'Lançar Metas Físicas Realizadas'
     , TRUE
     );

CREATE TABLE ppa.acao_meta_fisica_realizada(
    cod_acao                INTEGER             NOT NULL,
    timestamp_acao_dados    TIMESTAMP           NOT NULL,
    cod_recurso             INTEGER             NOT NULL,
    exercicio_recurso       CHAR(4)             NOT NULL,
    ano                     CHAR(1)             NOT NULL,
    valor                   NUMERIC(14,2)       NOT NULL,
    justificativa           VARCHAR(255)                ,
    CONSTRAINT pk_acao_meta_fisica_realizada    PRIMARY KEY                 (cod_acao, timestamp_acao_dados, cod_recurso, exercicio_recurso, ano),
    CONSTRAINT fk_acao_meta_fisica_realizada_1  FOREIGN KEY                 (cod_acao, timestamp_acao_dados, cod_recurso, exercicio_recurso, ano)
                                                REFERENCES ppa.acao_recurso (cod_acao, timestamp_acao_dados, cod_recurso, exercicio_recurso, ano)
);
GRANT ALL ON ppa.acao_meta_fisica_realizada TO urbem;


