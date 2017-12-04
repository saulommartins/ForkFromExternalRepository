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
    Página de formulário de Listagem da Localização
    Data de criação : 27/03/2006

    * @author Analista      : Diego
    * @author Desenvolvedor : Rodrigo D. Schreiner

    * @ignore

    * Casos de uso: uc-03.03.14

    $Id: LSManterLocalizacao.php 59612 2014-09-02 12:00:51Z gelson $
**/

  include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
  include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
  include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoLocalizacao.class.php");

  //Define o nome dos arquivos PHP
  $stPrograma = "ManterLocalizacao";
  $pgFilt = "FL".$stPrograma.".php";
  $pgList = "LS".$stPrograma.".php";
  $pgForm = "FM".$stPrograma.".php";
  $pgProc = "PR".$stPrograma.".php";
  $pgOcul = "OC".$stPrograma.".php";

  $stCaminho = CAM_GP_ALM_INSTANCIAS."localizacao/";

  $stAcao = $request->get('stAcao');

  if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
  }

  switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
  }

 //MANTEM FILTRO E PAGINACAO
 $stLink .= "&stAcao=".$stAcao;
 if ($_GET["pg"] and  $_GET["pos"]) {
     #sessao->transf["FiltroMontaLocalizacao"]["pg"]  = $_GET["pg"];
     #sessao->transf["FiltroMontaLocalizacao"]["pos"] = $_GET["pos"];
     $FiltroMontaLocalizacao["pg"] = $_GET["pg"];
     $FiltroMontaLocalizacao["pos"] = $_GET["pos"];
 }

    Sessao::write('FiltroMontaLocalizacao', $FiltroMontaLocalizacao);

 //USADO QUANDO EXISTIR FILTRO
 //NA FL O VAR LINK DEVE SER RESETADA
 if ( is_array(Sessao::read("FiltroMontaLocalizacao")) ) {
     $_REQUEST = Sessao::read("FiltroMontaLocalizacao");
     $_GET = Sessao::read("FiltroMontaLocalizacao");
 } else {
     foreach ($_REQUEST as $key => $valor) {
         #sessao->transf["FiltroMontaLocalizacao"][$key] = $valor;
         $FiltroMontaLocalizacao[$key] = $valor;
     }
 }

  if (Sessao::read('inCodAlmoxarifado')) {
    $inCodAlmoxarifado = Sessao::read('inCodAlmoxarifado');
  } else {
    $inCodAlmoxarifado = $_REQUEST['inCodAlmoxarifado'];
  }
  $obAlmoxarifadoLocalizacao = new RAlmoxarifadoLocalizacao();
  $obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarifado->setCodigo( $inCodAlmoxarifado );
  $obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarifado->consultar();
  $obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarifado->obRCGMAlmoxarifado->getNomCGM()."*";

  Sessao::write('FiltroMontaLocalizacao', $FiltroMontaLocalizacao);

  if (Sessao::read('inNomAlmoxarifado')) {
    $inNomAlmoxarifado = Sessao::read('inNomAlmoxarifado');
  } else {
    $inNomAlmoxarifado = $obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarifado->obRCGMAlmoxarifado->getNomCGM() ;
  }

  $obRegra = new RAlmoxarifadoLocalizacao;
  $rsLista = new RecordSet;

  $obRegra->obRAlmoxarifadoAlmoxarifado->setCodigo($inCodAlmoxarifado);

  if ($_REQUEST['stHdnLocalizacao']) {
   $obRegra->setLocalizacao($_REQUEST['stHdnLocalizacao']);
  }

  $obRegra->listar($rsLista);

  $obLista = new Lista;

  $obLista->setRecordSet( $rsLista );
  $obLista->setTitulo("Localizações Cadastradas" . ": " . $inCodAlmoxarifado." - ".$inNomAlmoxarifado );
  $obLista->addCabecalho();
  $obLista->ultimoCabecalho->addConteudo("&nbsp;");
  $obLista->ultimoCabecalho->setWidth( 5 );
  $obLista->commitCabecalho();
  $obLista->addCabecalho();
  $obLista->ultimoCabecalho->addConteudo("Código");
  $obLista->ultimoCabecalho->setWidth(10);
  $obLista->commitCabecalho();

  $obLista->addCabecalho();
  $obLista->ultimoCabecalho->addConteudo("Localização");
  $obLista->ultimoCabecalho->setWidth(70);
  $obLista->commitCabecalho();

  $obLista->addCabecalho();
  $obLista->ultimoCabecalho->addConteudo("&nbsp;");
  $obLista->ultimoCabecalho->setWidth(5);
  $obLista->commitCabecalho();

  $obLista->addDado();
  $obLista->ultimoDado->setAlinhamento("DIREITA");
  $obLista->ultimoDado->setCampo("cod_localizacao");
  $obLista->commitDado();

  $obLista->addDado();
  $obLista->ultimoDado->setAlinhamento("CENTRO");
  $obLista->ultimoDado->setCampo("localizacao");
  $obLista->commitDado();

  $obLista->addAcao();
  $obLista->ultimaAcao->setAcao ($stAcao);

  $obLista->ultimaAcao->addCampo("&inCodLocalizacao"  , "cod_localizacao" );
  $obLista->ultimaAcao->addCampo("&inCodAlmoxarifado" , "cod_almoxarifado");
  $obLista->ultimaAcao->addCampo("&stLocalizacao"  , "localizacao"     );

  if ($stAcao == "excluir") {
      $obLista->ultimaAcao->addCampo("&stDescQuestao"  ,"[cod_localizacao] - [localizacao]");
      $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
  } else {
      $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink);
  }
  $obLista->setAjuda("UC-03.03.14");
  $obLista->commitAcao();
  $obLista->show();

?>
