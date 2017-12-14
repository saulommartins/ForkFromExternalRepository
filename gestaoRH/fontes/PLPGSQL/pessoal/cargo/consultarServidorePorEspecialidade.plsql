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
 * PL para retorno os servidores cadastrados para a sequencia REGIME/SUBDIVISÃO/ESPECIALIDADE
 * Data de Criação   : 05/11/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza
 
 * @package URBEM
 * @subpackage 

 * @ignore # só use se FOR paginas que o cliente visualiza, se FOR mapeamento ou classe de negocio não se usa

 $Id:$
 */
CREATE OR REPLACE FUNCTION consultarServidoresPorEspecialidade(integer,integer,integer,varchar) RETURNS SETOF colunasConsultarServidoresPorCargo AS $$
DECLARE
    inCodRegime         ALIAS FOR $1;
    inCodSubDivisao     ALIAS FOR $2;
    inCodEspecialidade  ALIAS FOR $3;
    stEntidade          ALIAS FOR $4;
    stSql               VARCHAR;
    reRegistro          RECORD;
    reServidor          RECORD;
    inContador          INTEGER:=1;
    rwDescricao         colunasConsultarServidoresPorCargo%ROWTYPE;
BEGIN
    stSql := 'SELECT (SELECT registro FROM pessoal'|| stEntidade ||'.contrato WHERE cod_contrato = contrato_servidor.cod_contrato) as registro
                   , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = servidor.numcgm) as nom_cgm
                   , servidor.numcgm
                FROM pessoal'|| stEntidade ||'.contrato_servidor
                JOIN pessoal'|| stEntidade ||'.servidor_contrato_servidor
                  ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato
                JOIN pessoal'|| stEntidade ||'.servidor
                  ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor

           LEFT JOIN pessoal'|| stEntidade ||'.contrato_servidor_especialidade_cargo
                  ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato

           LEFT JOIN pessoal'|| stEntidade ||'.contrato_servidor_especialidade_funcao
                  ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato
                JOIN (  SELECT cod_contrato
                             , max(timestamp) as timestamp
                          FROM pessoal'|| stEntidade ||'.contrato_servidor_especialidade_funcao
                      GROUP BY cod_contrato) as max_contrato_servidor_especialidade_funcao
                  ON contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato
                 AND contrato_servidor_especialidade_funcao.timestamp = max_contrato_servidor_especialidade_funcao.timestamp
                JOIN pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                  ON contrato_servidor.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
                JOIN (  SELECT cod_contrato
                             , max(timestamp) as timestamp
                          FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                      GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                  ON contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato
                 AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp

                JOIN pessoal'|| stEntidade ||'.contrato_servidor_regime_funcao
                  ON contrato_servidor.cod_contrato = contrato_servidor_regime_funcao.cod_contrato
                JOIN (  SELECT cod_contrato
                             , max(timestamp) as timestamp
                          FROM pessoal'|| stEntidade ||'.contrato_servidor_regime_funcao
                      GROUP BY cod_contrato) as max_contrato_servidor_regime_funcao
                  ON contrato_servidor_regime_funcao.cod_contrato = max_contrato_servidor_regime_funcao.cod_contrato
                 AND contrato_servidor_regime_funcao.timestamp = max_contrato_servidor_regime_funcao.timestamp
                   
               WHERE ((    contrato_servidor_especialidade_cargo.cod_especialidade = '|| inCodEspecialidade ||'
                      AND contrato_servidor.cod_regime = '|| inCodRegime ||'
                      AND contrato_servidor.cod_sub_divisao = '|| inCodSubDivisao ||')
                  or (    contrato_servidor_especialidade_funcao.cod_especialidade = '|| inCodEspecialidade ||'
                      AND contrato_servidor_regime_funcao.cod_regime = '|| inCodRegime ||'
                      AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao = '|| inCodSubDivisao ||'))
                  AND recuperarSituacaoDoContrato(contrato_servidor.cod_contrato, 0, '|| quote_literal(stEntidade) ||') not IN (''R'',''P'')
             ORDER BY nom_cgm';

    FOR reServidor IN EXECUTE stSql LOOP
        rwDescricao.numcgm   := reServidor.numcgm;
        rwDescricao.nom_cgm  := reServidor.nom_cgm;
        rwDescricao.registro := reServidor.registro;
        RETURN NEXT rwDescricao;
        inContador := inContador + 1;
    END LOOP;        
END;
$$ language 'plpgsql';
