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
--    * Data de Criação: 03/07/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 29001 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2008-04-04 11:32:08 -0300 (Sex, 04 Abr 2008) $
--
--    * Casos de uso: uc-04.05.10
--*/

CREATE OR REPLACE FUNCTION deletarEventoCalculadoComplementar(INTEGER,INTEGER,INTEGER) RETURNS BOOLEAN as '

DECLARE
    inCodContrato               ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    inCodComplementar           ALIAS FOR $3;
    stSql                       VARCHAR := '''';
    Registro                    RECORD;
    stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
BEGIN
    stSql := ''SELECT evento_complementar_calculado.*
                 FROM folhapagamento''||stEntidade||''.evento_complementar_calculado
                    , folhapagamento''||stEntidade||''.ultimo_registro_evento_complementar
                    , folhapagamento''||stEntidade||''.registro_evento_complementar
                WHERE evento_complementar_calculado.cod_registro    = ultimo_registro_evento_complementar.cod_registro
                  AND evento_complementar_calculado.cod_evento      = ultimo_registro_evento_complementar.cod_evento
                  AND evento_complementar_calculado.cod_configuracao= ultimo_registro_evento_complementar.cod_configuracao
                  AND evento_complementar_calculado.timestamp_registro       = ultimo_registro_evento_complementar.timestamp
                  AND ultimo_registro_evento_complementar.cod_registro = registro_evento_complementar.cod_registro
                  AND ultimo_registro_evento_complementar.cod_evento   = registro_evento_complementar.cod_evento
                  AND ultimo_registro_evento_complementar.cod_configuracao = registro_evento_complementar.cod_configuracao
                  AND ultimo_registro_evento_complementar.timestamp = registro_evento_complementar.timestamp
                  AND registro_evento_complementar.cod_contrato = ''||inCodContrato||'' 
                  AND registro_evento_complementar.cod_complementar = ''||inCodComplementar||''
                  AND registro_evento_complementar.cod_periodo_movimentacao = ''||inCodPeriodoMovimentacao||'' '';                 
    FOR Registro IN EXECUTE stSql
    LOOP
        stSql := ''DELETE FROM folhapagamento''||stEntidade||''.evento_complementar_calculado_dependente WHERE cod_registro = ''||Registro.cod_registro||''
                                                                   AND cod_evento   = ''||Registro.cod_evento||''
                                                                   AND cod_configuracao = ''||Registro.cod_configuracao||''
                                                                   AND timestamp_registro    = ''''''||Registro.timestamp_registro||'''''' '';    
        EXECUTE stSql;                                                                   
        stSql := ''DELETE FROM folhapagamento''||stEntidade||''.evento_complementar_calculado WHERE cod_registro = ''||Registro.cod_registro||''
                                                                   AND cod_evento   = ''||Registro.cod_evento||''
                                                                   AND cod_configuracao = ''||Registro.cod_configuracao||''
                                                                   AND timestamp_registro    = ''''''||Registro.timestamp_registro||'''''' '';
        EXECUTE stSql;
    END LOOP;
    RETURN true;
END;
'LANGUAGE 'plpgsql';
