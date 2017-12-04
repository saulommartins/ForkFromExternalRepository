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
* $Id: buscaVinculoLancamento.plsql 30260 2008-06-10 13:51:53Z cercato $
*
* Caso de uso: uc-5.3.19
* Caso de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.4  2007/03/06 18:43:53  dibueno
Exibição da descrição do Credito/Grupo no relatorio

Revision 1.3  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.buscaVinculoLancamentoSemExercicio( INTEGER )  RETURNS VARCHAR AS '
DECLARE
    inCodLancamento ALIAS FOR $1;
    inExercicio     integer;
    inCalculo       integer;
    inGrupo         integer;
    stDesc          varchar;
    stDivida        varchar;

BEGIN

    SELECT
        max(cod_calculo)             
    INTO
        inCalculo
    FROM 
        arrecadacao.lancamento_calculo
    WHERE 
        cod_lancamento = inCodLancamento; 

    SELECT 
        cod_grupo,
        ano_exercicio
    INTO 
        inGrupo,
        inExercicio
    FROM 
        arrecadacao.calculo_grupo_credito
    WHERE 
        cod_calculo = inCalculo;

    if ( inGrupo is not null )  then
        select  cod_grupo||'' / ''||ano_exercicio||'' - ''||descricao
        into    stDesc 
        from    arrecadacao.grupo_credito
        where   cod_grupo = inGrupo 
        and ano_exercicio = inExercicio::varchar;
    else
        select  descricao_credito
        into    stDesc
        from    monetario.credito
        where   (cod_credito,cod_especie,cod_genero, cod_natureza)
        in      ( select    cod_credito,cod_especie,cod_genero, cod_natureza 
                  from      arrecadacao.calculo 
                  where     cod_calculo = inCalculo);
    end if;
    
    select
        cod_calculo
    into
        inGrupo
    from
        divida.parcela_calculo
    where
        parcela_calculo.cod_calculo = inCalculo;

    IF ( inGrupo IS NOT NULL ) THEN
        stDesc := ''D.A. ''||stDesc;
    END IF;
  
    return stDesc; 
END;
' LANGUAGE 'plpgsql';
