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
CREATE OR REPLACE FUNCTION pega1PercentualAposentadoria() RETURNS NUMERIC as $$
DECLARE
    stSql                       VARCHAR;
    nuPercentual                NUMERIC;
    crCursor                    REFCURSOR;
    stEntidade                  VARCHAR;
    inCodContrato               INTEGER;
BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
    inCodContrato := recuperarBufferInteiro('inCodContrato');
    inCodContrato := recuperaContratoServidorPensionista(inCodContrato);

    stSql := '      SELECT percentual
                      FROM pessoal'||stEntidade||'.aposentadoria
                INNER JOIN (      SELECT cod_contrato
                                       , MAX(timestamp) AS timestamp
                                    FROM pessoal'||stEntidade||'.aposentadoria
                                GROUP BY cod_contrato
                           ) AS MAXt
                        ON MAXt.cod_contrato = aposentadoria.cod_contrato
                       AND MAXt.timestamp    = aposentadoria.timestamp 
                 LEFT JOIN pessoal'||stEntidade||'.aposentadoria_excluida
                        ON aposentadoria_excluida.cod_contrato            = aposentadoria.cod_contrato
                       AND aposentadoria_excluida.timestamp_aposentadoria = aposentadoria.timestamp
                     WHERE aposentadoria_excluida.cod_contrato IS NULL
                       AND aposentadoria.cod_contrato = '|| inCodContrato ||'
                     ;
             ';

    EXECUTE stSql
       INTO nuPercentual;

    IF nuPercentual IS NULL THEN
        RETURN 0.00;
    ELSE
        RETURN nuPercentual;
    END IF;
END;
$$ LANGUAGE 'plpgsql';
