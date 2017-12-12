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
    * Paginae Oculta para funcionalidade Manter Bordero
    * Data de Criação   : 28/01/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.20

*/

/*
$Log$
Revision 1.9  2006/07/05 20:39:07  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );
include_once( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterBordero";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$obRMONAgencia = new RMONAgencia;
$obRMONConta = new RMONContaCorrente;

$rsAgencia = new RecordSet;

switch ($_REQUEST["stCtrl"]) {

    case "montaListaBordero":

        foreach ($_REQUEST['inCodEntidade'] as $key => $valor) {
            $stEntidades .= $valor . ", ";
        }

        $stEntidades = substr($stEntidades, 0, strlen($stEntidades - 1));

        $obRTesourariaBoletim = new RTesourariaBoletim();
        $obRTesourariaBoletim->addBordero();

        $obRTesourariaBoletim->roUltimoBordero->obROrcamentoEntidade->setCodigoEntidade($stEntidades);

        $obRTesourariaBoletim->roUltimoBordero->setExercicio($_REQUEST['stExercicio']);
        $obRTesourariaBoletim->roUltimoBordero->obRContabilidadePlanoBanco->setCodPlano($_REQUEST['inCodConta']);
        $obRTesourariaBoletim->roUltimoBordero->setCodBorderoInicial($_REQUEST['inCodigoBorderoInicial']);
        $obRTesourariaBoletim->roUltimoBordero->setCodBorderoFinal($_REQUEST['inCodigoBorderoFinal']);
        $obRTesourariaBoletim->roUltimoBordero->setTimestampBorderoInicial($_REQUEST['stDtInicial']);
        $obRTesourariaBoletim->roUltimoBordero->setTimestampBorderoFinal($_REQUEST['stDtFinal']);

        $obRTesourariaBoletim->roUltimoBordero->listar( $rsLista );

        $rsLista->addFormatacao("vl_pagamento","NUMERIC_BR");

        $obLista = new Lista;
        $obLista->setTitulo( "Registros" );
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsLista );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Entidade" );
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Borderô" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Banco / Ag. / Cta. Corr." );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor Borderô" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_entidade" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[bordero] / [exercicio]" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[cod_banco] / [cod_agencia] / [conta_corrente]" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_pagamento" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addAcao();

        $obLista->ultimaAcao->setAcao( $stAcao );
        $obLista->ultimaAcao->addCampo( "&inCodBordero"     , "cod_bordero"     );
        $obLista->ultimaAcao->addCampo( "&inCodEntidade"    , "cod_entidade"    );
        $obLista->ultimaAcao->addCampo( "&stExercicio  "    , "exercicio"       );
        $obLista->ultimaAcao->addCampo( "&stTipoBordero"    , "tipo_bordero"    );

        $pgProx = CAM_FW_POPUPS."relatorio/OCRelatorio.php";
        $stLink .= "&stCaminho=".CAM_GF_TES_INSTANCIAS."bordero/OCRelatorioBordero.php";
        $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );
        $stHTML = str_replace( "\\\'","\\'",$stHTML );

        SistemaLegado::executaFrameOculto("d.getElementById('spnLista').innerHTML = '".$stHTML."'");

    break;

}
?>
