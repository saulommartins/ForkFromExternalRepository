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
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.05.03,uc-02.05.04,uc-02.05.05,uc-02.05.06,uc-02.05.07,uc-02.05.08,uc-02.05.10,uc-02.05.11
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:50  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcers.fn_somatorio_contabil_siam(VARCHAR, VARCHAR, VARCHAR, VARCHAR,  INTEGER)  RETURNS numeric(14,2) AS '
DECLARE
    stCodEstrutural             ALIAS FOR $1;
    stTipo                      ALIAS FOR $2;
    stTipoRelatorio             ALIAS FOR $3;
    stTipoDespesa               ALIAS FOR $4;
    inMes                       ALIAS FOR $5;

    stSql                       VARCHAR   := '''';
    stTipoDespesaDesc           VARCHAR   := '''';
    stTipoDespesaDesc2          VARCHAR   := '''';
    nuSoma                      NUMERIC   := 0;
    inMesCont                   INTEGER   := 0;

    crCursor                    REFCURSOR;

BEGIN
    inMesCont := inMes;

    IF (stTipoDespesa=''P'') THEN
       stTipoDespesaDesc := ''pago'';
    ELSE
        IF (stTipoDespesa=''L'') THEN
            stTipoDespesaDesc := ''liqu'';
        ELSE
            stTipoDespesaDesc  := ''empe'';
            stTipoDespesaDesc2 := ''anul'';
        END IF;
    END IF;

    IF (stTipo=1) THEN
        stSql := ''
            SELECT sum('';
                WHILE inMesCont <= 12 LOOP
                    IF inMesCont<10 THEN
                        stSql := stSql || ''arre0'' || inMesCont || '' + '';
                    ELSE
                        IF inMesCont=12 THEN
                            stSql := stSql || ''arre'' || inMesCont;
                        ELSE
                            stSql := stSql || ''arre'' || inMesCont || '' + '';
                        END IF;
                    END IF;
                    inMesCont := inMesCont + 1;
                END LOOP;
            stSql := stSql || '') as valor
            FROM
                samlink.vw_siam_receita_2004
            WHERE
                cod_estrutural like '''''' || stCodEstrutural || ''%''''  '';
    ELSE
        stSql := ''
            SELECT sum('';
                WHILE inMesCont <= 12 LOOP
                    IF inMesCont<10 THEN
                        stSql := stSql || stTipoDespesaDesc || ''0'' || inMesCont || '' + '';
                    ELSE
                        IF inMesCont=12 THEN
                            stSql := stSql || stTipoDespesaDesc || inMesCont;
                        ELSE
                            stSql := stSql || stTipoDespesaDesc || inMesCont || '' + '';
                        END IF;
                    END IF;
                    inMesCont := inMesCont + 1;
                END LOOP;
                IF (stTipoDespesa=''E'') THEN
                    inMesCont = inMes;
                    WHILE inMesCont <= 12 LOOP
                        IF inMesCont<10 THEN
                            stSql := stSql || '' - '' || stTipoDespesaDesc2 || ''0'' || inMesCont;
                        ELSE
                            stSql := stSql || '' - '' || stTipoDespesaDesc2 || inMesCont;
                        END IF;
                        inMesCont := inMesCont + 1;
                    END LOOP;
                END IF;
            stSql := stSql || '') as valor
            FROM
               samlink.vw_siam_despesa_2004
            WHERE
                elemen like '''''' || stCodEstrutural || ''%'''' '';

            IF (stTipoRelatorio=1) THEN
                stSql := stSql || '' AND orgao <> ''''01'''' '';
            ELSE
                stSql := stSql || '' AND orgao = ''''01'''' '';
            END IF;

    END IF;


    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuSoma;
    CLOSE crCursor;


    RETURN nuSoma;
END;
' LANGUAGE 'plpgsql';
