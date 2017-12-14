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
    * Classe de mapeamento da tabela
    * Data de Criação: 16/02/2009

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela
  * Data de Criação: 16/02/2009

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoLancamentoAutorizacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoLancamentoAutorizacao()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.lancamento_autorizacao');

    $this->setCampoCod('cod_lancamento');
    $this->setComplementoChave('cod_item,cod_marca,cod_almoxarifado,cod_centro');

    $this->AddCampo('cod_lancamento','sequence',true,'',true,'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('cod_item','integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('cod_marca','integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('cod_almoxarifado','integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('cod_centro','integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('exercicio','varchar',true,4,false,'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('cod_autorizacao','integer',true,'',false,'TAlmoxarifadoLancamentoMaterial');
}

function montaRecuperaRelacionamento()
{
    $stSql = "
                SELECT aut.*
                     , mar.*
                     , mod.*
                     , to_char(aut.timestamp,'dd/mm/yyyy') as data_autorizacao
                     , vei.*
                     , unm.*
                     , cat_fro.*
                     , (
                         SELECT publico.concatenar_hifen( com.nom_combustivel ) as teste
                           FROM frota.veiculo_combustivel  as vco
                              , frota.combustivel          as com
                          WHERE vei.cod_veiculo = vco.cod_veiculo
                            AND vco.cod_combustivel = com.cod_combustivel
                        ) as nom_combustiveis
                     , (
                         SELECT CASE WHEN ret.cod_veiculo IS NOT NULL THEN ret.km_retorno ELSE uti.km_saida END AS x
                           FROM frota.utilizacao           as uti
                           LEFT JOIN frota.utilizacao_retorno   as ret
                                  ON (      uti.cod_veiculo = ret.cod_veiculo
                                        AND uti.dt_saida    = ret.dt_saida
                                        AND uti.hr_saida    = ret.hr_saida
                                      )
                           WHERE uti.cod_veiculo = vei.cod_veiculo
                        ORDER BY ret.dt_retorno DESC
                               , ret.hr_retorno DESC
                               , uti.dt_saida DESC
                               , uti.hr_saida DESC
                               , uti.cod_veiculo DESC
                         LIMIT 1
                        ) as kil_saida
                  FROM frota.autorizacao          as aut
                     , frota.item                 as ite
                     , almoxarifado.catalogo_item as cat_fro
                     , administracao.unidade_medida as unm
                     , frota.tipo_item            as tip
                     , frota.veiculo              as vei
                     , frota.modelo               as mod
                     , frota.marca                as mar
                     , frota.posto                as posto
                 WHERE aut.cod_item    = ite.cod_item
                   AND ite.cod_tipo    = tip.cod_tipo
                   AND ite.cod_item    = cat_fro.cod_item
                   AND cat_fro.cod_grandeza = unm.cod_grandeza
                   AND cat_fro.cod_unidade  = unm.cod_unidade
                   AND aut.cod_veiculo = vei.cod_veiculo
                   AND vei.cod_modelo  = mod.cod_modelo
                   AND vei.cod_marca   = mod.cod_marca
                   AND mod.cod_marca   = mar.cod_marca
                   AND aut.cgm_fornecedor = posto.cgm_posto
                   AND posto.interno = true

                ".($this->getDado('exercicio')          ? "AND aut.exercicio        = '".$this->getDado('exercicio')."'" : "" )."
                ".($this->getDado('cod_autorizacao')    ? "AND aut.cod_autorizacao  = ".$this->getDado('cod_autorizacao') : "" )."
                ".($this->getDado('stDataInicial')      ? "AND to_date(to_char(aut.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')  >= to_date('".$this->getDado('stDataInicial')."','dd/mm/yyyy') " : "" )."
                ".($this->getDado('stDataFinal')        ? "AND to_date(to_char(aut.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')  <= to_date('".$this->getDado('stDataFinal')  ."','dd/mm/yyyy') " : "" )."
                ".($this->getDado('stFiltro') )."

                ORDER BY aut.timestamp DESC";

    return $stSql;
}

}
