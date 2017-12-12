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
* $Id: fn_confrontacao_principal.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: UC-5.3.5
* Caso de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.9  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_confrontacao_principal(INTEGER)  RETURNS NUMERIC AS $$
DECLARE
    inIM                        ALIAS FOR $1;
    arRetorno                   VARCHAR;
    nuResultado                 NUMERIC;
    inLinhas                    INTEGER;
    boLog                       BOOLEAN;
BEGIN


    SELECT
        valor
    INTO
        nuResultado
    FROM
    (   SELECT
            i.inscricao_municipal,
            coalesce(sum(ce.valor),0) as valor
        FROM
            imobiliario.confrontacao_extensao ce
        INNER JOIN imobiliario.confrontacao c           ON  ce.cod_confrontacao     = c.cod_confrontacao    AND
                                                            ce.cod_lote             = c.cod_lote
        INNER JOIN imobiliario.confrontacao_trecho ct   ON  ce.cod_lote             = ct.cod_lote           AND
                                                            ce.cod_confrontacao     = ct.cod_confrontacao   AND
                                                            ct.principal = true
        INNER JOIN imobiliario.lote l                   ON  c.cod_lote              = l.cod_lote
        INNER JOIN imobiliario.vw_max_imovel_lote il           ON  l.cod_lote              = il.cod_lote
        INNER JOIN imobiliario.imovel i                 ON  il.inscricao_municipal  = i.inscricao_municipal
        WHERE
           i.inscricao_municipal = inIM
        GROUP BY i.inscricao_municipal
    ) as tabela_1
    WHERE
        tabela_1.inscricao_municipal = inIM;

    IF FOUND THEN
        boLog := arrecadacao.salva_log('arrecadacao.fn_confrontacao_principal',nuResultado::varchar);
    ELSE
        boLog := arrecadacao.salva_log('arrecadacao.fn_confrontacao_principal','Erro:'||nuResultado::varchar);
    END IF;

    RETURN nuResultado;
END;
$$ LANGUAGE plpgsql;
