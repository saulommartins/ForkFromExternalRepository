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
* $Id: fn_busca_origem_Lancamento_divida_ativa.plsql 39093 2009-03-25 13:56:29Z cercato $
*
* Caso de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.2  2007/10/11 19:59:00  cercato
otimizacao

Revision 1.1  2007/10/11 14:35:35  cercato
*** empty log message ***

*/
--busca sempre a origem da inscricao da divida ativa (mesmo se tiver cobranca)
CREATE OR REPLACE FUNCTION arrecadacao.fn_busca_origem_inscricao_divida_ativa( integer, integer, integer ) RETURNS VARCHAR AS $$
declare
    inCodInscricao    	ALIAS FOR $1;
    inExercicio         ALIAS FOR $2;
    inTipo              ALIAS FOR $3;
    stOrigem            VARCHAR := '';
    stSQL2              VARCHAR;
    stSQL1              VARCHAR;
    reRecordExecuta     RECORD;
    reRecordExecuta2    RECORD;

begin

-- TIPO :
    -- caso esteja com valor 0, mostra codigo do grupo / grupo_descricao
    -- caso esteja com valor 1, mostra codigo do grupo / ano exercicio
    -- caso valor = 2, mostra cod_grupo, cod_modulo , descricao e ano_exercicio

    stSQL2 := '
        SELECT DISTINCT
            (
                CASE WHEN lancamento.divida = true THEN
                    ''DA''
                ELSE
                    CASE WHEN acgc.cod_grupo IS NOT NULL THEN
                        CASE WHEN ( '|| inTipo ||' = 1 OR '|| inTipo ||' = 4 )  THEN
                            agc.descricao||'' / ''|| acgc.ano_exercicio 
                        ELSE
                            CASE WHEN '|| inTipo ||' = 2 THEN
                                ''§''|| acgc.cod_grupo||''§''|| agc.descricao||''§''|| acgc.ano_exercicio||''§§''|| agc.cod_modulo
                            ELSE
                                CASE WHEN '|| inTipo ||' = 6 THEN
                                    acgc.cod_grupo||'' / ''|| acgc.ano_exercicio||'' - ''|| agc.descricao
                                ELSE
                                    acgc.cod_grupo||'' § ''|| agc.descricao
                                END
                            END
                        END
                    ELSE
                        CASE WHEN ( '|| inTipo ||' = 1 OR '|| inTipo ||' = 4 )  THEN
                            mc.descricao_credito||'' / ''|| ac.exercicio
                        ELSE
                            CASE WHEN '|| inTipo ||' = 2 THEN
                                mc.cod_credito||''§§''|| mc.descricao_credito||''§''|| ac.exercicio||''§§''|| mc.cod_especie||''§''|| mc.cod_genero||''§''|| mc.cod_natureza
                            ELSE
                                to_char(mc.cod_credito,''FM999099'')||''.''|| to_char(mc.cod_especie,''FM999099'')||''.''|| to_char(mc.cod_genero,''FM999099'')||''.''|| mc.cod_natureza||'' - ''|| mc.descricao_credito||'' ''|| ac.exercicio
                            END
                        END
                    END
                END
            )::varchar AS stOrigem,
            CASE WHEN acgc.cod_grupo IS NOT NULL THEN
                1
            ELSE
                0
            END::integer AS inEhGrupo

        FROM
            divida.divida_parcelamento
        
        INNER JOIN
            (
                --validar cobranca
                SELECT
                    min( divida_parcelamento.num_parcelamento ) AS num_parcelamento,
                    divida_parcelamento.cod_inscricao,
                    divida_parcelamento.exercicio
        
                FROM
                    divida.divida_parcelamento
        
                LEFT JOIN
                    divida.parcela
                ON
                    parcela.num_parcelamento = divida_parcelamento.num_parcelamento
        
                WHERE
                    CASE WHEN parcela.num_parcelamento IS NOT NULL THEN
                        CASE WHEN ( 
                            SELECT
                                t.num_parcelamento
                            FROM
                                divida.parcela AS t
                            WHERE
                                t.num_parcelamento = divida_parcelamento.num_parcelamento
                                AND t.cancelada = true
                            LIMIT 1
                        ) IS NULL THEN
                            true
                        ELSE
                            false
                        END
                    ELSE
                        true
                    END
        
                GROUP BY
                    divida_parcelamento.cod_inscricao,
                    divida_parcelamento.exercicio
            )AS parcelamento
        ON
            parcelamento.num_parcelamento = divida_parcelamento.num_parcelamento
            AND parcelamento.cod_inscricao = divida_parcelamento.cod_inscricao
            AND parcelamento.exercicio = divida_parcelamento.exercicio
        
        INNER JOIN
            divida.parcela_origem
        ON
            parcela_origem.num_parcelamento = divida_parcelamento.num_parcelamento
        
        INNER JOIN
            arrecadacao.parcela
        ON
            parcela.cod_parcela = parcela_origem.cod_parcela
        
        INNER JOIN 
            arrecadacao.lancamento_calculo
        ON
            lancamento_calculo.cod_lancamento = parcela.cod_lancamento
        
        INNER JOIN
            arrecadacao.lancamento
        ON
            lancamento.cod_lancamento = lancamento_calculo.cod_lancamento
        
        INNER JOIN
            arrecadacao.calculo AS ac
        ON
            ac.cod_calculo = lancamento_calculo.cod_calculo
        
        INNER JOIN 
            monetario.credito as mc
        ON 
            mc.cod_credito = ac.cod_credito
            AND mc.cod_especie = ac.cod_especie
            AND mc.cod_genero = ac.cod_genero
            AND mc.cod_natureza = ac.cod_natureza
        
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
        
        WHERE
            divida_parcelamento.cod_inscricao = '|| inCodInscricao ||'
            AND divida_parcelamento.exercicio = '''|| inExercicio ||'''
    ';

    FOR reRecordExecuta2 IN EXECUTE stSQL2 LOOP
        IF ( reRecordExecuta2.stOrigem = 'DA' ) THEN
            stSQL1 := '
                SELECT DISTINCT
                    ''§§DA - ''||  ddp.cod_inscricao ||''§''|| ddp.exercicio AS origem

                FROM
                    divida.parcelamento as dp

                INNER JOIN 
                    divida.divida_parcelamento as ddp
                ON 
                    ddp.num_parcelamento = dp.num_parcelamento

                INNER JOIN 
                    divida.parcela as dpar
                ON 
                    dpar.num_parcelamento = dp.num_parcelamento

                INNER JOIN 
                    divida.parcela_calculo as dpc
                ON 
                    dpc.num_parcelamento = dpar.num_parcelamento
                    AND dpc.num_parcela = dpar.num_parcela

                INNER JOIN 
                    arrecadacao.lancamento_calculo as alc
                ON 
                    alc.cod_calculo = dpc.cod_calculo

                WHERE
                    alc.cod_lancamento in (
                        SELECT DISTINCT
                            parcela.cod_lancamento
                        FROM
                            divida.divida_parcelamento
    
                        INNER JOIN
                            divida.parcela_origem
                        ON
                            parcela_origem.num_parcelamento = divida_parcelamento.num_parcelamento
    
                        INNER JOIN
                            arrecadacao.parcela
                        ON
                            parcela.cod_parcela = parcela_origem.cod_parcela
    
                        WHERE
                            divida_parcelamento.cod_inscricao = '|| inCodInscricao ||'
                            AND divida_parcelamento.exercicio = '''|| inExercicio ||'''
                    );
            ';

            FOR reRecordExecuta IN EXECUTE stSQL1 LOOP
                stOrigem := reRecordExecuta.stOrigem ||'; '|| reRecordExecuta2.stOrigem ||'; '|| stOrigem;
            END LOOP;
        ELSEIF ( reRecordExecuta2.inEhGrupo = 1 ) THEN
            stSQL1 := '
                SELECT DISTINCT
                    CASE WHEN '|| inTipo ||' = 4 THEN
                        credito.descricao_credito
                    ELSE
                        to_char(credito.cod_credito,''FM999099'')||''.''|| to_char(credito.cod_especie,''FM999099'')||''.''|| to_char(credito.cod_genero,''FM999099'')||''.''|| credito.cod_natureza||'' - ''|| credito.descricao_credito
                    END AS cred_desc

                FROM
                    divida.divida_parcelamento               

                 INNER JOIN ( SELECT divida_parcelamento.cod_inscricao
                                   , divida_parcelamento.exercicio
                                   , max(divida_parcelamento.num_parcelamento) AS num_parcelamento
                                FROM divida.divida_parcelamento
			   LEFT JOIN divida.parcelamento_cancelamento
				  ON divida_parcelamento.num_parcelamento = parcelamento_cancelamento.num_parcelamento
			       WHERE parcelamento_cancelamento.num_parcelamento IS NULL
                            GROUP BY divida_parcelamento.cod_inscricao
                                   , divida_parcelamento.exercicio
                            )AS parcelamento

                         ON parcelamento.cod_inscricao = divida_parcelamento.cod_inscricao
                        AND parcelamento.exercicio = divida_parcelamento.exercicio                  

                INNER JOIN
                    divida.parcela_origem
                ON
                    parcela_origem.num_parcelamento = divida_parcelamento.num_parcelamento

                INNER JOIN 
                    monetario.credito
                ON 
                    credito.cod_credito = parcela_origem.cod_credito
                    AND credito.cod_especie = parcela_origem.cod_especie
                    AND credito.cod_genero = parcela_origem.cod_genero
                    AND credito.cod_natureza = parcela_origem.cod_natureza

                WHERE
                    divida_parcelamento.cod_inscricao = '|| inCodInscricao ||'
                    AND divida_parcelamento.exercicio = '''|| inExercicio ||'''';

            FOR reRecordExecuta IN EXECUTE stSQL1 LOOP
                IF ( stOrigem IS NOT NULL ) THEN
                    stOrigem := '; '|| stOrigem;
                END IF;

                IF ( inTipo = 4 ) THEN
                    stOrigem := reRecordExecuta.cred_desc ||' - '|| reRecordExecuta2.stOrigem;
                ELSE
                    stOrigem := reRecordExecuta.cred_desc ||'; '|| reRecordExecuta2.stOrigem;
                END IF;

            END LOOP;
        ELSE
            stOrigem := reRecordExecuta2.stOrigem ||'; '|| stOrigem;
        END IF;
    END LOOP;

    return stOrigem;

end;
$$language 'plpgsql';
