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
/*
    * Página Oculta de Despesa
    * Data de Criação   : 14/07/2005

    * @author Analista: Diego B Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-10-23 15:35:44 -0200 (Ter, 23 Out 2007) $

    * Casos de uso: uc-02.01.26
*/

/*
$Log$
Revision 1.9  2007/10/02 18:28:41  leandro.zis
Ticket#9844#

Revision 1.8  2007/07/24 19:54:42  leandro.zis
Bug#9637#

Revision 1.7  2006/11/20 22:57:25  gelson
Bug #7155#

Revision 1.6  2006/07/05 20:43:52  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "Despesa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];
$obROrcamentoDespesa = new ROrcamentoDespesa;
$obROrcamentoDespesa->setExercicio(Sessao::getExercicio() );

switch ($stCtrl) {
    case "mascaraClassificacao":
        $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['inCodClassificacao'] );
        $js .= "f.inCodClassificacao.value = '".$arMascClassificacao[1]."'; \n";
        SistemaLegado::executaIFrameOculto( $js );
    break;

   case "mascaraClassificacao2":
        //monta mascara da RUBRICA DE DESPESA
        $arMascClassificacao = Mascara::validaMascaraDinamica( $_REQUEST['stMascClassificacao'] , $_REQUEST['stMascClassificacaoDespesa'] );
        $js .= "f.stMascClassificacaoDespesa.value = '".$arMascClassificacao[1]."'; \n";

        //busca DESCRICAO DA RUBRICA DE DESPESA
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascara          ( $_REQUEST['stMascClassificacao'] );
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $arMascClassificacao[1]       );
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao );
        if ($stDescricao != "") {
//            $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$stDescricao.'";';
        } else {
            $js .= 'f.stMascClassificacaoDespesa.value = "";';
            $js .= 'f.stMascClassificacaoDespesa.focus();';
//            $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$null.'";';
//            $js .= "alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
        }
        echo $js;
        //SistemaLegado::executaiFrameOculto( $js );
    break;

    case 'buscaPopup':
    if ($_POST["inCodDespesa"] != "") {
        $obROrcamentoDespesa->setCodDespesa( $_POST["inCodDespesa"] );
        $obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
        $obErro = $obROrcamentoDespesa->listarDespesaUsuario( $rsDespesa );
        if (!$obErro->ocorreu()) {
            $stNomDespesa = $rsDespesa->getCampo( "descricao" );
        }
    } else $stNomDespesa = "";
    SistemaLegado::executaFrameOculto("retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stNomDespesa."')");
    break;

   case 'buscaValoresUnidade':
/*      if ($_GET['stSelecionado'] == "inNumOrgao") {
          $_POST["inNumUnidade"] = "";
      }*/

      if ($_GET['inNumOrgao'] != "") {
          $inCodOrgao = $_GET['inNumOrgao'];

          $obROrcamentoUnidadeOrcamentaria= new ROrcamentoUnidadeOrcamentaria;
          $obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $inCodOrgao );
          $obROrcamentoUnidadeOrcamentaria->listar( $rsUnidade, " ORDER BY num_unidade");

          if ( $rsUnidade->getNumLinhas() > -1 ) {
              $inContador = 1;
              $js .= "limpaSelect(f.inNumUnidade,0); \n";
              $js .= "f.inNumUnidade.options[0] = new Option('Selecione','', 'selected');\n";
              while ( !$rsUnidade->eof() ) {
                  $inNumUnidade   = $rsUnidade->getCampo("num_unidade");
                  $stNomUnidade   = $rsUnidade->getCampo("nom_unidade");
                  $selected       = "";
         /*         if ($inNumUnidade == $_POST["inNumUnidade"]) {
                      $selected = "selected";
                  }*/
                  $js .= "f.inNumUnidade.options[$inContador] = new Option('".$stNomUnidade."','".$inNumUnidade."','".$selected."'); \n";
                  $inContador++;
                  $rsUnidade->proximo();
              }
          } else {
              $js .= "limpaSelect(f.inNumUnidade,0); \n";
              $js .= "f.inNumUnidade.options[0] = new Option('Selecione','', 'selected');\n";
          }

      } else {
          $js .= "limpaSelect(f.inNumUnidade,0); \n";
          $js .= "f.inNumUnidade.options[0] = new Option('Selecione','', 'selected');\n";
      }
      $js .= "f.inNumUnidadeTxt.value = '';\n";
      echo $js;
  break;

}

?>
