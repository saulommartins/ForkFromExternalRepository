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
* Casos de uso: uc-02.00.00
*/

/*
$Log$
Revision 1.8  2006/09/28 09:56:56  eduardo
Bug #7060#

Revision 1.7  2006/07/14 17:58:35  andre.almeida
Bug #6556#

Alterado scripts de NOT IN para NOT EXISTS.

Revision 1.6  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_empenho_anula_liquidacoes_duplicadas() RETURNS boolean AS '
DECLARE
    stSql               VARCHAR   := '''';
    stNotaAtual         VARCHAR   := '''';
    valorAtual          NUMERIC   := 0.00;
    reRegistro          RECORD;
    msg                 VARCHAR;

BEGIN

    stSql :=''
    SELECT
        epl.cod_ordem,
        epl.cod_nota,
        epl.exercicio,
        epl.cod_entidade,
        epl.vl_pagamento,
        empenho.fn_total_saldo_liquidacao(enl.exercicio,enl.cod_nota,enl.cod_entidade) as saldo
    FROM
        empenho.ordem_pagamento         as eop,
        empenho.pagamento_liquidacao    as epl,
        empenho.nota_liquidacao         as enl
    WHERE
        eop.cod_ordem                   = epl.cod_ordem
    AND eop.exercicio                   = epl.exercicio
    AND eop.cod_entidade                = epl.cod_entidade

    AND epl.exercicio_liquidacao        = enl.exercicio
    AND epl.cod_nota                    = enl.cod_nota
    AND epl.cod_entidade                = enl.cod_entidade

    AND NOT EXISTS ( /*SELECT 1
                       FROM empenho.ordem_pagamento_anulada e_opa
                      WHERE e_opa.cod_ordem    = eop.cod_ordem
                        AND e_opa.exercicio    = eop.exercicio
                        AND e_opa.cod_entidade = eop.cod_entidade
                     */ 
                      select vl_a_anular 
                      from empenho.fn_consultar_valores_op( epl.exercicio, epl.cod_entidade, epl.cod_ordem ) 
                           as retorno ( cod_nota                integer, 
                                        exercicio_liquidacao    varchar, 
                                        vl_pagamento            numeric,
                                        vl_pagamento_anulado    numeric,
                                        vl_pago                 numeric,
                                        vl_pago_anulado         numeric,
                                        vl_a_anular             numeric
                                      )
                      where vl_a_anular > 0 
                   )
    AND NOT EXISTS ( SELECT 1
                       FROM empenho.pagamento_liquidacao_nota_liquidacao_paga e_plnl
                      WHERE e_plnl.cod_ordem            = epl.cod_ordem
                        AND e_plnl.cod_nota             = epl.cod_nota
                        AND e_plnl.exercicio            = epl.exercicio
                        AND e_plnl.exercicio_liquidacao = epl.exercicio_liquidacao
                        AND e_plnl.cod_entidade         = epl.cod_entidade
                   )
    ORDER BY epl.exercicio_liquidacao,epl.cod_nota,epl.exercicio,epl.cod_ordem
    '';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        IF reRegistro.cod_nota||reRegistro.cod_entidade||reRegistro.exercicio <> stNotaAtual THEN
            stNotaAtual    := reRegistro.cod_nota||reRegistro.cod_entidade||reRegistro.exercicio;
            valorAtual      := reRegistro.vl_pagamento;
        ELSE
            valorAtual      :=  valorAtual + reRegistro.vl_pagamento;
            IF valorAtual > reRegistro.saldo THEN
                msg := ''Anulada ordem: ''||reRegistro.cod_ordem;
                INSERT INTO empenho.ordem_pagamento_anulada(cod_ordem,exercicio,cod_entidade,motivo,vl_anulado) VALUES(reRegistro.cod_ordem,reRegistro.exercicio,reRegistro.cod_entidade,''Emissão duplicada erro no sistema.'',reRegistro.vl_pagamento);
            END IF;
        END IF;

    END LOOP;

    RETURN true;
END;

' LANGUAGE 'plpgsql';

