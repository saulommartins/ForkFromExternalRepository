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
/* organograma.fn_popula_de_para_orgaos
 *
 * Data de Criação : 19/04/2009


 * @author Analista : Gelson Wolowski Gonçalves
 * @author Desenvolvedor : Fábio Bertoldi

 * @package URBEM
 * @subpackage

 * $Id:  $
 */
CREATE OR REPLACE FUNCTION organograma.fn_popula_de_para_orgaos( ) RETURNS          INTEGER AS $$
DECLARE

    stSqlEntidade   VARCHAR;
    reRecord        RECORD;

    inCountExec     INTEGER := 0;
    inCountTotal    INTEGER := 0;
    
BEGIN

    SELECT organograma.fn_insere_de_para_orgaos_existentes('administracao.comunicado'                         ) INTO inCountExec;
    inCountTotal := inCountTotal + inCountExec;
    SELECT organograma.fn_insere_de_para_orgaos_existentes('administracao.usuario'                            ) INTO inCountExec;
    inCountTotal := inCountTotal + inCountExec;
    SELECT organograma.fn_insere_de_para_orgaos_existentes('administracao.impressora'                         ) INTO inCountExec;
    inCountTotal := inCountTotal + inCountExec;
    SELECT organograma.fn_insere_de_para_orgaos_existentes('frota.terceiros_historico'                        ) INTO inCountExec;
    inCountTotal := inCountTotal + inCountExec;
    SELECT organograma.fn_insere_de_para_orgaos_existentes('patrimonio.historico_bem'                         ) INTO inCountExec;
    inCountTotal := inCountTotal + inCountExec;
    SELECT organograma.fn_insere_de_para_orgaos_existentes('estagio.estagiario_estagio'                       ) INTO inCountExec;
    inCountTotal := inCountTotal + inCountExec;
    SELECT organograma.fn_insere_de_para_orgaos_existentes('folhapagamento.configuracao_empenho_lotacao'      ) INTO inCountExec;
    inCountTotal := inCountTotal + inCountExec;
    SELECT organograma.fn_insere_de_para_orgaos_existentes('folhapagamento.configuracao_empenho_lla_lotacao'  ) INTO inCountExec;
    inCountTotal := inCountTotal + inCountExec;
    SELECT organograma.fn_insere_de_para_orgaos_existentes('ima.configuracao_banpara_orgao'                   ) INTO inCountExec;
    inCountTotal := inCountTotal + inCountExec;
    SELECT organograma.fn_insere_de_para_orgaos_existentes('ima.configuracao_bb_orgao'                        ) INTO inCountExec;
    inCountTotal := inCountTotal + inCountExec;
    SELECT organograma.fn_insere_de_para_orgaos_existentes('ima.configuracao_besc_orgao'                      ) INTO inCountExec;
    inCountTotal := inCountTotal + inCountExec;
    SELECT organograma.fn_insere_de_para_orgaos_existentes('ima.configuracao_banrisul_orgao'                  ) INTO inCountExec;
    inCountTotal := inCountTotal + inCountExec;
    SELECT organograma.fn_insere_de_para_orgaos_existentes('pessoal.contrato_pensionista_orgao'               ) INTO inCountExec;
    inCountTotal := inCountTotal + inCountExec;
    SELECT organograma.fn_insere_de_para_orgaos_existentes('pessoal.contrato_servidor_orgao'                  ) INTO inCountExec;
    inCountTotal := inCountTotal + inCountExec;
    SELECT organograma.fn_insere_de_para_orgaos_existentes('ponto.configuracao_lotacao'                       ) INTO inCountExec;
    inCountTotal := inCountTotal + inCountExec;
    SELECT organograma.fn_insere_de_para_orgaos_existentes('sw_andamento'                                     ) INTO inCountExec;
    inCountTotal := inCountTotal + inCountExec;

    stSqlEntidade := '  SELECT DISTINCT cod_entidade
                          FROM administracao.entidade_rh
                         WHERE cod_entidade <> ( SELECT CAST(valor as integer)
                                                   FROM administracao.configuracao
                                                  WHERE exercicio = (
                                                                      SELECT MAX(exercicio)
                                                                        FROM administracao.configuracao
                                                                       WHERE parametro = ''migra_orgao''
                                                                    )
                                                    AND parametro = ''cod_entidade_prefeitura''
                                               )
                             ;
                     ';

    FOR reRecord IN EXECUTE stSqlEntidade LOOP

        SELECT organograma.fn_insere_de_para_orgaos_existentes('estagio_'       || reRecord.cod_entidade ||'.estagiario_estagio'              ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('folhapagamento_'|| reRecord.cod_entidade ||'.configuracao_empenho_lotacao'    ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('folhapagamento_'|| reRecord.cod_entidade ||'.configuracao_empenho_lla_lotacao') INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('ima_'           || reRecord.cod_entidade ||'.configuracao_banpara_orgao'      ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('ima_'           || reRecord.cod_entidade ||'.configuracao_bb_orgao'           ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('ima_'           || reRecord.cod_entidade ||'.configuracao_besc_orgao'         ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('ima_'           || reRecord.cod_entidade ||'.configuracao_banrisul_orgao'     ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('pessoal_'       || reRecord.cod_entidade ||'.contrato_pensionista_orgao'      ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('pessoal_'       || reRecord.cod_entidade ||'.contrato_servidor_orgao'         ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('ponto_'         || reRecord.cod_entidade ||'.configuracao_lotacao'            ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;

    END LOOP;

    RETURN inCountTotal;

END;
$$ LANGUAGE 'plpgsql';
