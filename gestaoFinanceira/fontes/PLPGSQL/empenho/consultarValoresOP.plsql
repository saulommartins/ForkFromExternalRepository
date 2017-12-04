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
* $Revision: 16074 $
* $Name$
* $Author: eduardo $
* $Date: 2006-09-28 06:56:56 -0300 (Qui, 28 Set 2006) $
*
* Casos de uso: uc-02.03.05
*/

/*
$Log$
Revision 1.1  2006/09/28 09:56:56  eduardo
Bug #7060#


*/

CREATE OR REPLACE FUNCTION empenho.fn_consultar_valores_op(VARCHAR,INTEGER,INTEGER) RETURNS SETOF RECORD AS $$ 

DECLARE
    stExercicio                ALIAS FOR $1;
    inCodEntidade              ALIAS FOR $2;
    inCodOrdem                 ALIAS FOR $3;
    stSql                      VARCHAR := '';
    reRegistro                 RECORD;
BEGIN
    stSql := '
    select  pl.cod_nota
           ,cast(pl.exercicio_liquidacao as varchar )
           ,coalesce(pl.vl_pagamento     ,0.00) as vl_pagamento
           ,coalesce(opla.vl_anulado     ,0.00) as vl_pagamento_anulado
           ,coalesce(pagamento.vl_pago   ,0.00) as vl_pago
           ,coalesce(pagamento.vl_anulado,0.00) as vl_pago_anulado
           ,(coalesce(pl.vl_pagamento,0.00)-coalesce(opla.vl_anulado,0.00)) - 
            (coalesce(pagamento.vl_pago,0.00)-coalesce(pagamento.vl_anulado,0.00)) as vl_a_anular
    from empenho.pagamento_liquidacao as pl
         left join (
                select  exercicio
                       ,cod_entidade
                       ,cod_ordem
                       ,exercicio_liquidacao
                       ,cod_nota
                       ,coalesce(sum(vl_anulado), 0) as vl_anulado
                from empenho.ordem_pagamento_liquidacao_anulada as opla
                group by exercicio
                        ,cod_entidade
                        ,cod_ordem
                        ,exercicio_liquidacao
                        ,cod_nota
              ) as opla
              on (     
                       pl.exercicio            = opla.exercicio
                   and pl.cod_entidade         = opla.cod_entidade
                   and pl.cod_ordem            = opla.cod_ordem
                   and pl.exercicio_liquidacao = opla.exercicio_liquidacao
                   and pl.cod_nota             = opla.cod_nota
                 )
          left join empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp
               on (
                        plnlp.exercicio    = pl.exercicio
                    and plnlp.cod_entidade = pl.cod_entidade
                    and plnlp.cod_ordem    = pl.cod_ordem
                  )
          left join (
                     select  nlp.exercicio
                            ,nlp.cod_entidade
                            ,nlp.cod_nota
                            ,coalesce(sum(nlp.vl_pago)    , 0) as vl_pago
                            ,coalesce(sum(nlpa.vl_anulado), 0) as vl_anulado
    
                     from empenho.nota_liquidacao_paga as nlp
                          left join empenho.nota_liquidacao_paga_anulada as nlpa
                               on (     nlp.exercicio    = nlpa.exercicio
                                    and nlp.cod_entidade = nlpa.cod_entidade
                                    and nlp.cod_nota     = nlpa.cod_nota
                                    and nlp.timestamp    = nlpa.timestamp
                                  )
                     where         nlp.cod_entidade = '|| inCodEntidade || ' 
                     group by  nlp.exercicio
                              ,nlp.cod_entidade
                              ,nlp.cod_nota
                    ) as pagamento on (
                                           pagamento.exercicio    = pl.exercicio_liquidacao
                                       and pagamento.cod_entidade = pl.cod_entidade
                                       and pagamento.cod_nota     = pl.cod_nota
                                      )  
    where     pl.cod_entidade = '|| inCodEntidade ||'
          and pl.cod_ordem    = '|| inCodOrdem    ||'
          and pl.exercicio    = '''||stExercicio||'''
   
    group by pl.cod_nota
            ,pl.exercicio_liquidacao
            ,pl.vl_pagamento  
            ,opla.vl_anulado  
            ,pagamento.vl_pago
            ,pagamento.vl_anulado


    order by pl.cod_nota, pl.exercicio_liquidacao
    ';

    for reRegistro in execute stSql
    loop
        return next reRegistro;
    end loop;

    return;

END;
$$ LANGUAGE plpgsql;
