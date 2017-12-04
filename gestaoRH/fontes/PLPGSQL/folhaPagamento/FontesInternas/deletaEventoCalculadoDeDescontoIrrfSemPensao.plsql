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
* script de funcao PLSQL
* 
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br

* $Revision: 23095 $
* $Name$
* $Autor: MArcia $
* Date: 2006/01/24 10:50:00 $
*
* Caso de uso: uc-04.05.48
*
* Objetivo: 

*/




CREATE OR REPLACE FUNCTION deletaEventoCalculadoDeDescontoIrrfSemPensao() RETURNS boolean as '

DECLARE

    inCodContrato                 INTEGER := 0;
    inCodPeriodoMovimentacao      INTEGER := 0;
    stDataFinalCompetencia        VARCHAR := '''';
    stTimestamp                   VARCHAR := '''';
    inCodEvento                   INTEGER := 0;
    boRetorno                     BOOLEAN := TRUE;
    stSql                         VARCHAR := '''';
    stTimestampTabelaIrrf         VARCHAR := '''';

stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


    inCodContrato := recuperarBufferInteiro(''inCodContrato'');

    inCodPeriodoMovimentacao := recuperarBufferInteiro(''inCodPeriodoMovimentacao'');

    stDataFinalCompetencia := recuperarBufferTexto(''stDataFinalCompetencia'');

    stTimestamp := pega0TimestampTabelaIrrfNaData( stDataFinalCompetencia );

    inCodEvento := pega0EventoDeDescontoIrrfParaSalarioNoTimestampTabela(stTimestamp);

    stSql := ''
          DELETE FROM folhapagamento''||stEntidade||''.evento_calculado
           WHERE cod_registro 
             IN( SELECT registro_evento.cod_registro
                 FROM folhapagamento''||stEntidade||''.registro_evento
                    , folhapagamento''||stEntidade||''.registro_evento_periodo
                WHERE registro_evento.cod_registro = registro_evento_periodo.cod_registro
                  AND registro_evento.cod_evento = ''||inCodEvento||''
                  AND registro_evento_periodo.cod_contrato    = ''||inCodContrato||''
                  AND registro_evento_periodo.cod_periodo_movimentacao  = ''||inCodPeriodoMovimentacao||''
               )'';

    EXECUTE stSql;

    RETURN boRetorno;
END;
' LANGUAGE 'plpgsql';

