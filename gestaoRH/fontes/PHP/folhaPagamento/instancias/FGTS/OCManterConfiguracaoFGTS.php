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
    * Oculto de Manter Configuração de FGTS
    * Data de Criação   : 10/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30766 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-26 10:16:48 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-04.05.42
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFGTS.class.php"                                     );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function validarCategoria()
{
    $obErro = new erro;
    if ( !$obErro->ocorreu() and $_POST['inCodCategoria'] == "" ) {
        $obErro->setDescricao("Campo Categoria da SEFIP inválido!()");
    }
    $arCategoria = Sessao::read("categoria");
    if ( !$obErro->ocorreu() and is_array($arCategoria) ) {
        foreach ($arCategoria as $arCategoria) {
            if ($arCategoria['inCodTxtCategoriaSefip'] == $_POST['inCodCategoria']) {
                $obErro->setDescricao("Esta Categoria da SEFIP já está incluída na lista!()");
                break;
            }
        }
    }

    return $obErro;
}

function incluirCategoria($boExecuta=false)
{
    global $_POST;
    $obErro = validarCategoria();
    if ( !$obErro->ocorreu() ) {

        $obRFolhaPagamentoFGTS = new RFolhaPagamentoFGTS;
        $obRFolhaPagamentoFGTS->addRPessoalCategoria();
        $obRFolhaPagamentoFGTS->roRPessoalCategoria->setCodCategoria( $_POST['inCodCategoria'] );
        $obRFolhaPagamentoFGTS->roRPessoalCategoria->listarCategoria($rsCategoria);
        $arCategoria = Sessao::read("categoria");
        $inId = ( is_array($arCategoria) ) ? $arCategoria[count($arCategoria)-1]['inId'] +1 : 1;
        $arTemp['inId']                     = $inId;
        $arTemp['inCodTxtCategoriaSefip']   = $_POST['inCodCategoria'];
        $arTemp['stDescricao']              = $rsCategoria->getCampo('descricao');
        $arCategoria[]= $arTemp;
        Sessao::write("categoria",$arCategoria);
        $stJs .= listarCategoria();
        $stJs .= limparCategoria();

    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');      \n";
        $stJs .= limparCategoria();
    }
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function alterarCategoria($boExecuta=false)
{
    $inIndex = 1;
    $arTemp = array();
    $arCategorias = Sessao::read("categoria");
    foreach ($arCategorias as $arCategoria) {
        $arCategoria['flValorDeposito']    = $_POST['flValorDeposito_'.$inIndex];
        $arCategoria['flValorRemuneracao'] = $_POST['flValorRemuneracao_'.$inIndex];
        $arTemp[] = $arCategoria;
        $inIndex++;
    }
    Sessao::write('categoria',$arTemp);
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function excluirCategoria($boExecuta=false)
{
    $arTemp = array();
    $arCategorias = Sessao::read("categoria");
    foreach ($arCategorias as $arCategoria) {
        if ($arCategoria['inId'] != $_GET['inId']) {
            $arTemp[] = $arCategoria;
        }
    }
    Sessao::write('categoria',$arTemp);
    $stJs .= listarCategoria();
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function limparCategoria($boExecuta=false)
{
    $stJs .= "f.inCodTxtCategoriaSefip.value = '';                  \n";
    $stJs .= "f.inCodCategoriaSefip.options[0].selected = true;     \n";
    Sessao::write('processo',"");
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function listarCategoria($boExecuta=false)
{
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array(Sessao::read('categoria')) ? Sessao::read('categoria') : array() );
    if ($rsRecordSet->getNumLinhas() > 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Categorias da SEFIP Cadastradas" );

        $obLista->setRecordSet( $rsRecordSet );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "SEFIP" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Alíquota para Depósito" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Alíquota sobre Contribuição Social" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stDescricao" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obTxtVlrDeposito = new Moeda;
        $obTxtVlrDeposito->setName       ( "flValorDeposito"                                        );
        $obTxtVlrDeposito->setValue      ( "flValorDeposito"                                         );
        $obTxtVlrDeposito->obEvento->setOnChange( "buscaValor('alterarCategoria');"                 );
        $obLista->addDadoComponente( $obTxtVlrDeposito );
        $obLista->ultimoDado->setCampo( "flValorDeposito" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDadoComponente();

        $obTxtVlrRemuneracao = new Moeda;
        $obTxtVlrRemuneracao->setName       ( "flValorRemuneracao"                                     );
        $obTxtVlrRemuneracao->setValue      ( "flValorRemuneracao"                                      );
        $obTxtVlrRemuneracao->obEvento->setOnChange( "buscaValor('alterarCategoria');"                 );
        $obLista->addDadoComponente( $obTxtVlrRemuneracao );
        $obLista->ultimoDado->setCampo( "flValorRemuneracao" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDadoComponente();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluiDado('excluirCategoria');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);

        while (!$rsRecordSet->eof()) {
            if ( $rsRecordSet->getCampo('flValorDeposito') != "" ) {
                $stJs2 .= "f.flValorDeposito_".$rsRecordSet->getCorrente().".value='".$rsRecordSet->getCampo('flValorDeposito')."';\n";
            }
            if ( $rsRecordSet->getCampo('flValorRemuneracao') != "" ) {
                $stJs2 .= "f.flValorRemuneracao_".$rsRecordSet->getCorrente().".value='".$rsRecordSet->getCampo('flValorRemuneracao')."';\n";
            }
            $rsRecordSet->proximo();
        }

    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnCategorias').innerHTML = '".$stHtml."';";
    $stJs .= $stJs2;
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function preencherEvento($boExecuta=false)
{
    $obRFolhaPagamentoFGTS = new RFolhaPagamentoFGTS;
    $obRFolhaPagamentoFGTS->addRFolhaPagamentoEvento();
    $inCodTipo = $_GET['inCodTipo'];
    $stNatureza= $_GET['stNatureza'];
    $obRFolhaPagamentoFGTS->roRFolhaPagamentoEvento->setNatureza($stNatureza);
    $obRFolhaPagamentoFGTS->roRFolhaPagamentoEvento->setCodigo($_POST['inCodigoFGTS'.$inCodTipo]);
    $obRFolhaPagamentoFGTS->roRFolhaPagamentoEvento->setEventoSistema( "true" );
    $obRFolhaPagamentoFGTS->roRFolhaPagamentoEvento->listarEvento($rsEvento);
    $stInner = "inCampoInnerFGTS".$inCodTipo;
    if ( $rsEvento->getNumLinhas() > 0 ) {
        $stJs .= "f.inCodigoFGTS".$inCodTipo.".value = '".$rsEvento->getCampo('codigo')."';             \n";
        $stJs .= "d.getElementById('".$stInner."').innerHTML = '".$rsEvento->getCampo('descricao')."';  \n";
    } else {
        $stJs .= "f.inCodigoFGTS".$inCodTipo.".value = '';                  \n";
        $stJs .= "d.getElementById('".$stInner."').innerHTML = '&nbsp;';    \n";
    }
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function preencherInnerEventos($boExecuta=false)
{
    $obRFolhaPagamentoFGTS = new RFolhaPagamentoFGTS;
    $obRFolhaPagamentoFGTS->setCodFGTS($_GET['inCodFGTS']);
    $obRFolhaPagamentoFGTS->addRFolhaPagamentoEvento();
    $obRFolhaPagamentoFGTS->roRFolhaPagamentoFGTSEvento->listarFGTSEvento($rsEventoFGTS);
    while (!$rsEventoFGTS->eof()) {
        $stInner          = "inCampoInnerFGTS".$rsEventoFGTS->getCampo('cod_tipo');
        $stJs .= "d.getElementById('".$stInner."').innerHTML = '".$rsEventoFGTS->getCampo('descricao')."';  \n";
        $rsEventoFGTS->proximo();
    }
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function limparForm($boExecuta=false)
{
    $obRFolhaPagamentoFGTS = new RFolhaPagamentoFGTS;
    $obRFolhaPagamentoFGTS->addRFolhaPagamentoEvento();
    $obRFolhaPagamentoFGTS->roRFolhaPagamentoEventoFGTS->listarEventoFGTS($rsTiposEvento);
    #sessao->transf = array();
    $stJs .= limparCID();
    $stJs .= limparCategoria();
    $stJs .= listarCID();
    $stJs .= listarCategoria();
    $stJs .= "f.flValorDependente.value = '';   \n";
    $stJs .= "f.flValorLimite.value = '';       \n";
    while (!$rsTiposEvento->eof()) {
        $stValue          = "inCodigoFGTS".$rsTiposEvento->getCampo('cod_tipo');
        $stInner          = "inCampoInnerFGTS".$rsTiposEvento->getCampo('cod_tipo');
        $stJs .= "f.".$stValue.".value = '';                                \n";
        $stJs .= "d.getElementById('".$stInner."').innerHTML = '&nbsp;';    \n";
        $rsTiposEvento->proximo();
    }
    $stJs .= "f.dtVigencia.value = '';          \n";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function processarFormAlteracao($boExecuta=false)
{
    $stJs .= preencherInnerEventos();
    $stJs .= listarCategoria();
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function submeterForm($boExecuta=false)
{
    $obErro = new erro;
    $stMensagem = "";
    if ( !is_array(Sessao::read('categoria')) ) {
        $obErro->setDescricao("Deve haver pelo menos uma categoria inserida na lista!()");
    }
    foreach ($_POST as $stChave=>$stValue) {
        if ( strpos($stChave,'flValorDeposito') === 0 and $stValue == "" ) {
            $inLinha = substr($stChave,16,strlen($stChave));
            $stMensagem .= "@Campo Alíquota para Depósito da linha ".$inLinha." da lista inválido!()";
        }
        if ( strpos($stChave,'flValorRemuneracao') === 0 and $stValue == "" ) {
            $inLinha = substr($stChave,19,strlen($stChave));
            $stMensagem .= "@Campo Alíquota sobre Remuneração da linha ".$inLinha." da lista inválido!()";
        }

    }
    if ($stMensagem != "") {
        $obErro->setDescricao($stMensagem);
    }
    if ( !$obErro->ocorreu() ) {
        $stJs .= "parent.frames[2].Salvar();";
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');      \n";
    }
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

/***
 Função: Busca Categoria
 Objeto: fazer a busca de categoria de sefip por codigo para preencher a busca na tela de Inclusão/Alteração
 Autor : Bruce Sena
 Data  : 24/03/2006
*/
Function buscaCategoria ( $boExecuta = true) {
    global $_POST;

    $inCodCategoria = $_POST['inCodCategoria'];

    if (!$inCodCategoria) {
         $stJs .= "d.getElementById('stCategoria').innerHTML = '&nbsp;'  ;";
    } else {

         include_once ( CAM_GRH_PES_NEGOCIO. "RPessoalCategoria.class.php" );

         $obRCategoria = new RPessoalCategoria;
         $obRCategoria->setCodCategoria ( $inCodCategoria );

         $obRsCategoria = new RecordSet;
         $obRCategoria->listarCategoria ( $obRsCategoria );

         if ( $obRsCategoria->getNumLinhas() > 0 ) {
             $stJs .= "f.inCodCategoria.value ='". $inCodCategoria  ."';             \n";
             $stJs .= "d.getElementById('stCategoria').innerHTML = '".$obRsCategoria->getCampo('descricao') ."';";
         } else {
             $stJs .= "f.inCodCategoria.value ='';             \n";
             $stJs .= "d.getElementById('stCategoria').innerHTML = '&nbsp;' ;";
             sistemaLegado::exibeAviso("O código digitado não existe(".$inCodCategoria. ")","","");
         }
    }
    sistemaLegado::executaFrameOculto($stJs);
}//Function buscaCategoria ( $boExecuta = true) {

function validarVigencia()
{
    if ( sistemaLegado::comparaDatas(Sessao::write('dtVigencia'),$_POST['dtVigencia']) ) {
        $stMensagem = "A vigência deve ser posterior a ".Sessao::read('dtVigencia');
        $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
        $stJs .= "f.dtVigencia.value = '".Sessao::read('dtVigencia')."';";
    }

    return $stJs;
}

switch ($stCtrl) {
    case 'preencherEvento':
        $stJs .= preencherEvento();
    break;
    case 'incluirCategoria':
        $stJs .= incluirCategoria();
    break;
    case 'alterarCategoria':
        $stJs .= alterarCategoria();
    break;
    case 'excluirCategoria':
        $stJs .= excluirCategoria();
    break;
    case 'limparCategoria':
        $stJs .= limparCategoria();
    break;
    case 'montaAlterarCategoria':
        $stJs .= montaAlterarCategoria();
    break;
    case 'limparForm':
        $stJs .= limparForm();
    break;
    case 'submeterForm':
        $stJs .= submeterForm();
    break;
    case 'buscaCategoria':
        $stJs = buscaCategoria();
    break;
    case 'validarVigencia':
        $stJs .= validarVigencia();
    break;

}
if($stJs)
    SistemaLegado::executaFrameOculto($stJs);

?>
