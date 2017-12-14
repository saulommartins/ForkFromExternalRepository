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
 * PL para retorno de horário do ponto de um contrato
 * Data de Criação   : 09/10/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */
CREATE OR REPLACE FUNCTION recuperaListaHorariosContrato(INTEGER,INTEGER,VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    inCodContrato   ALIAS FOR $1;
    inCodPonto      ALIAS FOR $2;
    stEntidade      ALIAS FOR $3;
    stRetorno       VARCHAR:='';
    stSql           VARCHAR;
    reRegistro      RECORD;
BEGIN
    stSql := 'SELECT to_char(importacao_ponto_horario.horario,''HH24:mi'') as horario
                FROM ponto'|| stEntidade ||'.importacao_ponto_horario
               WHERE cod_contrato = '|| inCodContrato ||'
                 AND cod_ponto = '|| inCodPonto ||'
            ORDER BY importacao_ponto_horario.horario';

    FOR reRegistro IN EXECUTE stSql LOOP
        stRetorno := stRetorno || reRegistro.horario  ||' - ';
    END LOOP;
    IF stRetorno != '' THEN
        stRetorno := substr(stRetorno,1,char_length(stRetorno)-3);
    END IF;
    RETURN stRetorno;
END;
$$ language 'plpgsql';
