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
* Versao 2.04.6
*
* Fabio Bertoldi - 20151105
*
*/

----------------
-- Ticket #23337
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
     ( 3092
     , 56
     , 'FMManterReceitaDespesaExtraRecurso.php'
     , 'configurar'
     , 16
     , 'Configurar Rec./Desp. Extra por Recurso'
     , 'Configurar Rec./Desp. Extra por Fonte de Recurso'
     , TRUE
     );

INSERT
  INTO administracao.configuracao
     ( cod_modulo
     , exercicio
     , parametro
     , valor
     )
VALUES
     ( 9
     , '2015'
     , 'indicador_contas_extras_recurso'
     , 'f'
     );

CREATE TABLE contabilidade.configuracao_contas_extras (
    exercicio   VARCHAR(4)    NOT NULL,
    cod_conta   INTEGER       NOT NULL,
    CONSTRAINT pk_configuracao_contas_extras   PRIMARY KEY (exercicio, cod_conta),
    CONSTRAINT fk_configuracao_contas_extras_1 FOREIGN KEY (exercicio, cod_conta)
                                               REFERENCES contabilidade.plano_conta (exercicio, cod_conta)
);
GRANT ALL ON contabilidade.configuracao_contas_extras TO urbem;


----------------
-- Ticket #
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_tables
      WHERE schemaname = 'contabilidade'
        AND tablename  = 'valor_lancamento_recurso'
          ;
    IF NOT FOUND THEN
        CREATE TABLE contabilidade.valor_lancamento_recurso (
            cod_lote       INTEGER       NOT NULL,
            tipo           VARCHAR(1)    NOT NULL,
            sequencia      INTEGER       NOT NULL,
            exercicio      VARCHAR(4)    NOT NULL,
            tipo_valor     VARCHAR(1)    NOT NULL,
            cod_entidade   INTEGER       NOT NULL,
            cod_recurso    INTEGER       NOT NULL,
            vl_recurso     NUMERIC(14,2) NOT NULL,
            CONSTRAINT pk_valor_lancamento_recurso   PRIMARY KEY                              (exercicio, cod_entidade, tipo, cod_lote, sequencia, tipo_valor, cod_recurso),
            CONSTRAINT fk_valor_lancamento_recurso_1 FOREIGN KEY                              (exercicio, cod_entidade, tipo, cod_lote, sequencia, tipo_valor)
                                                     REFERENCES contabilidade.valor_lancamento(exercicio, cod_entidade, tipo, cod_lote, sequencia, tipo_valor),
            CONSTRAINT fk_valor_lancamento_recurso_2 FOREIGN KEY                              (exercicio, cod_recurso)
                                                     REFERENCES orcamento.recurso             (exercicio, cod_recurso)
        );
        GRANT ALL ON contabilidade.valor_lancamento_recurso TO urbem;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #23494
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2016'
        AND parametro  = 'cnpj'
        AND valor      = '12028813000179'
          ;
    IF FOUND THEN
        INSERT
          INTO orcamento.recurso_direto
             ( exercicio
             , cod_recurso
             , nom_recurso
             , finalidade
             , tipo
             , cod_fonte
             , codigo_tc
             , cod_tipo_esfera
             )
        VALUES
             ( '2015'
             , 4013
             , 'Convênio INCRA-Infraestrutura'
             , 'Recuperação de Infraestrutura Básica em Projetos de Assentamentos'
             , 'V'
             , 1
             , NULL
             , 1
             );

        DELETE FROM orcamento.previsao_receita WHERE exercicio = '2015' AND cod_receita = 14;
        DELETE FROM contabilidade.desdobramento_receita WHERE exercicio = '2015' AND cod_receita_principal = 14;
        DELETE FROM orcamento.receita WHERE cod_receita = 14  AND exercicio = '2015';
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();
