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
* $Revision: 29078 $
* $Name$
* $Author: carlosadriano $
* $Date: 2008-04-09 09:20:51 -0300 (Qua, 09 Abr 2008) $
*
* Casos de uso: uc-02.04.22,uc-02.04.07,uc-02.04.04,uc-02.04.24 
*/


CREATE OR REPLACE FUNCTION tcemg.fn_busca_saldo_anterior_anexoIII(varchar, varchar, varchar, varchar, integer) RETURNS NUMERIC[] AS $$
DECLARE
    stExercicio     ALIAS FOR $1;
    inCodEntidade   ALIAS FOR $2;
    stDtInicial     ALIAS FOR $3;
    stDtFinal       ALIAS FOR $4;
    inCodPlano      ALIAS FOR $5;

    stDataInicialAnterior VARCHAR := '';
    stDataInicialFinal    VARCHAR := '';
    stSql                 VARCHAR := '';
    valor                 NUMERIC[];
    reRegistro            RECORD;

BEGIN

-- stDataInicialAnterior := SUBSTR(stDtInicial, 0, 7)||(stExercicio::INTEGER-1)::VARCHAR;
-- stDataInicialFinal    := SUBSTR(stDtFinal, 0, 7)||(stExercicio::INTEGER-1)::VARCHAR;

--Necessario fixar valor para o inicio do ano devido a implatantação do TCEMG
stDataInicialAnterior := '01/01/'||stExercicio;
stDataInicialFinal    := '01/01/'||stExercicio;
stSql :='
		SELECT COALESCE(vl_saldo_anterior,''0.00'') AS vl_saldo_anterior
              ,COALESCE(vl_saldo_atual, ''0.00'') AS vl_saldo_atual
        FROM                                                                                        
            contabilidade.fn_rl_balancete_verificacao('|| quote_literal(stExercicio) ||'
                                                    ,''cod_entidade IN ('|| inCodEntidade ||') and cod_plano = '|| inCodPlano ||' ''
                                                    ,'|| quote_literal(stDtInicial) ||'
                                                    ,'|| quote_literal(stDtFinal) ||'
                                                    ,''A''::CHAR)
        as retorno( cod_estrutural varchar                                                      
                ,nivel integer                                                               
                ,nom_conta varchar                                                           
                ,cod_sistema integer                                                         
                ,indicador_superavit char(12)                                                
                ,vl_saldo_anterior numeric                                                   
                ,vl_saldo_debitos  numeric                                                   
                ,vl_saldo_creditos numeric                                                   
                ,vl_saldo_atual    numeric                                                   
                )                             

                where cod_sistema  = 1
';

FOR reRegistro IN EXECUTE stSql
LOOP    
    valor[0] := reRegistro.vl_saldo_anterior;
    valor[1] := reRegistro.vl_saldo_atual;        
END LOOP;

RETURN valor;

END

$$ language 'plpgsql';
