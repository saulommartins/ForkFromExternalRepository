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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: valorPadraoDesc.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-01.03.96
*/

CREATE OR REPLACE FUNCTION economico.valor_padrao_desc(INTEGER,INTEGER,INTEGER,VARCHAR) RETURNS VARCHAR AS '
DECLARE
    inCodAtributo       ALIAS FOR $1;
    inCodModulo         ALIAS FOR $2;
    inCodCadastro       ALIAS FOR $3;
    stValores           ALIAS FOR $4;
    stValor             VARCHAR   := '''';
    stSaida             VARCHAR   := '''';
    reRegistro          RECORD;
    inCount             INTEGER := 1;
    arValores           INTEGER[] := array[0];

BEGIN
        arValores := string_to_array(trim(stValores),'','');
        
        WHILE true LOOP
            IF arValores[inCount] IS NULL THEN 
                EXIT; --Saida do Loop
            END IF;
            SELECT INTO 
                    stValor
                    valor_padrao
            FROM    administracao.atributo_valor_padrao
            WHERE   cod_atributo = inCodAtributo AND
                    cod_cadastro = inCodCadastro AND
                    cod_modulo   = inCodModulo   AND
                    cod_valor    = arValores[inCount];
            stSaida := stSaida || ''[][][]'' || stValor;
            inCount := inCount + 1;
        END LOOP;
        IF length(stSaida) > 1 THEN
            stSaida := substr(stSaida,7,length(stSaida));
        ELSE
            stSaida := stValores;
        END IF;

    RETURN stSaida;
END;
'language 'plpgsql';
