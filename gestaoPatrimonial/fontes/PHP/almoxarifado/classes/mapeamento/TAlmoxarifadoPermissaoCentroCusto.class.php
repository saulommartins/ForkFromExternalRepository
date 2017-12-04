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
/** Classe de mapeamento da tabela ALMOXARIFADO.CENTRO_CUSTO
    * Data de Criação: 27/10/2005

    * @author Analista     : Diego
    * @author Desenvolvedor: Rodrigo Schreiner

    * Casos de uso: uc-03.03.07
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE );

class TAlmoxarifadoPermissaoCentroCusto extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

 function TAlmoxarifadoPermissaoCentroCusto()
 {
   parent::Persistente();
   $this->setTabela('almoxarifado.centro_custo_permissao');
   $this->setCampoCod('');
   $this->setComplementoChave('cod_centro,numcgm');
   $this->AddCampo('cod_centro','integer',true,'',true,true);
   $this->AddCampo('numcgm', 'integer',true,'',true,true);
   $this->AddCampo('responsavel', 'boolean', true, false, false);
 }

 function RecuperaDisponiveis(&$rsDisponiveis,$stFiltro = "",$stOrdem = "",$boTransacao = "")
 {
  $obErro        = new Erro;
  $obConexao     = new Conexao;
  $rsDisponiveis = new RecordSet;
  $stSql         = $this->montaRecuperaDisponiveis().$stFiltro.$stOrdem;
  $obErro        = $obConexao->executaSQL( $rsDisponiveis, $stSql, $boTransacao );

  return $obErro;
 }

 function RecuperaRelacionados(&$rsRelacionados, $stFiltro = "",$stOrder = "",$boTransacao = "")
 {
  $obErro         = new Erro;
  $obConexao      = new Conexao;
  $rsRelacionados = new RecordSet;
  $stSql          = $this->montaRecuperaRelacionamento().$stFiltro.$stOrder;

  $obErro         = $obConexao->executaSQL($rsRelacionados,$stSql,$boTransacao);

  return $obErro;
 }

  public function RecuperaCentroCustoPermissao(&$rsCentroCustoPermissao,$stFiltro = "",$stOrdem = "",$boTransacao = "")
  {
    $obErro        = new Erro;
    $obConexao     = new Conexao;
    $rsCentroCustoPermissao = new RecordSet;
    $stSql         = $this->montaRecuperaCentroCustoPermissao().$stFiltro.$stOrdem;
    $obErro        = $obConexao->executaSQL( $rsCentroCustoPermissao, $stSql, $boTransacao );

    return $obErro;
  }

 function montaRecuperaDisponiveis()
 {
  $sSQL = "Select cc.cod_centro ,                                                       ".
          "       cc.descricao  ,                                                       ".
          "       to_char(cc.dt_vigencia, 'dd/mm/yyyy') as dt_vigencia                  ".
          "  From almoxarifado.centro_custo cc                                          ".
          " Where cc.cod_centro Not In(Select ccp.cod_centro                            ".
          "                              From almoxarifado.centro_custo_permissao ccp   ".
          "                             Where ccp.numcgm = ".$this->getDado("numcgm").")";

  return $sSQL;
 }

 function montaRecuperaRelacionamento()
 {
  $sSQL = "Select cc.cod_centro   ,                      ".
          "       cc.descricao    ,                      ".
          "       cc.dt_vigencia  ,                      ".
          "       ccp.responsavel                        ".
          "  From almoxarifado.centro_custo           cc,".
          "       almoxarifado.centro_custo_permissao ccp".
          " Where cc.cod_centro  = ccp.cod_centro        ";

   return $sSQL;
 }

 function montaRecuperaCentroCustoPermissao()
 {
    $sSQL = "SELECT cc.cod_centro ,                                          ".
      "             cc.descricao  ,                                          ".
      "             to_char(cc.dt_vigencia, 'dd/mm/yyyy') as dt_vigencia ,   ".
      "             ccp.responsavel ,                                        ".
      "             ccp.cod_centro as marcado                                ".
      "        FROM almoxarifado.centro_custo cc                             ".
      "   LEFT JOIN almoxarifado.centro_custo_permissao ccp                  ".
      "          ON cc.cod_centro = ccp.cod_centro                           ".
      "         AND ccp.numcgm = ".$this->getDado("numcgm");

    return $sSQL;
 }

}
?>
