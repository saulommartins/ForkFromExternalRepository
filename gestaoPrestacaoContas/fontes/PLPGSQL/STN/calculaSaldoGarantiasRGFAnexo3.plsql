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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 59612 $
* $Name$
* $Author: gelson $
* $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*
* Casos de uso: uc-06.01.22
*/

/*
$Log$
Revision 1.1  2006/09/26 10:15:54  cleisson
Inclusão

Revision 1.2  2006/08/04 13:48:47  jose.eduardo
Ajustes


*/

CREATE OR REPLACE FUNCTION stn.fn_rgf_calcula_saldo_garantias_anexo3(varchar,varchar,varchar,varchar,varchar) RETURNS NUMERIC(14,2) AS $$
DECLARE

    stExercicio                 ALIAS FOR $1;
    stEntidade                  ALIAS FOR $2;
    stFiltro                    ALIAS FOR $3;
    stDtInicial                 ALIAS FOR $4;
    stDtFinal                   ALIAS FOR $5;

    stAux                       VARCHAR   := '';
    stSql                       VARCHAR   := '';
    nuVlDebito                  NUMERIC   := 0;
    nuVlCredito                 NUMERIC   := 0;
    nuVlSaldo                   NUMERIC   := 0;
    crCursor                    REFCURSOR;
    
BEGIN

IF stDtInicial = stDtFinal THEN
    stAux := ' AND lo.dt_lote = TO_DATE('''||stDtInicial||''', ''dd/mm/yyyy'') ';
ELSE
    stAux := ' AND lo.dt_lote BETWEEN  TO_DATE('''|| stDtInicial ||''', ''dd/mm/yyyy'') AND TO_DATE('''|| stDtFinal ||''', ''dd/mm/yyyy'') ';
END IF;

stSql := '
    SELECT
        sum(coalesce(vl.vl_lancamento,0.00))
    FROM
         contabilidade.plano_conta      AS pc
        ,contabilidade.plano_analitica  AS pa
        ,contabilidade.conta_credito    AS cc
        ,contabilidade.valor_lancamento AS vl
        ,contabilidade.lote             AS lo
    WHERE
        pc.cod_conta      = pa.cod_conta
    AND pc.exercicio      = pa.exercicio
    
    AND pa.cod_plano      = cc.cod_plano
    AND pa.exercicio      = cc.exercicio
    
    AND cc.exercicio      = vl.exercicio     
    AND cc.cod_lote       = vl.cod_lote      
    AND cc.tipo           = vl.tipo          
    AND cc.sequencia      = vl.sequencia     
    AND cc.tipo_valor     = vl.tipo_valor    
    AND cc.cod_entidade   = vl.cod_entidade  
    
    AND vl.exercicio      = lo.exercicio     
    AND vl.cod_lote       = lo.cod_lote      
    AND vl.tipo           = lo.tipo          
    AND vl.cod_entidade   = lo.cod_entidade  
    
    AND pc.exercicio      = '''|| stExercicio || '''
    AND cc.cod_entidade   IN ('|| stEntidade || ')
    ' || stAux    || '
    ' || stFiltro || '

    ';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuVlCredito;
    CLOSE crCursor;


stSql := '
    SELECT
         sum(coalesce(vl_lancamento,0.00))
    FROM
         contabilidade.plano_conta      AS pc
        ,contabilidade.plano_analitica  AS pa
        ,contabilidade.conta_debito     AS cd
        ,contabilidade.valor_lancamento AS vl
        ,contabilidade.lote             AS lo
    WHERE
        pc.cod_conta      = pa.cod_conta
    AND pc.exercicio      = pa.exercicio
    
    AND pa.cod_plano      = cd.cod_plano
    AND pa.exercicio      = cd.exercicio
    
    AND cd.exercicio      = vl.exercicio     
    AND cd.cod_lote       = vl.cod_lote      
    AND cd.tipo           = vl.tipo          
    AND cd.sequencia      = vl.sequencia     
    AND cd.tipo_valor     = vl.tipo_valor    
    AND cd.cod_entidade   = vl.cod_entidade  
    
    AND vl.exercicio      = lo.exercicio     
    AND vl.cod_lote       = lo.cod_lote      
    AND vl.tipo           = lo.tipo          
    AND vl.cod_entidade   = lo.cod_entidade  
    
    AND pc.exercicio      = ''' || stExercicio || '''
    AND cd.cod_entidade   IN ('|| stEntidade || ')

    ' || stAux    || '
    ' || stFiltro || '

    ';


    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuVlDebito;
    CLOSE crCursor;

    nuVlSaldo := nuVlDebito - nuVlCredito;

    RETURN nuVlSaldo;

END;

$$language 'plpgsql';
