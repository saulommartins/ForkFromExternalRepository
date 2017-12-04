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
*/

/*
$Log$
Revision 1.4  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

/*
1 - EXERCICIO

2 - CODIGO ESTRUTUTAL DAS CONTAS A SER DELETADAS

3 - CODIGO ESTRUTUTAL DAS CONTAS A SER INCLUIDAS
4 - CLASSIFICAÇÃO CONTÁBIL DAS CONTAS A SER INCLUIDAS
5 - SISTEMA CONÁBIL DAS CONTAS A SER INCLUÍDAS
6 - TIPO (ANALITICA / SINTETICAS) DAS CONTAS A SER INCLUIDAS
7 - NOME DAS CONTAS A SER INCLUIDAS

*/
CREATE OR REPLACE FUNCTION contabilidade.fn_ajusta_plano_contabil(VARCHAR, VARCHAR[], VARCHAR[], VARCHAR[], VARCHAR[], VARCHAR[], VARCHAR[]) RETURNS BOOLEAN AS '
DECLARE
    stExercicio                 ALIAS FOR $1;
    arParametros2               ALIAS FOR $2;
    arParametros3               ALIAS FOR $3;
    arParametros4               ALIAS FOR $4;
    arParametros5               ALIAS FOR $5;
    arParametros6               ALIAS FOR $6;
    arParametros7               ALIAS FOR $7;
    stParametro                 VARCHAR;
    stSql                       VARCHAR;
    inCount                     INTEGER := 1;
    inCount2                    INTEGER := 1;
    inCount3                    INTEGER := 1;
    inTam                       INTEGER := 1;
    inCodClassificacaoContabil  INTEGER;
    inCodSistemaContabil        INTEGER;
    stDescricao                 VARCHAR := '''';
    stTemp                      VARCHAR := '''';
    crCursor            REFCURSOR;
BEGIN

WHILE replace(arParametros2[inCount],'''''''','''') <> '''' LOOP
    stSql   := ''SELECT cast(cod_classificacao as varchar) from contabilidade.classificacao_plano where cod_conta || ''''-'''' || exercicio IN (select cod_conta || ''''-'''' || exercicio from contabilidade.plano_conta where cod_estrutural ilike '''''' || replace(arParametros2[inCount],'''''''','''') || ''%'''' and exercicio='''''' || stExercicio || '''''') '';
    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO stDescricao;
    CLOSE crCursor;
    IF (stDescricao = '''' OR stDescricao is null) THEN
        stTemp = arParametros2[inCount];
    ELSE
        stSql   := ''DELETE from contabilidade.classificacao_plano where cod_conta || ''''-'''' || exercicio IN (select cod_conta || ''''-'''' || exercicio from contabilidade.plano_conta where cod_estrutural ilike '''''' || replace(arParametros2[inCount],'''''''','''') || ''%'''' and exercicio='''''' || stExercicio || '''''') '';
  --      EXECUTE stSql;
    END IF;

    stSql   := ''SELECT cast(cod_plano as varchar) from contabilidade.plano_banco where cod_plano || ''''-'''' || exercicio IN (select cod_plano || ''''-'''' || exercicio from contabilidade.plano_analitica where cod_conta || ''''-'''' || exercicio IN (select cod_conta || ''''-'''' || exercicio from contabilidade.plano_conta where cod_estrutural ilike '''''' || replace(arParametros2[inCount],'''''''','''') || ''%'''' and exercicio='''''' || stExercicio || '''''')) '';
    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO stDescricao;
    CLOSE crCursor;
    IF (stDescricao = '''' OR stDescricao is null) THEN
        stTemp = arParametros2[inCount];
    ELSE
        stSql   := ''DELETE from contabilidade.plano_banco where cod_plano || ''''-'''' || exercicio IN (select cod_plano || ''''-'''' || exercicio from contabilidade.plano_analitica where cod_conta || ''''-'''' || exercicio IN (select cod_conta || ''''-'''' || exercicio from contabilidade.plano_conta where cod_estrutural ilike '''''' || replace(arParametros2[inCount],'''''''','''') || ''%'''' and exercicio='''''' || stExercicio || '''''')) '';
--        EXECUTE stSql;
    END IF;

    stSql   := ''SELECT cast(cod_plano as varchar) from contabilidade.plano_recurso where cod_plano || ''''-'''' || exercicio IN (select cod_plano || ''''-'''' || exercicio from contabilidade.plano_analitica where cod_conta || ''''-'''' || exercicio IN (select cod_conta || ''''-'''' || exercicio from contabilidade.plano_conta where cod_estrutural ilike '''''' || replace(arParametros2[inCount],'''''''','''') || ''%'''' and exercicio='''''' || stExercicio || '''''')) '';
    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO stDescricao;
    CLOSE crCursor;
    IF (stDescricao = '''' OR stDescricao is null) THEN
        stTemp = arParametros2[inCount];
    ELSE
        stSql   := ''DELETE from contabilidade.plano_recurso where cod_plano || ''''-'''' || exercicio IN (select cod_plano || ''''-'''' || exercicio from contabilidade.plano_analitica where cod_conta || ''''-'''' || exercicio IN (select cod_conta || ''''-'''' || exercicio from contabilidade.plano_conta where cod_estrutural ilike '''''' || replace(arParametros2[inCount],'''''''','''') || ''%'''' and exercicio='''''' || stExercicio || '''''')) '';
--        EXECUTE stSql;
    END IF;

    stSql   := ''SELECT cast(cod_plano as varchar) from contabilidade.plano_analitica where cod_conta || ''''-'''' || exercicio IN (select cod_conta || ''''-'''' || exercicio from contabilidade.plano_conta where cod_estrutural ilike '''''' || replace(arParametros2[inCount],'''''''','''') || ''%'''' and exercicio='''''' || stExercicio || '''''') '';
    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO stDescricao;
    CLOSE crCursor;
    IF (stDescricao = '''' OR stDescricao is null) THEN
        stTemp = arParametros2[inCount];
    ELSE
        stSql   := ''DELETE from contabilidade.plano_analitica where cod_conta || ''''-'''' || exercicio IN (select cod_conta || ''''-'''' || exercicio from contabilidade.plano_conta where cod_estrutural ilike '''''' || replace(arParametros2[inCount],'''''''','''') || ''%'''' and exercicio='''''' || stExercicio || '''''') '';
--        EXECUTE stSql;
    END IF;

    stSql   := ''SELECT cast(cod_conta as varchar) from contabilidade.plano_conta where cod_estrutural ilike '''''' || replace(arParametros2[inCount],'''''''','''') || ''%''''  and exercicio='''''' || stExercicio || '''''' '';
    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO stDescricao;
    CLOSE crCursor;
    IF (stDescricao = '''' OR stDescricao is null) THEN
        stTemp = arParametros2[inCount];
    ELSE
        stSql   := ''DELETE from contabilidade.plano_conta where cod_estrutural ilike '''''' || replace(arParametros2[inCount],'''''''','''') || ''%''''  and exercicio='''''' || stExercicio || '''''' '';
--        EXECUTE stSql;
    END IF;

    inCount := inCount + 1;
END LOOP;
inCount := inCount - 1;

inCount = 1;

WHILE replace(arParametros3[inCount],'''''''','''') <> '''' LOOP

    IF (replace(arParametros4[inCount],'''''''','''') = ''E'') THEn
        inCodClassificacaoContabil = 1;
    ELSE
        IF (replace(arParametros4[inCount],'''''''','''') = ''L'') THEn
            inCodClassificacaoContabil = 2;
        ELSE
            IF (replace(arParametros4[inCount],'''''''','''') = ''R'') THEn
                inCodClassificacaoContabil = 3;
            ELSE
                IF (replace(arParametros4[inCount],'''''''','''') = ''O'') THEn
                    inCodClassificacaoContabil = 4;
                END IF;
            END IF;
        END IF;
    END IF;

    IF (replace(arParametros5[inCount],'''''''','''') = ''P'') THEn
        inCodSistemaContabil = 2;
    ELSE
        IF (replace(arParametros5[inCount],'''''''','''') = ''F'') THEn
            inCodSistemaContabil = 1;
        ELSE
            IF (replace(arParametros5[inCount],'''''''','''') = ''O'') THEn
                inCodSistemaContabil = 3;
            ELSE
                IF (replace(arParametros5[inCount],'''''''','''') = ''C'') THEn
                    inCodSistemaContabil = 4;
                END IF;
            END IF;
        END IF;
    END IF;

    stSql   := ''SELECT cast(cod_estrutural as varchar) from contabilidade.plano_conta where cod_estrutural ilike '''''' || replace(arParametros3[inCount],'''''''','''') || ''%''''  and exercicio='''''' || stExercicio || '''''' '';
    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO stDescricao;
    CLOSE crCursor;
    IF (stDescricao = '''' OR stDescricao is null) THEN
        stSql   := ''
            INSERT INTO contabilidade.plano_conta 
                (cod_conta, exercicio, nom_conta, cod_classificacao, cod_sistema, cod_estrutural) 
            VALUES (
                (SELECT max(cod_conta) + 1 FROM contabilidade.plano_conta WHERE exercicio = '''''' || stExercicio || ''''''),
                '''''' || stExercicio || '''''', 
                '''''' || replace(arParametros7[inCount],'''''''','''') || '''''', 
                '' || inCodClassificacaoContabil || '', 
                '' || inCodSistemaContabil || '', 
                '''''' || replace(arParametros3[inCount],'''''''','''') || ''''''
            ); 
        '';
--        EXECUTE stSql;

        WHILE inCount2 <= 23 LOOP
            stSql   := ''
                INSERT INTO contabilidade.classificacao_plano 
                    (cod_classificacao, exercicio, cod_conta, cod_posicao) 
                VALUES (
                    '' || substring(replace(arParametros3[inCount],'''''''',''''), inCount2, inTam) || '',
                    '''''' || stExercicio || '''''', 
                    (SELECT max(cod_conta) FROM contabilidade.plano_conta WHERE exercicio = '''''' || stExercicio || ''''''),
                    '' || inCount3 || ''
                ); 
            '';
--            EXECUTE stSql;

            IF (inCount3<6)THEN
                inCount2 := inCount2 + 2;
                IF(inCount3>4) THEN
                    inTam := 2;
                END IF;
            ELSE
                inCount2 := inCount2 + 3;
                inTam := 2;
            END IF;
            inCount3 := inCount3 + 1;
        END LOOP;

        IF (replace(arParametros6[inCount],'''''''','''') = ''A'') THEN
            stSql   := ''
                INSERT INTO contabilidade.plano_analitica
                    (cod_plano, exercicio, cod_conta) 
                VALUES (
                    (SELECT max(cod_conta) + 1 FROM contabilidade.plano_analitica WHERE exercicio = '''''' || stExercicio || ''''''),
                    '''''' || stExercicio || '''''',
                    (SELECT max(cod_conta) FROM contabilidade.plano_conta WHERE exercicio = '''''' || stExercicio || '''''')
                );
            '';
  --      EXECUTE stSql;
        END IF;

    ELSE
        stTemp = arParametros3[inCount];
    END IF;

inCount := inCount + 1;

END LOOP;

inCount := inCount - 1;


RETURN true;

END;
'LANGUAGE 'plpgsql';

