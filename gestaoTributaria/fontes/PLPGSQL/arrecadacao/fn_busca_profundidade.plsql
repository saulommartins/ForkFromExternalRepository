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
* $Id: fn_busca_profundidade.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.5
* Caso de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.12  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_busca_profundidade(INTEGER,INTEGER)  RETURNS NUMERIC(20,4) AS '
DECLARE
    inIM                    ALIAS FOR $1;
    inExercicio             ALIAS FOR $2;
--    reRegistro              RECORD;
    inDistrito              INTEGER := 0;
    nuConfrontacaoPrincipal NUMERIC := 0.0000;
    nuTestadaPadrao         NUMERIC := 0.0000;
    inProfundidadePadrao    NUMERIC := 0.0000;
    inAreaTerreno           NUMERIC := 0.0000;
    inProfundidadeEquival   NUMERIC := 0.0000;
    nuRetFunc               NUMERIC := 0.0000;
    stSql                   VARCHAR := '''';
    nuResultado             NUMERIC := 0.0000;
    boLog   BOOLEAN;
BEGIN

/* Busca Distrito */
    inDistrito := arrecadacao.fn_localizacao_distrito_imovel(inIM);

/* Busca Confrontação Principal */
    /*nuConfrontacaoPrincipal := arrecadacao.fn_resultado_num(arrecadacao.fn_confrontacao_principal(inIM));*/

/* Calcula testada padrao */
    nuTestadaPadrao := arrecadacao.fn_verifica_tbl1p2(3,inExercicio,cast(inDistrito as varchar));
/* Calcula profundidade padrao */
    inProfundidadePadrao := arrecadacao.fn_verifica_tbl1p2(1,inExercicio,cast(inDistrito as varchar));
/* Calcula area do terreno */
    inAreaTerreno := arrecadacao.fn_area_real(inIM);
/* Calcula Profundidade Equivalente */
    IF inAreaTerreno = 0.00 THEN inAreaTerreno := 1.00; END IF;
    IF nuTestadaPadrao = 0.00 THEN nuTestadaPadrao := 1.00; END IF;
    inProfundidadeEquival := inAreaTerreno / nuTestadaPadrao;

/* Fator profundidade */
    nuRetFunc = arrecadacao.fn_verifica_tbl(2,inExercicio,cast(inProfundidadeEquival as varchar),cast(inProfundidadePadrao as varchar));

    IF nuRetFunc = -1.00 THEN
        nuResultado := ( inProfundidadePadrao / inProfundidadeEquival ) ^ 0.5;
    ELSE
        nuResultado := nuRetFunc;
        IF nuResultado = 0.00 THEN
            nuResultado := 1.00;
        END IF;
    END IF;

    nuResultado := cast(nuResultado as numeric(20,4));

/* Retorna valor calculado */
    boLog := arrecadacao.salva_log(''arrecadacao.fn_busca_profundidade'',nuResultado::varchar);
    RETURN nuResultado;

END;
' LANGUAGE 'plpgsql';

