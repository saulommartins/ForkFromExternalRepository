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
    * Página de Oculto para Relatório Despesas SIOPS
    * Data de Criação  : 12/06/2008

    * @author Rodrigo Soares Rodrigues

    * Casos de uso : uc-02.01.01

    * $Id: OCDespesasSIOPS.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_FW_PDF."RRelatorio.class.php" );

$stCtrl = $_POST["stCtrl"] ? $_POST["stCtrl"] : $_GET["stCtrl"];

$stPrograma = "DespesasSIOPS";

$pgOcul = "OC".$stPrograma.".php";

switch( $_REQUEST['stCtrl'] ):

    case "preencheSpan":

        switch ($_REQUEST['stTipoRelatorio' ]) {
            case 'Trimestre':
                $obCmbTrimestre = new Select;
                $obCmbTrimestre->setRotulo        ( "*Trimestre"           );
                $obCmbTrimestre->setName          ( "cmbTrimestre"         );
                $obCmbTrimestre->addOption        ( "1", "1º Trimestre"    );
                $obCmbTrimestre->addOption        ( "2", "2º Trimestre"    );
                $obCmbTrimestre->addOption        ( "3", "3º Trimestre"    );
                $obCmbTrimestre->addOption        ( "4", "4º Trimestre"    );
                $obCmbTrimestre->setNull          ( true                   );
                $obCmbTrimestre->setStyle         ( "width: 220px"         );

                $obFormulario = new Formulario;
                $obFormulario->addComponente ( $obCmbTrimestre );

                $obFormulario->montaInnerHTML ();
                $stHTML = $obFormulario->getHTML ();

                $stHTML = str_replace( "\n" ,"" ,$stHTML );
                $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
                $stHTML = str_replace( "  " ,"" ,$stHTML );
                $stHTML = str_replace( "'","\\'",$stHTML );
                $stHTML = str_replace( "\\\\'","\\'",$stHTML );

                $stJs = "d.getElementById('spnTipoRelatorio').innerHTML = '".$stHTML."'";
                break;

            case 'Semestre':
                $obCmbSemestre = new Select;
                $obCmbSemestre->setRotulo        ( "*Semestre"            );
                $obCmbSemestre->setName          ( "cmbSemestre"          );
                $obCmbSemestre->addOption        ( "1", "1º Semestre"     );
                $obCmbSemestre->addOption        ( "2", "2º Semestre"     );
                $obCmbSemestre->setNull          ( true                   );
                $obCmbSemestre->setStyle         ( "width: 220px"         );

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

            case 'Bimestre':
                $obCmbBimestre = new Select;
                $obCmbBimestre->setRotulo        ( "*Bimestre"            );
                $obCmbBimestre->setName          ( "cmbBimestre"          );
                $obCmbBimestre->addOption        ( "1", "1º Bimestre"     );
                $obCmbBimestre->addOption        ( "2", "2º Bimestre"     );
                $obCmbBimestre->addOption        ( "3", "3º Bimestre"     );
                $obCmbBimestre->addOption        ( "4", "4º Bimestre"     );
                $obCmbBimestre->addOption        ( "5", "5º Bimestre"     );
                $obCmbBimestre->addOption        ( "6", "6º Bimestre"     );
                $obCmbBimestre->setNull          ( true                   );
                $obCmbBimestre->setStyle         ( "width: 220px"         );

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

            default:
                $stJs = "d.getElementById('spnTipoRelatorio').innerHTML = ''";
                break;
        }

    break;

endswitch;

echo $stJs;
