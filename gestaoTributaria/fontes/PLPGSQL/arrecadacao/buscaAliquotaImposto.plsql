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
* $Id: buscaAliquotaImposto.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.1  2007/01/23 11:03:03  fabio
correção da tag de caso de uso
e movidos do cadastro econômico


*/

CREATE OR REPLACE FUNCTION economico.fn_busca_aliquota_imposto( INTEGER, INTEGER )  RETURNS varchar AS '
DECLARE

    BOEDIFICACAO BOOLEAN;
    
    INEXERCICIO ALIAS FOR $2;
    INIMOVEL ALIAS FOR $1;
    INQUANTIDADE INTEGER;
    NUALIQUOTA NUMERIC;
    NUISENTO NUMERIC;
    NULIMITACãO NUMERIC;
    NURETORNO NUMERIC;
    NUUSOSOLO NUMERIC;
    NUVUPTERRENO NUMERIC;
    STISENTO VARCHAR := '''';
    STLIMITACAO VARCHAR := '''';
    STUSOSOLO VARCHAR := '''';
    STVUPTERRENO VARCHAR := '''';

    stTrue VARCHAR := ''true'';
    stVazio VARCHAR := '''';
    stConstrucao VARCHAR := ''Construção'';
    stTerreno VARCHAR := ''Terreno'';

BEGIN

    STUSOSOLO := RECUPERACADASTROIMOBILIARIOIMOVELUSODOSOLO(  INIMOVEL  );
    NUUSOSOLO := ARRECADACAO.FN_VC2NUM(  STUSOSOLO  );
    STLIMITACAO := RECUPERACADASTROIMOBILIARIOIMOVELLIMITACAO(  INIMOVEL  );
    NULIMITACãO := ARRECADACAO.FN_VC2NUM(  STLIMITACAO  );
    BOEDIFICACAO := ARRECADACAO.VERIFICAEDIFICACAOIMOVEL(  INIMOVEL  );
    STISENTO := RECUPERACADASTROIMOBILIARIOIMOVELISENCAOIPTU(  INIMOVEL  );
    NUISENTO := ARRECADACAO.FN_VC2NUM(  STISENTO  );


    IF     NUISENTO  =  1 THEN

        NUALIQUOTA := 0.00;

    END IF;


    IF     BOEDIFICACAO  =   ''true'' THEN
        IF   NUUSOSOLO  =  2 THEN
            NUALIQUOTA := ARRECADACAO.FN_BUSCA_TABELA_CONVERSAO(  15, INEXERCICIO, stConstrucao, stTrue, stVazio, stVazio  );
        ELSE
            NUALIQUOTA := ARRECADACAO.FN_BUSCA_TABELA_CONVERSAO(  15, INEXERCICIO, stConstrucao, stVazio, stTrue, stVazio  );
        END IF;
    ELSE
        IF         STLIMITACAO  =  ''1''::VARCHAR THEN
            NUALIQUOTA := ARRECADACAO.FN_BUSCA_TABELA_CONVERSAO(  14 , INEXERCICIO , stTerreno , stTrue, stVazio , stVazio  );
        ELSE
            NUALIQUOTA := ARRECADACAO.FN_BUSCA_TABELA_CONVERSAO(  14 , INEXERCICIO , stTerreno , stVazio , stTrue, stVazio  );
        END IF;
    END IF;

RETURN NUALIQUOTA;

END;
' LANGUAGE 'plpgsql';
