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
* $Date: 2014-09-02 09:00:51 -0300 (Ter, 02 Set 2014) $
*
* Casos de uso: uc-02.00.00
* Casos de uso: uc-02.00.00
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.6  2007/07/30 18:28:33  tonismar
alteração de uc-00.00.00 para uc-02.00.00

Revision 1.5  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcers.fn_exportacao_pagamento_novo(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidades	ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    stFiltro            ALIAS FOR $5;
    stSql               VARCHAR   := '''';
    reRegistro          RECORD;


BEGIN

    stSql := '
	SELECT
        nl.cod_nota,
	    nl.exercicio_empenho,
		nl.cod_empenho,
		nl.cod_entidade,
        pp.cod_ordem,
        abs(np.vl_pago) as vl_pago,
		op.observacao,
		to_date(to_char(np.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as data_pagamento,
        ''+'' as sinal_valor,
		0 as cod_operacao,
        cast( 0 as varchar) as debito_codigo_conta_verificacao,
        cast( 0 as varchar) as credito_codigo_conta_verificacao,
        np.oid
	FROM
        	empenho.nota_liquidacao as nl,
	        empenho.nota_liquidacao_paga as np,
        	empenho.pagamento_liquidacao_nota_liquidacao_paga as pp,
	        empenho.ordem_pagamento as op
	WHERE
            to_char(np.timestamp,''yyyy'') = '|| quote_literal(stExercicio) ||'
	    AND nl.cod_entidade     IN ('|| stCodEntidades ||')
        AND to_date(to_char(np.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'')
	            BETWEEN
                    to_date('|| quote_literal(stDtInicial) ||',''dd/mm/yyyy'')
	            AND to_date('|| quote_literal(stDtFinal) ||',''dd/mm/yyyy'')
        AND nl.exercicio        = np.exercicio
	    AND nl.cod_nota         = np.cod_nota
        AND nl.cod_entidade     = np.cod_entidade
	    AND np.cod_nota         = pp.cod_nota
        AND np.exercicio        = pp.exercicio_liquidacao
	    AND np.timestamp        = pp.timestamp
        AND pp.cod_ordem        = op.cod_ordem
	    AND pp.exercicio        = op.exercicio
        AND pp.cod_entidade     = op.cod_entidade
    UNION
	SELECT
        nl.cod_nota,
	    nl.exercicio_empenho,
	    nl.cod_empenho,
	    nl.cod_entidade,
       	pp.cod_ordem,
       	abs(npa.vl_anulado) as vl_pago,
	    op.observacao,
        to_date(to_char(npa.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') as data_pagamento_anulado,
        ''-'' as sinal_valor,
        0 as cod_operacao,
        cast( 0 as varchar) as debito_codigo_conta_verificacao,
        cast( 0 as varchar) as credito_codigo_conta_verificacao,
        npa.oid
	FROM
        empenho.nota_liquidacao as nl,
	    empenho.nota_liquidacao_paga as np,
	    empenho.nota_liquidacao_paga_anulada as npa,
        empenho.pagamento_liquidacao_nota_liquidacao_paga as pp,
	    empenho.ordem_pagamento as op
	WHERE
            to_char(npa.timestamp_anulada,''yyyy'') = '|| quote_literal(stExercicio) ||'
	    AND nl.cod_entidade     IN ('|| stCodEntidades ||')
        AND to_date(to_char(npa.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'')
	            BETWEEN
                    to_date('|| quote_literal(stDtInicial) ||',''dd/mm/yyyy'')
	            AND to_date('|| quote_literal(stDtFinal) ||',''dd/mm/yyyy'')
        AND nl.exercicio        = np.exercicio
	    AND nl.cod_nota         = np.cod_nota
        AND nl.cod_entidade     = np.cod_entidade
	    AND np.cod_nota         = pp.cod_nota
        AND np.exercicio        = pp.exercicio_liquidacao
	    AND np."timestamp"      = pp."timestamp"

        AND np.cod_entidade     = npa.cod_entidade
        AND np.cod_nota         = npa.cod_nota
        AND np.exercicio        = npa.exercicio
        AND np."timestamp"      = npa."timestamp"

        AND pp.cod_ordem        = op.cod_ordem
	    AND pp.exercicio        = op.exercicio
        AND pp.cod_entidade     = op.cod_entidade
            ' || stFiltro ;


    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';
