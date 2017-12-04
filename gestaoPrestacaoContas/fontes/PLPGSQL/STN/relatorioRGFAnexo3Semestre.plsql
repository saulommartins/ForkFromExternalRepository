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

Revision 1.1  2006/08/04 17:54:38  jose.eduardo
Inclusao


*/

CREATE OR REPLACE FUNCTION stn.fn_rgf_anexo3_semestre (varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stEntidade          ALIAS FOR $2;
    stSql               VARCHAR   := '';
    reRegistro          RECORD;
    stExercicioAnterior VARCHAR   := '';
    dtAnoAnterior       DATE;
BEGIN

    dtAnoAnterior := TO_DATE(stExercicio, 'yyyy') - 1;
    stExercicioAnterior := TO_CHAR(dtAnoAnterior,'yyyy');

stSql := '
    select
         cast(descricao as varchar)
        ,cast(saldo_exercicio_anterior as numeric)
        ,cast(saldo_primeiro_semestre as numeric)
        ,cast(saldo_segundo_semestre as numeric)
        ,cast(sequencia as numeric)
        ,cast(tipo as varchar)
    from(
        select 
             ''EXTERNAS'' as descricao
            ,null as saldo_exercicio_anterior
            ,null as saldo_primeiro_semestre 
            ,null as saldo_segundo_semestre 
            ,1   as sequencia
            ,''G'' as tipo
        
        UNION
        
        select 
             ''Aval ou fiança em operações de crédito'' as descricao
            ,coalesce(stn.fn_rgf_calcula_saldo_garantias_anexo3('|| quote_literal(stExercicioAnterior) ||','|| quote_literal(stEntidade) ||''', '' AND (
                                pc.cod_estrutural  = ''1.9.9.5.1.01.02.00.00.00'' 
                             or pc.cod_estrutural  = ''1.9.9.5.2.02.02.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.2.01.02.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.1.02.02.00.00.00''
                             ) '','|| quote_literal('01/01/'|| stExercicioAnterior) ||','|| quote_literal('31/12/'|| stExercicioAnterior) ||'), 0.00) as saldo_exercicio_anterior
            ,coalesce(stn.fn_rgf_calcula_saldo_garantias_anexo3('|| quote_literal(stExercicio) ||','|| quote_literal(stEntidade) ||', '' AND (
                                pc.cod_estrutural  = ''1.9.9.5.1.01.02.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.2.02.02.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.2.01.02.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.1.02.02.00.00.00''
                             ) '','|| quote_literal('01/01/'|| stExercicio) ||','|| quote_literal('30/06/'|| stExercicio) ||'), 0.00) as saldo_primeiro_semestre 
            ,coalesce(stn.fn_rgf_calcula_saldo_garantias_anexo3('|| quote_literal(stExercicio) ||','|| quote_literal(stEntidade) ||''', '' AND (
                                pc.cod_estrutural  = ''1.9.9.5.1.01.02.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.2.02.02.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.2.01.02.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.1.02.02.00.00.00''
                             ) '','|| quote_literal('01/01/'|| stExercicio) ||','|| quote_literal('31/12/'|| stExercicio) ||'), 0.00) as saldo_segundo_semestre 
            ,2   as sequencia
            ,''G'' as tipo
      
        UNION
        
        select 
             ''INTERNAS'' as descricao
            ,null as saldo_exercicio_anterior
            ,null as saldo_primeiro_semestre 
            ,null as saldo_segundo_semeste 
            ,3   as sequencia
            ,''G'' as tipo
        
        UNION
        
        select 
             ''Aval ou fiança em operações de crédito'' as descricao
            ,coalesce(stn.fn_rgf_calcula_saldo_garantias_anexo3('|| quote_literal(stExercicioAnterior) ||','|| quote_literal(stEntidade) ||', '' AND (
                                pc.cod_estrutural  = ''1.9.9.5.1.01.01.00.00.00'' 
                             or pc.cod_estrutural  = ''1.9.9.5.1.02.01.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.2.01.01.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.2.02.01.00.00.00''
                             ) '','|| quote_literal('01/01/'|| stExercicioAnterior) ||','|| quote_literal('31/12/'|| stExercicioAnterior) ||'), 0.00) as saldo_exercicio_anterior
            ,coalesce(stn.fn_rgf_calcula_saldo_garantias_anexo3('|| quote_literal(stExercicio) ||','|| quote_literal(stEntidade) ||', '' AND (
                                pc.cod_estrutural  = ''1.9.9.5.1.01.01.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.1.02.01.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.2.01.01.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.2.02.01.00.00.00''
                             ) '','|| quote_literal('01/01/'|| stExercicio) ||','|| quote_literal('30/06/'|| stExercicio) ||'), 0.00) as saldo_primeiro_semestre 
            ,coalesce(stn.fn_rgf_calcula_saldo_garantias_anexo3('|| quote_literal(stExercicio) ||','|| quote_literal(stEntidade) ||', '' AND (
                                pc.cod_estrutural  = ''1.9.9.5.1.01.01.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.1.02.01.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.2.01.01.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.2.02.01.00.00.00''
                             ) '','|| quote_literal('01/01/'|| stExercicio) ||','|| quote_literal('31/12/'|| stExercicio) ||'), 0.00) as saldo_segundo_semestre 
            ,4   as sequencia
            ,''G'' as tipo
    
        UNION
        
        select 
             ''Outras Garantias'' as descricao
            ,coalesce(stn.fn_rgf_calcula_saldo_garantias_anexo3('|| quote_literal(stExercicioAnterior) ||','|| quote_literal(stEntidade) ||', '' AND (
                                pc.cod_estrutural  = ''1.9.9.5.9.00.00.00.00.00'' 
                             ) '','|| quote_literal('01/01/'|| stExercicioAnterior) ||','|| quote_literal('31/12/'|| stExercicioAnterior) ||'), 0.00) as saldo_exercicio_anterior
            ,coalesce(stn.fn_rgf_calcula_saldo_garantias_anexo3('|| quote_literal(stExercicio) ||','|| quote_literal(stEntidade) ||', '' AND (
                                pc.cod_estrutural  = ''1.9.9.5.9.00.00.00.00.00''
                             ) '','|| quote_literal('01/01/'|| stExercicio) ||','|| quote_literal('30/06/'|| stExercicio) ||'), 0.00) as saldo_primeiro_semestre 
            ,coalesce(stn.fn_rgf_calcula_saldo_garantias_anexo3('|| quote_literal(stExercicio) ||','|| quote_literal(stEntidade) ||', '' AND (
                                pc.cod_estrutural  = ''1.9.9.5.9.00.00.00.00.00''
                             ) '','|| quote_literal('01/01/'|| stExercicio) ||','|| quote_literal('31/12/'|| stExercicio) ||'), 0.00) as saldo_segundo_semestre 
            ,5   as sequencia
            ,''G'' as tipo
        
        UNION 

        select 
             ''GARANTIAS EXTERNAS'' as descricao 
            ,null as saldo_exercicio_anterior
            ,null as saldo_primeiro_semestre 
            ,null as saldo_segundo_semestre 
            ,1    as sequencia
            ,''CG'' as tipo
        
        UNION
        
        select 
             ''Aval ou fiança em operações de crédito'' as descricao
            ,coalesce(stn.fn_rgf_calcula_saldo_garantias_anexo3('|| quote_literal(stExercicioAnterior) ||','|| quote_literal(stEntidade) ||', '' AND (
                                pc.cod_estrutural  = ''1.9.9.5.6.01.02.00.00.00'' 
                             or pc.cod_estrutural  = ''1.9.9.5.6.02.02.00.00.00''
                             ) '','|| quote_literal('01/01/'|| stExercicioAnterior) ||','|| quote_literal('31/12/'|| stExercicioAnterior) ||'), 0.00) as saldo_exercicio_anterior


            ,coalesce(stn.fn_rgf_calcula_saldo_garantias_anexo3('|| quote_literal(stExercicio) ||','|| quote_literal(stEntidade) ||', '' AND (
                                pc.cod_estrutural  = ''1.9.9.5.6.01.02.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.6.02.02.00.00.00''
                             ) '','|| quote_literal('01/01/'|| stExercicio) ||','|| quote_literal('30/06/'|| stExercicio) ||'), 0.00) as saldo_primeiro_semestre 
            ,coalesce(stn.fn_rgf_calcula_saldo_garantias_anexo3('|| quote_literal(stExercicio) ||','|| quote_literal(stEntidade) ||', '' AND (
                                pc.cod_estrutural  = ''1.9.9.5.6.01.02.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.6.02.02.00.00.00''
                             ) '','|| quote_literal('01/01/'|| stExercicio) ||','|| quote_literal('31/12/'|| stExercicio) ||'), 0.00) as saldo_segundo_semestre 
            ,2   as sequencia
            ,''CG'' as tipo
        
        UNION
        
        select 
             ''GARANTIAS INTERNAS'' as descricao 
            ,null as saldo_exercicio_anterior
            ,null as saldo_primeiro_semestre 
            ,null as saldo_segundo_semeste 
            ,3   as sequencia
            ,''CG'' as tipo
        
        UNION
        
        select 
             ''Aval ou fiança em operações de crédito'' as descricao
            ,coalesce(stn.fn_rgf_calcula_saldo_garantias_anexo3('|| quote_literal(stExercicioAnterior) ||','|| quote_literal(stEntidade) ||', '' AND (
                                pc.cod_estrutural  = ''1.9.9.5.6.01.01.00.00.00'' 
                             or pc.cod_estrutural  = ''1.9.9.5.6.02.01.00.00.00''
                             ) '','|| quote_literal('01/01/'|| stExercicioAnterior) ||','|| quote_literal('31/12/'|| stExercicioAnterior) ||'), 0.00) as saldo_exercicio_anterior
            ,coalesce(stn.fn_rgf_calcula_saldo_garantias_anexo3('|| quote_literal(stExercicio) ||','|| quote_literal(stEntidade) ||', '' AND (
                                pc.cod_estrutural  = ''1.9.9.5.6.01.01.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.6.02.01.00.00.00''
                             ) '','|| quote_literal('01/01/'|| stExercicio) ||','|| quote_literal('30/06/'|| stExercicio) ||'), 0.00) as saldo_primeiro_semestre
            ,coalesce(stn.fn_rgf_calcula_saldo_garantias_anexo3('|| quote_literal(stExercicio) ||','|| quote_literal(stEntidade) ||', '' AND (
                                pc.cod_estrutural  = ''1.9.9.5.6.01.01.00.00.00''
                             or pc.cod_estrutural  = ''1.9.9.5.6.02.01.00.00.00''
                             ) '','|| quote_literal('01/01/'|| stExercicio) ||','|| quote_literal('31/12/'|| stExercicio) ||'), 0.00) as saldo_segundo_semestre 
            ,4    as sequencia
            ,''CG'' as tipo
        
        order by tipo desc,sequencia
    )as tbl 
';
 
     
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

RETURN;

END;

$$language 'plpgsql';

