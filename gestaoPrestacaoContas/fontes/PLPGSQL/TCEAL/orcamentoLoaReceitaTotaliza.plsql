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
* $Revision: 56934 $
* $Name$
* $Author: gelson $
* $Date: 2014-01-08 17:46:44 -0200 (Wed, 08 Jan 2014) $
*
* Casos de uso: uc-02.00.00
* Casos de uso: uc-02.08.07
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.7  2007/07/30 18:28:33  tonismar
alteração de uc-00.00.00 para uc-02.00.00

Revision 1.6  2006/07/05 20:37:44  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tceal.fn_orcamento_loa_receita_totaliza(varchar,varchar,varchar,varchar,integer) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1         ;
    stCodEntidades      ALIAS FOR $2         ;
    stDataInicial       ALIAS FOR $3         ;
    stDataFinal         ALIAS FOR $4         ;
    stCodReceita        ALIAS FOR $5         ;
    stSql               VARCHAR   := ''''    ;
    reRegistro          RECORD               ;
    arRetorno           NUMERIC[] := array[0];
BEGIN
    stSql := '
    SELECT coalesce(tabela.vl_arrecadado,0.00) as vl_arrecadado
    
      FROM orcamento.fn_balancete_receita('|| quote_literal(stExercicio) ||','''','|| quote_literal(stDataInicial) ||', '|| quote_literal(stDataFinal) ||', '|| quote_literal(stCodEntidades) ||','''','''','''','''','''','''','''' )
           AS tabela
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
                     
     WHERE tabela.cod_receita = '|| stCodReceita || '
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        reRegistro.vl_arrecadado := reRegistro.vl_arrecadado * -1;
        
        RETURN NEXT reRegistro;
    END LOOP;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';
