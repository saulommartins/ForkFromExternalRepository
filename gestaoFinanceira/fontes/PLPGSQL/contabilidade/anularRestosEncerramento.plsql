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
* $Revision: 1.6 $
* $Name:  $
* $Author: cleisson $
* $Date: 2006/07/05 20:37:31 $
*
* Casos de uso: uc-02.02.02
*/

/*
$Log: consultaClassPlano.plsql,v $
Revision 1.6  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION contabilidade.fn_anular_restos_encerramento(VARCHAR) RETURNS INTEGER AS '

DECLARE
    reRecord            RECORD;
    stOut               VARCHAR := '''';
    stSql               VARCHAR := '''';
    stExercicio         ALIAS FOR $1;
    inLotes             INTEGER := 0;

BEGIN

    stSql := ''
        SELECT *
        FROM
             contabilidade.lote as lo
        WHERE  
             exercicio   = ''''''|| stExercicio ||''''''
        AND  nom_lote ilike ''''ENCERRAMENTO DO EXERCÍCIO''''
        AND  tipo = ''''M''''
        '';

    FOR reRecord IN EXECUTE stSql LOOP

        delete from contabilidade.conta_debito      where cod_lote = reRecord.cod_lote and exercicio=''''||stExercicio||'''' and tipo=''M'' and cod_entidade=reRecord.cod_entidade;
        delete from contabilidade.conta_credito     where cod_lote = reRecord.cod_lote and exercicio=''''||stExercicio||'''' and tipo=''M'' and cod_entidade=reRecord.cod_entidade;
        delete from contabilidade.valor_lancamento  where cod_lote = reRecord.cod_lote and exercicio=''''||stExercicio||'''' and tipo=''M'' and cod_entidade=reRecord.cod_entidade;
        delete from contabilidade.lancamento        where cod_lote = reRecord.cod_lote and exercicio=''''||stExercicio||'''' and tipo=''M'' and cod_entidade=reRecord.cod_entidade;
        delete from contabilidade.lote              where cod_lote = reRecord.cod_lote and exercicio=''''||stExercicio||'''' and tipo=''M'' and cod_entidade=reRecord.cod_entidade;

        inLotes := inLotes + 1;

    END LOOP;

    UPDATE administracao.configuracao set valor = ''F'' WHERE parametro = ''virada_GF'' and exercicio = ''''||stExercicio||''''; 

    RETURN inLotes;

END;
'LANGUAGE 'plpgsql';

------------ NOVA VERSAO 2013

CREATE OR REPLACE FUNCTION contabilidade.fn_anular_restos_encerramento(VARCHAR,INTEGER) RETURNS INTEGER AS '

DECLARE
    reRecord            RECORD;
    stOut               VARCHAR := '''';
    stSql               VARCHAR := '''';
    stExercicio         ALIAS FOR $1;
    inCodEntidade       ALIAS FOR $2;
    inLotes             INTEGER := 0;

BEGIN

    stSql := ''
        SELECT *
        FROM
             contabilidade.lote as lo
        WHERE  
             exercicio   = ''''''|| stExercicio ||''''''
        AND  nom_lote ilike ''''ENCERRAMENTO DO EXERCÍCIO''''
        AND  tipo = ''''M''''
        AND  cod_entidade = '' || inCodEntidade || ''
        '';

    FOR reRecord IN EXECUTE stSql LOOP

        delete from contabilidade.conta_debito      where cod_lote = reRecord.cod_lote and exercicio=''''||stExercicio||'''' and tipo=''M'' and cod_entidade=reRecord.cod_entidade;
        delete from contabilidade.conta_credito     where cod_lote = reRecord.cod_lote and exercicio=''''||stExercicio||'''' and tipo=''M'' and cod_entidade=reRecord.cod_entidade;
        delete from contabilidade.valor_lancamento  where cod_lote = reRecord.cod_lote and exercicio=''''||stExercicio||'''' and tipo=''M'' and cod_entidade=reRecord.cod_entidade;
        delete from contabilidade.lancamento        where cod_lote = reRecord.cod_lote and exercicio=''''||stExercicio||'''' and tipo=''M'' and cod_entidade=reRecord.cod_entidade;
        delete from contabilidade.lote              where cod_lote = reRecord.cod_lote and exercicio=''''||stExercicio||'''' and tipo=''M'' and cod_entidade=reRecord.cod_entidade;

        inLotes := inLotes + 1;

    END LOOP;

    UPDATE administracao.configuracao set valor = ''F'' WHERE parametro = ''virada_GF'' and exercicio = ''''||stExercicio||''''; 

    RETURN inLotes;

END;
'LANGUAGE 'plpgsql';
