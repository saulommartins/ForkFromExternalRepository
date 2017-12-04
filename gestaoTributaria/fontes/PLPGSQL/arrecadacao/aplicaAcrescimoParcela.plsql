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
* $Id: aplicaAcrescimoParcela.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.1  2006/10/02 17:25:42  domluc
Correção de Bug relatado pela Michelle, repassado pelo Fabio

*/

CREATE OR REPLACE FUNCTION arrecadacao.aplica_acrescimo_parcela(varchar,integer,integer,date, integer , integer) returns numeric as '
declare
    stNumeracao     ALIAS FOR $1;
    inExercicio     ALIAS FOR $2;
    inCodParcela    ALIAS FOR $3;
    dtDataBase      ALIAS FOR $4;
    inCodAcrescimo  ALIAS FOR $5;
    inCodTipo       ALIAS FOR $6;
    stFuncao        VARCHAR;
    stSqlCreditos   VARCHAR;
    stSqlFuncoes    VARCHAR;
    stExecuta       VARCHAR;
    nuRetorno       NUMERIC := 0.00;
    reRecord        RECORD;
    reRecordFuncoes RECORD;
    reRecordExecuta RECORD;
begin
    stFuncao := '''';
   -- pegar calculos/creditos para o lancamento da parcela
   stSqlCreditos := ''
                      SELECT  CAL.cod_calculo
                            , CAL.cod_credito
                            , CAL.cod_especie
                            , CAL.cod_genero
                            , CAL.cod_natureza
                            , case 
                                when PARREE.cod_parcela is not null then PARREE.vencimento
                                else PAR.vencimento
                              end as vencimento
                            , CASE when ((PAR.vencimento > ''''''||dtDataBase||'''''' ) OR ( PAR.nr_parcela = 0 ) OR ( PAR.valor <= 0) ) then              
                                0.00
                              ELSE     
                              (
                                (
                                    (
                                        LC.valor * arrecadacao.calculaProporcaoParcela ( CAR.cod_parcela )
                                    )
                                        * 
                                    ( 
                                        100 / COALESCE ( (
                                                            SELECT valor 
                                                            FROM arrecadacao.parcela_desconto 
                                                            WHERE cod_parcela =  PAR.cod_parcela  
                                                         ),
                                                            PAR.valor 
                                                       )

                                    )
                                )
                                    *
                                ( 
                                    CASE WHEN ''||inCodTipo||'' = 3 THEN
                                        aplica_multa ( CAR.numeracao, CAR.exercicio::int, PAR.cod_parcela, ''''''||dtDataBase||'''''' )
                                    WHEN ''||inCodTipo||'' = 2 THEN
                                        aplica_juro ( CAR.numeracao, CAR.exercicio::int, PAR.cod_parcela, ''''''||dtDataBase||'''''' )
                                    ELSE 
                                        aplica_correcao ( CAR.numeracao, CAR.exercicio::int, PAR.cod_parcela, ''''''||dtDataBase||'''''' )
                                    END * arrecadacao.calculaProporcaoParcela(PAR.cod_parcela)
                                )
                                    /
                                100
                              )
                             END::numeric(14,2) AS valor
                        FROM   arrecadacao.carne CAR
                            INNER JOIN arrecadacao.parcela PAR ON PAR.cod_parcela = CAR.cod_parcela
                            INNER JOIN arrecadacao.lancamento LAN ON LAN.cod_lancamento = PAR.cod_lancamento
                            INNER JOIN arrecadacao.lancamento_calculo LC ON LC.cod_lancamento = LAN.cod_lancamento
                            INNER JOIN arrecadacao.calculo CAL ON CAL.cod_calculo = LC.cod_calculo
                            LEFT JOIN ( SELECT * 
                                    FROM arrecadacao.parcela_reemissao
                                    WHERE cod_parcela = ''||inCodParcela||''
                                ORDER BY timestamp ASC limit 1
                            ) PARREE
                            ON PARREE.cod_parcela = PAR.cod_parcela
                       WHERE 
                         CAR.cod_parcela = ''||inCodParcela||''      
                         AND  CAR.numeracao = ''''''||stNumeracao||''''''   
                         AND  CAR.exercicio = ''''''||inExercicio||''''''
   '';

    FOR reRecord IN EXECUTE stSqlCreditos LOOP
        nuRetorno := nuRetorno + reRecord.valor;
    END LOOP;


   return nuRetorno::numeric(14,2);
end;
'language 'plpgsql';
