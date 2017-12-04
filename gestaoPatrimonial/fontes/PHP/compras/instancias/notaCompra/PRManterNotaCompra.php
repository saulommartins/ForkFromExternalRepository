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
    * Página de Processamento dos Parâmetros do Arquivo de Relacionamento das Despesas
    * Data de Criação   : 24/10/2006

    * @author Analista: Cassiano Vasconcelos
    * @author Desenvolvedor: Thiago La Delfa Cabelleira

    * @ignore

    * Casos de uso: uc-03.04.29

    $Id: PRManterNotaCompra.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_COM_MAPEAMENTO."TComprasOrdemCompraNota.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterNotaCompra";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

Sessao::setTrataExcecao( true );
$obTComprasOrdemCompraNota = new TComprasOrdemCompraNota();
Sessao::getTransacao()->setMapeamento( $obTComprasOrdemCompraNota );
$stAcao = $request->get('stAcao');

switch ($_REQUEST['stAcao']) {
  case 'incluir':
    $arDadosNota = Sessao::read('arDadosNota');
    $obTComprasOrdemCompraNota->setDado('cod_ordem',$arDadosNota[0]['cod_ordem']);
    $obTComprasOrdemCompraNota->setDado('cgm_fornecedor',$arDadosNota[0]['cgm_fornecedor']);
    $obTComprasOrdemCompraNota->setDado('cod_nota',$arDadosNota[0]['cod_nota']);
    $obTComprasOrdemCompraNota->setDado('exercicio',$arDadosNota[0]['exercicio']);
    $obTComprasOrdemCompraNota->setDado('cod_entidade',$arDadosNota[0]['cod_entidade']);
/*
    echo $obTComprasOrdemCompraNota->getDado('cod_nota').' / ';
    echo $obTComprasOrdemCompraNota->getDado('cod_entidade').' / ';
    echo $obTComprasOrdemCompraNota->getDado('exercicio').' / ';
    echo $obTComprasOrdemCompraNota->getDado('cgm_fornecedor').' / ';
    echo $obTComprasOrdemCompraNota->getDado('cod_ordem');
    break;
*/
    $obTComprasOrdemCompraNota->inclusao();

    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=incluir","Nota de Compra ".$obTComprasOrdemCompraNota->getDado('cod_nota')."","incluir","incluir_n", Sessao::getId(), "../");

  break;

    case 'excluir':
      $pgProx = $pgList."?".Sessao::getId().'&stAcao='.$stAcao;
      Sessao::getExcecao()->setLocal('telaprincipal');

      $obTComprasOrdemCompraNota->setDado('exercicio',$_REQUEST['exercicio']);
      $obTComprasOrdemCompraNota->setDado('cod_entidade',$_REQUEST['cod_entidade']);
      $obTComprasOrdemCompraNota->setDado('cod_ordem',$_REQUEST['inCodOrdem']);
      $obTComprasOrdemCompraNota->exclusao();

      $obTComprasOrdemCompraNota->setDado('cod_nota',$_REQUEST['numNota']);

      $obErro = $obTComprasOrdemCompraNota->exclusao( Sessao::getTransacao() );

      if ( !$obErro->ocorreu() ) {
        sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir","Nota de Compra: ".$_REQUEST["numNota"],"excluir","aviso", Sessao::getId(), "../");
      } else {
             SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir","Não foi possível excluir a Nota de Compra ".$_REQUEST["numNota"],"n_excluir","erro", Sessao::getId(), "../");
            //sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
      }

    break;

}//fim do switch

Sessao::encerraExcecao();
?>
