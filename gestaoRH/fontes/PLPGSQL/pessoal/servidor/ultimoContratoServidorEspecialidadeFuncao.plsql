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
* ultimo_contrato_servidor_especialidade_funcao
* Data de Criação   : 29/06/2009


* @author Analista      Dagiane
* @author Desenvolvedor Rafael Garbin

* @package URBEM
* @subpackage 

* @ignore # 

$Id:$
*/

CREATE OR REPLACE FUNCTION ultimo_contrato_servidor_especialidade_funcao(VARCHAR, INTEGER) RETURNS SETOF colunasUltimoContratoServidorEspecialidadeFuncao AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    inCodPeriodoMovimentacao        ALIAS FOR $2;
    stSql                           VARCHAR;
    rwEspecialidadeFuncao           colunasUltimoContratoServidorEspecialidadeFuncao%ROWTYPE;
    stTimestampFechamentoPeriodo    VARCHAR;
    reRegistro                      RECORD;
BEGIN
    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidade);

    stSql := '    SELECT contrato_servidor_especialidade_funcao.cod_contrato
                       , contrato_servidor_especialidade_funcao.cod_especialidade as cod_especialidade_funcao
                    FROM pessoal'|| stEntidade ||'.contrato_servidor_especialidade_funcao
              INNER JOIN (  SELECT contrato_servidor_especialidade_funcao.cod_contrato
                                 , max(contrato_servidor_especialidade_funcao.timestamp) as timestamp
                              FROM pessoal'|| stEntidade ||'.contrato_servidor_especialidade_funcao
                             WHERE contrato_servidor_especialidade_funcao.timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                          GROUP BY contrato_servidor_especialidade_funcao.cod_contrato) as max_contrato_servidor_especialidade_funcao
                      ON max_contrato_servidor_especialidade_funcao.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato
                     AND max_contrato_servidor_especialidade_funcao.timestamp = contrato_servidor_especialidade_funcao.timestamp';

    FOR reRegistro IN EXECUTE stSql LOOP
        rwEspecialidadeFuncao.cod_contrato              := reRegistro.cod_contrato;
        rwEspecialidadeFuncao.cod_especialidade_funcao  := reRegistro.cod_especialidade_funcao;
        
        RETURN NEXT rwEspecialidadeFuncao;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';
