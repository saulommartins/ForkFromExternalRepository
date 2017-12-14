<?php
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
?>
<?php
/**
    * Arquivo de mapeamento para a função que busca os dados  de despesa do pessoal
    * Data de Criação   : 22/01/2009

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Lucas Andrades Mendes

    * @package URBEM
    * @subpackage

    $Id: FTCEMGDespesaTotalPessoalPE.class.php 63722 2015-10-01 21:07:31Z arthur $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGDespesaTotalPessoalPE extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();

    $this->setTabela('tcemg.siace_despesa_total_pessoal');

    $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_entidade'  ,'varchar',false,''    ,false,false);    
    $this->AddCampo('mes'           ,'varchar',false,''    ,false,false);
    $this->AddCampo('data_inicial'  ,'varchar',false,''    ,false,false);
    $this->AddCampo('data_final'    ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_estrutural','varchar',false,''    ,false,false);
    $this->AddCampo('nivel'         ,'integer',false,''    ,false,false);
    $this->AddCampo('tipo_situacao' ,'varchar',false,''    ,false,false);
}

function montaRecuperaTodos()
{
  $stSql = "
            SELECT ".$this->getDado('mes')." AS mes
                  ,(
                    (
                    SELECT SUM(COALESCE(venc1.valor,0.00))
                      FROM (
                            --Calculando os valores referente a Vencimentos e vantagens*/
                            -- Calculando os valores referente as contas 3.1.90.04.00.00 + 3.1.90.11.00.00 + 3.1.90.16.00.00 + 3.1.90.94.00.00
                            SELECT valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                ,'".$this->getDado('data_final')."'
                                                                                ,'".$this->getDado('exercicio')."'
                                                                                ,'".$this->getDado('cod_entidade')."'
                                                                                ,'3.3.1.9.0.04'
                                                                                ,6
                                                                                ,'liquidado'
                                                                              )
                            UNION
                            SELECT valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                ,'".$this->getDado('data_final')."'
                                                                                ,'".$this->getDado('exercicio')."'
                                                                                ,'".$this->getDado('cod_entidade')."'
                                                                                ,'3.3.1.9.0.11'
                                                                                ,6
                                                                                ,'liquidado'
                                                                              )
                            UNION
                            SELECT valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                ,'".$this->getDado('data_final')."'
                                                                                ,'".$this->getDado('exercicio')."'
                                                                                ,'".$this->getDado('cod_entidade')."'
                                                                                ,'3.3.1.9.0.16'
                                                                                ,6
                                                                                ,'liquidado'
                                                                              )
                            UNION
                            SELECT valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                ,'".$this->getDado('data_final')."'
                                                                                ,'".$this->getDado('exercicio')."'
                                                                                ,'".$this->getDado('cod_entidade')."'
                                                                                ,'3.3.1.9.0.94'
                                                                                ,6
                                                                                ,'liquidado'
                                                                              )
                          ) AS venc1
                  )
                    -
                  (
                  SELECT SUM(COALESCE(venc2.valor,0.00))
                    FROM (
                          -- Calculando os valores referente as contas 3.1.90.11.07, 3.1.90.11.08, 3.1.90.11.09
                          SELECT valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                ,'".$this->getDado('data_final')."'
                                                                                ,'".$this->getDado('exercicio')."'
                                                                                ,'".$this->getDado('cod_entidade')."'
                                                                                ,'3.3.1.9.0.11.07'
                                                                                ,7
                                                                                ,'liquidado'
                                                                              )
                         UNION
                         SELECT valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                ,'".$this->getDado('data_final')."'
                                                                                ,'".$this->getDado('exercicio')."'
                                                                                ,'".$this->getDado('cod_entidade')."'
                                                                                ,'3.3.1.9.0.11.08'
                                                                                ,7
                                                                                ,'liquidado'
                                                                              )
                         UNION
                         SELECT valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                ,'".$this->getDado('data_final')."'
                                                                                ,'".$this->getDado('exercicio')."'
                                                                                ,'".$this->getDado('cod_entidade')."'
                                                                                ,'3.3.1.9.0.11.09'
                                                                                ,7
                                                                                ,'liquidado'
                                                                              )
                        ) AS venc2
                    )
                  ) AS venc_vantagens
                , (
                    SELECT SUM(COALESCE(valor,0.00)) AS valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                                    ,'".$this->getDado('data_final')."'
                                                                                                    ,'".$this->getDado('exercicio')."'
                                                                                                    ,'".$this->getDado('cod_entidade')."'
                                                                                                    ,'3.3.1.9.0.01'
                                                                                                    ,6
                                                                                                    ,'liquidado'
                                                                                                  )
                  ) AS inativos
                , (
                    SELECT SUM(COALESCE(valor,0.00)) AS valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                                    ,'".$this->getDado('data_final')."'
                                                                                                    ,'".$this->getDado('exercicio')."'
                                                                                                    ,'".$this->getDado('cod_entidade')."'
                                                                                                    ,'3.3.1.9.0.03'
                                                                                                    ,6
                                                                                                    ,'liquidado'
                                                                                                  )
                  ) AS pensionistas
                , (
                    SELECT SUM(COALESCE(valor,0.00)) AS valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                                    ,'".$this->getDado('data_final')."'
                                                                                                    ,'".$this->getDado('exercicio')."'
                                                                                                    ,'".$this->getDado('cod_entidade')."'
                                                                                                    ,'3.3.1.9.0.05'
                                                                                                    ,6
                                                                                                    ,'liquidado'
                                                                                                  )
                  ) AS salario_familia
                , (
                    SELECT SUM(COALESCE(valor,0.00)) AS valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                                    ,'".$this->getDado('data_final')."'
                                                                                                    ,'".$this->getDado('exercicio')."'
                                                                                                    ,'".$this->getDado('cod_entidade')."'
                                                                                                    ,'3.3.1.9.0.11.07'
                                                                                                    ,7
                                                                                                    ,'liquidado'
                                                                                                  )
                  ) AS sub_prefeito
                , (
                    SELECT SUM(COALESCE(valor,0.00)) AS valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                                    ,'".$this->getDado('data_final')."'
                                                                                                    ,'".$this->getDado('exercicio')."'
                                                                                                    ,'".$this->getDado('cod_entidade')."'
                                                                                                    ,'3.3.1.9.0.11.08'
                                                                                                    ,7
                                                                                                    ,'liquidado'
                                                                                                  )
                  ) AS sub_vicepref
                , (
                    SELECT SUM(COALESCE(valor,0.00)) AS valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                                    ,'".$this->getDado('data_final')."'
                                                                                                    ,'".$this->getDado('exercicio')."'
                                                                                                    ,'".$this->getDado('cod_entidade')."'
                                                                                                    ,'3.3.1.9.0.11.09'
                                                                                                    ,7
                                                                                                    ,'liquidado'
                                                                                                  )
                  ) AS sub_secretarios
                , (
                    SELECT SUM(COALESCE(obp.valor,0.00))
                      FROM (
                            SELECT valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                ,'".$this->getDado('data_final')."'
                                                                                ,'".$this->getDado('exercicio')."'
                                                                                ,'".$this->getDado('cod_entidade')."'
                                                                                ,'3.3.1.9.0.13.03'
                                                                                ,7
                                                                                ,'liquidado'
                                                                              )
                            UNION
                            SELECT valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                ,'".$this->getDado('data_final')."'
                                                                                ,'".$this->getDado('exercicio')."'
                                                                                ,'".$this->getDado('cod_entidade')."'
                                                                                ,'3.3.1.9.1.13.99'
                                                                                ,7
                                                                                ,'liquidado'
                                                                              )
                            UNION
                            SELECT valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                ,'".$this->getDado('data_final')."'
                                                                                ,'".$this->getDado('exercicio')."'
                                                                                ,'".$this->getDado('cod_entidade')."'
                                                                                ,'3.3.1.9.0.13.99'
                                                                                ,7
                                                                                ,'liquidado'
                                                                              )
                          ) AS obp
                  ) AS obrig_patronais
                , (
                    SELECT SUM(COALESCE(rep.valor,0.00))
                      FROM (
                            SELECT valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                ,'".$this->getDado('data_final')."'
                                                                                ,'".$this->getDado('exercicio')."'
                                                                                ,'".$this->getDado('cod_entidade')."'
                                                                                ,'3.3.1.9.0.13.02'
                                                                                ,7
                                                                                ,'liquidado'
                                                                              )
                            UNION
                            SELECT valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                ,'".$this->getDado('data_final')."'
                                                                                ,'".$this->getDado('exercicio')."'
                                                                                ,'".$this->getDado('cod_entidade')."'
                                                                                ,'3.3.1.9.1.13.02'
                                                                                ,7
                                                                                ,'liquidado'
                                                                              )
                            ) AS rep
                  ) AS repasse_patronal
                , (
                    SELECT SUM(COALESCE(valor,0.00)) AS valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                                    ,'".$this->getDado('data_final')."'
                                                                                                    ,'".$this->getDado('exercicio')."'
                                                                                                    ,'".$this->getDado('cod_entidade')."'
                                                                                                    ,'3.3.1.9.0.91'
                                                                                                    ,6
                                                                                                    ,'liquidado'
                                                                                                  )
                  ) AS sent_jud_pessoal
                , (
                    SELECT SUM(COALESCE(valor,0.00)) AS valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                                    ,'".$this->getDado('data_final')."'
                                                                                                    ,'".$this->getDado('exercicio')."'
                                                                                                    ,'".$this->getDado('cod_entidade')."'
                                                                                                    ,'3.3.1.7.1.70'
                                                                                                    ,6
                                                                                                    ,'liquidado'
                                                                                                  )
                  ) AS outras_despesas_pessoal
                , (
                    SELECT SUM(COALESCE(valor,0.00)) AS valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                                    ,'".$this->getDado('data_final')."'
                                                                                                    ,'".$this->getDado('exercicio')."'
                                                                                                    ,'".$this->getDado('cod_entidade')."'
                                                                                                    ,'3.3.1.9.0.92'
                                                                                                    ,6
                                                                                                    ,'liquidado'
                                                                                                  )
                  ) AS desp_exercicios_ant
                , (
                    SELECT SUM(COALESCE(valor,0.00)) AS valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                                    ,'".$this->getDado('data_final')."'
                                                                                                    ,'".$this->getDado('exercicio')."'
                                                                                                    ,'".$this->getDado('cod_entidade')."'
                                                                                                    ,'3.3.1.9.0.94'
                                                                                                    ,6
                                                                                                    ,'liquidado'
                                                                                                  )
                  ) AS indenizacao_demissao
                , 0.00 AS inc_demissao_volunt
                , 0.00 AS sent_judiciais_ant
                , 0.00 AS exclusao_desp_ant
                , (
                    SELECT SUM(COALESCE(inat.valor,0.00))
                      FROM (
                            SELECT valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                ,'".$this->getDado('data_final')."'
                                                                                ,'".$this->getDado('exercicio')."'
                                                                                ,(SELECT valor FROM administracao.configuracao
                                                                                   WHERE cod_modulo = 8 
                                                                                     AND parametro ilike 'cod_entidade_rpps'
                                                                                     AND exercicio = '".$this->getDado('exercicio')."'
                                                                                  )
                                                                                ,'3.3.1.9.0.01.01'
                                                                                ,7
                                                                                ,'liquidado'
                                                                              )
                            UNION
                            SELECT valor FROM tcemg.siace_despesa_total_pessoal('".$this->getDado('data_inicial')."'
                                                                                ,'".$this->getDado('data_final')."'
                                                                                ,'".$this->getDado('exercicio')."'
                                                                                ,(SELECT valor FROM administracao.configuracao
                                                                                   WHERE cod_modulo = 8 
                                                                                     AND parametro ilike 'cod_entidade_rpps'
                                                                                     AND exercicio = '".$this->getDado('exercicio')."'
                                                                                  )
                                                                                ,'3.3.1.9.0.03.01'
                                                                                ,7
                                                                                ,'liquidado'
                                                                              )
                            ) AS inat
                  ) AS inat_pens_cust_proprio
                  , 'S' AS nada_declarar_pessoal
  ";

  return $stSql;
}

}//END CLASS

?>