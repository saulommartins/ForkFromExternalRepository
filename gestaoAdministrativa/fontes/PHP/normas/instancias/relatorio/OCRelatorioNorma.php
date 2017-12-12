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
 * Arquivo de instância para relatório de normas
 * Data de Criação: 25/07/2005

 * @author Analista: Cassiano
 * @author Desenvolvedor: Cassiano

 $Revision: 13988 $
 $Name$
 $Author: cassiano $
 $Date: 2006-08-15 15:49:27 -0300 (Ter, 15 Ago 2006) $

 Casos de uso: uc-01.04.03
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"            );
include_once( CAM_GA_NORMAS_NEGOCIO."RNormasNorma.class.php" );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];
$obRRelatorio           = new RRelatorio;
$obRNormasNorma = new RNormasNorma;

if ($stCtrl == '') {
    $filtrosRelatorio = Sessao::read('filtroRelatorio');
    $stCtrl = $filtrosRelatorio['stCtrl'];
}

// Acoes por pagina
switch ($stCtrl) {
    case "vigente":
        $obRNormasNorma->obRNorma->obRTipoNorma->setCodTipoNorma($filtrosRelatorio['inCodTipoNorma']);
        $obRNormasNorma->obRNorma->setDataInicialPublicacao($filtrosRelatorio['dtDataInicial']);
        $obRNormasNorma->obRNorma->setDataFinalPublicacao($filtrosRelatorio['dtDataFinal']);
        $obRNormasNorma->obRNorma->setDataInicialAssinatura($filtrosRelatorio['dtAssInicial']);
        $obRNormasNorma->obRNorma->setDataFinalAssinatura($filtrosRelatorio['dtAssFinal']);

        $obRNormasNorma->geraRecordSet( $rsTipoNorma  );
        Sessao::write('rsTipoNorma',$rsTipoNorma);
        $obRRelatorio->executaFrameOculto( "OCGeraRelatorioNorma.php" );
        break;

    case "revogada":
        $obRNormasNorma->obRNorma->obRTipoNorma->setCodTipoNorma($filtrosRelatorio['inCodTipoNorma']);
        $obRNormasNorma->obRNorma->setDataInicialPublicacao($filtrosRelatorio['dtDataInicial']);
        $obRNormasNorma->obRNorma->setDataFinalPublicacao($filtrosRelatorio['dtDataFinal']);
        $obRNormasNorma->obRNorma->setDataInicialAssinatura($filtrosRelatorio['dtAssInicial']);
        $obRNormasNorma->obRNorma->setDataFinalAssinatura($filtrosRelatorio['dtAssFinal']);

        $obRNormasNorma->geraRecordSet( $rsTipoNorma  );
        Sessao::write('rsTipoNorma',$rsTipoNorma);
        $obRRelatorio->executaFrameOculto( "OCGeraRelatorioNorma.php" );
        break;

    case "vigente_ate":
        
        $obRNormasNorma->obRNorma->obRTipoNorma->setCodTipoNorma($filtrosRelatorio['inCodTipoNorma']);
        $obRNormasNorma->obRNorma->setDataInicialPublicacao($filtrosRelatorio['dtDataInicial']);
        $obRNormasNorma->obRNorma->setDataFinalPublicacao($filtrosRelatorio['dtDataFinal']);
        $obRNormasNorma->obRNorma->setDataInicialAssinatura($filtrosRelatorio['dtAssInicial']);
        $obRNormasNorma->obRNorma->setDataFinalAssinatura($filtrosRelatorio['dtAssFinal']);
        $obRNormasNorma->obRNorma->setDataTermino($filtrosRelatorio['dtTermino']);

        $obRNormasNorma->geraRecordSet( $rsTipoNorma  );
        Sessao::write('rsTipoNorma',$rsTipoNorma);
        $obRRelatorio->executaFrameOculto( "OCGeraRelatorioNorma.php" );

        break;

    case "check_vigente_ate":
        $obForm = new Form;

        $obDtTermino = new Data;
        $obDtTermino->setName       ('dtTermino');
        $obDtTermino->setId         ('dtTermino');
        $obDtTermino->setRotulo     ("Data de término");
        $obDtTermino->setTitle      ("Informe a data de termino da norma");
        $obDtTermino->setNull     (false);

        $obFormulario = new Formulario;
        $obFormulario->addForm( $obForm );
        $obFormulario->addComponente( $obDtTermino );
        $obFormulario->montaInnerHTML();

        $stJs.= "jQuery('#addCmpDtTermino').html('".$obFormulario->getHTML()."');";
        echo $stJs;
        break;

    case "check_revogada":
    case "check_vigente":
        $stJs.= "jQuery('#addCmpDtTermino').html('');";
        echo $stJs;
        break;
}

?>
