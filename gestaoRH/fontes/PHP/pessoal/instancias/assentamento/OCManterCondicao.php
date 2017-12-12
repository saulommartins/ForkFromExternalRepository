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
* Oculto de condição do assentamento
* Data de Criação: 05/08/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage

$Revision: 30860 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.04.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamentoVinculado.class.php"    );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCondicaoAssentamento.class.php"     );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamento.class.php"             );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalVantagem.class.php"                 );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCondicao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$obRPessoalVantagem               = new RPessoalVantagem;
$obRPessoalAssentamento1          = new RPessoalAssentamento($obRPessoalVantagem);
$obRPessoalAssentamento2          = new RPessoalAssentamento($obRPessoalVantagem);
$obRPessoalCondicaoAssentamento   = new RPessoalCondicaoAssentamento();
$obRPessoalAssentamentoVinculado  = new RPessoalAssentamentoVinculado( $obRPessoalAssentamento1,$obRPessoalAssentamento2,$obRPessoalCondicaoAssentamento );
$rsClassificacao = $rsAssentamento = $rsFuncao = new Recordset;

function montaAssentamentoVinculado($boExecuta=false)
{
    $rsRecordSet = new Recordset;
    $arRecordSet = Sessao::read('assentamentoVinculado');
    $rsRecordSet->preenche( is_array($arRecordSet) ? $arRecordSet : array() );

    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Assentamento Vinculados" );
        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Classificação" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Assentamento" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Dias Incidência" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Dias" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Condição" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Fórmula" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inNomClassificacaoVinculacaoTxt" );
        $obLista->ultimoDado->setAlinhamento('CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stSiglaVinculado" );
        $obLista->ultimoDado->setAlinhamento('CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inDiasIncidencia" );
        $obLista->ultimoDado->setAlinhamento('CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inDiasVinculado" );
        $obLista->ultimoDado->setAlinhamento('CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stCondicao" );
        $obLista->ultimoDado->setAlinhamento('CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inCodFuncaoTxt" );
        $obLista->ultimoDado->setAlinhamento('CENTRO' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:alteraDado('montaAlterar');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->ultimaAcao->addCampo("2","inCodClassificacaoVinculacaoTxt");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:alteraDado('excluirAssentamentoVinculado');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();
    }

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnAssentamentosVinculados').innerHTML = '".$stHtml."';";
    $stJs .= "f.boCondicao[0].checked                  = true;";
    $stJs .= "f.inDiasIncidencia.value                 = '';";
    $stJs .= "f.inDiasIncidencia.disabled              = false;";
    $stJs .= "f.inCodClassificacaoVinculacaoTxt.value  = '';";
    $stJs .= "f.inCodClassificacaoVinculacao.value     = '';";
    $stJs .= "f.inCodAssentamentoVinculacao.value      = '';";
    $stJs .= "f.stSiglaVinculado.value                 = '';";
    $stJs .= "f.inDiasVinculado.value                  = '';";
    $stJs .= "f.stFuncao.value                         = '';";
    $stJs .= "f.inCodFuncao.value                      = '';";
    $stJs .= "f.inAssentamentoVinculado.value          = '';";

    if ($boExecuta == true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function incluirAssentamentoVinculado($boExecuta=false)
{
   global $obRPessoalAssentamento1;
   global $obRPessoalAssentamentoVinculado;

   if ($_REQUEST['inAssentamentoVinculado'] == "") {
      $obErro = new erro;
      //Verifica se o assentamentoVinculado já não existe na sessão com os mesmos valores da chave.
      $assentamentoVinculado = Sessao::read('assentamentoVinculado');
      if ( is_array($assentamentoVinculado) ) {
         reset($assentamentoVinculado);

         while ( list( $arId ) = each( $assentamentoVinculado ) ) {
            if (   ($assentamentoVinculado[$arId]['boCondicao'] == $_POST['boCondicao'])
               and ($assentamentoVinculado[$arId]['inDiasIncidencia'] == $_POST['inDiasIncidencia'])
               and ($assentamentoVinculado[$arId]['inCodClassificacaoVinculacaoTxt'] == $_POST['inCodClassificacaoVinculacaoTxt'])
               and ($assentamentoVinculado[$arId]['inCodAssentamentoVinculacao'] == $_POST['inCodAssentamentoVinculacao'])
               and ($assentamentoVinculado[$arId]['inDiasVinculado'] == $_POST['inDiasVinculado'])
            ) {
               $obErro->setDescricao('Já existe um assentamento vinculado com estes valores de condição, dias para incidência, classificação, assentamento e dias (protelados/averbados).');
            }
         }
      }
      if ($_POST['stHdnCodClassificacao'] == $_POST['inCodClassificacaoVinculacao'] and  $_POST['stHdnCodAssentamento'] == $_POST['inCodAssentamentoVinculacao']) {
         $obErro->setDescricao("Um assentamento não pode ser protelado ou averbado por ele mesmo.");
      }
      if ( ($_POST['stHdnCodClassificacao'] == "" and  $_POST['inCodClassificacao'] == "") or  ($_POST['stHdnCodAssentamento'] == "" and $_POST['inCodAssentamento'] == "") ) {
         $obErro->setDescricao("A classificação e o assentamento devem estar selecionados.");
      }
      if ($_POST['inCodClassificacaoVinculacao'] == "" or $_POST['inCodAssentamentoVinculacao'] == "") {
         $obErro->setDescricao("A classificação e o assentamento do vinculado devem estar selecionados1.");
      }
      if ($_POST['inDiasVinculado'] == "") {
         $obErro->setDescricao("O assentamento vinculado deve possuir dias protelados/averbados.");
      }
      if ($_POST['inDiasIncidencia'] == "") {
         $obErro->setDescricao("O assentamento vinculado deve possuir dias para incidência.");
      }
      if ( !$obErro->ocorreu() ) {
         global $rsClassificacao;
         global $obRPessoalAssentamentoVinculado;

         $obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->obRPessoalClassificacaoAssentamento->setCodClassificacaoAssentamento( $_POST['inCodClassificacaoVinculacaoTxt'] );
         $obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->obRPessoalClassificacaoAssentamento->listarClassificacao( $rsClassificacao );

         $arElementos = array ();
         $arElementosTemp = $assentamentoVinculado;
         if ( is_array($arElementos) ) {
               $inUltimoId = $arElementosTemp[count($arElementosTemp)-1]['inId'];
               $inUltimoId++;
         } else {
               $inUltimoId = 1;
         }

         $arElementos['inId']                            = $inUltimoId;
         $arElementos['boCondicao']                      = $_POST['boCondicao'];
         $arElementos['inCodClassificacaoVinculacaoTxt'] = $_POST['inCodClassificacaoVinculacao'];
         $arElementos['inNomClassificacaoVinculacaoTxt'] = $rsClassificacao->getCampo('descricao');
         $arElementos['inCodAssentamentoVinculacao']     = $_POST['inCodAssentamentoVinculacao'];
         $arElementos['stSiglaVinculado']                = $_POST['stSiglaVinculado'];
         if ($_POST['boCondicao'] == 'a') {
               $stCondicao = "Averbados";
         } elseif ($_POST['boCondicao'] == 'p') {
               $stCondicao = "Protelados";
         }
         if ($_POST['boDia'] == false) {
               $arElementos['inDiasIncidencia']            = $_POST['inDiasIncidencia'];
         } else {
               $arElementos['inDiasIncidencia']            = '1/2';
         }
         $arElementos['inDiasVinculado']                 = $_POST['inDiasVinculado'];
         $arElementos['stCondicao']                      = $stCondicao;
         $arElementos['inCodFuncaoTxt']                  = $_POST['inCodFuncao'];
         $assentamentoVinculado[]                        = $arElementos;
         Sessao::write('assentamentoVinculado', $assentamentoVinculado);

         $stJs  = montaAssentamentoVinculado();
         $stJs .= desabilitaCampos();

         if ($boExecuta == true) {
               sistemaLegado::executaFrameOculto($stJs);
         } else {
               return $stJs;
         }
      } else {
         sistemaLegado::exibeAviso($obErro->getDescricao()," "," ");
      }
   } else {
      sistemaLegado::exibeAviso("Alteração em processo, clique em alterar para confirmar alteração ou limpar para cancelar."," "," ");
   }
}

function desabilitaCampos($boExecuta=false)
{
    $stJs .= "if (f.inCodClassificacaoTxt.value != '') {    \n";
    $stJs .= "d.frm.inCodClassificacaoTxt.readOnly = true;  \n";
    $stJs .= "f.inCodClassificacaoTxt.style.color = '#333333';\n";
    $stJs .= "f.stHdnCodClassificacao.value = f.inCodClassificacaoTxt.value; \n";
    $stJs .= "f.inCodClassificacao.disabled = true;         \n";
    $stJs .= "}                                             \n";
    $stJs .= "if (f.stSigla.value != '') {                  \n";
    $stJs .= "d.frm.stSigla.readOnly = true;                \n";
    $stJs .= "f.stSigla.style.color = '#333333';            \n";
    $stJs .= "f.stHdnCodAssentamento.value = f.inCodAssentamento.value; \n";
    $stJs .= "f.inCodAssentamento.disabled = true;          \n";
    $stJs .= "}                                             \n";

    if ($boExecuta == true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function habilitaCampos($boExecuta=false)
{
    $stJs .= "d.frm.inCodClassificacaoTxt.readOnly = false; \n";
    $stJs .= "f.inCodClassificacao.disabled = false;        \n";
    $stJs .= "d.frm.stSigla.readOnly = false                \n";
    $stJs .= "f.inCodAssentamento.disabled = false;          \n";

    if ($boExecuta == true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function desabilitaVinculacao($boExecuta=false)
{
    $stJs .= "d.frm.inCodClassificacaoVinculacaoTxt.readOnly = true;  \n";
    $stJs .= "f.inCodClassificacaoVinculacaoTxt.style.color = '#333333';\n";
    $stJs .= "f.stHdnCodClassificacaoVinculado.value =  f.inCodClassificacaoVinculacao.value; \n";
    $stJs .= "f.inCodClassificacaoVinculacao.disabled = true;         \n";
    $stJs .= "d.frm.stSiglaVinculado.readOnly = true;                \n";
    $stJs .= "f.stSiglaVinculado.style.color = '#333333';            \n";
    $stJs .= "f.stHdnCodAssentamentoVinculado.value =  f.inCodAssentamentoVinculacao.value; \n";
    $stJs .= "f.inCodAssentamentoVinculacao.disabled = true;          \n";

    if ($boExecuta == true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function habilitaVinculacao($boExecuta=false)
{
    $stJs .= "d.frm.inCodClassificacaoVinculacaoTxt.readOnly = false;  \n";
    $stJs .= "f.inCodClassificacaoVinculacaoTxt.style.color = '#000000';\n";
    $stJs .= "f.inCodClassificacaoVinculacao.disabled = false;         \n";
    $stJs .= "d.frm.stSiglaVinculado.readOnly = false;                \n";
    $stJs .= "f.stSiglaVinculado.style.color = '#000000';            \n";
    $stJs .= "f.inCodAssentamentoVinculacao.disabled = false;          \n";

    if ($boExecuta == true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function excluirAssentamentoVinculado($boExecuta=false)
{
    $id = $_REQUEST['inId'];
    $inCount = 0;
    $assentamentoVinculado = Sessao::read('assentamentoVinculado');
    reset($assentamentoVinculado);
    $arTMP = array();
    while ( list( $arId ) = each( $assentamentoVinculado ) ) {
        if ($assentamentoVinculado[$arId]["inId"] != $id) {
            $inCount= $inCount + 1;
            $arElementos['inId']           = $inCount;
            $arElementos['boCondicao']                      = $assentamentoVinculado[$arId]["boCondicao"];
            $arElementos['inCodClassificacaoVinculacaoTxt'] = $assentamentoVinculado[$arId]["inCodClassificacaoVinculacaoTxt"];
            $arElementos['inNomClassificacaoVinculacaoTxt'] = $assentamentoVinculado[$arId]["inNomClassificacaoVinculacaoTxt"];
            $arElementos['inCodAssentamentoVinculacao']     = $assentamentoVinculado[$arId]["inCodAssentamentoVinculacao"];
            $arElementos['stSiglaVinculado']                = $assentamentoVinculado[$arId]["stSiglaVinculado"];
            $arElementos['inDiasIncidencia']                = $assentamentoVinculado[$arId]["inDiasIncidencia"];
            $arElementos['inDiasVinculado']                 = $assentamentoVinculado[$arId]["inDiasVinculado"];
            $arElementos['stCondicao']                      = $assentamentoVinculado[$arId]["stCondicao"];
            $arElementos['inCodFuncaoTxt']                  = $assentamentoVinculado[$arId]["inCodFuncaoTxt"];

            if ($_REQUEST['stAcao'] == 'alterar') {
                $arElementos['inCodCondicao']                   = $assentamentoVinculado[$arId]["inCodCondicao"];
                $arElementos['stTimestamp']                     = $assentamentoVinculado[$arId]["stTimestamp"];
                $arElementos['inCodAssentamento']               = $assentamentoVinculado[$arId]["inCodAssentamento"];
                $arElementos['stTimestampAssentamento']         = $assentamentoVinculado[$arId]["stTimestampAssentamento"];
                $arElementos['inCodAssentamentoVinculado']      = $assentamentoVinculado[$arId]["inCodAssentamentoVinculado"];
                $arElementos['stTimestampAssentamentoVinculado']= $assentamentoVinculado[$arId]["stTimestampAssentamentoVinculado"];
            }
            $arTMP[] = $arElementos;
        }
    }
    Sessao::write('assentamentoVinculado', $arTMP);

    $stJs = montaAssentamentoVinculado();
    if ( sizeof($arTMP) == 0 ) {
        $stJs .= "f.inCodClassificacaoTxt.disabled = false;      \n";
        $stJs .= "f.inCodClassificacao.disabled = false;         \n";
        $stJs .= "f.stSigla.disabled = false;                    \n";
        $stJs .= "f.inCodAssentamento.disabled = false;          \n";
    }
    $stJs .= habilitaVinculacao();
    if ($boExecuta == true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function alterarAssentamentoVinculado($boExecuta=false)
{
    $obErro = new erro;
    if ( ($_POST['stHdnCodClassificacao'] == $_POST['inCodClassificacaoVinculacao']) and  ($_POST['stHdnCodAssentamento'] == $_POST['inCodAssentamentoVinculacao']) ) {
       $obErro->setDescricao("Um assentamento não pode ser protelado ou averbado por ele mesmo.");
    }
    if ($_POST['stHdnCodClassificacao'] == "" or  $_POST['stHdnCodAssentamento'] == "") {
       $obErro->setDescricao("A classificação e o assentamento devem estar selecionados.");
    }
    if ( ( ($_POST['stHdnCodClassificacaoVinculado'] == "") and ($_POST['inCodClassificacaoVinculacao'] == "") ) or ( ($_POST['stHdnCodAssentamentoVinculado'] == "") and ($_POST['inCodAssentamentoVinculacao'] == "") ) ) {
       $obErro->setDescricao("A classificação e o assentamento para vinculação devem estar selecionados .");
    }
    if ($_POST['inDiasIncidencia'] == "") {
       $obErro->setDescricao("O assentamento vinculado deve possuir dias para incidência.");
    }
    if ($_POST['inDiasVinculado'] == "") {
       $obErro->setDescricao("O assentamento vinculado deve possuir dias protelados/averbados.");
    }

    if ( !$obErro->ocorreu() ) {
       global $rsClassificacao;
       global $obRPessoalAssentamentoVinculado;

       $obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->obRPessoalClassificacaoAssentamento->setCodClassificacaoAssentamento( $_POST['inCodClassificacaoVinculacaoTxt'] );
       $obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->obRPessoalClassificacaoAssentamento->listarClassificacao( $rsClassificacao );

       $id = $_REQUEST['inAssentamentoVinculado'];
       $inCount = 0;
       $assentamentoVinculado = Sessao::read('assentamentoVinculado');
       reset($assentamentoVinculado);
       $arTMP = array();
       while ( list( $arId ) = each( $assentamentoVinculado ) ) {
          if ($$assentamentoVinculado[$arId]["inId"] == $id) {
                $inCount = $inCount + 1;
                $arElementos['inId']                            = $inCount;
                $arElementos['boCondicao']                      = $_POST['boCondicao'];
                $arElementos['inCodClassificacaoVinculacaoTxt'] = $_POST['inCodClassificacaoVinculacao'];
                $arElementos['inNomClassificacaoVinculacaoTxt'] = $rsClassificacao->getCampo('descricao');
                $arElementos['inCodAssentamentoVinculacao']     = $_POST['stSiglaVinculado'];
                $arElementos['stSiglaVinculado']                = $_POST['stSiglaVinculado'];
                if ($_POST['boCondicao'] == 'a') {
                   $stCondicao = "Averbados";
                } elseif ($_POST['boCondicao'] == 'p') {
                   $stCondicao = "Protelados";
                }
                if ($_POST['boDia'] == false) {
                   $arElementos['inDiasIncidencia']            = $_POST['inDiasIncidencia'];
                } else {
                   $arElementos['inDiasIncidencia']            = '1/2';
                }
                $arElementos['inDiasVinculado']                 = $_POST['inDiasVinculado'];
                $arElementos['stCondicao']                      = $stCondicao;
                $arElementos['inCodFuncaoTxt']                  = $_POST['inCodFuncaoTxt'];
                if ($_REQUEST['stAcao'] == 'alterar') {
                   $arElementos['inCodCondicao']                   = $assentamentoVinculado[$arId]["inCodCondicao"];
                   $arElementos['stTimestamp']                     = $assentamentoVinculado[$arId]["stTimestamp"];
                   $arElementos['inCodAssentamento']               = $assentamentoVinculado[$arId]["inCodAssentamento"];
                   $arElementos['stTimestampAssentamento']         = $assentamentoVinculado[$arId]["stTimestampAssentamento"];
                   $arElementos['inCodAssentamentoVinculado']      = $assentamentoVinculado[$arId]["inCodAssentamentoVinculado"];
                   $arElementos['stTimestampAssentamentoVinculado']= $assentamentoVinculado[$arId]["stTimestampAssentamentoVinculado"];
                }
                $arTMP[] = $arElementos;
          } else {
                $arTMP[] = $assentamentoVinculado[$arId];
          }
       }
       Sessao::write('assentamentoVinculado', $arTMP);
       $stJs = montaAssentamentoVinculado();
       $stJs .= habilitaVinculacao();
       if ($boExecuta == true) {
          sistemaLegado::executaFrameOculto($stJs);
       } else {
          return $stJs;
       }
   } else {
       sistemaLegado::exibeAviso($obErro->getDescricao()," "," ");
   }
}

function montaAlterarAssentamentoVinculado($boExecuta=false)
{
    global $obRPessoalAssentamentoVinculado;
    global $rsAssentamento;
    global $rsClassificacao;
    global $rsFuncao;

    $id = $_REQUEST['inId'];
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( Sessao::read('assentamentoVinculado') );
    $rsAssentamento = new Recordset;
    $inCodClassificacaoVinculacao = $_GET['inCodClassificacaoVinculacao'];

    $obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->listarAssentamentoNaoVinculado( $rsAssentamento );
    $obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->obRPessoalClassificacaoAssentamento->listarClassificacao( $rsClassificacao );
    $obRPessoalAssentamentoVinculado->obRFuncao->listar( $rsFuncao );

    while (!$rsRecordSet->eof()) {
        if ($rsRecordSet->getCampo('inId') == $id) {
            if ( $rsRecordSet->getCampo('boCondicao') == 'p' ) {
                $stJs .= "f.boCondicao[0].checked                  = true;\n";
            } else {
                $stJs .= "f.boCondicao[1].checked                  = true;\n";
            }

            $stJs .= "f.inDiasIncidencia.value                 = '".$rsRecordSet->getCampo('inDiasIncidencia')."';\n";
            $stJs .= "f.inCodClassificacaoVinculacaoTxt.value  = '".$rsRecordSet->getCampo('inCodClassificacaoVinculacaoTxt')."';\n";
            $inContador = 1;
            $boControle = true;

            while ( !$rsClassificacao->eof() ) {
                if ( $rsClassificacao->getCampo('cod_classificacao')  == $rsRecordSet->getCampo('inCodClassificacaoVinculacaoTxt') ) {
                    $boControle = false;
                    break;
                }
                $inContador++;
                $rsClassificacao->proximo();
            }

            if ($boControle) {
                $inContador = 0;
            }

            $stJs .= "f.inCodClassificacaoVinculacao.options[$inContador].selected  = true;\n";
            $stJs .= preencheAssentamentoNaoVinculado(false,$_GET['inCodClassificacaoVinculacao'],'inCodAssentamentoVinculacao');
            $stJs .= "f.stSiglaVinculado.value  = '".$rsRecordSet->getCampo('inCodAssentamentoVinculacao')."';\n";
            $inContador = 1;
            $boControle = true;
            $rsAssentamento->setPrimeiroElemento();

            while ( !$rsAssentamento->eof() ) {
                if ( $rsAssentamento->getCampo('sigla_sem_espaco')  == $rsRecordSet->getCampo('inCodAssentamentoVinculacao') ) {
                    $boControle = false;
                    break;
                }
                $inContador++;
                $rsAssentamento->proximo();
            }
            
            if ($boControle) {
                $inContador = 0;
            }

            $stJs .= "f.inCodAssentamentoVinculacao.options[".$inContador."].selected  = true;\n";
            $arDiasVinculado = explode("/",$rsRecordSet->getCampo('inDiasVinculado'));
            $stJs .= "f.inDiasVinculado.value                  = '".$arDiasVinculado[0]."';\n";
            $stJs .= "f.inCodFuncao.value                      = '".$rsRecordSet->getCampo('inCodFuncaoTxt')."';\n";

            $inContador = 1;
            $boControle = true;
            while ( !$rsFuncao->eof() ) {
                if ( $rsFuncao->getCampo('cod_funcao')  == $rsRecordSet->getCampo('inCodFuncaoTxt') ) {
                    $boControle = false;
                    break;
                }
                $inContador++;
                $rsFuncao->proximo();
            }
            if ($boControle) {
                $inContador = 0;
            }
            $stJs .= "f.inCodFuncao.options[".$inContador."].selected  = true;\n";
            $stJs .= "f.inAssentamentoVinculado.value  = $id;\n";
        }
        $rsRecordSet->proximo();
    }
    if ($boExecuta == true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function preencheAssentamento($boExecuta=false,$stFiltro,$stCombo)
{
    global $obRPessoalAssentamentoVinculado;
    global $rsAssentamento;

    $stJs .= "limpaSelect(f.$stCombo,0); \n";
    $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

    if ($_REQUEST['stAcao'] == 'incluir') {
        $inCodClassificacao = $_POST[ $stFiltro ];
        $obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->obRPessoalClassificacaoAssentamento->setCodClassificacaoAssentamento( $inCodClassificacao );
        $obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->listarAssentamentoDisponivel( $rsAssentamento );
    } else {
        $assentamentoVinculado = Sessao::read('assentamentoVinculado');
        $inCodClassificacao = $assentamentoVinculado[$_REQUEST['inId']][""];
        $inCodClassificacao = $_REQUEST['inCodClassificacao'];
        $obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->obRPessoalClassificacaoAssentamento->setCodClassificacaoAssentamento( $inCodClassificacao );
        $obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->listarAssentamento( $rsAssentamento );
        $stSelecionado = $_REQUEST['stSigla']  ;
    }
    if ( $rsAssentamento->getNumLinhas() > 0 ) {
        $inCount = 0;
        while (!$rsAssentamento->eof()) {
            $inCount++;
            $inId   = $rsAssentamento->getCampo("sigla_sem_espaco");
            $stDesc = $rsAssentamento->getCampo("descricao");
            if ( trim($stSelecionado) == trim($inId) ) {
                $stSelected = 'selected';
            } else {
                $stSelected = '';
            }
            $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
            $rsAssentamento->proximo();
        }
    }

    if ($inCodClassificacao == '') {
      $stJs .= "f.stSigla.value = '';\n";
    }

    if ($boExecuta == true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function preencheAssentamentoNaoVinculado($boExecuta=false,$inCodClassificacaoVinculado,$stCombo)
{
    global $obRPessoalAssentamentoVinculado;
    global $rsAssentamento;
    $stJs .= "limpaSelect(f.$stCombo,0); \n";
    $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";
    if ($inCodClassificacaoVinculado != "") {
        $inCodClassificacao = $inCodClassificacaoVinculado;
        $rsAssentamento = new Recordset;
         if ($_REQUEST['stSigla'] != "" and $_REQUEST['stAcao'] != 'alterar') {
            $obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->obRPessoalClassificacaoAssentamento->setCodClassificacaoAssentamento( $inCodClassificacao );
            $obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->setSigla( $_REQUEST['stSigla'] );
            $obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->listarAssentamento( $rsAssentamento );
         }
        $obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->setCodAssentamento( $rsAssentamento->getCampo('cod_assentamento') );
        $obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->listarAssentamentoNaoVinculado( $rsAssentamento );
        
        $inCount = 0;
        while (!$rsAssentamento->eof()) {
            $inCount++;
            $inId   = $rsAssentamento->getCampo("sigla_sem_espaco");
            $stDesc = $rsAssentamento->getCampo("descricao");
            if ($stSelecionado == $inId) {
                $stSelected = 'selected';
            } else {
                $stSelected = '';
            }
            
            $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
            $rsAssentamento->proximo();
        }
    } else {
      $stJs .= "f.stSiglaVinculado.value = '';\n";
    }

    if ($boExecuta == true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

/*
Função: buscaFunção;
Objetivo: procurar na tabela um registro na tabela Funções (eu acho) por codigo e se
          encontra-lo preencher o busca inner com a descrição da função
data: 02/03/2006
autor Bruce
*/
function buscaFuncao($boExecuta = false)
{
    if ($_POST["inCodFuncao"]) {
        $arCodFuncao = explode('.',$_POST["inCodFuncao"]);
        $obRFuncao = new RFuncao;

        $obRFuncao->setCodFuncao                           ( $arCodFuncao[2] );
        $obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
        $obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );

        $obRFuncao->consultar();
        $stNomeFuncao = $obRFuncao->getNomeFuncao();

        if ( !empty($stNomeFuncao) ) {
            $stJs .= "d.getElementById('stFuncao').innerHTML = '".$stNomeFuncao."';\n";
        } else {
            $stJs .= "f.inCodFuncao.value = '';          \n";
            $stJs .= "f.inCodFuncao.focus();             \n";
            $stJs .= "d.getElementById('stFuncao').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Valor inválido. (".$_POST["inCodFuncao"].")','form','erro','".Sessao::getId()."');";

//            $stJs .= "alertaAviso('@Função informada não existe. (".$_POST["inCodFuncao"]."),'form','erro','".Sessao::getId()."');";
       }
    }
    if ($boExecuta == true) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

// Acoes por pagina

switch ($stCtrl) {
    case 'buscaFuncao':
        $stJs = buscaFuncao();
    break;
    case "incluirAssentamentoVinculado":
        $stJs = incluirAssentamentoVinculado();
    break;
    case "excluirAssentamentoVinculado":
        $stJs = excluirAssentamentoVinculado();
    break;
    case "alterarAssentamentoVinculado":
        $stJs = alterarAssentamentoVinculado();
    break;
    case "montaAlterar":
        $stJs .= montaAlterarAssentamentoVinculado();
        if ($_REQUEST['stAcao'] == 'alterar') {
//             $stJs .= preencheAssentamento(false,'inCodClassificacaoVinculacao','inCodAssentamentoVinculacao');
            $stJs .= desabilitaVinculacao();
        }
    break;
    case "preencheAssentamento1":
        $stJs = preencheAssentamento(false,'inCodClassificacao','inCodAssentamento');
    break;
    case "preencheAssentamento2":
        $stJs = preencheAssentamentoNaoVinculado(false,$_REQUEST['inCodClassificacaoVinculacao'],'inCodAssentamentoVinculacao');
    break;
    case "desabilitaCampos":
        $stJs = desabilitaCampos();
    break;
    case "alteracao":
        $stJs  = preencheAssentamento(false,'inCodClassificacao','inCodAssentamento');
        $stJs .= desabilitaCampos();
        $stJs .= montaAssentamentoVinculado();
    break;
    case "limpaForm":
        $stJs  = "f.reset();";
        $stJs .= habilitaCampos();
    break;
}

if($stJs)
    sistemaLegado::executaFrameOculto($stJs);

?>
