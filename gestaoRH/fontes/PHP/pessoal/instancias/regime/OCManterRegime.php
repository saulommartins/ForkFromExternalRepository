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
    * Página Oculta de Pessoal Regime
    * Data de Criação   : 22/04/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 30860 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.04.05

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$rsFaixas = new RecordSet;

function listarSubDivisao($arRecordSet, $boExecuta=true)
{
    $rsRecordSet = new Recordset;
    if ( is_array($arRecordSet) ) {
       if ( count( $arRecordSet ) > 0 ) {
           $rsRecordSet->preenche( $arRecordSet );
       }
    }

    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Subdivisões cadastradas" );

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Descrição" );
        $obLista->ultimoCabecalho->setWidth( 85 );
        $obLista->commitCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "descricao" );
        $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:alteraDado('montaAlterar');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:alteraDado('excluirSubDivisao');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnSubDivisao').innerHTML = '".$stHtml."';";
    $stJs .= "f.stDescricaoSubDivisao.value = '';";
    $stJs .= "f.stSubDivisao.value          = '';";

    if ($boExecuta==true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

// Acoes por pagina

switch ($stCtrl) {
  case "MontaSubDivisao":
        $stMensagem = false;
        $arElementos = array ();
        $rsRecordSet = new Recordset;

         if ( count(Sessao::read('subDivisao')) <= 0 ) {
            $bocontrole = true;
            $inProxId = 1;
        } else {
            $rsRecordSet->preenche( Sessao::read('subDivisao') );
            $rsRecordSet->setUltimoElemento();
            $inUltimoId = $rsRecordSet->getCampo("inId");

            if (!$inUltimoId) {
                $inProxId = 1;
            } else {
                $inProxId = $inUltimoId + 1;
            }

            while (!$rsRecordSet->eof()) {
               if ($rsRecordSet->getCampo('descricao') == $_POST['stDescricaoSubDivisao']) {
                   $stSubDivisao = $_POST['stDescricaoSubDivisao'];
                   sistemaLegado::exibeAviso("Já existe uma subdivisão com o nome($stDescricaoSubDivisao) para este regime!"," "," ");
                   break;
               } else {
                 $bocontrole = true;
               }
              $rsRecordSet->proximo();
            }
        }
        if ($bocontrole == 'true') {
            $arSubDivisao = Sessao::read('subDivisao');
            $arElementos['inId']      = $inProxId;
            $arElementos['descricao'] = $_POST['stDescricaoSubDivisao'];
            $arSubDivisao[]           = $arElementos;
            listarSubDivisao( $arSubDivisao );
            Sessao::write('subDivisao', $arSubDivisao);
        }

  break;

    case "montaAlterar":
        $id = $_REQUEST['inId'];
        $rsRecordSet = new Recordset;
        $rsRecordSet->preenche( Sessao::read('subDivisao') );
        while (!$rsRecordSet->eof()) {
           if ($rsRecordSet->getCampo('inId') == $id) {
               $stSubDivisao = $rsRecordSet->getCampo('descricao');
               $stJs .= "f.stSubDivisao.value          = '$id';";
               $stJs .= "f.stDescricaoSubDivisao.value = '$stSubDivisao';";
           }
           $rsRecordSet->proximo();
         }
    break;

    case "alterarSubDivisao":

           if (!$_REQUEST['stSubDivisao']) {
               sistemaLegado::exibeAviso("É necessário selecionar uma subdivisão para ser alterada!"," "," ");
               break;
           } else {
             $id = $_REQUEST['stSubDivisao'];
             $arSubDivisao = Sessao::read('subDivisao');
             reset($arSubDivisao);
             while ( list( $arId ) = each( $arSubDivisao ) ) {
                  if ($arSubDivisao[$arId]["inId"] == $id) {
                      $arElementos['inId']           = $arSubDivisao[$arId]["inId"];
                      $arElementos['descricao']      = $_REQUEST['stDescricaoSubDivisao'];
                      $arElementos['inCodSubDivisao']= $arSubDivisao[$arId]["inCodSubDivisao"];
                      $arTMP[] = $arElementos;
                  } else {
                      $arElementos['inId']           = $arSubDivisao[$arId]["inId"];
                      $arElementos['descricao']      = $arSubDivisao[$arId]["descricao"];
                      $arElementos['inCodSubDivisao']= $arSubDivisao[$arId]["inCodSubDivisao"];
                      $arTMP[] = $arElementos;
                  }
              }
              Sessao::write('subDivisao', $arTMP);
              listarSubDivisao( $arTMP );
           }
    break;

    case "excluirSubDivisao":
        $id = $_REQUEST['inId'];
        $arSubDivisao = Sessao::read('subDivisao');
        $inCount = 0;

        while ( list( $arId ) = each( $arSubDivisao  ) ) {
            if ($arSubDivisao [$arId]["inId"] != $id) {
                $inCount= $inCount + 1;
                $arElementos['inId']           = $inCount;
                $arElementos['descricao']      = $arSubDivisao[$arId]["descricao"];
                $arElementos['inCodSubDivisao']= $arSubDivisao[$arId]["inCodSubDivisao"];
                $arTMP[] = $arElementos;
            }

        }
        Sessao::write('subDivisao', $arTMP);
        listarSubDivisao( $arTMP );
    break;

    case 'preencheInner':
        $arSubDivisao = Sessao::read('subDivisao');
        if ( count( $arSubDivisao ) ) {
            $stJs .= listarSubDivisao( $arSubDivisao, false );
        }
    break;
}
if($stJs)
    sistemaLegado::executaFrameOculto($stJs);

?>
