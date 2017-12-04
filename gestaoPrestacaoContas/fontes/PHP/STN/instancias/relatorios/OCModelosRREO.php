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
    * Página de Oculto para Relatório de modelos
    * Data de Criação   : 25/01/2006

    $Id: OCModelosRREO.php 61067 2014-12-03 18:56:31Z carlos.silva $

    * @author Tonismar Régis Bernardo

    * @ignore

    * Casos de uso : uc-06.02.11
                     uc-06.02.12
                     uc-06.02.13
                     uc-06.02.15
                     uc-06.02.17
                     uc-06.02.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_FW_PDF."RRelatorio.class.php" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

$stCtrl = $request->get('stCtrl');
$pgGera = $request->get('pgGera');

$stPrograma = "ModelosRREO";

$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$arFiltro = Sessao::read('filtroRelatorio');
$stJs = "";
switch( $arFiltro["stAcao"] ):
    case "anexo3":
        $obRRelatorio = new RRelatorio;
        $obRRelatorio->executaFrameOculto( $pgGera );
    break;
endswitch;

switch( $stCtrl ):

    case "preencheSpan":
        
    switch ($request->get('stTipoRelatorio')) {
        case "Bimestre":
            $obCmbBimestre = new Select;
            $obCmbBimestre->setRotulo        ( "Bimestre"    );
            $obCmbBimestre->setName          ( "cmbBimestre" );
            
            $stRelatorioAcao = Sessao::read('relatorio');
            
            switch ($stRelatorioAcao) {
                
                case 'anexo11':
                case 'anexo9novo':
                    $obCmbBimestre->addOption ( "6", "6º Bimestre" );
                break;
                case 'anexo14':
                    $obCmbBimestre->addOption ( "6", "6º Bimestre" );
                break;
                default:
                    $obCmbBimestre->addOption ( "1", "1º Bimestre" );
                    $obCmbBimestre->addOption ( "2", "2º Bimestre" );
                    $obCmbBimestre->addOption ( "3", "3º Bimestre" );
                    $obCmbBimestre->addOption ( "4", "4º Bimestre" );
                    $obCmbBimestre->addOption ( "5", "5º Bimestre" );
                    $obCmbBimestre->addOption ( "6", "6º Bimestre" );
                break;
            }
            
            $obCmbBimestre->setNull          ( false                  );
            $obCmbBimestre->setStyle         ( "width: 220px"        );
            
            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obCmbBimestre );
            
            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();
            
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );
            
            $stJs = "d.getElementById('spnTipoRelatorio').innerHTML = '".$stHTML."'";
        break;
    
        case "Mes":
            $obCmbMes = new Mes;
            $obCmbMes->setRotulo ( "*Mês" );
            $obCmbMes->setId     ( "cmbMes" );
            $obCmbMes->setName   ( "cmbMes" );
            
            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obCmbMes );
            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();
            
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );
            
            $stJs = "d.getElementById('spnTipoRelatorio').innerHTML = '".$stHTML."'";
        break;
    
        case "Quadrimestre":
            $obCmbQuadrimestre = new Select;
            $obCmbQuadrimestre->setRotulo        ( "*Quadrimestre"           );
            $obCmbQuadrimestre->setName          ( "cmbQuadrimestre"         );
            $obCmbQuadrimestre->addOption        ( "", "Selecione"           );
            $obCmbQuadrimestre->addOption        ( "1", "1º Quadrimestre"    );
            $obCmbQuadrimestre->addOption        ( "2", "2º Quadrimestre"    );
            $obCmbQuadrimestre->addOption        ( "3", "3º Quadrimestre"    );
            $obCmbQuadrimestre->setNull          ( true                      );
            $obCmbQuadrimestre->setStyle         ( "width: 220px"            );
            
            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obCmbQuadrimestre );
            
            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();
            
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );
            
            $stJs = "d.getElementById('spnTipoRelatorio').innerHTML = '".$stHTML."'";
        break;
    
        case "Semestre":
            $obCmbSemestre = new Select;
            $obCmbSemestre->setRotulo        ( "*Semestre"           );
            $obCmbSemestre->setName          ( "cmbSemestre"         );
            $obCmbSemestre->addOption        ( "", "Selecione"       );
            $obCmbSemestre->addOption        ( "1", "1º Semestre"    );
            $obCmbSemestre->addOption        ( "2", "2º Semestre"    );
            $obCmbSemestre->setNull          ( true                  );
            $obCmbSemestre->setStyle         ( "width: 220px"        );
            
            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obCmbSemestre );
            
            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();
            
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );
            
            $stJs = "d.getElementById('spnTipoRelatorio').innerHTML = '".$stHTML."'";
        break;
    
        default:
            $stJs = "d.getElementById('spnTipoRelatorio').innerHTML = ''";
        break;
    }
    break;

    case 'Valida' :
        $arValores = Sessao::read('arValores');
        if ( count( $arValores ) == 0 ) {
            $stMensagem = 'Nenhum recurso informado';
        }
        if (!$stMensagem) {
            $stJs .= "document.frm.action='OCGeraRREOAnexo14.php?Sessao::getId()'; \n";
            $stJs .= "document.frm.submit();                       \n";
        } else {
            $stJs .= "alertaAviso('".$stMensagem."!','frm','alerta','".Sessao::getId()."'); \n";
        }

    break;

    case 'ValidaEntidade':
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
        $obTOrcamentoEntidade = new TOrcamentoEntidade();
        $obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
        $obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );
        $conta = 0;
        while (!$rsEntidade->eof()) {
            $conta = $conta + 1;
            if ( strpos( strtolower($rsEntidade->getCampo('nom_cgm')),'prefeitura') > -1 ) {
                $stNomeEntidade = $rsEntidade->getCampo('nom_cgm');
            }
            $rsEntidade->proximo();
        }

        if ($conta > 1) {
            if ($stNomeEntidade == '') {
                $stMensagem = 'É obrigatório selecionar a entidade Prefeitura, quando selecionar mais de uma entidade!';
                $stJs .= "alertaAviso('".$stMensagem."!','frm','erro','".Sessao::getId()."'); \n";
            } else {
                $stJs .= "document.frm.action='".$pgGera."?".http_build_query($_REQUEST)."'; \n";
                $stJs .= "document.frm.submit();                       \n";
            }
        } else {

            $stJs .= "document.frm.action='".$pgGera."?".http_build_query($_REQUEST)."'; \n";
            $stJs .= "document.frm.submit();                       \n";
        }
    break;

    case 'incluirLista' :
        $arValores = Sessao::read('arValores');
        if ($_REQUEST['inCodRecurso'] == '') {
            $stMensagem = 'Preencha o campo Recurso';
        } else {
            if ( count( $arValores ) > 0 ) {
                foreach ($arValores AS $arTemp) {
                    if ($arTemp['inCodRecurso'] == $_REQUEST['inCodRecurso']) {
                        $stMensagem = 'Este item já está na lista';
                        break;
                    }
                }
            }
        }
        if (!$stMensagem) {
            $inCount = sizeof($arValores);
            $arValores[$inCount]['id'                  ] = $inCount + 1;
            $arValores[$inCount]['inCodRecurso'    ] = str_replace(',','.',str_replace('.','',$_REQUEST[ "inCodRecurso"     ]));
            $arValores[$inCount]['stDescricaoRecurso'    ] = str_replace(',','.',str_replace('.','',$_REQUEST[ "stDescricaoRecurso"     ]));

            $stJs .= "$('inCodRecurso').value = '';";
            $stJs .= "$('stDescricaoRecurso').innerHTML = '&nbsp;';";
            Sessao::write('arValores',$arValores);
            $stJs .= montaLista( $arValores );
        } else {
            $stJs .= "alertaAviso('".$stMensagem."!','frm','erro','".Sessao::getId()."'); \n";
        }

    break;

    case 'excluirLista' :
        $arValores = Sessao::read('arValores');
        foreach ($arValores AS $arTemp) {
            if ($arTemp['inCodRecurso'] != $_REQUEST['inCodRecurso']) {
                $arAux[] = $arTemp;
            }
        }
        $arValores = $arAux;
        Sessao::write('arValores',$arValores);
        $stJs .= montaLista( $arValores );

    break;

    case 'montaLista' :
        include CAM_GPC_STN_MAPEAMENTO . 'TSTNRecursoRREOAnexo14.class.php';
        $obTSTNRecursoRREOAnexo14 = new TSTNRecursoRREOAnexo14();
        $obTSTNRecursoRREOAnexo14->setDado('exercicio',Sessao::getExercicio());
        $obTSTNRecursoRREOAnexo14->recuperaRelacionamento($rsRecurso);
        $i = 0;
        while (!$rsRecurso->eof()) {
            $arValores[$i]['id'                ] = $i;
            $arValores[$i]['inCodRecurso'      ] = $rsRecurso->getCampo('cod_recurso');
            $arValores[$i]['stDescricaoRecurso'] = $rsRecurso->getCampo('nom_recurso');
            $rsRecurso->proximo();
            $i++;
        }
        Sessao::write('arValores',$arValores);
        $stJs .= montaLista( $arValores, ($request->get('boReadOnly') == 'true' ) ? true : false );

    break;

endswitch;

function montaLista($arItens, $boReadOnly = false)
{
    global $pgOcul;

    if ( !is_array($arItens) ) {
        $arItens = array();
    }

    $rsItens = new RecordSet();
    $rsItens->preenche( $arItens );

    $obTable = new Table();
    $obTable->setRecordset( $rsItens );
    $obTable->setSummary( 'Lista de Recursos' );

    $obTable->Head->addCabecalho( 'Código', 10 );
    $obTable->Head->addCabecalho( 'Descrição',60 );

    $obTable->Body->addCampo( 'inCodRecurso', 'C' );
    $obTable->Body->addCampo( 'stDescricaoRecurso', 'E' );

    if (!$boReadOnly) {
        $obTable->Body->addAcao( 'excluir', "JavaScript:ajaxJavaScript(  '".CAM_GPC_STN_INSTANCIAS."relatorios/".$pgOcul."?".Sessao::getId()."&inCodRecurso=%s', 'excluirLista' );", array( 'inCodRecurso' ) );
    }

    $obTable->montaHTML( true );
    if ( $rsItens->getNumLinhas() > 0 ) {
        return "$('spnLista').innerHTML = '".$obTable->getHtml()."';";
    } else {
        return "$('spnLista').innerHTML = '&nbsp;';";
    }
}

echo $stJs;
