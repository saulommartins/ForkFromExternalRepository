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
/* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
*
* Casos de uso: uc-06.01.24
*/



CREATE OR REPLACE FUNCTION stn.pl_saldo_contas (  varchar, varchar , varchar , varchar, varchar, varchar) RETURNS numeric AS $$
DECLARE
    exercicio alias for $1;
    data_ini  alias for $2;
    data_fim  alias for $3;
    condicao  alias for $4;
    stEntidades alias for $5;
    stRPPS alias for $6;
    reRegistro RECORD;
    nuSaldo Numeric;
    stSql   varchar := '';
    stSqlRPPS   varchar := '';
    stCondEntidades varchar;
    inCodEntidadeRPPS integer;
    crCursor    REFCURSOR;    
BEGIN

stCondEntidades := ' ' ;

stSqlRPPS = ' SELECT valor FROM administracao.configuracao where parametro = ''cod_entidade_rpps'' AND cod_modulo = 8 AND exercicio = ''' || exercicio || ''' ';

OPEN crCursor FOR EXECUTE stSqlRPPS;
    FETCH crCursor INTO inCodEntidadeRPPS;
CLOSE crCursor;

if ( stRPPS = 'false' ) then

    stCondEntidades := ' and valor_lancamento.cod_entidade in ( ' || stEntidades || ' )  and valor_lancamento.cod_entidade not in ( ' || inCodEntidadeRPPS || ' ) ';

else

    stCondEntidades := ' and valor_lancamento.cod_entidade in ( ' || stEntidades || ' )  and valor_lancamento.cod_entidade in ( ' || inCodEntidadeRPPS || ' ) ';

end if; 

stSql = 'select 
              coalesce ( 
              ( select sum ( valor_lancamento.vl_lancamento )
                   from contabilidade.plano_conta
                   join contabilidade.plano_analitica
                     on ( plano_conta.exercicio = plano_analitica.exercicio 
                    and   plano_conta.cod_conta = plano_analitica.cod_conta ) 
                   left join contabilidade.plano_recurso
                     on ( plano_recurso.exercicio = plano_analitica.exercicio
                    and   plano_recurso.cod_plano = plano_analitica.cod_plano )             
                   left join orcamento.recurso_direto
                     on ( recurso_direto.exercicio = plano_recurso.exercicio
                    and   recurso_direto.cod_recurso = plano_recurso.cod_recurso )
                   join contabilidade.conta_credito
                     on ( plano_analitica.exercicio = conta_credito.exercicio
                    and   plano_analitica.cod_plano = conta_credito.cod_plano )
                   join contabilidade.valor_lancamento 
                     on ( conta_credito.exercicio    = valor_lancamento.exercicio 
                    and   conta_credito.cod_entidade = valor_lancamento.cod_entidade 
                    and   conta_credito.tipo         = valor_lancamento.tipo         
                    and   conta_credito.cod_lote     = valor_lancamento.cod_lote     
                    and   conta_credito.sequencia    = valor_lancamento.sequencia    
                    and   conta_credito.tipo_valor   = valor_lancamento.tipo_valor )
                   join contabilidade.lote 
                     on ( valor_lancamento.exercicio    = lote.exercicio     
                    and   valor_lancamento.cod_entidade = lote.cod_entidade  
                    and   valor_lancamento.tipo         = lote.tipo          
                    and   valor_lancamento.cod_lote     = lote.cod_lote )
                   join contabilidade.lancamento
                     on ( lancamento.cod_lote       = valor_lancamento.cod_lote
                    and   lancamento.tipo           = valor_lancamento.tipo
                    and   lancamento.exercicio      = valor_lancamento.exercicio
                    and   lancamento.sequencia      = valor_lancamento.sequencia
                    and   lancamento.cod_entidade   = valor_lancamento.cod_entidade 
                    and   lancamento.cod_historico  not between 800 and 899 )
                  where  plano_conta.exercicio =  ''' ||   exercicio || ''' and  ' ||  condicao || '
                    and lote.dt_lote between to_date( '''|| data_ini ||''' , ''dd/mm/yyyy'' ) 
                                       and   to_date( '''|| data_fim ||''' , ''dd/mm/yyyy'' )  
                    ' || stCondEntidades || '  ) , 0 )
              + 
              coalesce (
               ( select sum ( valor_lancamento.vl_lancamento )
                    from contabilidade.plano_conta plano_conta
                    join contabilidade.plano_analitica
                    left join contabilidade.plano_recurso
                      on ( plano_recurso.exercicio = plano_analitica.exercicio
                     and   plano_recurso.cod_plano = plano_analitica.cod_plano )             
                    left join orcamento.recurso_direto
                      on ( recurso_direto.exercicio = plano_recurso.exercicio
                     and   recurso_direto.cod_recurso = plano_recurso.cod_recurso )
                      on ( plano_conta.exercicio = plano_analitica.exercicio 
                     and   plano_conta.cod_conta = plano_analitica.cod_conta ) 
                    join contabilidade.conta_debito
                      on ( plano_analitica.exercicio = conta_debito.exercicio
                     and   plano_analitica.cod_plano = conta_debito.cod_plano )
                    join contabilidade.valor_lancamento 
                      on ( conta_debito.exercicio    = valor_lancamento.exercicio 
                     and   conta_debito.cod_entidade = valor_lancamento.cod_entidade 
                     and   conta_debito.tipo         = valor_lancamento.tipo         
                     and   conta_debito.cod_lote     = valor_lancamento.cod_lote     
                     and   conta_debito.sequencia    = valor_lancamento.sequencia    
                     and   conta_debito.tipo_valor   = valor_lancamento.tipo_valor )
                    join contabilidade.lote 
                      on ( valor_lancamento.exercicio    = lote.exercicio     
                     and   valor_lancamento.cod_entidade = lote.cod_entidade  
                     and   valor_lancamento.tipo         = lote.tipo          
                     and   valor_lancamento.cod_lote     = lote.cod_lote )
                   join contabilidade.lancamento
                     on ( lancamento.cod_lote       = valor_lancamento.cod_lote
                    and   lancamento.tipo           = valor_lancamento.tipo
                    and   lancamento.sequencia      = valor_lancamento.sequencia
                    and   lancamento.exercicio      = valor_lancamento.exercicio
                    and   lancamento.cod_entidade   = valor_lancamento.cod_entidade 
                    and   lancamento.cod_historico  not between 800 and 899 )
                   where plano_conta.exercicio =  ''' ||   exercicio || ''' and  ' ||  condicao || '
                     and lote.dt_lote between to_date( '''|| data_ini ||''' , ''dd/mm/yyyy'' ) 
                                        and   to_date( '''|| data_fim ||''' , ''dd/mm/yyyy'' ) 
                     ' || stCondEntidades || ' ) , 0 )
                   as saldo '; 

FOR reRegistro IN EXECUTE stSql
LOOP
    nuSaldo := reRegistro.saldo;
END LOOP;


return nuSaldo;
end;
$$ LANGUAGE 'plpgsql';

