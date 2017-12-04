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
* $Id: fn_rl_gc_valor_pago.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.13
*/

/*
$Log$
Revision 1.3  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_rl_gc_valor_pago ( integer , numeric )  RETURNS NUMERIC(14,2) AS '
DECLARE
    inCodGrupo    ALIAS FOR $1;
    dtDataBase      ALIAS FOR $2;
    nuResultado     NUMERIC;
    reRecord        RECORD;
BEGIN
                    
        select 
            coalesce (sum(APAG.valor), 0.00) as soma
        INTO
            nuResultado
        FROM
        
                arrecadacao.calculo as CALC
            INNER JOIN
                arrecadacao.credito_grupo as ACG
            ON
                ACG.cod_genero    = CALC.cod_genero AND
                ACG.cod_natureza = CALC.cod_natureza AND
                ACG.cod_especie   = CALC.cod_especie AND
                ACG.cod_credito   = CALC.cod_credito AND
                ACG.exercicio = CALC.exercicio
                
            INNER JOIN
                arrecadacao.grupo_credito as AGC
            ON
                AGC.cod_grupo       = ACG.cod_grupo AND
                AGC.ano_exercicio  = CALC.exercicio
                
            INNER JOIN
                arrecadacao.lancamento_calculo as ALC
            ON
                ALC.cod_calculo = CALC.cod_calculo
                
            INNER JOIN
                arrecadacao.lancamento as ALAN
            ON
                ALAN.cod_lancamento = ALC.cod_lancamento
                
            INNER JOIN
                arrecadacao.parcela as APAR
            ON
                APAR.cod_lancamento = ALAN.cod_lancamento 
              
            INNER JOIN
                arrecadacao.carne as CAR                                                                                            
            ON
                CAR.numeracao = (select arrecadacao.fn_consulta_somente_numeracao_parcela ( APAR.cod_parcela, (CALC.exercicio)::numeric) )

            INNER JOIN
                arrecadacao.pagamento as APAG
            ON
                APAG.numeracao = CAR.numeracao
                                                
                
        WHERE
            CALC.exercicio = dtDataBase
            AND AGC.cod_grupo = inCodGrupo
        
        group by AGC.descricao  ;
                
    return coalesce (nuResultado, 0.00 );
END;
' LANGUAGE 'plpgsql';
