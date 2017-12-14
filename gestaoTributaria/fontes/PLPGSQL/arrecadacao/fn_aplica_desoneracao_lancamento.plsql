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
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_aplica_desoneracao_lancamento.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.04
*/

/*
$Log$
Revision 1.2  2007/05/31 14:49:41  cercato
Bug #9340#

Revision 1.1  2007/05/31 14:04:26  cercato
Bug #9340#

*/


CREATE OR REPLACE FUNCTION aplica_desoneracao_lancamento(integer,integer,integer,integer,numeric,date,integer,integer) returns varchar as $$
declare
    inCodCredito        ALIAS FOR $1;
    inCodEspecie        ALIAS FOR $2;
    inCodGenero         ALIAS FOR $3;
    inCodNatureza       ALIAS FOR $4;
    nuValor             ALIAS FOR $5;
    dtDataAtual         ALIAS FOR $6;
    inInsc              ALIAS FOR $7;
    inNumCGM            ALIAS FOR $8;
    stSqlFuncoes        VARCHAR;
    stSqlFuncoes2       VARCHAR;
    stExecuta           VARCHAR;
    stExecuta2          VARCHAR;
    stOcorrencia        VARCHAR;
    nuRetorno           NUMERIC;
    stRetorno           VARCHAR;
    reRecordFuncoes     RECORD;
    reRecordFuncoes2    RECORD;
    reRecordExecuta     RECORD;
    boUtilizar          BOOLEAN;
    boDesonerado        BOOLEAN;

begin
    stSqlFuncoes := '
        SELECT
            administracao.funcao.nom_funcao as funcao,
            arrecadacao.desoneracao.expiracao,
            arrecadacao.desoneracao.prorrogavel,
            arrecadacao.desoneracao.revogavel,
            arrecadacao.desoneracao.cod_desoneracao

        FROM
            arrecadacao.desoneracao

        INNER JOIN
            administracao.funcao
        ON
            administracao.funcao.cod_funcao         = arrecadacao.desoneracao.cod_funcao
            AND administracao.funcao.cod_modulo     = arrecadacao.desoneracao.cod_modulo
            AND administracao.funcao.cod_biblioteca = arrecadacao.desoneracao.cod_biblioteca

        WHERE
            arrecadacao.desoneracao.cod_credito      = '|| inCodCredito  ||'
            AND arrecadacao.desoneracao.cod_especie  = '|| inCodEspecie  ||'
            AND arrecadacao.desoneracao.cod_genero   = '|| inCodGenero   ||'
            AND arrecadacao.desoneracao.cod_natureza = '|| inCodNatureza ||'
    ';
    -- executa
    nuRetorno := nuValor;
    stRetorno := '';
    FOR reRecordFuncoes IN EXECUTE stSqlFuncoes LOOP

        IF (inInsc IS NOT NULL) THEN

            stExecuta2 := '
                SELECT
                    arrecadacao.desonerado.data_concessao,
                    arrecadacao.desonerado.data_prorrogacao,
                    arrecadacao.desonerado.data_revogacao,
                    arrecadacao.desonerado.ocorrencia,
                    arrecadacao.desonerado.numcgm

                FROM 
                    arrecadacao.desonerado

                LEFT JOIN
                    arrecadacao.desonerado_cad_economico
                ON
                    arrecadacao.desonerado_cad_economico.cod_desoneracao = arrecadacao.desonerado.cod_desoneracao
                    AND arrecadacao.desonerado_cad_economico.ocorrencia  = arrecadacao.desonerado.ocorrencia
                    AND arrecadacao.desonerado_cad_economico.numcgm      = arrecadacao.desonerado.numcgm

                LEFT JOIN
                    arrecadacao.desonerado_imovel
                ON
                    arrecadacao.desonerado_imovel.cod_desoneracao = arrecadacao.desonerado.cod_desoneracao
                    AND arrecadacao.desonerado_imovel.ocorrencia  = arrecadacao.desonerado.ocorrencia
                    AND arrecadacao.desonerado_imovel.numcgm      = arrecadacao.desonerado.numcgm

                WHERE
                    arrecadacao.desonerado.cod_desoneracao = '||reRecordFuncoes.cod_desoneracao||'
                    AND ( arrecadacao.desonerado_cad_economico.inscricao_economica = '||inInsc||' OR arrecadacao.desonerado_imovel.inscricao_municipal = '||inInsc||' ) 
            ';
        ELSE
            stExecuta2 := '
                SELECT
                    arrecadacao.desonerado.data_concessao,
                    arrecadacao.desonerado.data_prorrogacao,
                    arrecadacao.desonerado.data_revogacao,
                    arrecadacao.desonerado.ocorrencia,
                    arrecadacao.desonerado.numcgm

                FROM 
                    arrecadacao.desonerado

                WHERE
                    arrecadacao.desonerado.cod_desoneracao  = '||reRecordFuncoes.cod_desoneracao||'
                    AND arrecadacao.desonerado.numcgm       = '||inNumCGM||'
            ';

        END IF;

        IF ( (dtDataAtual <= reRecordFuncoes.expiracao) OR reRecordFuncoes.expiracao IS NULL ) THEN
            boUtilizar := true;
        ELSE
            boUtilizar := false;
        END IF;

        boDesonerado := false;
        stOcorrencia := '';
        IF ( boUtilizar = true OR (boUtilizar = false AND reRecordFuncoes.prorrogavel = true)) THEN
            FOR reRecordFuncoes2 IN EXECUTE stExecuta2 LOOP
                IF ( dtDataAtual <= reRecordFuncoes2.data_prorrogacao ) THEN
                    boUtilizar := true;
                END IF;

                IF ( (reRecordFuncoes.revogavel = true) AND (dtDataAtual >= reRecordFuncoes2.data_revogacao) ) THEN
                    boUtilizar := false;
                END IF;

                boDesonerado := true;
                stOcorrencia := reRecordFuncoes2.ocorrencia||'§'||reRecordFuncoes2.numcgm;
            END LOOP;
        END IF;

        IF (boUtilizar = true AND boDesonerado = true) THEN
            stExecuta :=  'SELECT '||reRecordFuncoes.funcao||'('||inInsc||', '||nuValor||') as valor ';     
            FOR reRecordExecuta IN EXECUTE stExecuta LOOP                
                nuRetorno := reRecordExecuta.valor;
                stRetorno := nuRetorno||'§'||reRecordFuncoes.cod_desoneracao||'§'||stOcorrencia;
            END LOOP;
        END IF;
    END LOOP;


   return stRetorno;
end;
$$ language 'plpgsql';
