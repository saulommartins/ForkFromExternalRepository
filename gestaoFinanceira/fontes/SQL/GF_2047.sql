
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
* Versao 2.04.7
*
* Fabio Bertoldi - 20160120
*
*/

----------------
-- Ticket #23437
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
VALUES
     ( 2
     , 10
     , 11
     , 'Restos a Pagar por Credor'
     , 'LHRPCredor.php'
     );


----------------
-- Ticket #23568
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL       VARCHAR;
    reRecord    RECORD;
BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio  = '2016'
        AND cod_modulo = 2
        AND parametro  = 'cnpj'
        AND valor      = '12198693000158'
          ;
    IF NOT FOUND THEN
        stSQL := '
                     SELECT autorizacao_empenho.exercicio
                          , autorizacao_empenho.cod_autorizacao
                          , autorizacao_empenho.cod_entidade
                          , reserva_saldos.cod_despesa
                          , reserva_saldos.cod_reserva
                          , reserva_saldos.vl_reserva
                          , SUM(item_pre_empenho.vl_total) AS total_autorizacao
                       FROM empenho.autorizacao_empenho
                       JOIN empenho.autorizacao_reserva
                         ON autorizacao_reserva.exercicio       = autorizacao_empenho.exercicio
                        AND autorizacao_reserva.cod_entidade    = autorizacao_empenho.cod_entidade
                        AND autorizacao_reserva.cod_autorizacao = autorizacao_empenho.cod_autorizacao
                       JOIN orcamento.reserva_saldos
                         ON autorizacao_reserva.cod_reserva = reserva_saldos.cod_reserva
                        AND autorizacao_reserva.exercicio   = reserva_saldos.exercicio
                       JOIN empenho.item_pre_empenho
                         ON item_pre_empenho.exercicio       = autorizacao_empenho.exercicio
                        AND item_pre_empenho.cod_pre_empenho = autorizacao_empenho.cod_pre_empenho
                      WHERE reserva_saldos.vl_reserva = 0
                        AND autorizacao_empenho.exercicio = ''2016''
                   group by autorizacao_empenho.exercicio
                          , autorizacao_empenho.cod_autorizacao
                          , autorizacao_empenho.cod_entidade
                          , reserva_saldos.cod_despesa
                          , reserva_saldos.cod_reserva
                          , reserva_saldos.vl_reserva
                   order by autorizacao_empenho.cod_entidade
                          , autorizacao_empenho.cod_autorizacao
                 ';
        FOR reRecord IN EXECUTE stSQL LOOP
            UPDATE orcamento.reserva_saldos
               SET vl_reserva = reRecord.total_autorizacao
             WHERE reserva_saldos.cod_reserva = reRecord.cod_reserva
               AND reserva_saldos.exercicio   = reRecord.exercicio
                 ;
        END LOOP;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #23555
----------------

CREATE TABLE tcers.nota_fiscal(
    exercicio       CHAR(4)     NOT NULL,
    cod_nota        INTEGER     NOT NULL,
    cod_entidade    INTEGER     NOT NULL,
    nro_nota        VARCHAR(20) NOT NULL,
    nro_serie       VARCHAR(8)  NOT NULL,
    data_emissao    DATE        NOT NULL,
    CONSTRAINT pk_nota_fiscal   PRIMARY KEY                         (exercicio, cod_nota, cod_entidade),
    CONSTRAINT fk_nota_fiscal_1 FOREIGN KEY                         (exercicio, cod_nota, cod_entidade)
                                REFERENCES empenho.nota_liquidacao  (exercicio, cod_nota, cod_entidade)
);
GRANT ALL ON tcers.nota_fiscal TO GROUP urbem;


-------------------------------------------------------------------------------------
-- MANUTENCAO PARA CRIACAO DE RECURSOS COM cod_fonte EM BRANCO (2016 e 2017 - FRANVER
-------------------------------------------------------------------------------------

INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2017',8,'masc_recurso'      , '999'  );
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2017',8,'recurso_destinacao', 'false');

UPDATE orcamento.recurso
   SET cod_fonte = (
                     SELECT sw_fn_mascara_dinamica(
                                                    (SELECT valor
                                                       FROM administracao.configuracao
                                                      WHERE parametro  = 'masc_recurso'
                                                        AND exercicio  = '2016'
                                                        AND cod_modulo = 8
                                                    )
                                                    , cod_recurso::VARCHAR
                                                  )
                   )
 WHERE cod_fonte = ''
   AND exercicio = '2016'
     ;

UPDATE orcamento.recurso
   SET cod_fonte = (
                     SELECT sw_fn_mascara_dinamica(
                                                    (SELECT valor
                                                       FROM administracao.configuracao
                                                      WHERE parametro  = 'masc_recurso'
                                                        AND exercicio  = '2017'
                                                        AND cod_modulo = 8
                                                    )
                                                    , cod_recurso::VARCHAR
                                                  )
                   )
 WHERE cod_fonte = ''
   AND exercicio = '2017'
     ;

