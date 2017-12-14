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
* $Revision: $
* $Name: $
* $Author: gelson $
* $Date: $
*
*/

CREATE OR REPLACE FUNCTION tcmgo.arquivo_exportacao_orcamento_rec(varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidades      ALIAS FOR $2;
    stDataInicial       ALIAS FOR $3;
    stDataFinal         ALIAS FOR $4;
    stSql               VARCHAR   := '';
    reRegistro          RECORD;
    arRetorno           NUMERIC[] := array[0];
BEGIN
    stSql := '
    SELECT tabela.cod_estrutural
         , tabela.cod_recurso
         , tabela.cod_receita
         , tabela.descricao
         , coalesce(tabela.vl_previsto     ,0.00) as vl_previsto
      FROM
    
    orcamento.fn_balancete_receita('|| quote_literal(stExercicio) ||','''','|| quote_literal(stDataInicial) ||', '|| quote_literal(stDataFinal) ||', '|| quote_literal(stCodEntidades) ||','''','''','''','''','''','''','''' )
    as tabela
    (
        cod_estrutural      VARCHAR ,
        cod_receita         INTEGER ,
        cod_recurso         VARCHAR(13) ,
        descricao           VARCHAR ,
        vl_previsto         NUMERIC ,
        vl_arrecadado       NUMERIC ,
        vl_arrecadado_ano   NUMERIC ,
        diferenca           NUMERIC
    )
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';
