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
 * PL recuperaDadosCertidaoTempoServidoCompleta
 * Data de Criação   : 16/09/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Rafael Gabin
 
 * @package URBEM
 * @subpackage 

 $Id:$ 
 */

CREATE OR REPLACE FUNCTION recuperaDadosCertidaoModeloINSS(INTEGER, VARCHAR, DATE, DATE) RETURNS SETOF colunasDadosCertidaoModeloINSS AS $$
DECLARE
    inCodContrato       ALIAS FOR $1;
    stEntidade          ALIAS FOR $2;
    dtPeriodoInicial    ALIAS FOR $3;
    dtPeriodoFinal      ALIAS FOR $4;
    stSql               VARCHAR;
    stExercicio         VARCHAR;
    reRegistro          RECORD;
    arPeriodos          VARCHAR[]:='{}';
    arPeriodo           VARCHAR[]:='{}';
    arPeriodoProximo    VARCHAR[]:='{}';
    inContador          INTEGER:=0;
    inIndex             INTEGER:=0;
    rwRetorno           colunasDadosCertidaoModeloINSS%ROWTYPE;
BEGIN
    SELECT max(valor) as exercicio
      into stExercicio
      FROM administracao.configuracao 
     WHERE parametro = 'ano_exercicio';

    stSql := '     SELECT (SELECT descricao FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = contrato_servidor_funcao.cod_cargo) as funcao
                        , to_char(contrato_servidor_funcao.vigencia, ''dd/mm/yyyy'') as periodo_inicial
                        , recuperaDescricaoOrgao(contrato_servidor_orgao.cod_orgao,('|| quote_literal(stExercicio ||'-01-01') ||')::date) as lotacao
                     FROM pessoal'|| stEntidade ||'.contrato_servidor_funcao
               INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor_orgao
                       ON contrato_servidor_funcao.cod_contrato = contrato_servidor_orgao.cod_contrato
               INNER JOIN organograma.orgao
                       ON contrato_servidor_orgao.cod_orgao = orgao.cod_orgao
               INNER JOIN (  SELECT cod_contrato
                                  , cod_cargo
                                  , MAX(timestamp) as timestamp
                              FROM pessoal'|| stEntidade ||'.contrato_servidor_funcao
                          GROUP BY cod_contrato
                                 , cod_cargo) as max_contrato_servidor_funcao
                       ON contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato
                      AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp
                      AND contrato_servidor_funcao.cod_cargo = max_contrato_servidor_funcao.cod_cargo
                    WHERE contrato_servidor_funcao.cod_contrato = '|| inCodContrato ||'
                 ORDER BY contrato_servidor_funcao.vigencia;';

    inContador:=1;
    FOR reRegistro IN  EXECUTE stSql LOOP
        arPeriodos[inContador]:= reRegistro.periodo_inicial ||'#'|| reRegistro.funcao ||'#'|| reRegistro.lotacao;
        inContador := inContador + 1;
    END LOOP;

    inContador:= inContador-1;
    FOR inIndex IN 1 .. inContador LOOP
        arPeriodo := string_to_array(arPeriodos[inIndex], '#');        
        rwRetorno.periodo_inicial := arPeriodo[1];
        rwRetorno.funcao          := arPeriodo[2];
        rwRetorno.lotacao         := arPeriodo[3];

        arPeriodoProximo := string_to_array(arPeriodos[inIndex+1], '#');
        IF arPeriodo[1] = arPeriodoProximo[1] THEN
            rwRetorno.periodo_final   := arPeriodo[1];
        ELSE
            IF arPeriodoProximo[1] IS NOT NULL THEN
                stSql := 'SELECT to_char(to_date('|| quote_literal(arPeriodoProximo[1]) ||',''dd/mm/yyyy'')- 1,''dd/mm/yyyy'')';
            ELSE
                stSql := 'SELECT to_char('|| quote_literal(dtPeriodoFinal) ||'::date,''dd/mm/yyyy'')';
            END IF;
            rwRetorno.periodo_final   := selectIntoVarchar(stSql);
        END IF;

        RETURN NEXT rwRetorno;
    END LOOP;
END;
$$ language 'plpgsql';
