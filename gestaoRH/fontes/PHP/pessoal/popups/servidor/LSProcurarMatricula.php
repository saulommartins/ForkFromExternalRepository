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
* Arquivo instância para popup de Servidor
* Data de Criação: 30/08/2006

* @author Analista: Vandré
* @author Desenvolvedor: Vandré

$Id: LSProcurarMatricula.php 66538 2016-09-13 20:01:05Z carlos.silva $

Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarMatricula";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId();
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stFncJavaScript .= " function insereMatricula(num) {  \n";
$stFncJavaScript .= " var sNum;                        \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$_REQUEST["campoNum"].".value = sNum; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$_REQUEST["campoNum"].".focus(); \n";
//validacao adicionada para incluir e validar o digito verificador da acao de Alterar Servidor pelo numero de contrato que foi selecionado na lista da popUp
if (isset($_REQUEST['boValidaDigito']) ) {
    $stFncJavaScript .= " ajaxJavaScript( '".CAM_GRH_PES_PROCESSAMENTO."OCContratoDigitoVerificador.php?".Sessao::getId()."&inContrato='+sNum, 'verificaDigitoVerificador2' );\n";    
}else{
    $stFncJavaScript .= " window.close(); \n";    
}
$stFncJavaScript .= " }                   \n";

$obTPessoalContrato = new TPessoalContrato();
$stFiltro = "";
$stLink   = "&campoNum=".$_REQUEST["campoNum"];
$stLink  .= "&stSituacao=".$_REQUEST['stSituacao'];
$stLink  .= "&stTipo=".$_REQUEST["stTipo"];

$stLink .= "&stAcao=".$stAcao;
$rsLista = new RecordSet;
$stOrdem = " nom_cgm";

$stNome = $_REQUEST["campoNom"];

if ($_REQUEST["campoNom"]) {
        $stFiltro .= " AND lower(nom_cgm) like lower('".trim($stNome)."%')||'%' ";
}

//Analisa o tipo de listagem que deverá ser feita do case abaixo
if(Sessao::read('stTipoListagem') == 'geral' and
    ( Sessao::read('stAcaoFormulario') == 'alterar' or
      Sessao::read('stAcaoFormulario') == 'excluir') )
    {
    $_REQUEST["stTipo"] = 'geral';
}

//Filtros da sessão
$arFiltro = Sessao::read('filtroRelatorio');
//Define qual listagem deverá ser feita
switch ($_REQUEST["stTipo"]) {
    case "pensionista":
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalPensionista.class.php");
        $obTPessoalPensionista = new TPessoalPensionista();
    if ( !empty($stFiltro) ) {
           Sessao::write('filtroRelatorio', $stFiltro);
        } else {
            $stFiltro = Sessao::read('filtroRelatorio');
        }
        $obTPessoalPensionista->recuperaCgmDoRegistro($rsLista,$stFiltro);
        break;
    case "aposentado":
        if ( !empty($stFiltro) ) {
            Sessao::write('filtroRelatorio', $stFiltro);
        } else {
            $stFiltro = Sessao::read('filtroRelatorio');
        }
        $stFiltro .= "   AND EXISTS(  										    		   																																																															\n";
        $stFiltro .= "  	  SELECT aposentadoria.cod_contrato, max(aposentadoria.timestamp) AS timestamp FROM pessoal.aposentadoria																																														\n";
        $stFiltro .= "		  INNER JOIN (SELECT A.cod_contrato, max(A.timestamp) AS timestamp FROM pessoal.aposentadoria A WHERE A.cod_contrato = contrato.cod_contrato GROUP BY A.cod_contrato) AS max_aposentadoria 																									\n";
        $stFiltro .= "		  	ON max_aposentadoria.cod_contrato = aposentadoria.cod_contrato AND aposentadoria.timestamp = max_aposentadoria.timestamp 															  																																\n";
        $stFiltro .= "          AND NOT EXISTS ( SELECT AE.cod_contrato, max(AE.timestamp_aposentadoria) AS timestamp_aposentadoria FROM pessoal.aposentadoria_excluida AE WHERE AE.cod_contrato = max_aposentadoria.cod_contrato AND AE.timestamp_aposentadoria = max_aposentadoria.timestamp GROUP BY AE.cod_contrato)  \n";
        $stFiltro .= " 			GROUP BY aposentadoria.cod_contrato																																																																					\n";
        $stFiltro .= "   )													   																																																																		\n";
        $stFiltro .= " AND NOT EXISTS (SELECT 1 \n";
        $stFiltro .= "                   FROM pessoal.contrato_servidor_caso_causa\n";
        $stFiltro .= "                  WHERE contrato.cod_contrato = contrato_servidor_caso_causa.cod_contrato)\n";
        $obTPessoalContrato->recuperaCgmDoRegistroServidor($rsLista,$stFiltro,$stOrdem,"");
        break;
    case "ferias":
        if ( !empty($stFiltro) ) {
            Sessao::write('filtroRelatorio', $stFiltro);
        } else {
            $stFiltro = Sessao::read('filtroRelatorio');
        }
        $obTPessoalContrato->recuperaCgmDoRegistroServidor($rsLista,$stFiltro,$stOrdem,"");
        break;
    case "servidor":
        if ( !empty($stFiltro) ) {
            Sessao::write('filtroRelatorio', $stFiltro);
        } else {
            $stFiltro = Sessao::read('filtroRelatorio');
        }
        $stFiltro .= "   AND contrato.cod_contrato NOT IN ( 							                       			\n";
        $stFiltro .= " 		  SELECT contrato_pensionista.cod_contrato FROM pessoal.contrato_pensionista      			\n";
        $stFiltro .= "   )															 				           			\n";
        $stFiltro .= "   AND contrato.cod_contrato NOT IN(										    		   			\n";
        $stFiltro .= "  	  SELECT contrato_servidor_caso_causa.cod_contrato FROM pessoal.contrato_servidor_caso_causa\n";
        $stFiltro .= "   )   																	               			\n";
        $stFiltro .= "   AND contrato.cod_contrato NOT IN(										    		   			\n";
        $stFiltro .= "  	  SELECT cod_contrato from pessoal.aposentadoria											\n";
        $stFiltro .= "   )   																	               			\n";
        $obTPessoalContrato->recuperaCgmDoRegistroServidor($rsLista,$stFiltro,$stOrdem,"");
        break;
    case "geral":
        if ( !empty($stFiltro) ) {
           Sessao::write('filtroRelatorio', $stFiltro);
        } else {
           $stFiltro = Sessao::read('filtroRelatorio');
        }
    $obTPessoalContrato->recuperaCgmDoRegistroServidor($rsLista,$stFiltro,$stOrdem,"");
        break;
    case "geral_contrato_servidor":
        if ( !empty($stFiltro) ) {
           Sessao::write('filtroRelatorio', $stFiltro);
        } else {
           $stFiltro = Sessao::read('filtroRelatorio');
        }
        if ($_REQUEST['stSituacao'] == "rescindidos") {
            $stFiltro .= " AND EXISTS (SELECT 1                                                                     \n";
            $stFiltro .= "               FROM pessoal.contrato_servidor_caso_causa        \n";
            $stFiltro .= "              WHERE contrato.cod_contrato = contrato_servidor_caso_causa.cod_contrato )   \n";
        }
        if ($_REQUEST['stSituacao'] == "ativos") {
            $stFiltro .= " AND NOT EXISTS (SELECT 1                                                                     \n";
            $stFiltro .= "                   FROM pessoal.contrato_servidor_caso_causa        \n";
            $stFiltro .= "                  WHERE contrato.cod_contrato = contrato_servidor_caso_causa.cod_contrato )   \n";
        }
        $obTPessoalContrato->recuperaCgmDoRegistro($rsLista,$stFiltro,$stOrdem,"");
        break;
    case "desconto_externo_irrf":
        if ( !empty($stFiltro) ) {
           Sessao::write('filtroRelatorio', $stFiltro);
        } else {
           $stFiltro = Sessao::read('filtroRelatorio');
        }

        $stFiltro .= " 	  AND EXISTS (  																	  			\n";
        $stFiltro .= "	  	SELECT * FROM ( SELECT cod_contrato, max(timestamp) AS timestamp FROM folhapagamento.desconto_externo_irrf irrf WHERE cod_contrato = contrato.cod_contrato GROUP BY cod_contrato ) AS max_IRRF\n";
        $stFiltro .= "      WHERE NOT EXISTS ( SELECT cod_contrato, max(timestamp) AS timestamp FROM folhapagamento.desconto_externo_irrf_anulado WHERE cod_contrato = max_IRRF.cod_contrato AND timestamp = max_IRRF.timestamp GROUP BY cod_contrato ) \n";
        $stFiltro .= "	  ) 						   																	\n";
        $obTPessoalContrato->recuperaCgmDoRegistro($rsLista,$stFiltro,$stiOrdem,"");
        break;
    case "desconto_externo_previdencia":
        if ( !empty($stFiltro) ) {
           Sessao::write('filtroRelatorio', $stFiltro);
        } else {
           $stFiltro = Sessao::read('filtroRelatorio');
        }

        $stFiltro .= " 	  AND EXISTS (  																	  			\n";
        $stFiltro .= "	  	SELECT * FROM ( SELECT cod_contrato, max(timestamp) AS timestamp FROM folhapagamento.desconto_externo_previdencia WHERE cod_contrato = contrato.cod_contrato GROUP BY cod_contrato ) AS max_previdencia\n";
        $stFiltro .= "      WHERE NOT EXISTS ( SELECT cod_contrato, max(timestamp) AS timestamp FROM folhapagamento.desconto_externo_previdencia_anulado WHERE cod_contrato = max_previdencia.cod_contrato AND timestamp = max_previdencia.timestamp GROUP BY cod_contrato ) \n";
        $stFiltro .= "	  ) 						   																	\n";
        $obTPessoalContrato->recuperaCgmDoRegistro($rsLista,$stFiltro,$stiOrdem,"");
        break;
    case 'contrato_ativos':
        if ( !empty($stFiltro) ) {
           Sessao::write('filtroRelatorio', $stFiltro);
        } else {
           $stFiltro = Sessao::read('filtroRelatorio');
        }
        if ($_REQUEST['stSituacao'] == "ativos") {
            $stFiltro .= " AND NOT EXISTS (SELECT 1 
                                            FROM pessoal.contrato_servidor_caso_causa 
                                            WHERE contrato.cod_contrato = contrato_servidor_caso_causa.cod_contrato 
                            )     
                         AND situacao = 'Ativo' \n";
        }
        $obTPessoalContrato->recuperaCgmDoRegistro($rsLista,$stFiltro,$stOrdem,"");
        break;
    case 'rescindir_contrato':
        if ($_REQUEST['stSituacao'] == "ativos") {
            $stFiltro .= " AND NOT EXISTS (SELECT 1                                                                   \n";
            $stFiltro .= "                   FROM pessoal.contrato_servidor_caso_causa                                \n";
            $stFiltro .= "                  WHERE contrato.cod_contrato = contrato_servidor_caso_causa.cod_contrato ) \n";
            $stFiltro .= " AND recuperarSituacaoDoContratoLiteral(contrato.cod_contrato, 0, '') = 'Ativos'            \n";
        }
    break;
    
    default:
        if ( !empty($stFiltro) ) {
           Sessao::write('filtroRelatorio', $stFiltro);
        } else {
           $stFiltro = Sessao::read('filtroRelatorio');
        }
        if ($_REQUEST['stSituacao'] == "rescindidos") {
            $stFiltro .= " AND EXISTS (SELECT 1                                                                     \n";
            $stFiltro .= "               FROM pessoal.contrato_servidor_caso_causa        \n";
            $stFiltro .= "              WHERE contrato.cod_contrato = contrato_servidor_caso_causa.cod_contrato )   \n";
        }
        
        if ($_REQUEST['stSituacao'] == "ativos") {
            $stFiltro .= " AND NOT EXISTS (SELECT 1                                                                   \n";
            $stFiltro .= "                   FROM pessoal.contrato_servidor_caso_causa                                \n";
            $stFiltro .= "                  WHERE contrato.cod_contrato = contrato_servidor_caso_causa.cod_contrato ) \n";
        }

        $obTPessoalContrato->recuperaCgmDoRegistro($rsLista,$stFiltro,$stOrdem,"");
        break;
}


$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Matrícula");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("CGM / Nome");
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Situação");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "registro" );
$obLista->ultimoDado->setAlinhamento('DIREITA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "situacao" );
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->commitDado();
$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:window.close(); insereMatricula();" );
$obLista->ultimaAcao->addCampo("1","registro");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;

$obBtnCancelar = new Button;
$obBtnCancelar->setName                 ( 'cancelar'                                        );
$obBtnCancelar->setValue                ( 'Cancelar'                                        );
$obBtnCancelar->obEvento->setOnClick    ( "window.close();"                                 );

$obBtnFiltro = new Button;
$obBtnFiltro->setName                   ( 'filtro'                                          );
$obBtnFiltro->setValue                  ( 'Filtro'                                          );
$obBtnFiltro->obEvento->setOnClick      ( "Cancelar('".$pgFilt.$stLink."','telaPrincipal');");

$obFormulario->defineBarra              ( array( $obBtnCancelar,$obBtnFiltro ) , '', ''     );
$obFormulario->obJavaScript->addFuncao  ( $stFncJavaScript                                  );
$obFormulario->show();

?>
