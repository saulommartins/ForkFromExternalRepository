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
* Valor de um lancamento!
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: buscaLancamentosIM.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.6  2007/03/19 20:27:17  dibueno
Bug #8416#

Revision 1.5  2007/02/23 14:40:37  dibueno
Bug #8416#

Revision 1.4  2007/02/22 15:01:06  dibueno
Bug #8416#

Revision 1.3  2007/02/21 19:49:02  dibueno
Bug #8416#

Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.buscaLancamentosIM( VARCHAR, VARCHAR, VARCHAR, VARCHAR, int ) returns varchar as '
declare
    inInscricao     ALIAS FOR $1;
    inInscricao2    ALIAS FOR $2;
    inCodGrupo      ALIAS FOR $3;
    inCodGrupo2     ALIAS FOR $4;
    inExercicio     ALIAS FOR $5;
    stRetorno       varchar := '''';
    stAuxiliar      varchar := '''';
    stAuxiliarGrupo varchar := '''';
    reRecord        RECORD;
    inInscricaoA    VARCHAR;
    inInscricaoB    VARCHAR;
    inCodGrupoA     VARCHAR;
    inCodGrupoB     VARCHAR;
    stSql           varchar := '''';
    boPrimeiro      BOOLEAN;
BEGIN


    IF inInscricao = ''lista'' THEN
        stAuxiliar := '' ic.inscricao_municipal in (''|| inInscricao2 ||'')'';

    ELSE

        IF inInscricao2 = ''0'' THEN
            inInscricaoB := inInscricao;
        ELSE

            IF inInscricao = ''0'' THEN
                inInscricaoA := inInscricao2;
            ELSE
                inInscricaoA := inInscricao;
            END IF;

            inInscricaoB := inInscricao2;
        END IF;
        stAuxiliar := ''( ic.inscricao_municipal between ''||inInscricaoA||'' and ''||inInscricaoB||'' )'';
    END IF;

    -- MONTAGEM DO FILTRO DE GRUPOS

    IF ( inCodGrupo != '''' ) OR ( inCodGrupo2 != '''' ) THEN
        IF inCodGrupo = ''lista'' THEN
            stAuxiliarGrupo := '' acgc.cod_grupo in (''|| inCodGrupo2 ||'')'';

        ELSE

            IF ( inCodGrupo2 = '''' ) THEN
                inCodGrupoB := inCodGrupo;
                inCodGrupoA := inCodGrupo;
            ELSE

                IF inCodGrupo = '''' THEN
                    inCodGrupoA := inCodGrupo2;
                ELSE
                    inCodGrupoA := inCodGrupo;
                END IF;

                inCodGrupoB := inCodGrupo2;
            END IF;

            stAuxiliarGrupo := ''( acgc.cod_grupo between ''||inCodGrupoA||'' and ''||inCodGrupoB||'' )'';

        END IF;
    END IF;

    IF stAuxiliar != '''' and stAuxiliarGrupo != '''' THEN
        stAuxiliar := stAuxiliar || '' AND '' || stAuxiliarGrupo;
    ELSE
        stAuxiliar := stAuxiliar || stAuxiliarGrupo;
    END IF;


    stSql := ''
        select distinct
            alc.cod_lancamento
        from
            arrecadacao.imovel_calculo ic
            inner join arrecadacao.calculo ac
            on ac.cod_calculo=ic.cod_calculo
            inner join arrecadacao.lancamento_calculo alc
            on alc.cod_calculo=ac.cod_calculo
            INNER JOIN arrecadacao.calculo_grupo_credito as acgc
            ON acgc.cod_calculo = ic.cod_calculo
            AND acgc.ano_exercicio = ac.exercicio
        where
            ''|| stAuxiliar ||''
            and ac.exercicio = ''|| inExercicio
    ;


    boPrimeiro := true;

    FOR reRecord IN EXECUTE stSql LOOP
        IF ( boPrimeiro ) THEN
            stRetorno := reRecord.cod_lancamento;
        ELSE
            stRetorno := stRetorno||'', ''||reRecord.cod_lancamento;
        END IF;
        boPrimeiro := false;
    END LOOP;

    --return substring(stRetorno from 1 for char_length(stRetorno)-1);
    return stRetorno;
end;
'language 'plpgsql';
