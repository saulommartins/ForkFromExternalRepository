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
--    * Data de Criação: 14/11/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23133 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-07 12:40:10 -0300 (Qui, 07 Jun 2007) $
--
--    * Casos de uso: uc-04.05.19
--*/
CREATE OR REPLACE FUNCTION pegaIncidenciaFerias(INTEGER) RETURNS BOOLEAN as $$
DECLARE
    inCodIncidencia     ALIAS FOR $1;
    inCodContrato       INTEGER;
    inCodServidor       INTEGER;
    inContador          INTEGER;
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN
    inCodContrato := recuperarBufferInteiro('inCodContrato');
    inCodServidor := pega0ServidorDoContrato(inCodContrato);

    inContador := selectIntoInteger('SELECT count(pensao_incidencia.*) as contador
                              FROM pessoal'||stEntidade||'.pensao
                                 , (SELECT cod_pensao
                                         ,  max(timestamp) as timestamp
                                      FROM pessoal'||stEntidade||'.pensao
                                    GROUP BY cod_pensao) as max_pensao
                                 , pessoal'||stEntidade||'.pensao_incidencia
                             WHERE pensao.cod_pensao = max_pensao.cod_pensao
                               AND pensao.timestamp = max_pensao.timestamp
                               AND pensao.cod_pensao = pensao_incidencia.cod_pensao
                               AND pensao.timestamp = pensao_incidencia.timestamp   
                               AND pensao.cod_servidor = '||inCodServidor||'
                               AND pensao_incidencia.cod_incidencia = '||inCodIncidencia);
    IF inContador = 1 THEN
        RETURN TRUE;
    ELSE
        RETURN FALSE;
    END IF; 
END;
$$ LANGUAGE 'plpgsql';
