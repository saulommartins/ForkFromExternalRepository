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
--/**
--    * Função PLSQL
--    * Data de Criação: 25/10/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23157 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-11 14:19:50 -0300 (Seg, 11 Jun 2007) $
--
--    * Casos de uso: uc-04.05.18
--*/

CREATE OR REPLACE FUNCTION  inserirEventosAutomaticosRescisao(INTEGER,VARCHAR,VARCHAR[]) RETURNS BOOLEAN as $$
DECLARE
    inCodTipo                   ALIAS FOR $1;
    stDesdobramentos            ALIAS FOR $2;
    arIncidencias               ALIAS FOR $3;
    arDesdobramentos            VARCHAR[];
    stDesdobramento             VARCHAR;
    inCodContrato               INTEGER;
    inCodPeriodoMovimentacao    INTEGER;
    inCodEvento                 INTEGER;
    inIndex                     INTEGER;
    inCountArray                INTEGER;
    dtVigencia                  VARCHAR := '';
    boRetorno                   BOOLEAN;
    boInserir                   BOOLEAN:=false;
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN
    inCodContrato              := recuperarBufferInteiro('inCodContrato');
    inCodPeriodoMovimentacao   := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    dtVigencia                 := recuperarBufferTexto('dtVigenciaIrrf');
    inCodEvento := selectIntoInteger(' SELECT cod_evento
                               FROM folhapagamento'||stEntidade||'.tabela_irrf_evento
                                  , folhapagamento'||stEntidade||'.tabela_irrf
                                  , (SELECT max(timestamp) as timestamp
                                          , cod_tabela
                                       FROM folhapagamento'||stEntidade||'.tabela_irrf
                                      WHERE tabela_irrf.vigencia = '''||dtVigencia||'''
                                   GROUP BY cod_tabela) as max_tabela_irrf
                              WHERE tabela_irrf_evento.cod_tipo = '||inCodTipo||'
                                AND tabela_irrf_evento.cod_tabela = tabela_irrf.cod_tabela
                                AND tabela_irrf_evento.timestamp  = tabela_irrf.timestamp
                                AND tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                                AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp');
                       
    arDesdobramentos := string_to_array(stDesdobramentos,'#');
    FOR inIndex IN 1 .. 5
    LOOP   
        stDesdobramento := arDesdobramentos[inIndex];
        IF stDesdobramento IS NOT NULL THEN
            --S#A#V#P#D
            --(S)
            IF stDesdobramento = 'S' THEN
                boInserir := true;
            END IF;                        
            --Incicência em Aviso Prévio(A)
            IF stDesdobramento = 'A' AND arIncidencias[3] = 't' THEN
                boInserir := true;
            END IF;            
            --Incicência em Férias Vencidas(V) or Férias Proporcionais(P)
            IF (stDesdobramento = 'V' or stDesdobramento = 'P') AND arIncidencias[2] = 't' THEN
                boInserir := true;
            END IF;
            --Incicência em Décimo Terceiro(D)
            IF stDesdobramento = 'D' AND arIncidencias[1] = 't' THEN
                boInserir := true;
            END IF;    
            IF boInserir is true THEN
                boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,stDesdobramento);
            END IF;
            boInserir := false;
        END IF;
    END LOOP;
    return boRetorno;
END;
$$ LANGUAGE 'plpgsql';
