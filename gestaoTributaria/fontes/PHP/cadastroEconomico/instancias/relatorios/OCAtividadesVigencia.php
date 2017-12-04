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
    * Frame Oculto para Atividade
    * Data de Criacao   : 13/01/2009

    * @ignore

    *Casos de uso: uc-05.02.15

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividade.class.php" );

switch ($_REQUEST['stCtrl']) {
    case "montaAtividade":
        if ($_REQUEST["inCodVigencia"]) {
            $obTxtNomAtividade = new TextBox;
            $obTxtNomAtividade->setName  ( "stNomAtividade" );
            $obTxtNomAtividade->setRotulo( "Nome da Atividade" );
            $obTxtNomAtividade->setTitle ( "" ) ;
            $obTxtNomAtividade->setSize  ( "80" ) ;
            $obTxtNomAtividade->setMaxLength(240);
            $obTxtNomAtividade->setInteiro( false );

            $obTCEMAtividade = new TCEMAtividade;
            $obTCEMAtividade->recuperaMaxCodEstrutural( $rsListaEstrutura );

            $arEstrutura = explode( ".", $rsListaEstrutura->getCampo("cod_estrutural") );
            $stEstrutura = "";
            for ( $inX=0; $inX<count($arEstrutura); $inX++ ) {
                if ( $inX )
                    $stEstrutura .= ".";

                for ( $inY=0; $inY<strlen($arEstrutura[$inX]); $inY++ ) {
                    $stEstrutura .= "9";
                }
            }

            $obCodInicio = new TextBox;
            $obCodInicio->setName  ( "inCodInicio" );
            $obCodInicio->setRotulo( "Atividade" );
            $obCodInicio->setTitle ( "Informe um período" ) ;
            $obCodInicio->setMascara( $stEstrutura );

            $obLblPeriodo = new Label;
            $obLblPeriodo->setValue( " até " );

            $obCodTermino = new TextBox;
            $obCodTermino->setName     ( "inCodTermino" );
            $obCodTermino->setRotulo   ( "Atividade" );
            $obCodTermino->setTitle    ( "Informe um período" );
            $obCodTermino->setMascara  ( $stEstrutura );

            $obFormulario = new Formulario;
            $obFormulario->addComponente( $obTxtNomAtividade ) ;
            $obFormulario->agrupaComponentes( array( $obCodInicio, $obLblPeriodo, $obCodTermino ) );
            $obFormulario->montaInnerHTML();
            $js = "d.getElementById('spnAtividade').innerHTML = '". $obFormulario->getHTML(). "';\n";
        } else {
            $js = "d.getElementById('spnAtividade').innerHTML = '&nbsp;';\n";
        }

        SistemaLegado::executaFrameOculto($js);
        break;
}

?>
