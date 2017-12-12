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
    * Oculto de Inicializacao Vale-Tranporte Servidor
    * Data de Criação: 01/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Eduardo Antunez

    * @ignore

    $Revision: 30931 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.06.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                                             );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"                                              );
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioGrupoConcessao.class.php"                                     );
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioConcessaoValeTransporte.class.php"                            );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                          );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                       );

function geraSpan()
{
    $obFormulario = new Formulario;
    switch ($_POST['stInicializacao']) {

        case 'contrato':
            $obFiltroContrato = new IFiltroContrato();
            $obFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->setNull( false );
            $obFiltroContrato->geraFormulario( $obFormulario );
        break;

        case 'cgm':
            $obFiltroCGMContrato = new IFiltroCGMContrato();

            $obFiltroCGMContrato->setTipoContrato('vigente');
            $obFiltroCGMContrato->geraFormulario( $obFormulario );
        break;

        case 'grupo':
            $obTxtCodGrupo = new TextBox;
            $obTxtCodGrupo->setRotulo                   ( "Grupo"                                                   );
            $obTxtCodGrupo->setName                     ( "inCodGrupo"                                              );
            $obTxtCodGrupo->setValue                    ( ""                                                        );
            $obTxtCodGrupo->setMaxLength                ( 10                                                        );
            $obTxtCodGrupo->setSize                     ( 10                                                        );
            $obTxtCodGrupo->setInteiro                  ( true                                                      );

            $obRBeneficioGrupoConcessao = new RBeneficioGrupoConcessao;
            $obRBeneficioGrupoConcessao->listarGrupoConcessao($rsGrupo);

            $obCmbGrupo = new Select;
            $obCmbGrupo->setName                        ( "stGrupo"                                                 );
            $obCmbGrupo->setStyle                       ( "width: 250px"                                            );
            $obCmbGrupo->setRotulo                      ( "Grupos"                                                  );
            $obCmbGrupo->setValue                       ( ""                                                        );
            $obCmbGrupo->setNull                        ( false                                                     );
            $obCmbGrupo->addOption                      ( "", "Selecione"                                           );
            $obCmbGrupo->setCampoID                     ( "[cod_grupo]"                                             );
            $obCmbGrupo->setCampoDesc                   ( "[descricao]"                                             );
            $obCmbGrupo->preencheCombo                  ( $rsGrupo                                                  );

            $obFormulario->addTitulo                    ( "Inicialização por Grupo"                                 );
            $obFormulario->addComponenteComposto        ( $obTxtCodGrupo , $obCmbGrupo                              );
        break;
    }
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnInicializacao').innerHTML ='".$obFormulario->getHTML()."';\n" ;
    $stJs .= "f.stOpcaoEval.value  = '".$stEval."';\n";

    return $stJs;
}

//Preenche a combo de meses com o mês atual e o mês posterior
function preencheMes()
{
    include_once (CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoPeriodoMovimentacao.class.php');

    $obRBeneficioConcessaoValeTransporte = new RBeneficioConcessaoValeTransporte;
    $obRBeneficioConcessaoValeTransporte->listarMes($rsMeses);
    $arMeses = $rsMeses->getElementos();

    $obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
    $obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);
    $stDataUltimaMovimentacao = $rsUltimaMovimentacao->getCampo( 'dt_final' );
    $stDataUltimaMovimentacao = explode( '/', $stDataUltimaMovimentacao );

    $inMesAtual = $stDataUltimaMovimentacao[1];
    $inAnoAtual = $stDataUltimaMovimentacao[2];

    $inAno      = (int) $_POST['inAno'];
    if ($inAno == $inAnoAtual) {
        $stJs .= "f.stMes[1] = new Option('".$arMeses[$inMesAtual-1]['descricao']."','".$inMesAtual."'); \n";
        $inMesProx  = $inMesAtual + 1;
        if ($inMesProx <= 12)
            $stJs .= "f.stMes[2] = new Option('".$arMeses[$inMesProx-1]['descricao'] ."','".$inMesProx ."'); \n";
    } elseif (($inAno == $inAnoAtual+1) && ($inMesAtual == 12)) {
        $stJs .= "limpaSelect(f.stMes,0); \n";
        $stJs .= "f.stMes[0] = new Option('Selecione','','selected');\n";
        $stJs .= "f.stMes[1] = new Option('".$arMeses[0]['descricao']."','1'); \n";
    } else {
        $stJs .= "limpaSelect(f.stMes,0);";
        $stJs .= "f.stMes[0] = new Option('Selecione','','selected');\n";
    }

    return $stJs;

}

function geraSpanLista()
{
    $rsLista = new RecordSet;
    $arSessaoContratosInicializados = Sessao::read('arContratosInicializados');
    $arSessaoGruposInicializados    = Sessao::read('arGruposInicializados');

    if ($_REQUEST['stConcessao'] == 'contrato') {

        if (is_array($arSessaoContratosInicializados) && sizeof($arSessaoContratosInicializados) > 0)
            $rsLista->preenche($arSessaoContratosInicializados);

        $obLista = new Lista;
        $obLista->setTitulo( "Concessões Inicializadas por Matrícula" );
        $obLista->setRecordSet( $rsLista );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Matrícula");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Concessão");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Tipo");
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Mês");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Ano");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Quantidade");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "contrato" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "concessao" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "tipo" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "mes" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "ano" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "quantidade" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);

        $stJs .= "d.getElementById('spnConcessao').innerHTML = '".$stHtml."';";

    } elseif ($_REQUEST['stConcessao'] == 'grupo') {

        if (is_array($arSessaoGruposInicializados) && sizeof($arSessaoGruposInicializados) > 0)
            $rsLista->preenche($arSessaoGruposInicializados);

        $obLista = new Lista;
        $obLista->setTitulo( "Concessões Inicializadas por Grupo" );
        $obLista->setRecordSet( $rsLista );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Grupo");
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Concessão");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Tipo");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Mês");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Ano");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Quantidade");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "grupo" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "concessao" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "tipo" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "mes" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "ano" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "quantidade" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);

        $stJs .= "d.getElementById('spnConcessao').innerHTML = '".$stHtml."';";

    }

    return $stJs;
}

switch ($_REQUEST["stCtrl"]) {
    case "iniciaFormulario":
        $stJs .= geraSpan();
        $stJs .= preencheMes();
    break;
    case "geraSpan":
        $stJs .= geraSpan();
    break;
    case "preencheMes":
        $stJs .= preencheMes();
    break;
    case "geraSpanLista":
        $stJs .= geraSpanLista();
    break;
}

if ($stJs) {
    SistemaLegado::ExecutaFrameOculto($stJs);
}

?>
