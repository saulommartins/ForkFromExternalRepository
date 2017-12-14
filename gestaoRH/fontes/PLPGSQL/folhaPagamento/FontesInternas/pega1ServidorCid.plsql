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
--    * Data de Criação: 19/08/2010
--
--
--    * @author Fabio Bertoldi Rodrigues
--
--    * Casos de uso: uc-04.00.00
--
--    $Id:  $ 
--*/
CREATE OR REPLACE FUNCTION pega1ServidorCid() RETURNS NUMERIC as $$
DECLARE
    stSql                       VARCHAR;
    nuCid                       NUMERIC;
    stEntidade                  VARCHAR;
    inCodContrato               INTEGER;
    inPensionista               INTEGER;
BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
    inCodContrato := recuperarBufferInteiro('inCodContrato');
    inPensionista := recuperarBufferInteiro('inPensionista');

    IF inPensionista = 0 THEN

        stSql := '          SELECT cod_cid::numeric
                              FROM pessoal'||stEntidade||'.servidor_cid
                        INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                                ON servidor_contrato_servidor.cod_servidor = servidor_cid.cod_servidor
                        INNER JOIN (    SELECT MAX(timestamp) AS timestamp
                                             , cod_servidor
                                          FROM pessoal'||stEntidade||'.servidor_cid
                                      GROUP BY cod_servidor
                                   )    AS MAXt
                                ON MAXt.cod_servidor = servidor_cid.cod_servidor
                               AND MAXt.timestamp    = servidor_cid.timestamp
                             WHERE servidor_contrato_servidor.cod_contrato = '|| inCodContrato ||'
                                 ;
                 ';

    ELSE

        stSql := '      SELECT cod_cid::numeric
                          FROM pessoal'||stEntidade||'.contrato_pensionista
                    INNER JOIN pessoal'||stEntidade||'.pensionista_cid
                            ON pessoal'||stEntidade||'.contrato_pensionista.cod_pensionista =pessoal'||stEntidade||'.pensionista_cid.cod_pensionista
                         WHERE pessoal'||stEntidade||'.contrato_pensionista.cod_contrato = '|| inCodContrato ||'
                             ;
                 ';

    END IF;

    EXECUTE stSql
       INTO nuCid;

    RETURN nuCid;
END;
$$ LANGUAGE 'plpgsql';

