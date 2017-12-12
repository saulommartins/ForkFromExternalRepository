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
    * Classe Oculta de Ajuestes Modelos
    * Data de Criação   : 18/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso uc-02.05.01

*/

/*
$Log$
Revision 1.4  2006/07/05 20:45:22  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_LRF_NEGOCIO."RLRFTCERSModelo.class.php"      );

//Define o nome dos arquivos PHP
$stPrograma = "AjustesModelos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "CO".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRLRFTCERSModelo = new RLRFTCERSModelo();
$obRLRFTCERSModelo->addQuadro();
$obRLRFTCERSModelo->roUltimoQuadro->addContaPlano();

switch ($stCtrl) {
    case 'montaListaEntidadeValor':
        $x = 1;
        foreach ($sessao->transf5 as $arContaPlano) {
            $obRLRFTCERSModelo->setExercicio( $arContaPlano['stExercicio'] );
            $obRLRFTCERSModelo->setCodModelo( $arContaPlano['inCodModelo'] );
            $obRLRFTCERSModelo->roUltimoQuadro->setCodQuadro( $arContaPlano['inCodQuadro'] );
            $obRLRFTCERSModelo->roUltimoQuadro->roUltimaContaPlano->setMes( $arContaPlano['inMes'] );
            $obRLRFTCERSModelo->roUltimoQuadro->roUltimaContaPlano->obROrcamentoEntidade->setCodigoEntidade( $arContaPlano['inCodEntidade'] );
            $obRLRFTCERSModelo->roUltimoQuadro->roUltimaContaPlano->listar( $rsLista );

            $rsLista->addFormatacao( 'vl_ajuste', 'NUMERIC_BR' );

            $obLista = new Lista;
            $obLista->setMostraPaginacao( false );
            $obLista->setRecordSet( $rsLista );
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Classificação");
            $obLista->ultimoCabecalho->setWidth( 15 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Descrição");
            $obLista->ultimoCabecalho->setWidth( 60 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Adição/Exclusão");
            $obLista->ultimoCabecalho->setWidth( 15 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "cod_estrutural" );
            $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "nom_conta" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();

            $obTxtValor = new Numerico();
            $obTxtValor->setName       ( "nuValor_".$arContaPlano['inCodQuadro']."_[cod_conta]_" );
            $obTxtValor->setSize       ( 21             );
            $obTxtValor->setMaxLength  ( 21             );
            $obTxtValor->setValue      ( 'vl_ajuste'    );
            $obTxtValor->setAlign      ( 'RIGHT'        );
            $obLista->addDadoComponente( $obTxtValor    );
            $obLista->commitDadoComponente();

            $obLista->montaHTML();
            $stHTML = $obLista->getHTML();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\'",$stHTML );

            $js .= "d.getElementById('spnQuadro".$x."').innerHTML = '".$stHTML."';";
            $x++;
        }
        SistemaLegado::executaFrameOculto($js);
    break;
}
?>
