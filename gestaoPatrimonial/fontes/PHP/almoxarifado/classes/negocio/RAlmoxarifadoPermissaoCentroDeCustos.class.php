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
  * Classe de Regra de Almoxarifado
  * Data de Criação   : 26/12/2005

   * @author Analista     : Diego
   * @author Desenvolvedor: Rodrigo Schreiner

   * Casos de uso: uc-03.03.07
**/

/*
$Log$
Revision 1.7  2006/07/06 14:04:47  diego
Retirada tag de log com erro.

Revision 1.6  2006/07/06 12:09:32  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_FW_BANCO_DADOS."Transacao.class.php");
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoPermissaoCentroCusto.class.php");

class RAlmoxarifadoPermissaoCentroDeCustos
{
/**
   * @access Private
   * @var Object
*/
var $obTransacao;
/**
   * @access Private
   * @var Object
*/
var $obRCgmPessoaFisica;

/**
   * @access Private
   * @var Array
*/
var $arCentroDeCusto;

/**
   * @access Private
   * @var Object
*/
var $roUltimoCentro;

/**
   * @access Private
   * @var Object
*/
var $obTAlmoxarifadoPermissaoCentroCusto;

/**
     * Método construtor
     * @access Public
*/
 function RAlmoxarifadoPermissaoCentroDeCustos()
 {
  include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php");
  $this->obRCGMPessoaFisica = new RCGMPessoaFisica();
 }

 function setCentroCusto($inCodigo) { $this->inCodCentroCusto = $inCodigo; }

 function getCentroCusto() { return $this->inCodCentroCusto; }

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

 function listarDisponiveis(&$rsDisponiveis,$stOrder = "",$obTransacao = "")
 {
  $obTAlmoxarifadoPermissaoCentroCusto = new TAlmoxarifadoPermissaoCentroCusto();
  $obTAlmoxarifadoPermissaoCentroCusto->setDado("numcgm",$this->obRCGMPessoaFisica->getNumCGM());
  $obErro    = $obTAlmoxarifadoPermissaoCentroCusto->RecuperaDisponiveis($rsDisponiveis,$stFiltro,$stOrder,$boTransacao);

  return $obErro;
 }

 function listarRelacionados(&$rsRelacionados,$stOrder = "",$obTransacao = "")
 {
  $obTAlmoxarifadoPermissaoCentroCusto = new TAlmoxarifadoPermissaoCentroCusto();
  $stFiltro  = "";
  $stFiltro  = " And ccp.numcgm = ".$this->obRCGMPessoaFisica->getNumCGM();
  $stOrder   = " Order By ccp.cod_centro Asc";
  $obErro    = $obTAlmoxarifadoPermissaoCentroCusto->RecuperaRelacionados($rsRelacionados,$stFiltro,$stOrder,$boTransacao);

  return $obErro;
 }

 function listarCentroCustoPermissao(&$rsCentroCustoPermissao,$stOrder = "",$obTransacao = "")
 {
  $obTAlmoxarifadoPermissaoCentroCusto = new TAlmoxarifadoPermissaoCentroCusto();
  $obTAlmoxarifadoPermissaoCentroCusto->setDado("numcgm",$this->obRCGMPessoaFisica->getNumCGM());
  $stFiltro = "";
  $boTransacao ="";
  $obErro    = $obTAlmoxarifadoPermissaoCentroCusto->RecuperaCentroCustoPermissao($rsCentroCustoPermissao,$stFiltro,$stOrder,$boTransacao);

  return $obErro;
 }

 function listar(&$rsRecordSet,$stOrder = "",$boTransacao = "")
 {
  include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoPermissaoCentroCusto.class.php");
  $obTAlmoxarifadoPermissaoCentroCusto = new TAlmoxarifadoPermissaoCentroCusto();
  $stFiltro = "";

  if ( $this->roUltimoCentro->getCodigo()) {
   $stFiltro .= " And cc.cod_centro = ".$this->roUltimoCentro->getCodigo();
  }
  if ($this->obRCGMPessoaFisica->getNumCGM()) {
   $stFiltro .= " And ccp.numcgm = ".$this->obRCGMPessoaFisica->getNumCGM();
  }
  $obErro = $obTAlmoxarifadoPermissaoCentroCusto->recuperaRelacionados($rsRecordSet, $stFiltro, $stOrder, $boTransacao);

  return $obErro;
 }

 function addCentroDeCustos()
 {
  include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCentroDeCustos.class.php");
  $this->arCentroDeCusto[] = new RAlmoxarifadoCentroDeCustos();
  $this->roUltimoCentro    = &$this->arCentroDeCusto[ count($this->arCentroDeCusto) - 1];
 }

 function salvarResponsavel($boTransacao = "")
 {
  $obTAlmoxarifadoPermissaoCentroCusto = new TAlmoxarifadoPermissaoCentroCusto();

  $obTAlmoxarifadoPermissaoCentroCusto->setDado("numcgm",$this->obRCGMPessoaFisica->getNumCGM());
  $obTAlmoxarifadoPermissaoCentroCusto->setDado("cod_centro",$this->roUltimoCentro->getCodigo());

  $this->listar($rsRelacionados,'',$boTransacao);

  while (!($rsRelacionados->eof())) {
   $permissao = $rsRelacionados->getCampo("responsavel");
    if ($permissao == "t") {
    $obErro = $obTAlmoxarifadoPermissaoCentroCusto->exclusao($boTransacao);
   }
  $rsRelacionados->proximo();
  }

   $obTAlmoxarifadoPermissaoCentroCusto->setDado("cod_centro",$this->roUltimoCentro->getCodigo());
   $obTAlmoxarifadoPermissaoCentroCusto->setDado("responsavel",true);
   $obErro = $obTAlmoxarifadoPermissaoCentroCusto->inclusao($boTransacao);

   return $obErro;

 }

 function excluir($boTransacao = "")
 {
  $obTAlmoxarifadoPermissaoCentroCusto = new TAlmoxarifadoPermissaoCentroCusto;
  if ($this->roUltimoCentro->getCodigo()) {
      $obTAlmoxarifadoPermissaoCentroCusto->setDado( "cod_centro" ,$this->roUltimoCentro->getCodigo());
      $obTAlmoxarifadoPermissaoCentroCusto->setDado("numcgm",$this->obRCGMPessoaFisica->getNumCGM());
  }
  $obErro = $obTAlmoxarifadoPermissaoCentroCusto->exclusao($boTransacao);

  return $obErro;
 }

 function excluirTodosCentrosCustos($boTransacao = "")
 {
   $obTAlmoxarifadoPermissaoCentroCusto = new TAlmoxarifadoPermissaoCentroCusto;
   $obTAlmoxarifadoPermissaoCentroCusto->setComplementoChave('cod_centro,numcgm,responsavel');
   $obTAlmoxarifadoPermissaoCentroCusto->setDado("numcgm",$this->obRCGMPessoaFisica->getNumCGM());
   $obTAlmoxarifadoPermissaoCentroCusto->setDado("responsavel","false");

   $obErro = $obTAlmoxarifadoPermissaoCentroCusto->exclusao($boTransacao);

   return $obErro;
 }

 function salvar($boTransacao = "")
 {
  $obErro = new Erro;
  $obTAlmoxarifadoPermissaoCentroCusto = new TAlmoxarifadoPermissaoCentroCusto();
  $obTAlmoxarifadoPermissaoCentroCusto->setDado("numcgm",$this->obRCGMPessoaFisica->getNumCGM());

  $this->listarRelacionados($rsRelacionados);
  while (!($rsRelacionados->eof())) {
   $codigo    = $rsRelacionados->getCampo("cod_centro");
   $permissao = $rsRelacionados->getCampo("responsavel");
   if ($permissao != "t") {
    $obTAlmoxarifadoPermissaoCentroCusto->setDado("cod_centro",$codigo);
    $obErro = $obTAlmoxarifadoPermissaoCentroCusto->exclusao($boTransacao);
   }
  $rsRelacionados->proximo();
  }

  $rsListar = new Recordset;

  for ($inCount=0;$inCount<count($this->arCentroDeCusto);$inCount++) {
   $obCentroDeCustos = $this->arCentroDeCusto[$inCount];
   $obTAlmoxarifadoPermissaoCentroCusto->setDado("cod_centro",$obCentroDeCustos->getCodigo());
   $obErro = $obTAlmoxarifadoPermissaoCentroCusto->inclusao($boTransacao);

   if ($obErro->ocorreu()) {
    break;
   }

  }

  return $obErro;
 }

  public function salvarCentroCusto($boTransacao = "")
  {
    $obErro = new Erro;
    $obTAlmoxarifadoPermissaoCentroCusto = new TAlmoxarifadoPermissaoCentroCusto();
    $obTAlmoxarifadoPermissaoCentroCusto->setDado("numcgm",$this->obRCGMPessoaFisica->getNumCGM());
    $obTAlmoxarifadoPermissaoCentroCusto->setDado("cod_centro",$this->getCentroCusto());
    $obTAlmoxarifadoPermissaoCentroCusto->setDado("responsavel","false");

    $obErro = $obTAlmoxarifadoPermissaoCentroCusto->inclusao($boTransacao);
    if ($obErro->ocorreu()) {
      break;
    }

    return $obErro;
  }
}
