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
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_busca_origem_Lancamento_sem_exercicio.plsql 34825 2008-10-23 18:01:44Z cercato $
*
* Caso de uso: uc-05.04.10
*/

CREATE OR REPLACE FUNCTION divida.fn_busca_origem_num_parcelamento_para_livro( integer ) RETURNS VARCHAR AS $$
declare
    inNumParcelamento   ALIAS FOR $1;
    stOrigem            VARCHAR := '';
    stSqlFuncoes        VARCHAR;
    reRecordFuncoes 	RECORD;

begin
    stSqlFuncoes := '
        SELECT
            (
                CASE WHEN (( acgc.cod_grupo IS NOT NULL ) AND ( parcela_origem.cod_forma_inscricao != 2 )) THEN
                    agc.descricao
                ELSE
                    mc.descricao_credito
                END
            )::varchar AS origem
        
        FROM
            arrecadacao.lancamento as al

        INNER JOIN
            (
                SELECT 
                    parcela.cod_lancamento,
                    parcela_origem.num_parcelamento,
                    modalidade_vigencia.cod_forma_inscricao,
                    MAX (calculo.cod_calculo) as cod_calculo
                FROM
                    divida.parcela_origem
                INNER JOIN
                    arrecadacao.parcela
                ON
                    parcela.cod_parcela = parcela_origem.cod_parcela
                INNER JOIN
                    arrecadacao.lancamento_calculo
                ON 
                    parcela.cod_lancamento = lancamento_calculo.cod_lancamento

                INNER JOIN
                    arrecadacao.calculo
                ON
                    calculo.cod_credito = parcela_origem.cod_credito
                    AND calculo.cod_especie = parcela_origem.cod_especie
                    AND calculo.cod_natureza = parcela_origem.cod_natureza
                    AND calculo.cod_genero = parcela_origem.cod_genero
                    AND calculo.cod_calculo = lancamento_calculo.cod_calculo

                INNER JOIN
                    divida.parcelamento
                ON
                    parcelamento.num_parcelamento = parcela_origem.num_parcelamento
        
                INNER JOIN
                    divida.modalidade_vigencia
                ON
                    modalidade_vigencia.timestamp = parcelamento.timestamp_modalidade
                    AND modalidade_vigencia.cod_modalidade = parcelamento.cod_modalidade

                WHERE
                    parcela_origem.num_parcelamento = '||inNumParcelamento||'
                group by 1,2,3
            )AS parcela_origem
        ON
            parcela_origem.cod_lancamento = al.cod_lancamento

        INNER JOIN
            arrecadacao.calculo as ac
        ON
            ac.cod_calculo = parcela_origem.cod_calculo
        LEFT JOIN
            arrecadacao.calculo_grupo_credito as acgc
        ON
            acgc.cod_calculo = ac.cod_calculo
            AND acgc.ano_exercicio = ac.exercicio
        LEFT JOIN
            arrecadacao.grupo_credito as agc
        ON
            agc.cod_grupo = acgc.cod_grupo
            AND agc.ano_exercicio = acgc.ano_exercicio
        LEFT JOIN
            monetario.credito as mc
        ON
            mc.cod_credito = ac.cod_credito
            AND mc.cod_especie = ac.cod_especie
            AND mc.cod_genero = ac.cod_genero
            AND mc.cod_natureza = ac.cod_natureza ';

    FOR reRecordFuncoes IN EXECUTE stSqlFuncoes LOOP
        stOrigem := reRecordFuncoes.origem || ';' || stOrigem;
    END LOOP;

    return stOrigem;
end;
$$ language 'plpgsql';
