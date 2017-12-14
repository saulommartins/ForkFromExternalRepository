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
/* recuperarSituacaoDoContratoRescisaoPostergada
 * 
 * Data de Criação : 29/01/2009


 * @author Analista : Dagiane
 * @author Desenvolvedor : Alex Cardoso
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */

-- A - Ativo
-- P - Aposentado
-- R - Rescindido
-- E - Pensionista

CREATE OR REPLACE FUNCTION recuperarSituacaoDoContratoRescisaoPostergada(INTEGER, INTEGER, VARCHAR) RETURNS VARCHAR as $$
DECLARE
    inCodContrato                   ALIAS FOR $1;
    inCodPeriodoMovimentacao        ALIAS FOR $2;
    stEntidade                      ALIAS FOR $3;
    stSQL                           VARCHAR:='';
    stRetorno                       VARCHAR:='';
    rePeriodoMovimentacao           RECORD;
    crCursor                        REFCURSOR;
BEGIN

    stRetorno := recuperarSituacaoDoContrato(inCodContrato, inCodPeriodoMovimentacao, stEntidade);
    
    IF stRetorno = 'R' THEN
    
        stSQL := 'SELECT dt_inicial
                       , dt_final 
                    FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                   WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao;
                   
        OPEN crCursor FOR EXECUTE stSQL;
            FETCH crCursor INTO rePeriodoMovimentacao;
        CLOSE crCursor;    
        
        stSQL := 'SELECT true
                    FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
                   WHERE cod_contrato = '|| inCodContrato ||'
                     AND dt_rescisao BETWEEN '|| quote_literal(rePeriodoMovimentacao.dt_inicial) ||' AND '|| quote_literal(rePeriodoMovimentacao.dt_final) ||' ';
                     
        IF selectIntoBoolean(stSQL) IS TRUE THEN
            RETURN 'A';
        END IF; 
    END IF;
    
    RETURN stRetorno;
END;
$$ LANGUAGE 'plpgsql';
