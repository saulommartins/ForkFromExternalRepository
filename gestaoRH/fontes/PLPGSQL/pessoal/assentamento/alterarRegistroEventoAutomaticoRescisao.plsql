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
--    * Data de Criação: 24/04/2007
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 25091 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-08-27 16:03:59 -0300 (Seg, 27 Ago 2007) $
--
--    * Casos de uso: uc-04.04.44
--*/

CREATE OR REPLACE FUNCTION alterarRegistroEventoAutomaticoRescisao(INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR,NUMERIC) RETURNS BOOLEAN as '

DECLARE
    inCodContrato               ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    inCodEvento                 ALIAS FOR $3;
    stFixado                    ALIAS FOR $4;
    stDesdobramento             ALIAS FOR $5;
    nuQuantidadeValor           ALIAS FOR $6;
    boRetorno                   BOOLEAN;
    inCodRegistro               INTEGER;
    stSql                       VARCHAR;
    stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
BEGIN
    inCodRegistro := selectIntoInteger(''SELECT ultimo_registro_evento_rescisao.cod_registro
                                 FROM folhapagamento''||stEntidade||''.ultimo_registro_evento_rescisao
                                    , folhapagamento''||stEntidade||''.registro_evento_rescisao
                                WHERE ultimo_registro_evento_rescisao.cod_registro = registro_evento_rescisao.cod_registro
                                  AND registro_evento_rescisao.cod_contrato = ''||inCodContrato||''
                                  AND registro_evento_rescisao.cod_periodo_movimentacao = ''||inCodPeriodoMovimentacao||''
                                  AND ultimo_registro_evento_rescisao.cod_evento = ''||inCodEvento||''
                                  AND ultimo_registro_evento_rescisao.desdobramento = ''''''||stDesdobramento||'''''' '');

    IF stFixado = ''V'' THEN
        stSql := ''UPDATE folhapagamento''||stEntidade||''.registro_evento_rescisao
           SET valor = ''||nuQuantidadeValor||''
         WHERE cod_registro = ''||inCodRegistro;
        EXECUTE stSql;
    ELSE
        stSql := ''UPDATE folhapagamento''||stEntidade||''.registro_evento_rescisao
           SET quantidade = ''||nuQuantidadeValor||''
         WHERE cod_registro = ''||inCodRegistro;
        EXECUTE stSql;
    END IF;
    RETURN boRetorno;
END;
'LANGUAGE 'plpgsql';
