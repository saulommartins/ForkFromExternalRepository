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
* Casos de uso: uc-02.00.00
*/

/*
$Log$
Revision 1.5  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_exclui_lancamentos_empenho(varchar, varchar,varchar, varchar,varchar, varchar) RETURNS BOOLEAN AS $$
DECLARE
    stExercicio                     ALIAS FOR $1;
    stCodEntidades                  ALIAS FOR $2;
    inCodEmpenhoInicial             ALIAS FOR $3;
    inCodEmpenhoFinal               ALIAS FOR $4;
    stDataInicial                   ALIAS FOR $5;
    stDataFinal                     ALIAS FOR $6;
    stTipoLancamento    VARCHAR   := 'E';

    stSql               VARCHAR   := '';
    reRegistro          RECORD;

BEGIN

    stSql := '
        DELETE From contabilidade.empenhamento
         where contabilidade.lote.exercicio                   = '|| quote_literal(stExercicio)      ||'
           And contabilidade.lote.tipo                        = '|| quote_literal(stTipoLancamento) ||'
           And contabilidade.lote.exercicio                   = contabilidade.lancamento.exercicio
           And contabilidade.lote.cod_lote                    = contabilidade.lancamento.cod_lote
           And contabilidade.lote.tipo                        = contabilidade.lancamento.tipo
           And contabilidade.lote.cod_entidade                = contabilidade.lancamento.cod_entidade

           And contabilidade.lancamento.exercicio             = contabilidade.lancamento_empenho.exercicio
           And contabilidade.lancamento.cod_lote              = contabilidade.lancamento_empenho.cod_lote
           And contabilidade.lancamento.tipo                  = contabilidade.lancamento_empenho.tipo
           And contabilidade.lancamento.cod_entidade          = contabilidade.lancamento_empenho.cod_entidade
           And contabilidade.lancamento.sequencia             = contabilidade.lancamento_empenho.sequencia

           And contabilidade.lancamento_empenho.exercicio     = contabilidade.empenhamento.exercicio
           And contabilidade.lancamento_empenho.cod_lote      = contabilidade.empenhamento.cod_lote
           And contabilidade.lancamento_empenho.tipo          = contabilidade.empenhamento.tipo
           And contabilidade.lancamento_empenho.cod_entidade  = contabilidade.empenhamento.cod_entidade
           And contabilidade.lancamento_empenho.sequencia     = contabilidade.empenhamento.sequencia

           And contabilidade.empenhamento.exercicio           = empenho.empenho.exercicio
           And contabilidade.empenhamento.cod_empenho         = empenho.empenho.cod_empenho
           And contabilidade.empenhamento.cod_entidade        = empenho.empenho.cod_entidade
           And contabilidade.empenhamento.cod_entidade        IN  ('|| stCodEntidades ||')

   ';

    if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial<>'') then
        stSql := stSql || ' And empenho.empenho.cod_empenho >= '|| inCodEmpenhoInicial ||' ';
    end if;

    if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal<>'') then
        stSql := stSql || ' And empenho.empenho.cod_empenho <= '|| inCodEmpenhoFinal ||' ';
    end if;

    if (stDataInicial is not null and stDataInicial<>'') then
        stSql := stSql || ' And empenho.empenho.dt_empenho >= to_date('|| quote_literal(stDataInicial) ||',''dd/mm/yyyy'') ';
    end if;

    if (stDataFinal is not null and stDataFinal<>'') then
        stSql := stSql || ' And empenho.empenho.dt_empenho <= to_date('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'') ';
    end if;

/*    EXECUTE stSql;


    stSql := '
      DELETE From contabilidade.lancamento_empenho
         where contabilidade.lote.exercicio                   = '|| quote_literal(stExercicio)      ||'
           And contabilidade.lote.tipo                        = '|| quote_literal(stTipoLancamento) ||'
           And contabilidade.lote.exercicio            = contabilidade.lancamento.exercicio
           And contabilidade.lote.cod_lote             = contabilidade.lancamento.cod_lote
           And contabilidade.lote.tipo                 = contabilidade.lancamento.tipo
           And contabilidade.lote.cod_entidade         = contabilidade.lancamento.cod_entidade

           And contabilidade.lancamento.exercicio      = contabilidade.lancamento_empenho.exercicio
           And contabilidade.lancamento.cod_lote       = contabilidade.lancamento_empenho.cod_lote
           And contabilidade.lancamento.tipo           = contabilidade.lancamento_empenho.tipo
           And contabilidade.lancamento.cod_entidade   = contabilidade.lancamento_empenho.cod_entidade
           And contabilidade.lancamento.sequencia      = contabilidade.lancamento_empenho.sequencia

           And contabilidade.lancamento_empenho.exercicio     = contabilidade.empenhamento.exercicio
           And contabilidade.lancamento_empenho.cod_lote      = contabilidade.empenhamento.cod_lote
           And contabilidade.lancamento_empenho.tipo          = contabilidade.empenhamento.tipo
           And contabilidade.lancamento_empenho.cod_entidade  = contabilidade.empenhamento.cod_entidade
           And contabilidade.lancamento_empenho.sequencia     = contabilidade.empenhamento.sequencia

           And contabilidade.empenhamento.exercicio           = empenho.empenho.exercicio
           And contabilidade.empenhamento.cod_empenho         = empenho.empenho.cod_empenho
           And contabilidade.empenhamento.cod_entidade        = empenho.empenho.cod_entidade
           And contabilidade.empenhamento.cod_entidade        IN  (' || stCodEntidades || ')

   ';

    if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial<>'') then
        stSql := stSql || ' And empenho.empenho.cod_empenho >= ' || inCodEmpenhoInicial || ' ';
    end if;

    if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal<>'') then
        stSql := stSql || ' And empenho.empenho.cod_empenho <= ' || inCodEmpenhoFinal || ' ';
    end if;

    if (stDataInicial is not null and stDataInicial<>'') then
        stSql := stSql || ' And empenho.empenho.dt_empenho >= to_date('|| quote_literal(stDataInicial) ||',''dd/mm/yyyy'') ';
    end if;

    if (stDataFinal is not null and stDataFinal<>'') then
        stSql := stSql || ' And empenho.empenho.dt_empenho <= to_date('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'') ';
    end if;

    EXECUTE stSql;


    stSql := '
      DELETE From contabilidade.conta_credito
         where contabilidade.lote.exercicio                   = '|| quote_literal(stExercicio)      ||'
           And contabilidade.lote.tipo                        = '|| quote_literal(stTipoLancamento) ||'
            And contabilidade.lote.exercicio    = contabilidade.lancamento.exercicio
            And contabilidade.lote.cod_lote     = contabilidade.lancamento.cod_lote
            And contabilidade.lote.tipo         = contabilidade.lancamento.tipo
            And contabilidade.lote.cod_entidade = contabilidade.lancamento.cod_entidade

           And contabilidade.lancamento.exercicio      = contabilidade.lancamento_empenho.exercicio
           And contabilidade.lancamento.cod_lote       = contabilidade.lancamento_empenho.cod_lote
           And contabilidade.lancamento.tipo           = contabilidade.lancamento_empenho.tipo
           And contabilidade.lancamento.cod_entidade   = contabilidade.lancamento_empenho.cod_entidade
           And contabilidade.lancamento.sequencia      = contabilidade.lancamento_empenho.sequencia

           And contabilidade.lancamento_empenho.exercicio     = contabilidade.empenhamento.exercicio
           And contabilidade.lancamento_empenho.cod_lote      = contabilidade.empenhamento.cod_lote
           And contabilidade.lancamento_empenho.tipo          = contabilidade.empenhamento.tipo
           And contabilidade.lancamento_empenho.cod_entidade  = contabilidade.empenhamento.cod_entidade
           And contabilidade.lancamento_empenho.sequencia     = contabilidade.empenhamento.sequencia

           And contabilidade.empenhamento.exercicio           = empenho.empenho.exercicio
           And contabilidade.empenhamento.cod_empenho         = empenho.empenho.cod_empenho
           And contabilidade.empenhamento.cod_entidade        = empenho.empenho.cod_entidade
           And contabilidade.empenhamento.cod_entidade        IN  ('|| stCodEntidades ||')

            And contabilidade.lancamento.exercicio            = contabilidade.valor_lancamento.exercicio
            And contabilidade.lancamento.cod_lote             = contabilidade.valor_lancamento.cod_lote
            And contabilidade.lancamento.tipo                 = contabilidade.valor_lancamento.tipo
            And contabilidade.lancamento.cod_entidade         = contabilidade.valor_lancamento.cod_entidade
            And contabilidade.lancamento.sequencia            = contabilidade.valor_lancamento.sequencia

            And contabilidade.valor_lancamento.exercicio      = contabilidade.conta_credito.exercicio
            And contabilidade.valor_lancamento.cod_lote       = contabilidade.conta_credito.cod_lote
            And contabilidade.valor_lancamento.tipo           = contabilidade.conta_credito.tipo
            And contabilidade.valor_lancamento.cod_entidade   = contabilidade.conta_credito.cod_entidade
            And contabilidade.valor_lancamento.sequencia      = contabilidade.conta_credito.sequencia
            And contabilidade.valor_lancamento.tipo_valor     = contabilidade.conta_credito.tipo_valor
   ';

    if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial<>'') then
        stSql := stSql || ' And empenho.empenho.cod_empenho >= ' || inCodEmpenhoInicial || ' ';
    end if;

    if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal<>'') then
        stSql := stSql || ' And empenho.empenho.cod_empenho <= ' || inCodEmpenhoFinal || ' ';
    end if;

    if (stDataInicial is not null and stDataInicial<>'') then
        stSql := stSql || ' And empenho.empenho.dt_empenho >= to_date('|| quote_literal(stDataInicial) ||',''dd/mm/yyyy'') ';
    end if;

    if (stDataFinal is not null and stDataFinal<>'') then
        stSql := stSql || ' And empenho.empenho.dt_empenho <= to_date('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'') ';
    end if;

    EXECUTE stSql;


    stSql := '
      DELETE From contabilidade.conta_debito
         where contabilidade.lote.exercicio                   = '|| quote_literal(stExercicio)      ||'
           And contabilidade.lote.tipo                        = '|| quote_literal(stTipoLancamento) ||'
            And contabilidade.lote.exercicio    = contabilidade.lancamento.exercicio
            And contabilidade.lote.cod_lote     = contabilidade.lancamento.cod_lote
            And contabilidade.lote.tipo         = contabilidade.lancamento.tipo
            And contabilidade.lote.cod_entidade = contabilidade.lancamento.cod_entidade

           And contabilidade.lancamento.exercicio      = contabilidade.lancamento_empenho.exercicio
           And contabilidade.lancamento.cod_lote       = contabilidade.lancamento_empenho.cod_lote
           And contabilidade.lancamento.tipo           = contabilidade.lancamento_empenho.tipo
           And contabilidade.lancamento.cod_entidade   = contabilidade.lancamento_empenho.cod_entidade
           And contabilidade.lancamento.sequencia      = contabilidade.lancamento_empenho.sequencia

           And contabilidade.lancamento_empenho.exercicio     = contabilidade.empenhamento.exercicio
           And contabilidade.lancamento_empenho.cod_lote      = contabilidade.empenhamento.cod_lote
           And contabilidade.lancamento_empenho.tipo          = contabilidade.empenhamento.tipo
           And contabilidade.lancamento_empenho.cod_entidade  = contabilidade.empenhamento.cod_entidade
           And contabilidade.lancamento_empenho.sequencia     = contabilidade.empenhamento.sequencia

           And contabilidade.empenhamento.exercicio           = empenho.empenho.exercicio
           And contabilidade.empenhamento.cod_empenho         = empenho.empenho.cod_empenho
           And contabilidade.empenhamento.cod_entidade        = empenho.empenho.cod_entidade
           And contabilidade.empenhamento.cod_entidade        IN  ('|| stCodEntidades ||')

            And contabilidade.lancamento.exercicio            = contabilidade.valor_lancamento.exercicio
            And contabilidade.lancamento.cod_lote             = contabilidade.valor_lancamento.cod_lote
            And contabilidade.lancamento.tipo                 = contabilidade.valor_lancamento.tipo
            And contabilidade.lancamento.cod_entidade         = contabilidade.valor_lancamento.cod_entidade
            And contabilidade.lancamento.sequencia            = contabilidade.valor_lancamento.sequencia

            And contabilidade.valor_lancamento.exercicio      = contabilidade.conta_debito.exercicio
            And contabilidade.valor_lancamento.cod_lote       = contabilidade.conta_debito.cod_lote
            And contabilidade.valor_lancamento.tipo           = contabilidade.conta_debito.tipo
            And contabilidade.valor_lancamento.cod_entidade   = contabilidade.conta_debito.cod_entidade
            And contabilidade.valor_lancamento.sequencia      = contabilidade.conta_debito.sequencia
            And contabilidade.valor_lancamento.tipo_valor     = contabilidade.conta_debito.tipo_valor
   ';

    if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial<>'') then
        stSql := stSql || ' And empenho.empenho.cod_empenho >= '|| inCodEmpenhoInicial ||' ';
    end if;

    if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal<>'') then
        stSql := stSql || ' And empenho.empenho.cod_empenho <= '|| inCodEmpenhoFinal ||' ';
    end if;

    if (stDataInicial is not null and stDataInicial<>'') then
        stSql := stSql || ' And empenho.empenho.dt_empenho >= to_date('|| quote_literal(stDataInicial) ||',''dd/mm/yyyy'') ';
    end if;

    if (stDataFinal is not null and stDataFinal<>'') then
        stSql := stSql || ' And empenho.empenho.dt_empenho <= to_date('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'') ';
    end if;

    EXECUTE stSql;


    stSql := '
      DELETE From contabilidade.valor_lancamento
         where contabilidade.lote.exercicio                   = '|| quote_literal(stExercicio)      ||'
           And contabilidade.lote.tipo                        = '|| quote_literal(stTipoLancamento) ||'
            And contabilidade.lote.exercicio    = contabilidade.lancamento.exercicio
            And contabilidade.lote.cod_lote     = contabilidade.lancamento.cod_lote
            And contabilidade.lote.tipo         = contabilidade.lancamento.tipo
            And contabilidade.lote.cod_entidade = contabilidade.lancamento.cod_entidade

           And contabilidade.lancamento.exercicio      = contabilidade.lancamento_empenho.exercicio
           And contabilidade.lancamento.cod_lote       = contabilidade.lancamento_empenho.cod_lote
           And contabilidade.lancamento.tipo           = contabilidade.lancamento_empenho.tipo
           And contabilidade.lancamento.cod_entidade   = contabilidade.lancamento_empenho.cod_entidade
           And contabilidade.lancamento.sequencia      = contabilidade.lancamento_empenho.sequencia

           And contabilidade.lancamento_empenho.exercicio     = contabilidade.empenhamento.exercicio
           And contabilidade.lancamento_empenho.cod_lote      = contabilidade.empenhamento.cod_lote
           And contabilidade.lancamento_empenho.tipo          = contabilidade.empenhamento.tipo
           And contabilidade.lancamento_empenho.cod_entidade  = contabilidade.empenhamento.cod_entidade
           And contabilidade.lancamento_empenho.sequencia     = contabilidade.empenhamento.sequencia

           And contabilidade.empenhamento.exercicio           = empenho.empenho.exercicio
           And contabilidade.empenhamento.cod_empenho         = empenho.empenho.cod_empenho
           And contabilidade.empenhamento.cod_entidade        = empenho.empenho.cod_entidade
           And contabilidade.empenhamento.cod_entidade        IN  ('|| stCodEntidades ||')

            And contabilidade.lancamento.exercicio            = contabilidade.valor_lancamento.exercicio
            And contabilidade.lancamento.cod_lote             = contabilidade.valor_lancamento.cod_lote
            And contabilidade.lancamento.tipo                 = contabilidade.valor_lancamento.tipo
            And contabilidade.lancamento.cod_entidade         = contabilidade.valor_lancamento.cod_entidade
            And contabilidade.lancamento.sequencia            = contabilidade.valor_lancamento.sequencia

   ';

    if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial<>'') then
        stSql := stSql || ' And empenho.empenho.cod_empenho >= '|| inCodEmpenhoInicial ||' ';
    end if;

    if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal<>'') then
        stSql := stSql || ' And empenho.empenho.cod_empenho <= '|| inCodEmpenhoFinal ||' ';
    end if;

    if (stDataInicial is not null and stDataInicial<>'') then
        stSql := stSql || ' And empenho.empenho.dt_empenho >= to_date('|| quote_literal(stDataInicial) ||',''dd/mm/yyyy'') ';
    end if;

    if (stDataFinal is not null and stDataFinal<>'') then
        stSql := stSql || ' And empenho.empenho.dt_empenho <= to_date('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'') ';
    end if;

    EXECUTE stSql;


    stSql := '
      DELETE From contabilidade.lancamento
         where contabilidade.lote.exercicio                   = '|| quote_literal(stExercicio)      ||'
           And contabilidade.lote.tipo                        = '|| quote_literal(stTipoLancamento) ||'
            And contabilidade.lote.exercicio    = contabilidade.lancamento.exercicio
            And contabilidade.lote.cod_lote     = contabilidade.lancamento.cod_lote
            And contabilidade.lote.tipo         = contabilidade.lancamento.tipo
            And contabilidade.lote.cod_entidade = contabilidade.lancamento.cod_entidade

           And contabilidade.lancamento.exercicio      = contabilidade.lancamento_empenho.exercicio
           And contabilidade.lancamento.cod_lote       = contabilidade.lancamento_empenho.cod_lote
           And contabilidade.lancamento.tipo           = contabilidade.lancamento_empenho.tipo
           And contabilidade.lancamento.cod_entidade   = contabilidade.lancamento_empenho.cod_entidade
           And contabilidade.lancamento.sequencia      = contabilidade.lancamento_empenho.sequencia

           And contabilidade.lancamento_empenho.exercicio     = contabilidade.empenhamento.exercicio
           And contabilidade.lancamento_empenho.cod_lote      = contabilidade.empenhamento.cod_lote
           And contabilidade.lancamento_empenho.tipo          = contabilidade.empenhamento.tipo
           And contabilidade.lancamento_empenho.cod_entidade  = contabilidade.empenhamento.cod_entidade
           And contabilidade.lancamento_empenho.sequencia     = contabilidade.empenhamento.sequencia

           And contabilidade.empenhamento.exercicio           = empenho.empenho.exercicio
           And contabilidade.empenhamento.cod_empenho         = empenho.empenho.cod_empenho
           And contabilidade.empenhamento.cod_entidade        = empenho.empenho.cod_entidade
           And contabilidade.empenhamento.cod_entidade        IN  ('|| stCodEntidades ||')

   ';

    if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial<>'') then
        stSql := stSql || ' And empenho.empenho.cod_empenho >= '|| inCodEmpenhoInicial ||' ';
    end if;

    if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal<>'') then
        stSql := stSql || ' And empenho.empenho.cod_empenho <= '|| inCodEmpenhoFinal ||' ';
    end if;

    if (stDataInicial is not null and stDataInicial<>'') then
        stSql := stSql || ' And empenho.empenho.dt_empenho >= to_date('|| quote_literal(stDataInicial) ||',''dd/mm/yyyy'') ';
    end if;

    if (stDataFinal is not null and stDataFinal<>'') then
        stSql := stSql || ' And empenho.empenho.dt_empenho <= to_date('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'') ';
    end if;

    EXECUTE stSql;


    stSql := '
      DELETE From contabilidade.lote
         where contabilidade.lote.exercicio                   = '|| quote_literal(stExercicio)      ||'
           And contabilidade.lote.tipo                        = '|| quote_literal(stTipoLancamento) ||'
            And contabilidade.lote.exercicio    = contabilidade.lancamento.exercicio
            And contabilidade.lote.cod_lote     = contabilidade.lancamento.cod_lote
            And contabilidade.lote.tipo         = contabilidade.lancamento.tipo
            And contabilidade.lote.cod_entidade = contabilidade.lancamento.cod_entidade

           And contabilidade.lancamento.exercicio      = contabilidade.lancamento_empenho.exercicio
           And contabilidade.lancamento.cod_lote       = contabilidade.lancamento_empenho.cod_lote
           And contabilidade.lancamento.tipo           = contabilidade.lancamento_empenho.tipo
           And contabilidade.lancamento.cod_entidade   = contabilidade.lancamento_empenho.cod_entidade
           And contabilidade.lancamento.sequencia      = contabilidade.lancamento_empenho.sequencia

           And contabilidade.lancamento_empenho.exercicio     = contabilidade.empenhamento.exercicio
           And contabilidade.lancamento_empenho.cod_lote      = contabilidade.empenhamento.cod_lote
           And contabilidade.lancamento_empenho.tipo          = contabilidade.empenhamento.tipo
           And contabilidade.lancamento_empenho.cod_entidade  = contabilidade.empenhamento.cod_entidade
           And contabilidade.lancamento_empenho.sequencia     = contabilidade.empenhamento.sequencia

           And contabilidade.empenhamento.exercicio           = empenho.empenho.exercicio
           And contabilidade.empenhamento.cod_empenho         = empenho.empenho.cod_empenho
           And contabilidade.empenhamento.cod_entidade        = empenho.empenho.cod_entidade
           And contabilidade.empenhamento.cod_entidade        IN  ('|| stCodEntidades ||')

   ';

    if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial<>'') then
        stSql := stSql || ' And empenho.empenho.cod_empenho >= '|| inCodEmpenhoInicial ||' ';
    end if;

    if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal<>'') then
        stSql := stSql || ' And empenho.empenho.cod_empenho <= '|| inCodEmpenhoFinal ||' ';
    end if;

    if (stDataInicial is not null and stDataInicial<>'') then
        stSql := stSql || ' And empenho.empenho.dt_empenho >= to_date('|| quote_literal(stDataInicial) ||',''dd/mm/yyyy'') ';
    end if;

    if (stDataFinal is not null and stDataFinal<>'') then
        stSql := stSql || ' And empenho.empenho.dt_empenho <= to_date('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'') ';
    end if;

    EXECUTE stSql;


    stSql := '';
    FOR reRegistro IN EXECUTE stSql
    LOOP

    END LOOP;
*/

    RETURN true;
END;
$$ language 'plpgsql';
