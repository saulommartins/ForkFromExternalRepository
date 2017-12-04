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
* ultimo_servidor_pis_pasep
* Data de Criação   : 25/02/2010


* @author Analista      Dagiane
* @author Desenvolvedor Eduardo Schitz

* @package URBEM
* @subpackage 

* @ignore # 

$Id:$
*/

CREATE OR REPLACE FUNCTION ultimo_servidor_pis_pasep(VARCHAR, INTEGER) RETURNS SETOF colunasUltimoServidorPisPasep AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    inCodPeriodoMovimentacao        ALIAS FOR $2;
    rwPisPasep                      colunasUltimoServidorPisPasep%ROWTYPE;
    stTimestampFechamentoPeriodo    VARCHAR;
    stSql                           VARCHAR;
    reRegistro                      RECORD;
BEGIN
    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidade);

    stSql := '    SELECT servidor_pis_pasep.cod_servidor
                    FROM pessoal'|| stEntidade ||'.servidor_pis_pasep
              INNER JOIN (  SELECT servidor_pis_pasep.cod_servidor
                                 , max(servidor_pis_pasep.timestamp) as timestamp
                              FROM pessoal'|| stEntidade ||'.servidor_pis_pasep
                             WHERE servidor_pis_pasep.timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                          GROUP BY servidor_pis_pasep.cod_servidor) as max_servidor_pis_pasep
                      ON max_servidor_pis_pasep.cod_servidor = servidor_pis_pasep.cod_servidor
                     AND max_servidor_pis_pasep.timestamp = servidor_pis_pasep.timestamp';

    FOR reRegistro IN EXECUTE stSql LOOP
        rwPisPasep.cod_servidor := reRegistro.cod_servidor;
        
        RETURN NEXT rwPisPasep;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';
