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
* Arquivo instância para popup de Convênio
* Data de Criação: 08/11/2005

* @author Analista: Fábio Bertoldi Rodrigues
* @author Desenvolvedor: Diego Bueno Coelho

    * $Id: OCProcurarConvenio.php 63839 2015-10-22 18:08:07Z franver $

Casos de uso: uc-05.05.04
*/

/*
$Log$
Revision 1.3  2006/09/18 08:47:14  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );

$obRMONConta    = new RMONContaCorrente;
$obRMONAgencia  = new RMONAgencia;

//Define o nome dos arquivos PHP
$stPrograma    = "ProcurarConvenio";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

//echo 'acao: ' . $_REQUEST['stCtrl'].'<br>';
switch ($_REQUEST['stCtrl']) {

case "preencheAgencia":

    $js .= "f.inNumAgencia.value=''; \n";
    $js .= "limpaSelect(f.cmbAgencia,1); \n";
    $js .= "f.cmbAgencia[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.stNumeroConta.value = 'asz';\n";

    if ($_REQUEST['cmbBanco']) {

        $obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST['cmbBanco'] );
        $obRMONAgencia->listarAgencia( $rsAgencia );

        $inContador = 1;
        while ( !$rsAgencia->eof() ) {

            $inNumAgencia = $rsAgencia->getCampo( "num_agencia" );
            $stNomAgencia = $rsAgencia->getCampo( "nom_agencia" );

            $js .= "f.inNumAgencia.value='".$inCodAgencia."'; \n";
            $js .= "f.cmbAgencia.options[$inContador] = new Option('".$stNomAgencia."','".$inNumAgencia."'); \n";
            $inContador++;
            $rsAgencia->proximo();
        }
    }
    //print_r ($_REQUEST);
    if ($_REQUEST["stLimpar"] == "limpar") {
        $js .= "f.inNumAgencia.value='".$_REQUEST["inNumAgencia"]."'; \n";
        $js .= "f.cmbAgencia.options[".$_REQUEST["inNumAgencia"]."].selected = true; \n";
    }

    sistemaLegado::executaIFrameOculto($js);

break;

}

?>
