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
* Versao 2.02.7
*
* Fabio Bertoldi - 20140422
*
*/

----------------
-- Ticket #20717
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
     ( 2966
     , 480
     , 'FLManterConfiguracaoOcorrenciaFuncional.php'
     , 'incluir'
     , 11
     , ''
     , 'Relacionar Ocorrências Funcionais'
     , TRUE
     );


ALTER TABLE tceal.ocorrencia_funcional_assentamento DROP CONSTRAINT fk_tceal_ocorrencia_funcional_assentamento_1;
ALTER TABLE tceal.ocorrencia_funcional              DROP CONSTRAINT pk_tceal_ocorrencia;

ALTER TABLE tceal.ocorrencia_funcional              ADD  CONSTRAINT pk_tceal_ocorrencia PRIMARY KEY (cod_ocorrencia, exercicio);
ALTER TABLE tceal.ocorrencia_funcional_assentamento ADD  CONSTRAINT fk_tceal_ocorrencia_funcional_assentamento_1 FOREIGN KEY (cod_ocorrencia, exercicio)

                                                                                                                 REFERENCES tceal.ocorrencia_funcional (cod_ocorrencia, exercicio);

INSERT
  INTO tceal.ocorrencia_funcional
     ( cod_ocorrencia
     , descricao
     , exercicio
     )
SELECT cod_ocorrencia
     , descricao
     , '2014' AS exercicio
  FROM tceal.ocorrencia_funcional
 WHERE exercicio = '2013'
     ;

----------------
-- Ticket #21872
----------------

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_config_cod_norma'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_config_cod_norma'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_config_complementacao_loa'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_config_complementacao_loa'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_config_credito_adicional'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_config_credito_adicional'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_config_credito_antecipacao'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_config_credito_antecipacao'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_config_credito_interno'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_config_credito_interno'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_config_credito_externo'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_config_credito_externo'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_config_metas_receitas_anuais'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_config_metas_receitas_anuais'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_config_receitas_primarias'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_config_receitas_primarias'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_config_metas_despesas_anuais'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_config_metas_despesas_anuais'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_config_despesas_primarias'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_config_despesas_primarias'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_config_resultado_primario'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_config_resultado_primario'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_config_resultado_nominal'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_config_resultado_nominal'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_config_divida_publica_consolidada'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_config_divida_publica_consolidada'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_config_divida_publica_liquida'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_config_divida_publica_liquida'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_orgao_prefeitura'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_orgao_prefeitura'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_unidade_prefeitura'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_unidade_prefeitura'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_orgao_camara'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_orgao_camara'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_unidade_camara'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_unidade_camara'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_orgao_rpps'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_orgao_rpps'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_unidade_rpps'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_unidade_rpps'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_orgao_outros'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_orgao_outros'
           )
     ;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
SELECT '2014'
     , 62
     , 'tceal_unidade_outros'
     , ''
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.configuracao
              WHERE exercicio = '2014'
                AND cod_modulo = 62
                AND parametro = 'tceal_unidade_outros'
           )
     ;


CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL       VARCHAR;
    reRecord    RECORD;
BEGIN
    stSQL := '
                 SELECT cod_entidade
                   FROM orcamento.entidade
               GROUP BY cod_entidade
                      ;
             ';
    FOR reRecord IN EXECUTE stSQL LOOP
        INSERT
          INTO administracao.configuracao_entidade
             ( exercicio
             , cod_entidade
             , cod_modulo
             , parametro
             , valor
             )
        SELECT '2014'
             , reRecord.cod_entidade
             , 62
             , 'tceal_configuracao_unidade_autonoma'
             , ''
         WHERE 0 = (
                     SELECT COUNT(1)
                       FROM administracao.configuracao_entidade
                      WHERE exercicio    = '2014'
                        AND cod_entidade = reRecord.cod_entidade
                        AND cod_modulo   = 62
                        AND parametro    = 'tceal_configuracao_unidade_autonoma'
                   )
             ;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #21871
----------------

CREATE TABLE tcemg.empenho_registro_precos (
    cod_entidade            INTEGER     NOT NULL,
    numero_processo_adesao  INTEGER     NOT NULL,
    exercicio_adesao        CHAR(4)     NOT NULL,
    cod_empenho             INTEGER     NOT NULL,
    exercicio_empenho       CHAR(4)     NOT NULL,
    CONSTRAINT pk_empenho_registro_de_precos    PRIMARY KEY (cod_entidade, numero_processo_adesao, exercicio_adesao, cod_empenho, exercicio_empenho),
    CONSTRAINT fk_empenho_registro_de_precos_1  FOREIGN KEY                (cod_entidade, cod_empenho, exercicio_empenho)
                                                REFERENCES empenho.empenho (cod_entidade, cod_empenho, exercicio)
);
GRANT ALL ON tcemg.empenho_registro_precos TO urbem;


----------------
-- Ticket #21717
----------------

UPDATE administracao.acao
   SET nom_arquivo = 'FLManterConfiguracaoOcorrenciaFuncional.php'
 WHERE cod_acao    = 2966
     ;


----------------
-- Ticket #21804
----------------

ALTER TABLE tcemg.configuracao_orgao DROP CONSTRAINT pk_configuracao_orgao;

ALTER TABLE tcemg.configuracao_orgao ADD CONSTRAINT pk_configuracao_orgao PRIMARY KEY (cod_entidade, exercicio, num_cgm, tipo_responsavel);

