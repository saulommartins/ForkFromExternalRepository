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
--    * Data de Criação: 00/00/0000
--
--
--    * @author Projetista: Vandré Miguel Ramos
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23402 $
--    $Author: souzadl $
--    $Date: 2007-06-20 16:57:16 -0300 (Qua, 20 Jun 2007) $
--
--    * Casos de uso: uc-04.05.24
--*/

CREATE OR REPLACE FUNCTION  verificaAdiantamento(VARCHAR) RETURNS BOOLEAN as $$

DECLARE
    stAnoAdiantamento           ALIAS FOR $1;
    inCodContrato               INTEGER;
    inContador                  INTEGER;
    boRetorno                   BOOLEAN := FALSE;
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN
    inCodContrato              := recuperarBufferInteiro('inCodContrato');
    inContador := selectIntoInteger(' 
                SELECT count(periodo_movimentacao.cod_periodo_movimentacao) as contador
                 FROM   folhapagamento'|| stEntidade ||'.periodo_movimentacao
                       ,folhapagamento'|| stEntidade ||'.contrato_servidor_periodo
                       ,folhapagamento'|| stEntidade ||'.registro_evento_decimo
                       ,folhapagamento'|| stEntidade ||'.ultimo_registro_evento_decimo
                       ,folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                WHERE to_char(dt_inicial,''yyyy'') = '|| stAnoAdiantamento ||'
                  AND to_char(dt_final,''yyyy'')   = '|| stAnoAdiantamento ||'

                  AND periodo_movimentacao.cod_periodo_movimentacao   = contrato_servidor_periodo.cod_periodo_movimentacao
                  AND registro_evento_decimo.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao
                  AND registro_evento_decimo.cod_contrato             = contrato_servidor_periodo.cod_contrato
                  AND registro_evento_decimo.cod_contrato             = '|| inCodContrato ||'
                  AND registro_evento_decimo.desdobramento            = ''A''

                  AND registro_evento_decimo.cod_registro             = ultimo_registro_evento_decimo.cod_registro
                  AND registro_evento_decimo.cod_evento               = ultimo_registro_evento_decimo.cod_evento
                  AND registro_evento_decimo.timestamp                = ultimo_registro_evento_decimo.timestamp
                  AND registro_evento_decimo.desdobramento            = ultimo_registro_evento_decimo.desdobramento

                  AND ultimo_registro_evento_decimo.cod_registro      = evento_decimo_calculado.cod_registro
                  AND ultimo_registro_evento_decimo.cod_evento        = evento_decimo_calculado.cod_evento
                  AND ultimo_registro_evento_decimo.timestamp         = evento_decimo_calculado.timestamp_registro
                  AND ultimo_registro_evento_decimo.desdobramento     = evento_decimo_calculado.desdobramento'
               );
    IF inContador > 0 THEN
       boRetorno := TRUE;
    END IF;

    RETURN boRetorno;
END;
$$LANGUAGE 'plpgsql';

