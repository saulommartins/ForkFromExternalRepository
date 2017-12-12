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
* $Id: fn_busca_testada.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.5
* Caso de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.9  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_busca_testada(INTEGER,INTEGER)  RETURNS NUMERIC(20,4) AS '
DECLARE
    inIM                    ALIAS FOR $1;
    inExercicio             ALIAS FOR $2;
    inDistrito              INTEGER := 0;
    nuConfrontacaoPrincipal NUMERIC := 0.0000;
    nuTestadaPadrao         NUMERIC := 0.0000;
    nuRetFunc               NUMERIC := 0.0000;
    nuResultado             NUMERIC := 0.0000;
    boLog BOOLEAN;
BEGIN
    inDistrito := arrecadacao.fn_localizacao_distrito_imovel(inIM);
    nuConfrontacaoPrincipal := arrecadacao.fn_confrontacao_principal(inIM);
    nuTestadaPadrao := arrecadacao.fn_busca_tabela_conversao(3,inExercicio,cast(inDistrito as varchar),'''','''','''');

   nuRetFunc :=  arrecadacao.fn_verifica_tbl(4,inExercicio,cast(nuConfrontacaoPrincipal as varchar), cast(nuTestadaPadrao as varchar));
    IF nuRetFunc = -1.00 THEN
        IF nuConfrontacaoPrincipal = 0.00   THEN nuConfrontacaoPrincipal := 1.00;   END IF;
        IF nuTestadaPadrao = 0.00           THEN nuTestadaPadrao := 1.00;           END IF;
        nuResultado := (nuConfrontacaoPrincipal / nuTestadaPadrao) ^ 0.2500;
    ELSE
        nuResultado := nuRetFunc;
        IF nuResultado = 0.00 THEN
            nuResultado :=1.00;
        END IF;
    END IF;
    boLog := arrecadacao.salva_log(''arrecadacao.fn_busca_testada'',nuResultado::varchar);
    nuResultado := cast(nuResultado as numeric(20,4));
    RETURN nuResultado;

END;
' LANGUAGE 'plpgsql';
