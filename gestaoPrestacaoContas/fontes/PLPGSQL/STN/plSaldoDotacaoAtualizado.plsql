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

-- Apenas o Saldo Inicial + Suplementações - Reduções
create or replace function stn.fn_saldo_dotacao_atualizado ( varchar, varchar, varchar, varchar, varchar ) RETURNS numeric AS $$
--                                                exercicio, cod_estrutural, cod_entidade, data_ini, data_fim
select sum ( despesa.vl_original
     + coalesce ( (select sum (suplementacao_suplementada.valor )
                     from orcamento.suplementacao_suplementada
                     join orcamento.suplementacao
                       on ( suplementacao_suplementada.exercicio         = suplementacao.exercicio
                      and   suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao )
                    where suplementacao_suplementada.exercicio   = despesa.exercicio
                      and suplementacao_suplementada.cod_despesa = despesa.cod_despesa
                      and suplementacao.dt_suplementacao BETWEEN to_date( $4,'dd/mm/yyyy') AND to_date($5,'dd/mm/yyyy')
                  ), 0 )

     -  coalesce ( ( select sum (suplementacao_reducao.valor)
                      from orcamento.suplementacao_reducao
                      join orcamento.suplementacao
                        on ( suplementacao_reducao.exercicio         = suplementacao.exercicio
                       and   suplementacao_reducao.cod_suplementacao = suplementacao.cod_suplementacao )
                     where suplementacao_reducao.exercicio   = despesa.exercicio
                       and suplementacao_reducao.cod_despesa = despesa.cod_despesa
                       and suplementacao.dt_suplementacao BETWEEN to_date( $4,'dd/mm/yyyy') AND to_date($5,'dd/mm/yyyy')
                    ) , 0 )     

    )  as saldo
  from orcamento.despesa
  join orcamento.conta_despesa
    on ( despesa.cod_conta = conta_despesa.cod_conta
   and   despesa.exercicio = conta_despesa.exercicio  )
 where despesa.exercicio = $1  
   and conta_despesa.cod_estrutural like ( publico.fn_mascarareduzida ($2) || '%' )
   and despesa.cod_entidade::varchar in ( $3 )
$$ LANGUAGE 'sql';
