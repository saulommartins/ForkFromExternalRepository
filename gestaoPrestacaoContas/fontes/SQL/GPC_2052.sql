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
* Versao 2.05.2
*
* Fabio Bertoldi - 20160530
*
*/

----------------
-- Ticket #23805
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    boAtivo     BOOLEAN;
    stAtributo  VARCHAR;

    stSQL       VARCHAR;
    reRecord    RECORD;
BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2016'
        AND parametro  = 'cod_uf'
        AND valor      = '2'
          ;
    IF FOUND THEN
        boAtivo := TRUE;
    ELSE
        boAtivo := FALSE;
    END IF;

    stAtributo := 'INSERT
                     INTO administracao.atributo_dinamico
                        ( cod_modulo
                        , cod_cadastro
                        , cod_atributo
                        , cod_tipo
                        , nao_nulo
                        , nom_atributo
                        , ajuda
                        , ativo
                        , interno
                        , indexavel
                        )
                   SELECT 10
                        , 1
                        , 120
                        , 2
                        , FALSE
                        , ''Número do Processo''
                        , ''Número do processo referente ao empenho''
                        , '|| boAtivo ||'
                        , TRUE
                        , FALSE
                    WHERE 0 = (
                                SELECT COUNT(1)
                                  FROM administracao.atributo_dinamico
                                 WHERE cod_modulo   = 10
                                   AND cod_cadastro = 1
                                   AND cod_atributo = 120
                              )
                        ;';
    EXECUTE stAtributo;

    stAtributo := 'INSERT
                     INTO administracao.atributo_dinamico
                        ( cod_modulo
                        , cod_cadastro
                        , cod_atributo
                        , cod_tipo
                        , nao_nulo
                        , nom_atributo
                        , ajuda
                        , ativo
                        , interno
                        , indexavel
                        )
                   SELECT 10
                        , 1
                        , 121
                        , 2
                        , FALSE
                        , ''Ano do Processo''
                        , ''Ano do processo referente ao empenho''
                        , '|| boAtivo ||'
                        , TRUE
                        , FALSE
                    WHERE 0 = (
                                SELECT COUNT(1)
                                  FROM administracao.atributo_dinamico
                                 WHERE cod_modulo   = 10
                                   AND cod_cadastro = 1
                                   AND cod_atributo = 121
                              )
                        ;';
    EXECUTE stAtributo;

    IF boAtivo = TRUE THEN
        stSQL := '
                   SELECT pre_empenho.cod_pre_empenho
                        , pre_empenho.exercicio
                        , empenho.cod_empenho
                        , empenho.dt_empenho
                     FROM empenho.pre_empenho
                     JOIN empenho.empenho
                       ON empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                      AND empenho.exercicio       = pre_empenho.exercicio
                        ;
                 ';
        FOR reRecord IN EXECUTE stSQL LOOP
            INSERT
              INTO empenho.atributo_empenho_valor
                 ( cod_pre_empenho
                 , exercicio
                 , cod_modulo
                 , cod_cadastro
                 , cod_atributo
                 , timestamp
                 , valor
                 )
            SELECT reRecord.cod_pre_empenho
                 , reRecord.exercicio
                 , 10
                 , 1
                 , 120
                 , (reRecord.dt_empenho||' 00:00:00.000')::timestamp
                 , reRecord.cod_empenho::TEXT
             WHERE 0 = (
                         SELECT COUNT(1)
                           FROM empenho.atributo_empenho_valor
                          WHERE cod_pre_empenho = reRecord.cod_pre_empenho
                            AND exercicio       = reRecord.exercicio
                            AND cod_modulo      = 10
                            AND cod_cadastro    = 1
                            AND cod_atributo    = 120
                       )
                 ;
            INSERT
              INTO empenho.atributo_empenho_valor
                 ( cod_pre_empenho
                 , exercicio
                 , cod_modulo
                 , cod_cadastro
                 , cod_atributo
                 , timestamp
                 , valor
                 )
            SELECT reRecord.cod_pre_empenho
                 , reRecord.exercicio
                 , 10
                 , 1
                 , 121
                 , (reRecord.dt_empenho||' 00:00:00.000')::timestamp
                 , reRecord.exercicio
             WHERE 0 = (
                         SELECT COUNT(1)
                           FROM empenho.atributo_empenho_valor
                          WHERE cod_pre_empenho = reRecord.cod_pre_empenho
                            AND exercicio       = reRecord.exercicio
                            AND cod_modulo      = 10
                            AND cod_cadastro    = 1
                            AND cod_atributo    = 121
                       )
                 ;
        END LOOP;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #23092
----------------

CREATE TABLE tcmba.arquivo_concilia (
    cod_conciliacao   INTEGER       NOT NULL,
    chave_conciliacao VARCHAR       NOT NULL,
    exercicio         CHAR(4)       NOT NULL,
    mes               INTEGER       NOT NULL,
    descricao         VARCHAR(250)          ,
    valor             NUMERIC(14,2)         ,
    CONSTRAINT pk_arquivo_concilia  PRIMARY KEY (cod_conciliacao,chave_conciliacao,exercicio,mes)
);
GRANT ALL ON TABLE tcmba.arquivo_concilia TO urbem;

