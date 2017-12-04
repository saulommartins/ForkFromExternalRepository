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
* $Id: GT_1931.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.93.1
*/

----------------
-- Ticket #13771
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL               VARCHAR;
    reRECORD            RECORD;
    stSQL2              VARCHAR;
    reRECORD2           RECORD;
    stRetorno           VARCHAR;

BEGIN
    stSQL := '
        SELECT DISTINCT
            divida_cgm.cod_inscricao,
            divida_cgm.exercicio

        FROM
            divida.divida_imovel

        INNER JOIN
            divida.divida_cgm
        ON
            divida_cgm.cod_inscricao = divida_imovel.cod_inscricao
            AND divida_cgm.exercicio = divida_imovel.exercicio

        LEFT JOIN
            divida.divida_estorno
        ON
            divida_estorno.cod_inscricao = divida_imovel.cod_inscricao
            AND divida_estorno.exercicio = divida_imovel.exercicio

        LEFT JOIN
            divida.divida_cancelada
        ON
            divida_cancelada.cod_inscricao = divida_imovel.cod_inscricao
            AND divida_cancelada.exercicio = divida_imovel.exercicio

        LEFT JOIN
            divida.divida_remissao
        ON
            divida_remissao.cod_inscricao = divida_imovel.cod_inscricao
            AND divida_remissao.exercicio = divida_imovel.exercicio

        INNER JOIN
            (
                SELECT
                    max(num_parcelamento) AS num_parcelamento,
                    cod_inscricao,
                    exercicio
                FROM
                    divida.divida_parcelamento
                GROUP BY
                    cod_inscricao,
                    exercicio
            )AS divida_parcelamento
        ON
            divida_parcelamento.cod_inscricao = divida_imovel.cod_inscricao
            AND divida_parcelamento.exercicio = divida_imovel.exercicio

        INNER JOIN
            divida.parcelamento
        ON
            parcelamento.num_parcelamento = divida_parcelamento.num_parcelamento

        LEFT JOIN
            divida.parcela
        ON
            parcela.num_parcelamento = parcelamento.num_parcelamento
            AND paga = false
            AND cancelada = false

        LEFT JOIN
            divida.parcela AS tparcela
        ON
            tparcela.num_parcelamento = parcelamento.num_parcelamento

        INNER JOIN
            divida.parcela_origem
        ON
            parcela_origem.num_parcelamento = parcelamento.num_parcelamento

        INNER JOIN
            arrecadacao.parcela AS ap
        ON
            ap.cod_parcela = parcela_origem.cod_parcela

        INNER JOIN
            arrecadacao.lancamento_calculo
        ON
            lancamento_calculo.cod_lancamento = ap.cod_lancamento

        INNER JOIN
            arrecadacao.imovel_calculo
        ON
            imovel_calculo.cod_calculo = lancamento_calculo.cod_calculo

        INNER JOIN
            imobiliario.proprietario
        ON
            proprietario.inscricao_municipal = imovel_calculo.inscricao_municipal

        WHERE
            divida_cancelada.cod_inscricao IS NULL
            AND divida_estorno.cod_inscricao IS NULL
            AND divida_remissao.cod_inscricao IS NULL
            AND CASE WHEN parcela.num_parcelamento IS NULL THEN
                    CASE WHEN tparcela.num_parcelamento IS NOT NULL THEN
                        false
                    ELSE
                        true
                    END
                ELSE
                    true
                END
            AND divida_cgm.numcgm != proprietario.numcgm
    ';

    FOR reRECORD IN EXECUTE stSQL LOOP
        stSQL2 := '
            SELECT
                proprietario.numcgm
            FROM
                imobiliario.proprietario
            INNER JOIN
                divida.divida_imovel
            ON
                divida_imovel.inscricao_municipal = proprietario.inscricao_municipal
            WHERE
                divida_imovel.cod_inscricao = '||reRECORD.cod_inscricao||'
                AND divida_imovel.exercicio = '||reRECORD.exercicio;

        DELETE FROM
            divida.divida_cgm
        WHERE
            divida_cgm.cod_inscricao = reRECORD.cod_inscricao
            AND divida_cgm.exercicio = reRECORD.exercicio;

        FOR reRECORD2 IN EXECUTE stSQL2 LOOP
            INSERT INTO
                divida.divida_cgm (cod_inscricao, exercicio, numcgm)
            VALUES (
                reRECORD.cod_inscricao,
                reRECORD.exercicio,
                reRECORD2.numcgm
            );
        END LOOP;
    END LOOP;

END;

$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


CREATE OR REPLACE FUNCTION manutencao_2( ) RETURNS VOID AS $$
DECLARE

    varAux  VARCHAR;

BEGIN

    SELECT valor
      INTO varAux
      FROM administracao.configuracao
     WHERE exercicio = '2008'
       AND parametro = 'cnpj'
       AND valor     = '13805528000180';

    IF FOUND THEN

            DELETE
              FROM divida.modalidade_acrescimo
             WHERE cod_modalidade in (173,176)
               AND timestamp      in ('2008-04-18 15:39:21.471','2008-05-05 10:53:05.871')
               AND cod_acrescimo  = 10;

            DELETE
              FROM arrecadacao.carne_devolucao
             WHERE numeracao IN ( 76106900000402462
                                , 76106900000402464
                                , 76106900000402465
                                , 76106900000402466
                                );
            
            INSERT
              INTO arrecadacao.carne_devolucao
                 ( numeracao
                 , cod_motivo
                 , dt_devolucao
                 , cod_convenio )
            VALUES ( '76106900000404791'
                 , 10
                 , now()
                 , 100
                 );
            
            INSERT
              INTO arrecadacao.carne_devolucao
                 ( numeracao
                 , cod_motivo
                 , dt_devolucao
                 , cod_convenio )
            VALUES ( '76106900000412051'
                 , 10
                 , now()
                 , 100
                 );
            
            INSERT
              INTO arrecadacao.carne_devolucao 
                 ( numeracao
                 , cod_motivo
                 , dt_devolucao
                 , cod_convenio )
            VALUES ( '76106900000421242'
                 , 10
                 , now()
                 , 100
                 );
            
            INSERT
              INTO arrecadacao.carne_devolucao 
                 ( numeracao
                 , cod_motivo
                 , dt_devolucao
                 , cod_convenio )
            VALUES ( '76106900000412053'
                 , 10
                 , now()
                 , 100
                 ); 

    END IF;


END;

$$ LANGUAGE 'plpgsql';

SELECT        manutencao_2();
DROP FUNCTION manutencao_2();



CREATE OR REPLACE FUNCTION manutencao_3() RETURNS VOID AS $$

DECLARE

    inCodBiblioteca     INTEGER;
    inCodModulo         INTEGER;
    inCodFuncao         INTEGER;

BEGIN

    SELECT cod_modulo
         , cod_biblioteca
         , cod_funcao
      INTO inCodModulo
         , inCodBiblioteca
         , inCodFuncao
      from administracao.funcao
     where nom_funcao = 'arrecadacao.buscaValorCalculoCredito';

    IF FOUND THEN

        UPDATE administracao.variavel
           set cod_tipo         = 1
         where cod_modulo       = inCodModulo
           and cod_biblioteca   = inCodBiblioteca
           and cod_funcao       = inCodFuncao
           and cod_variavel     = 2;

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao_3();
DROP FUNCTION manutencao_3();
