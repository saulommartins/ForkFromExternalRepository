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
* $Id: fn_rl_gc_busca_valores.plsql 59612 2014-09-02 12:00:51Z gelson $
*

* Caso de uso: uc-05.03.13
*/

/*
$Log$
Revision 1.6  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_rl_gc_busca_valores ( varchar, date )  RETURNS SETOF RECORD AS '
DECLARE
    stFiltro              ALIAS FOR $1;
    dtDataBase      ALIAS FOR $2;
    stSql1                VARCHAR;
    stSql2                VARCHAR;
    nuResultado     NUMERIC;
    reRecord1         RECORD;
    reRecord2         RECORD;
BEGIN
    

    stSql1 = '' 
    
        SELECT 
        
            AGC.cod_grupo,
            AGC.ano_exercicio as exercicio,
            AGC.descricao,
            0.00::numeric as pago_a_vista,
            0.00::numeric as pago_parcelado,
            0.00::numeric as juros,
            0.00::numeric as multas,
            0.00::numeric as total_pago,
            0.00::numeric as a_vencer,
            0.00::numeric as vencido,
            0.00::numeric as em_aberto,
            0.00::numeric as total
                        
        FROM
            arrecadacao.grupo_credito as AGC
        
        WHERE
             ''||stFiltro||''
    '';
        
        
        FOR reRecord1 IN EXECUTE stSql1 LOOP
                
                stSql2 := ''
                    
                SELECT
                    PAGO_VISTA.pago_a_vista,
                    PAGO_PARCELADO.pago_parcelado,
                    PAGO_JUROS.juros,
                    PAGO_MULTA.multas,
                    (PAGO_VISTA.pago_a_vista + PAGO_PARCELADO.pago_parcelado) as total_pago,
                    
                    ABERTO_VENCER.a_vencer,
                    ABERTO_VENCIDO.vencido,
                    (ABERTO_VENCER.a_vencer + ABERTO_VENCIDO.vencido) as em_aberto,
                    
                    (PAGO_VISTA.pago_a_vista + PAGO_PARCELADO.pago_parcelado + ABERTO_VENCER.a_vencer + ABERTO_VENCIDO.vencido) as total
                    
                FROM
                    ( select arrecadacao.fn_rl_gc_valor_pago_vista ( ''||reRecord1.cod_grupo||'',''|| reRecord1.exercicio ||'' ) as pago_a_vista ) as PAGO_VISTA,
                    ( select arrecadacao.fn_rl_gc_valor_pago_parcelado ( ''||reRecord1.cod_grupo||'',''|| reRecord1.exercicio ||'' )  as pago_parcelado ) as PAGO_PARCELADO,
                    ( select arrecadacao.fn_rl_gc_valor_pago_juros ( ''||reRecord1.cod_grupo||'',''|| reRecord1.exercicio ||'', ''''''||dtDataBase||''''''  ) as juros ) as PAGO_JUROS,
                    ( select arrecadacao.fn_rl_gc_valor_pago_multa ( ''||reRecord1.cod_grupo||'', ''|| reRecord1.exercicio ||'' , ''''''||dtDataBase||''''''  ) as multas ) as PAGO_MULTA,
                    
                    ( select arrecadacao.fn_rl_gc_valor_aberto_a_vencer ( ''||reRecord1.cod_grupo||'',''|| reRecord1.exercicio ||'', ''''''||dtDataBase||'''''' ) as a_vencer ) as ABERTO_VENCER,
                    ( select arrecadacao.fn_rl_gc_valor_aberto_vencido ( ''||reRecord1.cod_grupo||'',''|| reRecord1.exercicio ||'', ''''''||dtDataBase||'''''' )  as vencido ) as ABERTO_VENCIDO;
                    
                    '';
                            
                    
                FOR reRecord2 IN EXECUTE stSql2 LOOP
                    
                    reRecord1.pago_a_vista      := reRecord2.pago_a_vista;
                    reRecord1.pago_parcelado := reRecord2.pago_parcelado;
                    reRecord1.juros                   := reRecord2.juros;
                    reRecord1.multas                := reRecord2.multas;
                    reRecord1.total_pago          := reRecord2.total_pago;
                    
                    reRecord1.a_vencer         := reRecord2.a_vencer;
                    reRecord1.vencido           := reRecord2.vencido;
                    reRecord1.em_aberto      := reRecord2.em_aberto;
                    
                    reRecord1.total                := reRecord2.total;
                    
                END LOOP;

        return next reRecord1;
        END LOOP;
                           
    RETURN ;
END;
' LANGUAGE 'plpgsql';
