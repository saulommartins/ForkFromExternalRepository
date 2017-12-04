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
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.02.32
*/

/*
$Log$
Revision 1.2  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION contabilidade.fn_consistencia(varchar,varchar,varchar,varchar) RETURNS BOOLEAN AS '
DECLARE
    stExercicio      ALIAS FOR $1    ;
    stCodEntidade    ALIAS FOR $2    ;
    stDtInicial      ALIAS FOR $3    ;
    stDtFinal        ALIAS FOR $4    ;
    stSql            VARCHAR := '''' ;
    reRegistro       RECORD          ;
    inCount          INTEGER := 0    ;
    stSqlComplemento VARCHAR   := '''';
    arRetorno        NUMERIC[];
    reRegistro_atua  RECORD;
    errado           BOOLEAN;
BEGIN

-----------------------------------------------------------------------------------------------------------------
----
---- TABELA AUXILIAR PARA DETECÇÃO DE CONSISTÊNCIAS (Registro de todos empenhos)
----
------------------------------------------------------------------------------------------------------------------

-- Deletando a tabela criada anteriormente

stSql = ''delete from contabilidade.consistencia'';
execute stSql;


-- Insere todos empenhos do periodo e da entidade na tabela de trabalho ---
stSql = ''
    insert into contabilidade.consistencia
        select  empenho.cod_empenho,
                empenho.exercicio,
                empenho.cod_entidade,
	            empenho.dt_empenho,
                0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00
	    from    empenho.empenho 
        WHERE
        (
	        empenho.exercicio       = ''||quote_literal(stExercicio)||'' 
            or( empenho.dt_empenho      >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')
            and empenho.dt_empenho      <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
        )
	   and  empenho.cod_entidade    in (''||stCodEntidade||'')       
       order by empenho.cod_empenho
'';
execute stSql;


-- Apura o valor empenhado --
stSql := ''
update contabilidade.consistencia set
       vlremp = lista.vl_total       
  from
       ( select emp.cod_empenho   as cod_empenho,
                emp.cod_entidade  as cod_entidade,
                emp.exercicio     as exercicio,
                sum(ite.vl_total) as vl_total
           from empenho.empenho          as emp,
                empenho.item_pre_empenho as ite
          where emp.cod_pre_empenho = ite.cod_pre_empenho
            and emp.exercicio       = ite.exercicio
            and emp.cod_entidade    in (''|| stCodEntidade||'')
            and (
	            emp.exercicio       = ''||quote_literal(stExercicio)||'' 
                or( emp.dt_empenho      >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')
                and emp.dt_empenho      <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
            )
       group by emp.cod_empenho, emp.cod_entidade, emp.exercicio
       ) as lista
       where lista.cod_empenho  = contabilidade.consistencia.cod_empenho
       and   lista.cod_entidade = contabilidade.consistencia.cod_entidade
       and   lista.exercicio    = contabilidade.consistencia.exercicio
'';
execute stSql;


--- Apura valor anulado ---
stSql := ''
update contabilidade.consistencia set
       vlranu = lista.vl_total
       --,dt_empanu = lista.dt_empanu
  from
       ( select emp.cod_empenho     as cod_empenho,
                emp.cod_entidade    as cod_entidade,
                emp.exercicio       as exercicio,
                sum(ite.vl_anulado) as vl_total
           from empenho.empenho              as emp,           
                empenho.empenho_anulado_item as ite
          where emp.cod_empenho     = ite.cod_empenho
            and emp.exercicio       = ite.exercicio
            and emp.cod_entidade    = ite.cod_entidade
            and emp.cod_entidade    in (''||stCodEntidade||'')
            and (
	            emp.exercicio       = ''||quote_literal(stExercicio)||'' 
                or( to_date( to_char( ite.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')
                and to_date( to_char( ite.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
            )

       group by emp.cod_empenho, emp.cod_entidade, emp.exercicio
       ) as lista
        where lista.cod_empenho     = contabilidade.consistencia.cod_empenho
        and   lista.cod_entidade    = contabilidade.consistencia.cod_entidade
        and   lista.exercicio       = contabilidade.consistencia.exercicio
'';
execute stSql;


-- Apura valor liquidado --
stSql := ''
update contabilidade.consistencia set
       vlrliq = lista.vl_total
       --,dt_empliq = lista.dt_empliq
  from
       ( select emp.cod_empenho     as cod_empenho,
                emp.cod_entidade    as cod_entidade,
                emp.exercicio       as exercicio,
                sum(ite.vl_total)   as vl_total
           from empenho.empenho              as emp,
                empenho.nota_liquidacao      as liq,
	        empenho.nota_liquidacao_item as ite
          where emp.cod_empenho     = liq.cod_empenho
	    and emp.exercicio           = liq.exercicio_empenho
	    and emp.cod_entidade        = liq.cod_entidade
	    and liq.cod_nota            = ite.cod_nota 
	    and liq.exercicio           = ite.exercicio
	    and liq.cod_entidade        = ite.cod_entidade
        and (
	        emp.exercicio       = ''||quote_literal(stExercicio)||'' 
            or( liq.dt_liquidacao       >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')
            and liq.dt_liquidacao       <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
        )
	   and emp.cod_entidade        in (''||stCodEntidade||'')
       group by emp.cod_empenho, emp.cod_entidade, emp.exercicio
       ) as lista
       where lista.cod_empenho      = contabilidade.consistencia.cod_empenho
       and   lista.cod_entidade     = contabilidade.consistencia.cod_entidade
       and   lista.exercicio        = contabilidade.consistencia.exercicio
'';
execute stSql;


-- Apura valor anulado de liquidacao --
stSql := ''
update contabilidade.consistencia set
       vlrliqanu = coalesce(lista.vl_total,0.00)
  from
       ( select emp.cod_empenho     as cod_empenho,
                emp.cod_entidade    as cod_entidade,
                emp.exercicio       as exercicio,
                sum(ite.vl_anulado) as vl_total
           from empenho.empenho     as emp,
	        empenho.nota_liquidacao as liq,
	        empenho.nota_liquidacao_item_anulado as ite
          where emp.cod_empenho     = liq.cod_empenho
	    and emp.exercicio           = liq.exercicio_empenho
	    and emp.cod_entidade        = liq.cod_entidade
	    and liq.cod_nota            = ite.cod_nota
	    and liq.exercicio           = ite.exercicio
	    and liq.cod_entidade     = ite.cod_entidade
        and (
	        emp.exercicio       = ''||quote_literal(stExercicio)||'' 
            or( to_date( to_char( ite.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')
            and to_date( to_char( ite.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
        )
	    and emp.cod_entidade        in (''||stCodEntidade||'')
       group by emp.cod_empenho, emp.cod_entidade, emp.exercicio
       ) as lista
       where lista.cod_empenho      = contabilidade.consistencia.cod_empenho
       and   lista.cod_entidade     = contabilidade.consistencia.cod_entidade
       and   lista.exercicio        = contabilidade.consistencia.exercicio
'';
execute stSql;

     
-- Apura valor pago --
stSql := ''
update contabilidade.consistencia set
       vlrpag = lista.vl_total
       --,dt_emppag = lista.dt_emppag
  from
       ( select emp.cod_empenho     as cod_empenho,
                emp.cod_entidade    as cod_entidade,
                emp.exercicio       as exercicio,
                sum(pag.vl_pago)    as vl_total
           from empenho.empenho     as emp,
	        empenho.nota_liquidacao as liq,
		empenho.nota_liquidacao_paga as pag
        where emp.cod_empenho       = liq.cod_empenho
	    and emp.exercicio           = liq.exercicio_empenho
	    and emp.cod_entidade        = liq.cod_entidade
        and liq.cod_entidade        = pag.cod_entidade
        and liq.cod_nota            = pag.cod_nota
        and liq.exercicio           = pag.exercicio
        and (
	        emp.exercicio       = ''||quote_literal(stExercicio)||'' 
            or( to_date( to_char( pag.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')
            and to_date( to_char( pag.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
        )
	    and emp.cod_entidade        in (''||stCodEntidade||'')
       group by emp.cod_empenho, emp.cod_entidade, emp.exercicio
       ) as lista
       where lista.cod_empenho      = contabilidade.consistencia.cod_empenho
       and   lista.cod_entidade     = contabilidade.consistencia.cod_entidade
       and   lista.exercicio        = contabilidade.consistencia.exercicio
'';
execute stSql;


--- Apura valor de estorno de pagamento ---
stSql := ''
update contabilidade.consistencia set
       vlrpagest = coalesce(lista.vl_total,0.00)
       --,dt_emppagest = lista.dt_emppagest
  from
       ( select emp.cod_empenho     as cod_empenho,
                emp.cod_entidade    as cod_entidade,
                emp.exercicio       as exercicio,
                sum(pag.vl_anulado) as vl_total
           from empenho.empenho     as emp,
	        empenho.nota_liquidacao as liq,
		empenho.nota_liquidacao_paga_anulada as pag
          where emp.cod_empenho     = liq.cod_empenho
	    and emp.exercicio           = liq.exercicio_empenho
	    and emp.cod_entidade        = liq.cod_entidade
        and liq.cod_entidade        = pag.cod_entidade
        and liq.cod_nota            = pag.cod_nota
        and liq.exercicio           = pag.exercicio
        and (
	        emp.exercicio       = ''||quote_literal(stExercicio)||'' 
            or( to_date( to_char( pag.timestamp_anulada, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')
            and to_date( to_char( pag.timestamp_anulada, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
        )
	    and emp.cod_entidade        in (''||stCodEntidade||'')
       group by emp.cod_empenho, emp.cod_entidade, emp.exercicio
       ) as lista
       where lista.cod_empenho      = contabilidade.consistencia.cod_empenho
       and   lista.cod_entidade     = contabilidade.consistencia.cod_entidade
       and   lista.exercicio        = contabilidade.consistencia.exercicio
'';
execute stSql;


--- Apura valor empenhado nos lançamentos contabeis ---
stSql := ''
update contabilidade.consistencia set
       ctbemp = lista.vl_lancamento
  from
       (select  emp.cod_empenho                 as cod_empenho,
                emp.cod_entidade                as cod_entidade,
                emp.exercicio                   as exercicio,
                max(vlr.vl_lancamento)          as vl_lancamento
           from empenho.empenho                 as emp,
	        contabilidade.empenhamento          as cem,
	        contabilidade.lancamento_empenho    as lem,
		    contabilidade.lancamento            as lan,
		    contabilidade.valor_lancamento      as vlr,
            contabilidade.lote                  as lot
        where emp.cod_empenho         = cem.cod_empenho
	    and emp.exercicio     = cem.exercicio_empenho
	    and emp.cod_entidade  = cem.cod_entidade
	    and cem.cod_lote      = lem.cod_lote
	    and cem.tipo          = lem.tipo
	    and cem.exercicio     = lem.exercicio
	    and cem.cod_entidade  = lem.cod_entidade
	    and not lem.estorno
	    and lem.cod_lote      = lan.cod_lote
	    and lem.tipo          = lan.tipo
	    and lem.exercicio     = lan.exercicio
	    and lem.cod_entidade  = lan.cod_entidade
	    and lan.cod_lote      = lot.cod_lote
	    and lan.tipo          = lot.tipo
	    and lan.exercicio     = lot.exercicio
	    and lan.cod_entidade  = lot.cod_entidade
	    and lan.cod_lote      = vlr.cod_lote
	    and lan.tipo          = vlr.tipo
	    and lan.sequencia     = vlr.sequencia
	    and lan.exercicio     = vlr.exercicio
	    and lan.cod_entidade  = vlr.cod_entidade
	    and emp.cod_entidade  in (''||stCodEntidade||'')
	    and (
            emp.exercicio     = ''||quote_literal(stExercicio)||''
            or( lot.dt_lote       >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')        
            and lot.dt_lote       <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
        )
       group by emp.cod_empenho, emp.cod_entidade, emp.exercicio
       ) as lista
       where lista.cod_empenho      = contabilidade.consistencia.cod_empenho
       and   lista.cod_entidade     = contabilidade.consistencia.cod_entidade
       and   lista.exercicio        = contabilidade.consistencia.exercicio
'';
execute stSql;


--- Apura valores de empenhamento anulados nos lançamentos contabeis ---
stSql := ''
update contabilidade.consistencia set
       ctbanu       = lista.vl_lancamento
  from
       (select  emp.cod_empenho                 as cod_empenho,
                emp.cod_entidade                as cod_entidade,
                emp.exercicio                   as exercicio,
                max(vlr.vl_lancamento)          as vl_lancamento
           from empenho.empenho                 as emp,
	        contabilidade.empenhamento          as cem,
	        contabilidade.lancamento_empenho    as lem,
		    contabilidade.lancamento            as lan,
		    contabilidade.valor_lancamento      as vlr,
            contabilidade.lote                  as lot
        where emp.cod_empenho         = cem.cod_empenho
	    and emp.exercicio     = cem.exercicio_empenho
	    and emp.cod_entidade  = cem.cod_entidade
	    and cem.cod_lote      = lem.cod_lote
	    and cem.tipo          = lem.tipo
	    and cem.exercicio     = lem.exercicio
	    and cem.cod_entidade  = lem.cod_entidade
	    and lem.estorno
	    and lem.cod_lote      = lan.cod_lote
	    and lem.tipo          = lan.tipo
	    and lem.exercicio     = lan.exercicio
	    and lem.cod_entidade  = lan.cod_entidade
	    and lan.cod_lote      = lot.cod_lote
	    and lan.tipo          = lot.tipo
	    and lan.exercicio     = lot.exercicio
	    and lan.cod_entidade  = lot.cod_entidade
	    and lan.cod_lote      = vlr.cod_lote
	    and lan.tipo          = vlr.tipo
	    and lan.sequencia     = vlr.sequencia
	    and lan.exercicio     = vlr.exercicio
	    and lan.cod_entidade  = vlr.cod_entidade
	    and (
            emp.exercicio     = ''||quote_literal(stExercicio)||''
            or( lot.dt_lote       >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')        
            and lot.dt_lote       <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
        )
	    and emp.cod_entidade        in (''||stCodEntidade||'')
       group by emp.cod_empenho, emp.cod_entidade, emp.exercicio
       ) as lista
       where lista.cod_empenho      = contabilidade.consistencia.cod_empenho
       and   lista.cod_entidade     = contabilidade.consistencia.cod_entidade
       and   lista.exercicio        = contabilidade.consistencia.exercicio
'';
execute stSql;


--- Apura valores liquidados nos lançamentos contabeis ---
stSql := ''
update contabilidade.consistencia set
       ctbliq = lista.vl_lancamento
  from (
  select 
    sum(lista.vl_lancamento) as vl_lancamento,
    lista.cod_empenho,
    lista.cod_entidade,
    lista.exercicio
    from
       ( select ntl.cod_empenho                 as cod_empenho,
                ntl.cod_entidade                as cod_entidade,
                ntl.exercicio_empenho           as exercicio,
                abs(vlr.vl_lancamento)          as vl_lancamento,
                lot.cod_lote
           from empenho.nota_liquidacao         as ntl,
	        contabilidade.liquidacao            as liq,
	        contabilidade.lancamento_empenho    as lem,
     	    contabilidade.lancamento            as lan,
		    contabilidade.valor_lancamento      as vlr,
            contabilidade.lote                  as lot
          where ntl.cod_nota        = liq.cod_nota
	    and ntl.exercicio           = liq.exercicio_liquidacao
	    and ntl.cod_entidade        = liq.cod_entidade
	    and liq.cod_lote            = lem.cod_lote
	    and liq.tipo                = lem.tipo
	    and liq.exercicio           = lem.exercicio
	    and liq.cod_entidade        = lem.cod_entidade
	    and not lem.estorno         
	    and lem.cod_lote            = lan.cod_lote
	    and lem.tipo                = lan.tipo
	    and lem.exercicio           = lan.exercicio
	    and lem.cod_entidade        = lan.cod_entidade
	    and lan.cod_lote            = lot.cod_lote
	    and lan.tipo                = lot.tipo
	    and lan.exercicio           = lot.exercicio
	    and lan.cod_entidade        = lot.cod_entidade
	    and lan.cod_lote            = vlr.cod_lote
	    and lan.tipo                = vlr.tipo
	    and lan.sequencia           = vlr.sequencia
	    and lan.exercicio           = vlr.exercicio
	    and lan.cod_entidade        = vlr.cod_entidade
	    and ntl.cod_entidade        in (''||stCodEntidade||'')
	    and (
            ntl.exercicio_empenho   = ''||quote_literal(stExercicio)||''
            or( lot.dt_lote         >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')        
            and lot.dt_lote         <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
        )
        and vlr.tipo_valor=''''D''''
       group by ntl.cod_empenho, ntl.cod_entidade, ntl.exercicio_empenho, vlr.vl_lancamento, lot.cod_lote
       ) as lista
    group by lista.cod_empenho, lista.cod_entidade, lista.exercicio
  ) as lista
where lista.cod_empenho      = contabilidade.consistencia.cod_empenho
and   lista.cod_entidade     = contabilidade.consistencia.cod_entidade
and   lista.exercicio        = contabilidade.consistencia.exercicio
'';
execute stSql;


--- Apura valores de anulação de liquidações nos lançamentos contabeis ---
stSql := ''
update contabilidade.consistencia set
       ctbliqanu = lista.vl_lancamento
  from(
  select 
    sum(lista.vl_lancamento) as vl_lancamento,
    lista.cod_empenho,
    lista.cod_entidade,
    lista.exercicio
    from

       ( select ntl.cod_empenho                 as cod_empenho,
                ntl.cod_entidade                as cod_entidade,
                ntl.exercicio_empenho           as exercicio,
                abs(vlr.vl_lancamento)          as vl_lancamento,
                lot.cod_lote
           from empenho.nota_liquidacao         as ntl,
	        contabilidade.liquidacao            as liq,
	        contabilidade.lancamento_empenho    as lem,
     	    contabilidade.lancamento            as lan,
		    contabilidade.valor_lancamento      as vlr,
            contabilidade.lote                  as lot
          where ntl.cod_nota        = liq.cod_nota
	    and ntl.exercicio           = liq.exercicio_liquidacao
	    and ntl.cod_entidade        = liq.cod_entidade
	    and liq.cod_lote            = lem.cod_lote
	    and liq.tipo                = lem.tipo
	    and liq.exercicio           = lem.exercicio
	    and liq.cod_entidade        = lem.cod_entidade
	    and lem.estorno         
	    and lem.cod_lote            = lan.cod_lote
	    and lem.tipo                = lan.tipo
	    and lem.exercicio           = lan.exercicio
	    and lem.cod_entidade        = lan.cod_entidade
	    and lan.cod_lote            = lot.cod_lote
	    and lan.tipo                = lot.tipo
	    and lan.exercicio           = lot.exercicio
	    and lan.cod_entidade        = lot.cod_entidade
	    and lan.cod_lote            = vlr.cod_lote
	    and lan.tipo                = vlr.tipo
	    and lan.sequencia           = vlr.sequencia
	    and lan.exercicio           = vlr.exercicio
	    and lan.cod_entidade        = vlr.cod_entidade
	    and (
            ntl.exercicio_empenho   = ''||quote_literal(stExercicio)||''
            or( lot.dt_lote         >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')        
            and lot.dt_lote         <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
        )
	    and ntl.cod_entidade        in (''||stCodEntidade||'')
        and vlr.tipo_valor          = ''''D''''
       group by ntl.cod_empenho, ntl.cod_entidade, ntl.exercicio_empenho, vlr.vl_lancamento, lot.cod_lote
       ) as lista
    group by lista.cod_empenho, lista.cod_entidade, lista.exercicio
  ) as lista
where lista.cod_empenho      = contabilidade.consistencia.cod_empenho
and   lista.cod_entidade     = contabilidade.consistencia.cod_entidade
and   lista.exercicio        = contabilidade.consistencia.exercicio
'';
execute stSql;


--- Apura valores de pagamento entre contabilidade e empenho ---
stSql := ''
update contabilidade.consistencia set
       ctbpag       = lista.vl_lancamento
  from
       ( select ntl.cod_empenho                 as cod_empenho,
                ntl.cod_entidade                as cod_entidade,
                ntl.exercicio_empenho           as exercicio,
                max(vlr.vl_lancamento)          as vl_lancamento
           from empenho.nota_liquidacao         as ntl,
	        empenho.nota_liquidacao_paga        as ntp,
	        contabilidade.pagamento             as pag,
	        contabilidade.lancamento_empenho    as lem,
		    contabilidade.lancamento            as lan,
		    contabilidade.valor_lancamento      as vlr,
            contabilidade.lote                  as lot
          where ntl.cod_nota        = ntp.cod_nota
	    and ntl.exercicio           = ntp.exercicio
	    and ntl.cod_entidade        = ntp.cod_entidade
        and ntp.cod_nota            = pag.cod_nota
	    and ntp.exercicio           = pag.exercicio_liquidacao
	    and ntp.cod_entidade        = pag.cod_entidade
	    and ntp.timestamp           = pag.timestamp
	    and pag.cod_lote            = lem.cod_lote
	    and pag.tipo                = lem.tipo
	    and pag.exercicio           = lem.exercicio
	    and pag.cod_entidade        = lem.cod_entidade
	    and not lem.estorno         
	    and lem.cod_lote            = lan.cod_lote
	    and lem.tipo                = lan.tipo
	    and lem.exercicio           = lan.exercicio
	    and lem.cod_entidade        = lan.cod_entidade
	    and lan.cod_lote            = lot.cod_lote
	    and lan.tipo                = lot.tipo
	    and lan.exercicio           = lot.exercicio
	    and lan.cod_entidade        = lot.cod_entidade
	    and lan.cod_lote            = vlr.cod_lote
	    and lan.tipo                = vlr.tipo
	    and lan.sequencia           = vlr.sequencia
	    and lan.exercicio           = vlr.exercicio
	    and lan.cod_entidade        = vlr.cod_entidade
	    and ntl.cod_entidade        in (''||stCodEntidade||'')
	    and (
            ntl.exercicio_empenho   = ''||quote_literal(stExercicio)||''
            or( lot.dt_lote         >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')        
            and lot.dt_lote         <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
        )
       group by ntl.cod_empenho, ntl.cod_entidade, ntl.exercicio_empenho
       ) as lista
       where lista.cod_empenho      = contabilidade.consistencia.cod_empenho
       and   lista.cod_entidade     = contabilidade.consistencia.cod_entidade
       and   lista.exercicio        = contabilidade.consistencia.exercicio
'';
execute stSql;
        

--- Apura valores de estorno de pagamento entre contabilidade e empenho ---
stSql := ''
update contabilidade.consistencia set
       ctbpagest       = lista.vl_lancamento
  from
       ( select ntl.cod_empenho                 as cod_empenho,
                ntl.cod_entidade                as cod_entidade,
                ntl.exercicio_empenho           as exercicio,
                max(vlr.vl_lancamento)          as vl_lancamento
           from empenho.nota_liquidacao         as ntl,
	        empenho.nota_liquidacao_paga        as ntp,
	        contabilidade.pagamento             as pag,
	        contabilidade.lancamento_empenho    as lem,
		    contabilidade.lancamento            as lan,
		    contabilidade.valor_lancamento      as vlr,
            contabilidade.lote                  as lot
          where ntl.cod_nota        = ntp.cod_nota
	    and ntl.exercicio           = ntp.exercicio
	    and ntl.cod_entidade        = ntp.cod_entidade
        and ntp.cod_nota            = pag.cod_nota
	    and ntp.exercicio           = pag.exercicio_liquidacao
	    and ntp.cod_entidade        = pag.cod_entidade
	    and ntp.timestamp           = pag.timestamp
	    and pag.cod_lote            = lem.cod_lote
	    and pag.tipo                = lem.tipo
	    and pag.exercicio           = lem.exercicio
	    and pag.cod_entidade        = lem.cod_entidade
	    and lem.estorno         
	    and lem.cod_lote            = lan.cod_lote
	    and lem.tipo                = lan.tipo
	    and lem.exercicio           = lan.exercicio
	    and lem.cod_entidade        = lan.cod_entidade
	    and lan.cod_lote            = lot.cod_lote
	    and lan.tipo                = lot.tipo
	    and lan.exercicio           = lot.exercicio
	    and lan.cod_entidade        = lot.cod_entidade
	    and lan.cod_lote            = vlr.cod_lote
	    and lan.tipo                = vlr.tipo
	    and lan.sequencia           = vlr.sequencia
	    and lan.exercicio           = vlr.exercicio
	    and lan.cod_entidade        = vlr.cod_entidade
	    and ntl.cod_entidade        in (''||stCodEntidade||'')
	    and (
            ntl.exercicio_empenho   = ''||quote_literal(stExercicio)||''
            or( lot.dt_lote         >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')        
            and lot.dt_lote         <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
        )
       group by ntl.cod_empenho, ntl.cod_entidade, ntl.exercicio_empenho
       ) as lista
       where lista.cod_empenho      = contabilidade.consistencia.cod_empenho
       and   lista.cod_entidade     = contabilidade.consistencia.cod_entidade
       and   lista.exercicio        = contabilidade.consistencia.exercicio
'';
execute stSql;
-------------------------------------------------------------------------------------------------------------------
----
---- TABELA AUXILIAR PARA DETECÇÃO DE CONSISTÊNCIAS (Registro de todos lançamentos)
----
-------------------------------------------------------------------------------------------------------------------

-- Deletando a tabela criada anteriormente
select count(*) into inCount from pg_tables where tablename=''contabilidade.consistencia_contab'';
IF inCount>0 THEN
    stSql = ''drop table contabilidade.consistencia_contab'';
        execute stSql;
        END IF;

 -- Cria tabela de trabalho novamente (contabilidade.consistencia_contab)
stSql := ''
    create table contabilidade.consistencia_contab (
       cod_empenho           integer,
       exercicio_empenho     character(4),
       exercicio             character(4),
       cod_entidade           integer,
       cod_lote              integer,
       dt_lote               date,
       dt_empenho            date,
       tipo                  character(1),
       estorno               boolean,
       sequencia             integer,
       vl_lancamento         numeric(14,2),
       complemento           varchar(300)
    )
'';

stSql = ''delete from contabilidade.consistencia_contab'';
execute stSql;


execute stSql;

--- Insere todos lancamentos de empenhos ---
stSql := ''
    insert into contabilidade.consistencia_contab(
    select 0,
      ''''   '''',
      lan.exercicio,
      len.cod_entidade,
      lan.cod_lote,
          lot.dt_lote,
      null,
      lan.tipo,
      len.estorno,
      count(lan.sequencia) as sequencia,
      abs(vlr.vl_lancamento) as vl_lancamento,
      lan.complemento
    from contabilidade.lancamento_empenho as len,
          contabilidade.lancamento         as lan,
          contabilidade.lote               as lot,
      contabilidade.valor_lancamento   as vlr
    where 
      (len.tipo        = ''''E'''' or
       len.tipo        = ''''L'''' or
       len.tipo        = ''''P'''' )
      and len.cod_lote     = lan.cod_lote
      and len.tipo         = lan.tipo
      and len.exercicio    = lan.exercicio
      and len.cod_entidade = lan.cod_entidade
      
      and lan.cod_lote     = lot.cod_lote
      and lan.exercicio    = lot.exercicio
      and lan.tipo         = lot.tipo
      and lan.cod_entidade = lot.cod_entidade
      
      and lan.cod_lote     = vlr.cod_lote
      and lan.exercicio    = vlr.exercicio
      and lan.tipo         = vlr.tipo
      and lan.cod_entidade = vlr.cod_entidade
      and lan.sequencia    = vlr.sequencia
      and ''''D''''        = vlr.tipo_valor
      
      and lan.cod_entidade        in (''||stCodEntidade||'')
      and(
            lan.exercicio    = ''||quote_literal(stExercicio)||''
            or( lot.dt_lote             >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')        
            and lot.dt_lote             <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
      ) 
    group by
      lan.exercicio,
      len.cod_entidade,
      lan.cod_lote,
      lot.dt_lote,
      lan.tipo,
      len.estorno,
      abs(vlr.vl_lancamento),
      lan.complemento
)
'';
execute stSql;


--- Busca empenhos e datas de empenhamento ---
stSql := ''
    update contabilidade.consistencia_contab set
       cod_empenho         = lista.cod_empenho,
       dt_empenho          = lista.dt_empenho,
       exercicio_empenho   = lista.exercicio_empenho
    from
       ( select cem.cod_empenho       as cod_empenho,
                cem.exercicio_empenho as exercicio_empenho,
        		emp.dt_empenho        as dt_empenho,
                len.cod_entidade      as cod_entidade,
                cem.exercicio         as exercicio,
                cem.sequencia         as sequencia,
                cem.tipo              as tipo,
                cem.cod_lote          as cod_lote,
		        len.estorno           as estorno
           from contabilidade.lancamento_empenho as len,
	        contabilidade.empenhamento       as cem,
	        empenho.empenho                  as emp
          where len.cod_entidade      = cem.cod_entidade
	    and len.exercicio         = cem.exercicio
	    and len.cod_lote          = cem.cod_lote
	    and len.tipo              = cem.tipo
	    and len.sequencia         = cem.sequencia
	    and not len.estorno
	    and cem.cod_empenho       = emp.cod_empenho
	    and cem.exercicio_empenho = emp.exercicio
	    and cem.cod_entidade      = emp.cod_entidade
        and emp.cod_entidade      in (''||stCodEntidade||'')
        and(
            emp.exercicio         = ''||quote_literal(stExercicio)||''
            or(emp.dt_empenho        >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')        
            and emp.dt_empenho        <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
        )
       ) as lista
    where lista.exercicio    = contabilidade.consistencia_contab.exercicio
--         and lista.sequencia    = contabilidade.consistencia_contab.sequencia
	     and lista.tipo         = contabilidade.consistencia_contab.tipo
	     and lista.cod_lote     = contabilidade.consistencia_contab.cod_lote
	     and lista.estorno      = contabilidade.consistencia_contab.estorno
         and lista.cod_entidade = contabilidade.consistencia_contab.cod_entidade
'';
execute stSql;


--- Busca empenhos e datas de anulação ---
stSql := ''
    update contabilidade.consistencia_contab set
       cod_empenho         = lista.cod_empenho,
       dt_empenho          = lista.dt_empenho,
       exercicio_empenho   = lista.exercicio_empenho
    from( 
        select cem.cod_empenho       as cod_empenho,
                cem.exercicio_empenho as exercicio_empenho,
                to_date(to_char(anu.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') as dt_empenho,
                cem.exercicio         as exercicio,
                cem.sequencia         as sequencia,
                cem.tipo              as tipo,
                cem.cod_lote          as cod_lote,
                len.cod_entidade      as cod_entidade,
		        len.estorno           as estorno,
		        ani.vl_anulado        as vl_anulado

        from contabilidade.lancamento_empenho as len,
	        contabilidade.empenhamento       as cem,
	        empenho.empenho                  as emp,
		    empenho.empenho_anulado          as anu,
		    (select cod_entidade,
		            exercicio,
			        cod_empenho,
			        timestamp,
			        sum(vl_anulado) as vl_anulado
		    from empenho.empenho_anulado_item
		    where cod_entidade in (''||stCodEntidade||'')
		    group by cod_entidade,
		            exercicio,
			        cod_empenho,
			        timestamp
		    ) as ani
        where   len.cod_entidade      = cem.cod_entidade
	        and len.exercicio         = cem.exercicio
	        and len.cod_lote          = cem.cod_lote
	        and len.tipo              = cem.tipo
	        and len.sequencia         = cem.sequencia
	        and len.estorno
	        and cem.cod_empenho       = emp.cod_empenho
	        and cem.exercicio_empenho = emp.exercicio
	        and cem.cod_entidade      = emp.cod_entidade
	        and emp.cod_empenho       = anu.cod_empenho
	        and emp.exercicio         = anu.exercicio
	        and emp.cod_entidade      = anu.cod_entidade
	        and anu.cod_entidade      = ani.cod_entidade
	        and anu.exercicio         = ani.exercicio
	        and anu.cod_empenho       = ani.cod_empenho
	        and anu.timestamp         = ani.timestamp
            and(
                emp.exercicio         = ''||quote_literal(stExercicio)||''
                or(emp.dt_empenho        >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')        
                and emp.dt_empenho        <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
            )
            and emp.cod_entidade      in (''||stCodEntidade||'')

    ) as lista
    where   lista.exercicio    = contabilidade.consistencia_contab.exercicio
--        and lista.sequencia    = contabilidade.consistencia_contab.sequencia
	    and lista.tipo         = contabilidade.consistencia_contab.tipo
	    and lista.cod_lote     = contabilidade.consistencia_contab.cod_lote
	    and lista.estorno      = contabilidade.consistencia_contab.estorno
	    and lista.vl_anulado   = contabilidade.consistencia_contab.vl_lancamento
        and lista.cod_entidade = contabilidade.consistencia_contab.cod_entidade
'';
execute stSql;

--- Busca empenhos e datas de liquidação ---
stSql := ''
    update contabilidade.consistencia_contab set
       cod_empenho         = lista.cod_empenho,
       dt_empenho          = lista.dt_empenho,
       exercicio_empenho   = lista.exercicio_empenho
    from( 
        select  nlq.cod_empenho       as cod_empenho,
                nlq.exercicio_empenho as exercicio_empenho,
                nlq.dt_liquidacao     as dt_empenho,
                len.exercicio         as exercicio,
                len.sequencia         as sequencia,
                len.tipo              as tipo,
                len.cod_lote          as cod_lote,
                len.cod_entidade      as cod_entidade,
		        len.estorno           as estorno
        from    contabilidade.lancamento_empenho as len,
	            contabilidade.liquidacao         as liq,
		        empenho.nota_liquidacao          as nlq
		where   len.cod_entidade         = liq.cod_entidade
	        and len.exercicio            = liq.exercicio
	        and len.cod_lote             = liq.cod_lote
	        and len.tipo                 = liq.tipo
	        and len.sequencia            = liq.sequencia
	        and not len.estorno
            and liq.cod_nota             = nlq.cod_nota
	        and liq.exercicio_liquidacao = nlq.exercicio
	        and liq.cod_entidade         = nlq.cod_entidade
            and(
                nlq.exercicio_empenho    = ''||quote_literal(stExercicio)||''
                or(nlq.dt_liquidacao     >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')        
                and nlq.dt_liquidacao    <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
            )
            and nlq.cod_entidade      in (''||stCodEntidade||'')
        ) as lista
    where   lista.exercicio    = contabilidade.consistencia_contab.exercicio
--        and lista.sequencia    = contabilidade.consistencia_contab.sequencia
	    and lista.tipo         = contabilidade.consistencia_contab.tipo
	    and lista.cod_lote     = contabilidade.consistencia_contab.cod_lote
	    and lista.estorno      = contabilidade.consistencia_contab.estorno
        and lista.cod_entidade = contabilidade.consistencia_contab.cod_entidade
'';
execute stSql;

--- Busca empenhos e datas de anulacao de liquidação ---
stSql := ''
    update  contabilidade.consistencia_contab set
            cod_empenho         = lista.cod_empenho,
            dt_empenho          = lista.dt_empenho,
            exercicio_empenho   = lista.exercicio_empenho
    from( 
        select  nlq.cod_empenho       as cod_empenho,
                nlq.exercicio_empenho as exercicio_empenho,
                to_date(to_char(nla.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''')   as dt_empenho,
                len.exercicio         as exercicio,
                len.sequencia         as sequencia,
                len.tipo              as tipo,
                len.cod_lote          as cod_lote,
                len.cod_entidade      as cod_entidade,
		        len.estorno           as estorno,
		        nla.vl_anulado        as vl_anulado
        from    contabilidade.lancamento_empenho     as len,
	            contabilidade.liquidacao             as liq,
		        empenho.nota_liquidacao              as nlq,
		        (select cod_entidade,
		                exercicio,
			            cod_nota,
			            sum(vl_total) as vl_total
		        from empenho.nota_liquidacao_item
		        where cod_entidade  in (''||stCodEntidade||'')
		        group by    cod_entidade,
		                    exercicio,
			                cod_nota
        		) as nli,
		        (select cod_entidade,
		                exercicio,
			            cod_nota,
			            timestamp,
			            sum(vl_anulado) as vl_anulado
		        from empenho.nota_liquidacao_item_anulado
		        where cod_entidade in (''||stCodEntidade||'')
		        group by    cod_entidade,
		                    exercicio,
			                cod_nota,
			                timestamp
		        ) as nla
	    where   len.cod_entidade         = liq.cod_entidade
	        and len.exercicio            = liq.exercicio
	        and len.cod_lote             = liq.cod_lote
	        and len.tipo                 = liq.tipo
	        and len.sequencia            = liq.sequencia
	        and len.estorno
            and liq.cod_nota             = nlq.cod_nota
	        and liq.exercicio_liquidacao = nlq.exercicio
	        and liq.cod_entidade         = nlq.cod_entidade
            and nlq.cod_nota             = nli.cod_nota
	        and nlq.exercicio            = nli.exercicio
	        and nlq.cod_entidade         = nli.cod_entidade
            and nli.cod_nota             = nla.cod_nota       
	        and nli.exercicio            = nla.exercicio      
	        and nli.cod_entidade         = nla.cod_entidade   
            and nlq.cod_entidade      in (''||stCodEntidade||'')
            and(
                nlq.exercicio_empenho    = ''||quote_literal(stExercicio)||''
                or(nlq.dt_liquidacao     >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')        
                and nlq.dt_liquidacao    <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
            )
        ) as lista
    where   lista.exercicio    = contabilidade.consistencia_contab.exercicio
--        and lista.sequencia    = contabilidade.consistencia_contab.sequencia
	    and lista.tipo         = contabilidade.consistencia_contab.tipo
	    and lista.cod_lote     = contabilidade.consistencia_contab.cod_lote
	    and lista.estorno      = contabilidade.consistencia_contab.estorno
	    and lista.vl_anulado   = contabilidade.consistencia_contab.vl_lancamento
        and lista.cod_entidade = contabilidade.consistencia_contab.cod_entidade
'';
execute stSql;
-- LENTOO

--- Busca empenhos e datas de pagamento --
stSql := ''
    update contabilidade.consistencia_contab set
       cod_empenho         = lista.cod_empenho,
       dt_empenho          = lista.dt_empenho,
       exercicio_empenho   = lista.exercicio_empenho
    from( 
        select  nl.cod_empenho        as cod_empenho,
                nl.exercicio_empenho  as exercicio_empenho,
                to_date(to_char(nlp.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') as dt_empenho,
                len.exercicio         as exercicio,
                len.sequencia         as sequencia,
                len.tipo              as tipo,
                len.cod_lote          as cod_lote,
                len.cod_entidade      as cod_entidade,
		        len.estorno           as estorno
        from    contabilidade.lancamento_empenho as len,
	            contabilidade.pagamento          as pag,
		        empenho.nota_liquidacao_paga     as nlp,
                empenho.nota_liquidacao          as nl
		where   len.cod_entidade         = pag.cod_entidade
	        and len.exercicio            = pag.exercicio
	        and len.cod_lote             = pag.cod_lote
	        and len.tipo                 = pag.tipo
	        and len.sequencia            = pag.sequencia
	        and not len.estorno
            and pag.cod_nota             = nlp.cod_nota
	        and pag.exercicio_liquidacao = nlp.exercicio
	        and pag.cod_entidade         = nlp.cod_entidade
            and pag.timestamp            = nlp.timestamp
            and nlp.exercicio            = nl.exercicio
            and nlp.cod_nota             = nl.cod_nota
            and nlp.cod_entidade         = nl.cod_entidade
            and(
                nl.exercicio_empenho    = ''||quote_literal(stExercicio)||''
                or(to_date( to_char( nlp.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' )  >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')        
                and to_date( to_char( nlp.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' )  <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
            )
            and nlp.cod_entidade      in (''||stCodEntidade||'')

        ) as lista
    where   lista.exercicio    = contabilidade.consistencia_contab.exercicio
--        and lista.sequencia    = contabilidade.consistencia_contab.sequencia
	    and lista.tipo         = contabilidade.consistencia_contab.tipo
	    and lista.cod_lote     = contabilidade.consistencia_contab.cod_lote
	    and lista.estorno      = contabilidade.consistencia_contab.estorno
        and lista.cod_entidade = contabilidade.consistencia_contab.cod_entidade
'';
execute stSql;


--- Busca empenhos e data de estorno de pagamentos ---
stSql := ''
    update contabilidade.consistencia_contab set
       cod_empenho         = lista.cod_empenho,
       dt_empenho          = lista.dt_empenho,
       exercicio_empenho   = lista.exercicio_empenho
    from( 
        select  liq.cod_empenho       as cod_empenho,
                liq.exercicio_empenho as exercicio_empenho,
                to_date(to_char(nla.timestamp_anulada,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') as dt_empenho,
                len.exercicio         as exercicio,
                len.sequencia         as sequencia,
                len.tipo              as tipo,
                len.cod_lote          as cod_lote,
                len.cod_entidade      as cod_entidade,
		        len.estorno           as estorno
        from contabilidade.lancamento_empenho     as len,
    	        contabilidade.pagamento              as pag,
	        	empenho.nota_liquidacao_paga         as nlp,
        		empenho.nota_liquidacao              as liq,
        		empenho.nota_liquidacao_paga_anulada as nla
	    where   len.cod_entidade         = pag.cod_entidade
	        and len.exercicio            = pag.exercicio
	        and len.cod_lote             = pag.cod_lote
	        and len.tipo                 = pag.tipo
	        and len.sequencia            = pag.sequencia
	        and len.estorno
	        and pag.cod_entidade         = nlp.cod_entidade
	        and pag.cod_nota             = nlp.cod_nota
	        and pag.exercicio_liquidacao = nlp.exercicio
	        and pag.timestamp            = nlp.timestamp
	        and nlp.cod_nota             = liq.cod_nota
	        and nlp.exercicio            = liq.exercicio
	        and nlp.cod_entidade         = liq.cod_entidade
	        and nlp.cod_nota             = nla.cod_nota
	        and nlp.exercicio            = nla.exercicio
	        and nlp.cod_entidade         = nla.cod_entidade
	        and nlp.timestamp            = nla.timestamp
            and liq.cod_entidade      in (''||stCodEntidade||'')
            and(
                liq.exercicio_empenho    = ''||quote_literal(stExercicio)||''
                or(to_date( to_char( nla.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' )  >= to_date(''||quote_literal(stDtInicial)||'',''''dd/mm/yyyy'''')        
                and to_date( to_char( nla.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' )  <= to_date(''||quote_literal(stDtFinal)||'',''''dd/mm/yyyy''''))
            )

        ) as lista
    where   lista.exercicio    = contabilidade.consistencia_contab.exercicio
--        and lista.sequencia    = contabilidade.consistencia_contab.sequencia
        and lista.tipo         = contabilidade.consistencia_contab.tipo
        and lista.cod_lote     = contabilidade.consistencia_contab.cod_lote
        and lista.estorno      = contabilidade.consistencia_contab.estorno
        and lista.cod_entidade = contabilidade.consistencia_contab.cod_entidade
'';
execute stSql;

-------------------------------------------------------------------------------------------------------------------
---- contabilidade.consistencia_1
---- INCONSISTÊNCIAS NA ORDEM CRONOLÓGICA DOS EMPENHNOS 
----
-------------------------------------------------------------------------------------------------------------------
/*
-- Deletando a tabela criada anteriormente
select count(*) into inCount from pg_tables where tablename=''contabilidade.consistencia_1_tmp'';
IF inCount>0 THEN
    stSql = ''drop table contabilidade.consistencia_1_tmp'';
    execute stSql;
END IF;


stSql :=''
    create table contabilidade.consistencia_1_tmp as
        select 
            cod_entidade,
            cod_empenho,
            exercicio_empenho,
            to_char(dt_empenho,''''dd/mm/yyyy'''') as dt_empenho,
            sum(vl_emp)     as vl_emp,
            sum(vl_emp_anu) as vl_emp_anu,
            sum(vl_liq)     as vl_liq,
            sum(vl_liq_anu) as vl_liq_anu,
            sum(vl_pag)     as vl_pag,
            sum(vl_pag_anu) as vl_pag_anu
            from (
                select
                    cod_entidade,
                    cod_empenho,
                    exercicio_empenho,
                    dt_empenho,
                    CASE 
                        WHEN(tipo=''''E'''' and estorno=false) THEN vl_lancamento 
                        ELSE 0.00
                    END as vl_emp,
                    CASE 
                        WHEN(tipo=''''E'''' and estorno=true) THEN vl_lancamento 
                        ELSE 0.00
                    END as vl_emp_anu,
                    CASE 
                        WHEN(tipo=''''L'''' and estorno=false) THEN vl_lancamento 
                        ELSE 0.00
                    END as vl_liq,
                    CASE 
                        WHEN(tipo=''''L'''' and estorno=true) THEN vl_lancamento 
                        ELSE 0.00
                    END as vl_liq_anu,
                    CASE 
                        WHEN(tipo=''''P'''' and estorno=false) THEN vl_lancamento 
                        ELSE 0.00
                    END as vl_pag,
                    CASE 
                        WHEN(tipo=''''P''''and estorno=true) THEN vl_lancamento 
                        ELSE 0.00
                    END as vl_pag_anu
                from contabilidade.consistencia_contab
--                where cod_empenho<>''''0''''
                order by exercicio_empenho, cod_entidade, cod_empenho, dt_empenho, tipo, estorno
            ) as tbl
        group by cod_entidade, exercicio_empenho, cod_empenho, dt_empenho    
        order by exercicio_empenho, cod_entidade, cod_empenho, dt_empenho      
'';
execute stSql;

-- Deletando a tabela criada anteriormente
select count(*) into inCount from pg_tables where tablename=''contabilidade.consistencia_1'';
IF inCount>0 THEN
    stSql = ''drop table contabilidade.consistencia_1'';
    execute stSql;
END IF;

stSql :=''

'';
execute stSql;
*/

-------------------------------------------------------------------------------------------------------------------
---- contabilidade.consistencia_2
---- INCONSISTÊNCIAS ENTRE EXERCÍCIO E DATAS DE EMPENHOS OU LIQUIDAÇÕES
----
-------------------------------------------------------------------------------------------------------------------

-- Deletando a tabela criada anteriormente
stSql = ''delete from contabilidade.consistencia_2'';
execute stSql;

stSql :=''
    insert into contabilidade.consistencia_2
    select
        e.cod_entidade,
        e.cod_empenho,
        to_char(e.dt_empenho,''''dd/mm/yyyy'''') as dt_empenho,
        e.exercicio,
        to_char(l.dt_liquidacao,''''dd/mm/yyyy'''') as dt_liquidacao,

        l.exercicio as exercicio_liquidacao
    from
        empenho.empenho         as e
        left join empenho.nota_liquidacao as l on (
            e.cod_empenho = l.cod_empenho
        and e.exercicio   = l.exercicio_empenho
        and e.cod_entidade= l.cod_entidade
        )
    where
        to_char(e.dt_empenho,''''yyyy'''') <> e.exercicio
    or  to_char(l.dt_liquidacao,''''yyyy'''') <> l.exercicio
'';
execute stSql;

-------------------------------------------------------------------------------------------------------------------
---- contabilidade.consistencia_3
---- INCONSISTÊNCIAS ENTRE EXERCÍCIO DO LOTE E DATA DO LOTE
----
-------------------------------------------------------------------------------------------------------------------
-- Deletando a tabela criada anteriormente
stSql = ''delete from contabilidade.consistencia_3'';
execute stSql;

stSql :=''
    insert into contabilidade.consistencia_3
    select
        cod_entidade,
        cod_lote,
        exercicio,
        to_char(dt_lote,''''dd/mm/yyyy'''') as dt_lote,
        tipo
    from contabilidade.consistencia_contab 
    where to_char(dt_lote,''''yyyy'''') <> exercicio;
'';
execute stSql;

-------------------------------------------------------------------------------------------------------------------
---- contabilidade.consistencia_4
---- INCONSISTÊNCIAS NA DATA DO LOTE e DATA DO EMPENHO, LIQUIDACAO ou PAGAMENTO
----
-------------------------------------------------------------------------------------------------------------------
-- Deletando a tabela criada anteriormente
stSql = ''delete from contabilidade.consistencia_4'';
execute stSql;

stSql :=''
    insert into contabilidade.consistencia_4
        SELECT     
            cod_entidade,
            cod_lote,
            to_char(dt_empenho,''''dd/mm/yyyy'''') as dt_empenho,
            to_char(dt_lote,''''dd/mm/yyyy'''') as dt_lote,
            CASE 
                WHEN (tipo=''''E'''' and estorno=false) THEN ''''Emissão de Empenho''''                                             ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''E'''' and estorno=true   and exercicio <> exercicio_empenho) THEN ''''Anulação de Empenho RP ''''    ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''E'''' and estorno=true   and exercicio =  exercicio_empenho) THEN ''''Anulação de Empenho ''''       ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''L'''' and estorno=false  and exercicio =  exercicio_empenho) THEN ''''Liquidação de Empenho ''''     ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''L'''' and estorno=false  and exercicio <> exercicio_empenho) THEN ''''Liquidação de Empenho RP ''''  ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''L'''' and estorno=true   and exercicio =  exercicio_empenho) THEN ''''Anulação de Liquidação ''''    ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''L'''' and estorno=true   and exercicio <> exercicio_empenho) THEN ''''Anulação de Liquidação RP '''' ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''P'''' and estorno=false  and exercicio =  exercicio_empenho) THEN ''''Pagamento de Empenho ''''      ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''P'''' and estorno=false  and exercicio <> exercicio_empenho) THEN ''''Pagamento de Empenho RP ''''   ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''P'''' and estorno=true   and exercicio =  exercicio_empenho) THEN ''''Estorno de Pagamento ''''      ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''P'''' and estorno=true   and exercicio <> exercicio_empenho) THEN ''''Estorno de Pagamento RP ''''   ||cod_empenho||''''/''''||exercicio_empenho 
            END as complemento
    from contabilidade.consistencia_contab 
    where dt_empenho<> dt_lote;
'';
execute stSql;

-------------------------------------------------------------------------------------------------------------------
---- contabilidade.consistencia_5
----
---- INCONSISTÊNCIAS NA REGRA DE VALORES (EMPENHADO >= ANULADO) >= (LIQUIDADO >= ANULADO) >= (PAGO >= ESTORNADO)
----
-------------------------------------------------------------------------------------------------------------------
-- Deletando a tabela criada anteriormente
stSql = ''delete from contabilidade.consistencia_5'';
execute stSql;

stSql :=''
    insert into contabilidade.consistencia_5
    select
        cod_entidade,
        cod_empenho||''''/''''||exercicio as empenho,
        vlremp, 
        vlranu,
        vlrliq,
        vlrliqanu,
        vlrpag,
        vlrpagest
    from contabilidade.consistencia
    where 
        (vlremp < vlranu)                             or -- valor empenhado menor que empenhado anulado   
        ((vlremp - vlranu) < (vlrliq - vlrliqanu))    or -- valor empenhado menos anulado menor que valor liquidado - anulado
        (vlrliq < vlrliqanu)                          or -- valor liquidado menor que liquidado anulado
        ((vlrliq - vlrliqanu) < (vlrpag - vlrpagest)) or -- valor liquidado menos valor anulado menor que valor pago
        (vlrpag < vlrpagest)                             -- valor pago menor que valor pago estornado
'';       
execute stSql;

-------------------------------------------------------------------------------------------------------------------
---- contabilidade.consistencia_6
----
---- INCONSISTÊNCIAS ENTRE VALORES NO EMPENHO E NA CONTABILIDADE
----
-------------------------------------------------------------------------------------------------------------------
-- Deletando a tabela criada anteriormente
stSql = ''delete from contabilidade.consistencia_6'';
execute stSql;

-- Verifica empenhos com valor de emissao diferentes no empenho e contabilidade --
stSql :=''
insert into contabilidade.consistencia_6
    select 
        cod_entidade,
        cod_empenho||''''/''''||exercicio,
        vlremp,
        ctbemp,
        ''''Emissão de Empenho''''
    from
        contabilidade.consistencia
    where
        vlremp <> ctbemp
'';       
execute stSql;

-- Verifica empenhos com valor de anulação de empenho diferentes no empenho e contabilidade --
stSql :=''
insert into contabilidade.consistencia_6
    select 
        cod_entidade,
        cod_empenho||''''/''''||exercicio,
        vlranu,
        ctbanu,   
        ''''Anulação de Empenho''''
    from
        contabilidade.consistencia
    where
        vlranu <> ctbanu
'';       
execute stSql;

-- Verifica empenhos com valor de liquidação de empenho diferentes no empenho e contabilidade --
stSql :=''
insert into contabilidade.consistencia_6
    select 
        cod_entidade,
        cod_empenho||''''/''''||exercicio,
        vlrliq,
        ctbliq,   
        ''''Liquidação de Empenho''''
    from
        contabilidade.consistencia
    where
        vlrliq <> ctbliq
'';       
execute stSql;

-- Verifica empenhos com valor de anulação de liquidação de empenho diferentes no empenho e contabilidade --
stSql :=''
insert into contabilidade.consistencia_6
    select 
        cod_entidade,
        cod_empenho||''''/''''||exercicio,
        vlrliqanu,
        ctbliqanu,   
        ''''Anulação de Liquidação''''
    from
        contabilidade.consistencia
    where
        vlrliqanu <> ctbliqanu
'';       
execute stSql;

-------------------------------------------------------------------------------------------------------------------
---- contabilidade.consistencia_7
----
---- INCONSISTÊNCIAS NO SINAL DO VALOR EM LANÇAMENTOS DE CRÉDITO E DÉBITO
----
-------------------------------------------------------------------------------------------------------------------

-- Deletando a tabela criada anteriormente
stSql = ''delete from contabilidade.consistencia_7'';
execute stSql;

-- Verifica valores ''D'' na contabilidade com valor negativo  ou ''C'' na contabilidade com valor positivo e insere na tabela criada
stSql := ''
insert into contabilidade.consistencia_7
    select debito.cod_lote, debito.exercicio, debito.cod_entidade, debito.sequencia, debito.tipo, debito.vl_lancamento, credito.vl_lancamento
      from contabilidade.valor_lancamento as debito,
           contabilidade.valor_lancamento as credito
      where 
       ((debito.tipo_valor    = ''''D'''' and debito.vl_lancamento < 0 ) or (     
        credito.tipo_valor    = ''''C'''' and credito.vl_lancamento > 0 )) and
        debito.tipo          = credito.tipo          and
        debito.exercicio     = credito.exercicio     and
        debito.tipo_valor    = credito.tipo_valor    and
        debito.cod_entidade  = credito.cod_entidade  and
        debito.sequencia     = credito.sequencia     and
        debito.cod_lote      = credito.cod_lote      and
        
--        debito.exercicio     = ''||quote_literal(stExercicio)||'' and
        debito.cod_entidade  in (''||stCodEntidade||'')       
'';
execute stSql;

-------------------------------------------------------------------------------------------------------------------
---- contabilidade.consistencia_8
---- INCONSISTÊNCIAS NA QUANTIDADE DE LANÇAMENTOS CONTÁBEIS
----
-------------------------------------------------------------------------------------------------------------------
-- Deletando a tabela criada anteriormente
stSql = ''delete from contabilidade.consistencia_8'';
execute stSql;

stSql :=''
insert into contabilidade.consistencia_8 
    SELECT * from (
        SELECT
            cod_entidade,
            cod_lote,
            tipo,
            sequencia as lancamentos,
            CASE 
                WHEN (tipo=''''E'''' and estorno=false) THEN 7                                      -- emissão de empenho
                WHEN (tipo=''''E'''' and estorno=true   and exercicio <> exercicio_empenho) THEN 4  -- anulação de empenho RP
                WHEN (tipo=''''E'''' and estorno=true   and exercicio =  exercicio_empenho) THEN 7  -- anulação de empenho
                WHEN (tipo=''''L'''' and estorno=false  and exercicio =  exercicio_empenho) THEN 5  -- liquidação de empenho
                WHEN (tipo=''''L'''' and estorno=false  and exercicio <> exercicio_empenho) THEN 3  -- liquidação de empenho RP
                WHEN (tipo=''''L'''' and estorno=true   and exercicio =  exercicio_empenho) THEN 5  -- Anulação de liquidação
                WHEN (tipo=''''L'''' and estorno=true   and exercicio <> exercicio_empenho) THEN 3  -- Anulação de liquidação RP 
                WHEN (tipo=''''P'''' and estorno=false  and exercicio =  exercicio_empenho) THEN 2  -- Pagamento de empenho
                WHEN (tipo=''''P'''' and estorno=false  and exercicio <> exercicio_empenho) THEN 3  -- Pagamento de empenho RP
                WHEN (tipo=''''P'''' and estorno=true   and exercicio =  exercicio_empenho) THEN 2  -- Estorno de pagamento
                WHEN (tipo=''''P'''' and estorno=true   and exercicio <> exercicio_empenho) THEN 3  -- Estorno de pagamento RP
            END as lancamentos_corretos,
            CASE 
                WHEN (tipo=''''E'''' and estorno=false) THEN ''''Emissão de Empenho''''                                             ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''E'''' and estorno=true   and exercicio <> exercicio_empenho) THEN ''''Anulação de Empenho RP ''''    ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''E'''' and estorno=true   and exercicio =  exercicio_empenho) THEN ''''Anulação de Empenho ''''       ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''L'''' and estorno=false  and exercicio =  exercicio_empenho) THEN ''''Liquidação de Empenho ''''     ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''L'''' and estorno=false  and exercicio <> exercicio_empenho) THEN ''''Liquidação de Empenho RP ''''  ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''L'''' and estorno=true   and exercicio =  exercicio_empenho) THEN ''''Anulação de Liquidação ''''    ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''L'''' and estorno=true   and exercicio <> exercicio_empenho) THEN ''''Anulação de Liquidação RP '''' ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''P'''' and estorno=false  and exercicio =  exercicio_empenho) THEN ''''Pagamento de Empenho ''''      ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''P'''' and estorno=false  and exercicio <> exercicio_empenho) THEN ''''Pagamento de Empenho RP ''''   ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''P'''' and estorno=true   and exercicio =  exercicio_empenho) THEN ''''Estorno de Pagamento ''''      ||cod_empenho||''''/''''||exercicio_empenho 
                WHEN (tipo=''''P'''' and estorno=true   and exercicio <> exercicio_empenho) THEN ''''Estorno de Pagamento RP ''''   ||cod_empenho||''''/''''||exercicio_empenho 
            END as complemento

        FROM contabilidade.consistencia_contab
        ORDER by exercicio, cod_entidade, cod_empenho, tipo
        ) as tabela
    WHERE lancamentos <> lancamentos_corretos
'';
execute stSql;

-------------------------------------------------------------------------------------------------------------------
---- contabilidade.consistencia_9
---- LANÇAMENTOS EXISTENTES NA CONTABILIDADE SEM CORRESPONDÊNCIA NO EMPENHO
----
-------------------------------------------------------------------------------------------------------------------
-- Deletando a tabela criada anteriormente
stSql = ''delete from contabilidade.consistencia_9'';
execute stSql;

stSql :=''
insert into contabilidade.consistencia_9 
       SELECT
            cod_entidade,
            cod_lote,
            tipo,
            complemento
       FROM contabilidade.consistencia_contab 
       WHERE cod_empenho = 0
'';
execute stSql;



PERFORM 1 from pg_tables where tablename = ''consistencia_10'';
IF FOUND THEN
    drop table contabilidade.consistencia_10;
END IF;



stSql:=''create table contabilidade.consistencia_10 as


     


SELECT


    tabela.cod_conta,   
    tabela.cod_entidade,
    tabela.cod_plano,
    SUBSTR(plano_conta.cod_estrutural,1,1),
    tabela.natureza_saldo,
    plano_conta.exercicio,
    SUBSTR(plano_conta.nom_conta,1,60) as nome,
    plano_conta.cod_estrutural as cod_estrutural,

   CASE
     WHEN(plano_conta.cod_estrutural=1::VARCHAR and tabela.natureza_saldo <> ''''D'''')
            THEN true
     WHEN(plano_conta.cod_estrutural=2::VARCHAR and tabela.natureza_saldo <> ''''C'''')
            THEN true
     WHEN(plano_conta.cod_estrutural=3::VARCHAR and tabela.natureza_saldo <> ''''D'''')
            THEN true
     WHEN(plano_conta.cod_estrutural=4::VARCHAR and tabela.natureza_saldo <> ''''C'''')
            THEN true
     WHEN(plano_conta.cod_estrutural=5::VARCHAR and tabela.natureza_saldo <> ''''D'''')
            THEN true
     WHEN(plano_conta.cod_estrutural=6::VARCHAR and tabela.natureza_saldo <> ''''C'''')
            THEN true
     WHEN(plano_conta.cod_estrutural=9::VARCHAR and tabela.natureza_saldo <> ''''D'''')
      THEN true

   ELSE
      false

   END as tipo_saldo

FROM(
                  SELECT cod_entidade
                        ,plano_analitica.cod_plano
                        ,plano_analitica.cod_conta
                        ,plano_analitica.exercicio
                        ,plano_analitica.natureza_saldo
                   FROM contabilidade.conta_debito
                        INNER JOIN contabilidade.plano_analitica
                     ON conta_debito.cod_plano = plano_analitica.cod_plano
                    AND conta_debito.exercicio = plano_analitica.exercicio
  UNION

                 SELECT cod_entidade
                       ,plano_analitica.cod_plano
                       ,plano_analitica.cod_conta
                       ,plano_analitica.exercicio
                       ,plano_analitica.natureza_saldo
                  FROM contabilidade.conta_credito
                       INNER JOIN contabilidade.plano_analitica
                    ON conta_credito.cod_plano = plano_analitica.cod_plano
                   AND conta_credito.exercicio = plano_analitica.exercicio



) as tabela


   LEFT JOIN contabilidade.plano_conta
                          ON tabela.cod_conta = contabilidade.plano_conta.cod_conta
                       AND tabela.exercicio = contabilidade.plano_conta.exercicio
                       WHERE tabela.exercicio =''''stExercicio''''
                         AND tabela.cod_entidade IN (''|| stCodEntidade||'') 










'';
   
execute stSql;


PERFORM 1 from pg_tables where tablename = ''consistencia_11'';
IF FOUND THEN
    drop table contabilidade.consistencia_11;
END IF;


stSql:='' CREATE TABLE contabilidade.consistencia_11(
           vl_saldo_anterior       VARCHAR(50),
           vl_saldo_debitos        VARCHAR(50),
           vl_saldo_creditos       VARCHAR(50),
           vl_saldo_atual          VARCHAR(50),
           nom_conta               VARCHAR(255),
           cod_plano               INTEGER,
           natureza_saldo          CHAR,
           cod_entidade            INTEGER,      
           natureza_atual          CHAR,
           cod_classificacao         INTEGER
)'';

execute stSql;

stSql := ''        SELECT                              
                         pc.cod_classificacao
                        ,pc.cod_estrutural
                        ,tabela.cod_plano
                        ,tabela.cod_entidade
                        ,tabela.natureza_saldo  as natureza_atual
                        ,publico.fn_nivel(pc.cod_estrutural) as nivel
                        ,CAST(CASE WHEN tabela.cod_plano IS NULL THEN
                            pc.nom_conta
                         ELSE
                            tabela.cod_plano ||'''' - ''''|| pc.nom_conta
                         END AS varchar) AS nom_conta 
                        
                        ,0.00 as vl_saldo_anterior
                        ,0.00 as vl_saldo_debitos
                        ,0.00 as vl_saldo_creditos
                        ,0.00 as vl_saldo_atual

                  FROM  (
                  SELECT cod_entidade
                        ,plano_analitica.cod_plano
                        ,plano_analitica.cod_conta
                        ,plano_analitica.exercicio
                        ,plano_analitica.natureza_saldo
                   FROM contabilidade.conta_debito
                        INNER JOIN contabilidade.plano_analitica
                     ON conta_debito.cod_plano = plano_analitica.cod_plano
                    AND conta_debito.exercicio = plano_analitica.exercicio

                  UNION
    
                 SELECT cod_entidade
                       ,plano_analitica.cod_plano
                       ,plano_analitica.cod_conta
                       ,plano_analitica.exercicio
                       ,plano_analitica.natureza_saldo
                  FROM contabilidade.conta_credito
                       INNER JOIN contabilidade.plano_analitica
                    ON conta_credito.cod_plano = plano_analitica.cod_plano
                   AND conta_credito.exercicio = plano_analitica.exercicio
                           ) AS tabela

            


                   LEFT JOIN contabilidade.plano_conta  
                          AS pc  
                          ON tabela.cod_conta = pc.cod_conta
                         AND tabela.exercicio = pc.exercicio
                       WHERE tabela.exercicio =''''|| stExercicio ||''''
                         AND tabela.cod_entidade                        
                          IN (''|| stCodEntidade ||'')
'';



   FOR reRegistro IN EXECUTE stSql
    LOOP
        errado:=false;
        arRetorno := contabilidade.fn_totaliza_balancete_verificacao(
publico.fn_mascarareduzida(reRegistro.cod_estrutural) , stDtInicial, stDtFinal);
        reRegistro.vl_saldo_anterior := arRetorno[1];
        reRegistro.vl_saldo_debitos  := arRetorno[2];
        reRegistro.vl_saldo_creditos := arRetorno[3];
        reRegistro.vl_saldo_atual    := arRetorno[4];
    

       
      IF (reRegistro.natureza_saldo = ''C'')  AND (reRegistro.vl_saldo_atual > 0) 
      THEN         
           reRegistro.natureza_atual := ''D'';
           errado:=true;
       END IF;

       IF (reRegistro.natureza_saldo = ''D'' )AND (reRegistro.vl_saldo_atual < 0) 
       THEN
           reRegistro.natureza_atual  := ''C'';
           errado:=true;
       END IF;

     IF (errado) THEN
      INSERT INTO contabilidade.consistencia_11 
      VALUES(reRegistro.vl_saldo_anterior,reRegistro.vl_saldo_debitos,reRegistro.vl_saldo_creditos,reRegistro.vl_saldo_atual,SUBSTR(reRegistro.nom_conta,1,40),reRegistro.cod_plano,reRegistro.natureza_saldo,reRegistro.cod_entidade,reRegistro.natureza_atual,reRegistro.cod_classificacao);

   END IF;
     END LOOP;







return true;
END;
' LANGUAGE 'plpgsql';
