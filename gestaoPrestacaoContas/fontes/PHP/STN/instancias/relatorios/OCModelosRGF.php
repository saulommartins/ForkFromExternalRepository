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
 * Página de Formulario de Seleção de Impressora para Relatorio
 * Data de Criação   : 01/08/2006

 * @author Jose Eduardo Porto

 * @ignore

 * Casos de uso : uc-06.01.22
                  uc-06.01.23

$Id: OCModelosRGF.php 61069 2014-12-04 12:42:40Z michel $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";

$obRRelatorio = new RRelatorio;

$arFiltro = Sessao::read('filtroRelatorio');

if ($arFiltro['inCodEntidade']) {
    $stEntidade = "";
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stEntidade.= $valor . ",";
    }
    $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
}

switch ($arFiltro['stAcao']) {
    case "anexo1":
            $rsDadosAnexo1 = new RecordSet();

            include_once( CAM_GPC_STN_NEGOCIO."RSTNRGFAnexo1.class.php"  );

            $obRSTNRGFAnexo1 = new RSTNRGFAnexo1();

            if ( count($arFiltro['inCodEntidade']) > 0 ) {
               foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
                  $stInFiltro .= $valor.',';
               }
               $stInFiltro = substr($stInFiltro, 0,(strlen($stInFiltro)-1));
            }

            $obRSTNRGFAnexo1->setEntidade  ( $stInFiltro                     );
            $obRSTNRGFAnexo1->setExercicio ( $arFiltro['stExercicio' ] );
            $obRSTNRGFAnexo1->setBimestre  ( $arFiltro['bimestre']     );
            $obRSTNRGFAnexo1->geraRecordSet( $rsDadosAnexo1                  );

            Sessao::write('rsDadosAnexo1',$rsDadosAnexo1);

            $obRRelatorio->executaFrameOculto( "OCGeraRGFAnexo1.php" );
    break;
    case "anexo3":

        // Comentado devido a versão transferida para o Birt
        //if (//sessao->filtro['stTipoRelatorio'] == "Quadrimestre") {
        //    include_once( CAM_GPC_STN_NEGOCIO."RSTNRGFAnexo3.class.php"  );

        //    $obRSTNRGFAnexo3 = new RSTNRGFAnexo3();

        //    $obRSTNRGFAnexo3->setEntidade           ( $stEntidade                            );
        //    $obRSTNRGFAnexo3->setExercicio          ( //sessao->filtro['stExercicio']         );
        //    $obRSTNRGFAnexo3->setQuadrimestre       ( //sessao->filtro['cmbQuadrimestre']     );
        //    $obRSTNRGFAnexo3->geraRecordSet( $rsDadosAnexo3 );
        //
        //    //sessao->transf5 = $rsDadosAnexo3;
        //    $obRRelatorio->executaFrameOculto( "OCGeraRGFAnexo3.php" );
        //} else {
        //    include_once( CAM_GPC_STN_NEGOCIO."RSTNRGFAnexo3Semestre.class.php"  );
        //
        //    $obRSTNRGFAnexo3Semestre = new RSTNRGFAnexo3Semestre();

        //    $obRSTNRGFAnexo3Semestre->setEntidade           ( $stEntidade                            );
        //    $obRSTNRGFAnexo3Semestre->setExercicio          ( //sessao->filtro['stExercicio']         );
        //    $obRSTNRGFAnexo3Semestre->setSemestre           ( //sessao->filtro['cmbSemestre']         );
        //    $obRSTNRGFAnexo3Semestre->geraRecordSet( $rsDadosAnexo3 );
        //
        //    //sessao->transf5 = $rsDadosAnexo3;
        //    $obRRelatorio->executaFrameOculto( "OCGeraRGFAnexo3Semestre.php" );
        //}

    break;

    case "anexo4":

            include_once( CAM_GPC_STN_NEGOCIO."RSTNRGFAnexo4.class.php"  );

            $obRSTNRGFAnexo4 = new RSTNRGFAnexo4();

            if ( count($arFiltro['inCodEntidade']) > 0 ) {
               foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
                  $stInFiltro .= $valor.',';
               }
               $stInFiltro = substr($stInFiltro, 0,(strlen($stInFiltro)-1));
            }

            $obRSTNRGFAnexo4->setEntidade           ( $stInFiltro                     );
            $obRSTNRGFAnexo4->setExercicio          ( $arFiltro['stExercicio' ] );
            $obRSTNRGFAnexo4->setQuadrimestre       ( $arFiltro['quadrimestre'] );
            $obRSTNRGFAnexo4->geraRecordSet( $rsDadosAnexo4 );

            Sessao::write('rsDadosAnexo4',$rsDadosAnexo4);
            $obRRelatorio->executaFrameOculto( "OCGeraRGFAnexo4.php" );
    break;
}

switch ($request->get('stCtrl')) {

    case "preencheSpan":

        if ($_REQUEST['stTipoRelatorio' ] == "Mes") {

            $obCmbMes = new Mes;
            $obCmbMes->setRotulo ( "*Mês" );
            $obCmbMes->setId     ( "cmbMensal" );
            $obCmbMes->setName   ( "cmbMensal" );

            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obCmbMes );

            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();

            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );

            $js = "d.getElementById('spnTipoRelatorio').innerHTML = '".$stHTML."'";
        
        } elseif ($_REQUEST['stTipoRelatorio' ] == "Bimestre") {

            $obCmbBimestre = new Select;
            $obCmbBimestre->setRotulo        ( "*Bimestre"           );
            $obCmbBimestre->setName          ( "cmbBimestre"         );
            $obCmbBimestre->addOption        ( "", "Selecione"       );
            $obCmbBimestre->addOption        ( "1", "1º Bimestre"    );
            $obCmbBimestre->addOption        ( "2", "2º Bimestre"    );
            $obCmbBimestre->addOption        ( "3", "3º Bimestre"    );
            $obCmbBimestre->addOption        ( "4", "4º Bimestre"    );
            $obCmbBimestre->addOption        ( "5", "5º Bimestre"    );
            $obCmbBimestre->addOption        ( "6", "6º Bimestre"    );
            $obCmbBimestre->setNull          ( true                  );
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

            $js = "d.getElementById('spnTipoRelatorio').innerHTML = '".$stHTML."'";
        } elseif ($_REQUEST['stTipoRelatorio'] == "Quadrimestre") {

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

            $js = "d.getElementById('spnTipoRelatorio').innerHTML = '".$stHTML."'";
        } elseif ($_REQUEST['stTipoRelatorio'] == "Semestre") {

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

            $js = "d.getElementById('spnTipoRelatorio').innerHTML = '".$stHTML."'";
        } elseif ($_REQUEST['stTipoRelatorio'] == "UltimoQuadrimestre") {

            $obCmbQuadrimestre = new Select;
            $obCmbQuadrimestre->setRotulo        ( "*Quadrimestre"           );
            $obCmbQuadrimestre->setName          ( "cmbQuadrimestre"         );
            $obCmbQuadrimestre->addOption        ( "", "Selecione"           );
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

            $js = "d.getElementById('spnTipoRelatorio').innerHTML = '".$stHTML."'";
        } elseif ($_REQUEST['stTipoRelatorio'] == "UltimoSemestre") {

            $obCmbQuadrimestre = new Select;
            $obCmbQuadrimestre->setRotulo        ( "*Semestre"           );
            $obCmbQuadrimestre->setName          ( "cmbSemestre"         );
            $obCmbQuadrimestre->addOption        ( "", "Selecione"           );
            $obCmbQuadrimestre->addOption        ( "2", "2º Semestre"    );
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

            $js = "d.getElementById('spnTipoRelatorio').innerHTML = '".$stHTML."'";
        } else {
            $js = "d.getElementById('spnTipoRelatorio').innerHTML = ''";
        }

        SistemaLegado::executaFrameOculto($js);

    break;

}
?>
