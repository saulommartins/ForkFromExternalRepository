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
--    * Data de Criação: 13/11/2008
--
--
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * Casos de uso: uc-04.00.00
--
--    $Id: pega0DataContagemTempoContrato.sql 31697 2008-08-04 19:33:31Z souzadl $ 
--*/
CREATE OR REPLACE FUNCTION pega0DataRescisao() RETURNS DATE as $$
DECLARE
    stSql                       VARCHAR;
    dtRescisao                  DATE;
    crCursor                    REFCURSOR;
    stEntidade                  VARCHAR;
    inCodContrato               INTEGER;
BEGIN
    --Ticket #13869
    stEntidade := recuperarBufferTexto('stEntidade');
    inCodContrato := recuperarBufferInteiro('inCodContrato');
    stSql  := 'SELECT contrato_servidor_caso_causa.dt_rescisao
                 FROM pessoal'||stEntidade||'.contrato_servidor_caso_causa
                WHERE contrato_servidor_caso_causa.cod_contrato = '||inCodContrato;                
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO dtRescisao;
    CLOSE crCursor;                                 
    RETURN dtRescisao;
END;
$$ LANGUAGE 'plpgsql';