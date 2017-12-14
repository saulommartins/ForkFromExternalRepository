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
  * Página Oculta de Configuração de Receita/Despesa Extra por Fonte de Recurso
  * Data de Criação: 05/11/2015

  * @author Analista: Valtair Santos
  * @author Desenvolvedor: Franver Sarmento de Moraes
  * @ignore
  *
  * $Id: OCManterReceitaDespesaExtraRecurso.php 63906 2015-11-05 12:31:01Z franver $
  * $Revision: 63906 $
  * $Author: franver $
  * $Date: 2015-11-05 10:31:01 -0200 (Thu, 05 Nov 2015) $
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoConta.class.php';
require_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeConfiguracaoContasExtras.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterReceitaDespesaExtraRecurso";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

function consultarConfiguracao(){
    
    $boIndicadorContasExtrasRecursos = $_REQUEST['boIndicadorSaldoContasRecurso'];//SistemaLegado::pegaConfiguracao('indicador_contas_extras_recurso',9,Sessao::getExercicio(),$boTransacao);

    if($boIndicadorContasExtrasRecursos == 't'){

        $rsContas = new RecordSet();
        $obTContabilidadeConfiguracaoContasExtras = new TContabilidadeConfiguracaoContasExtras();
        $obTContabilidadeConfiguracaoContasExtras->setDado('exercicio', Sessao::getExercicio());
        $obErro = $obTContabilidadeConfiguracaoContasExtras->recuperaRelacionamento($rsContas, $stCondicao, $stOrdem, $boTransacao);

        while(!$rsContas->eof())
        {
            $inNovo = ($rsContas->getCorrente()-1);
            
            $arContas[$inNovo]['inId']           = $inNovo;
            $arContas[$inNovo]['exercicio']      = $rsContas->getCampo('exercicio');
            $arContas[$inNovo]['cod_conta']      = $rsContas->getCampo('cod_conta');
            $arContas[$inNovo]['nom_conta']      = $rsContas->getCampo('nom_conta');
            $arContas[$inNovo]['cod_estrutural'] = $rsContas->getCampo('cod_estrutural');
            
            $rsContas->proximo();
        }
        $rsContas->setPrimeiroElemento();
        
        Sessao::write("arContas", $arContas);
    }
    
    return $stJs;
}


function montaCampoConta(){
    
    $obHdnEscrituracao = new Hidden();
    $obHdnEscrituracao->setName ("stEscrituracao");
    $obHdnEscrituracao->setId   ("stEscrituracao");
    $obHdnEscrituracao->setValue("sintetica");
    
    require_once CAM_GF_CONT_COMPONENTES.'IPopUpEstruturalPlano.class.php';
    
    $obIIntervaloPopUpEstruturalPlano = new IPopUpEstruturalPlano();
    $obIIntervaloPopUpEstruturalPlano->setId("stDescEstruturalConta");
    $obIIntervaloPopUpEstruturalPlano->setTipoEscrituracao('sintetica');
    $obIIntervaloPopUpEstruturalPlano->setObrigatorioBarra(true);
    $obIIntervaloPopUpEstruturalPlano->obCampoCod->setNull(false);
    $obIIntervaloPopUpEstruturalPlano->obCampoCod->setId("stCodEstruturalConta");

    $obSpnListaContas = new Span();
    $obSpnListaContas->setId("spnListaContas");
    
    $obBtnIncluir = new Button();
    $obBtnIncluir->setValue("Incluir");
    $obBtnIncluir->obEvento->setOnClick("if( ValidaContas() ) { montaParametrosGET('incluirContas') }");

    $obFormulario = new Formulario();
    $obFormulario->addHidden    ( $obHdnEscrituracao );
    $obFormulario->addTitulo    ( "Contas de Receita/Despesa Extra por Fonte de Recurso" );
    $obFormulario->addComponente( $obIIntervaloPopUpEstruturalPlano );
    $obFormulario->defineBarra  ( array($obBtnIncluir) );
    $obFormulario->addSpan      ( $obSpnListaContas );

    $obFormulario->montaInnerHTML();

    $stHTML = $obFormulario->getHTML();
    
    $stJs .= " jQuery(\"#spnContaReceitaDespesaExtra\").html('".$stHTML."'); \n";
    
    return $stJs;
}

function montaContas( $boIndicadorSaldoContasRecurso )
{
    if($boIndicadorSaldoContasRecurso == "t"){
        $stJs .= montaCampoConta();
        $stJs .= gerarSpanContas(); 
    } else {
        $stJs .= "jQuery('#spnContaReceitaDespesaExtra').html(\"\");\n";
    }
    
    return $stJs;
}

function incluirContas()
{
    $stJs = processarContas('incluir');
    
    return $stJs;
}

function processarContas($stAcao){

    $arContas = Sessao::read('arContas');

    if(!is_array($arContas)){
        $arContas = array();
    }
    
    if ( validaInclusaoListaContas($arContas) ) {
        $rsConta = new RecordSet();
        
        $stCondicao = " AND plano_conta.escrituracao = '".$_REQUEST['stEscrituracao']."' \n";
            
        $obTContabilidadePlanoConta = new TContabilidadePlanoConta();
        $obTContabilidadePlanoConta->setDado('exercicio'     , Sessao::getExercicio()      );
        $obTContabilidadePlanoConta->setDado('cod_estrutural', $_REQUEST['stCodEstrutural']);
        
        $obTContabilidadePlanoConta->recuperaContaSintetica($rsConta, $stCondicao, $stOrdem, $boTransacao);
        
        $inNovo = count($arContas);
        $arContas[$inNovo]['inId']           = $inNovo;
        $arContas[$inNovo]['exercicio']      = Sessao::getExercicio();
        $arContas[$inNovo]['cod_conta']      = $rsConta->getCampo('cod_conta');
        $arContas[$inNovo]['nom_conta']      = $rsConta->getCampo('nom_conta');
        $arContas[$inNovo]['cod_estrutural'] = $rsConta->getCampo('cod_estrutural');
        
        Sessao::write("arContas", $arContas);
        
        $stJs .= gerarSpanContas();
        $stJs .= limparFormConta();
    } else {
        $stJs .= "alertaAviso('Essa conta já foi incluida na lista.','form','aviso','".Sessao::getId()."');\n";
    }
    return $stJs;
}

function validaInclusaoListaContas($arContas)
{
    foreach ($arContas as $key => $value) {
        if ( $_REQUEST['stCodEstrutural'] == $value['cod_estrutural'] && Sessao::getExercicio() == $value['exercicio']) {
            return false;
        }
    }
    return true;
}


function gerarSpanContas(){
    $rsRecordSet = new recordset();
    $rsRecordSet->preenche(Sessao::read("arContas"));

    $rsRecordSet->ordena('cod_estrutural', "ASC", SORT_STRING);
    
    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Lista de Contas de Despesa/Receita Extra" );
    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Código Estrutural" );
    $obLista->ultimoCabecalho->setWidth( 13 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Nome da Conta" );
    $obLista->ultimoCabecalho->setWidth( 73 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Ação" );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_estrutural]");
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[nom_conta]");
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirContas');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaInnerHtml();
    $stHtml = $obLista->getHTML();
    $stJs .= "jQuery('#spnListaContas').html('".$stHtml."');";

    return $stJs;
}

function limparFormConta(){

    $stJs .= "jQuery('#stCodEstruturalConta').val('');";
    $stJs .= "jQuery('#HdnstCodEstrutural').val('');";
    $stJs .= "jQuery('#stDescEstruturalConta').html('');";
    
    return $stJs;
}

function excluirContas()
{
    $arContas = Sessao::read("arContas");

    $arTemp = array();
    foreach ($arContas as $arConta) {
        if ($arConta["inId"] != $_GET["inId"]) {
            $arTemp[] = $arConta;
        }
    }
    Sessao::write('arContas',$arTemp);
    $stJs .= gerarSpanContas();

    return $stJs;
}


$stSctrl = $request->get('stCtrl');
switch ($stSctrl) {
    case 'consultarConfiguracao':
        $stJs .= consultarConfiguracao();
        break;
    case 'montaContas':
        $boValidaRadio = $request->get('boIndicadorSaldoContasRecurso');
        $stJs .= montaContas( $boValidaRadio );
        break;
    case 'incluirContas':
        $stJs .= incluirContas();
        break;
    case 'excluirContas':
        $stJs .= excluirContas();
        break;
}

echo $stJs;

?>