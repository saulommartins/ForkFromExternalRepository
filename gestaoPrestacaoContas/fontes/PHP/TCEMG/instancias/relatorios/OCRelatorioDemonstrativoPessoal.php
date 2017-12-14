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

  * Página de Relatório de Demonstrativo de Gastos com Pessoal
    * Data de Criação   : 09/07/2014
    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal
    * @ignore
    *   
    * $Id: 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_FW_PDF."RRelatorio.class.php" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

$stCtrl = $request->get('stCtrl');
$pgGera = $request->get('pgGera');

$stPrograma = "RelatorioDemonstrativoPessoal";

$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$arFiltro = Sessao::read('filtroRelatorio');
$stJs = "";


switch( $stCtrl ):
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

    case "preencheSpan":

        if ( $request->get('inTipoPeriodo') == "Bimestre" ) {

            $obCmbBimestre = new Select;
            $obCmbBimestre->setRotulo ( "Bimestre"    );
            $obCmbBimestre->setName   ( "cmbBimestre" );
            $obCmbBimestre->addOption ( "1", "1º Bimestre" );
            $obCmbBimestre->addOption ( "2", "2º Bimestre" );
            $obCmbBimestre->addOption ( "3", "3º Bimestre" );
            $obCmbBimestre->addOption ( "4", "4º Bimestre" );
            $obCmbBimestre->addOption ( "5", "5º Bimestre" );
            $obCmbBimestre->addOption ( "6", "6º Bimestre" );
            
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

            $stJs .= "d.getElementById('spnPeriodo').innerHTML = '".$stHTML."'";
        } elseif ( $request->get('inTipoPeriodo') == "Quadrimestre") {

            $obCmbQuadrimestre = new Select;
            $obCmbQuadrimestre->setRotulo( "*Quadrimestre"        );
            $obCmbQuadrimestre->setName  ( "cmbQuadrimestre"      );
            $obCmbQuadrimestre->addOption( "1", "1º Quadrimestre" );
            $obCmbQuadrimestre->addOption( "2", "2º Quadrimestre" );
            $obCmbQuadrimestre->addOption( "3", "3º Quadrimestre" );
            $obCmbQuadrimestre->setNull  ( true                   );
            $obCmbQuadrimestre->setStyle ( "width: 220px"         );

            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obCmbQuadrimestre );

            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();

            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );

            $stJs .= "d.getElementById('spnPeriodo').innerHTML = '".$stHTML."'";
        } elseif ( $request->get('inTipoPeriodo') == "Semestre" ) {

            $obCmbSemestre = new Select;
            $obCmbSemestre->setRotulo( "*Semestre"        );
            $obCmbSemestre->setName  ( "cmbSemestre"      );
            $obCmbSemestre->addOption( "1", "1º Semestre" );
            $obCmbSemestre->addOption( "2", "2º Semestre" );
            $obCmbSemestre->setNull  ( true               );
            $obCmbSemestre->setStyle ( "width: 220px"     );

            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obCmbSemestre );

            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();

            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );

            $stJs .= "d.getElementById('spnPeriodo').innerHTML = '".$stHTML."'";
        } elseif ( $request->get('inTipoPeriodo') == "Trimestre" ) {
            $obCmbTrimestre = new Select;
            $obCmbTrimestre->setRotulo( "*Trimestre"        );
            $obCmbTrimestre->setName  ( "cmbTrimestre"      );
            $obCmbTrimestre->addOption( "1", "1º Trimestre" );
            $obCmbTrimestre->addOption( "2", "2º Trimestre" );
            $obCmbTrimestre->addOption( "3", "3º Trimestre" );
            $obCmbTrimestre->addOption( "4", "4º Trimestre" );
            $obCmbTrimestre->setNull  ( true                );
            $obCmbTrimestre->setStyle ( "width: 220px"      );

            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obCmbTrimestre );

            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();

            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );

            $stJs .= "d.getElementById('spnPeriodo').innerHTML = '".$stHTML."'";
        }else {
            $stJs = "d.getElementById('spnPeriodo').innerHTML = ''";
        }

    break;
  
endswitch;

echo $stJs;
