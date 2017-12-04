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
    * Página de processamento oculto para o cadastro de Conta Corrente
    * Data de Criação   : 04/11/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lizandro Kirst da Silva

    * @ignore

    * $Id: OCManterConta.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.05.03
*/

/*
$Log$
Revision 1.4  2006/09/15 14:57:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterConta";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );
$obRMONAgencia = new RMONAgencia;
$obRMONConta = new RMONContaCorrente;

$obRMONAgencia->obRMONBanco->listarBanco($rsBanco);
$obRMONAgencia->listarAgencia($rsAgencia);

// SELECIONA ACAO
switch ($_REQUEST ["stCtrl"]) {
    case "preencheAgencia":
        $js .= "f.inNumAgencia.value=''; \n";
        $js .= "limpaSelect(f.cmbAgencia,1); \n";
        $js .= "f.cmbAgencia[0] = new Option('Selecione','', 'selected');\n";
        if ($_REQUEST['inNumbanco']) {
            $obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inNumbanco"] );
            $obRMONAgencia->listarAgencia( $rsAgencia );
            
            $inContador = 1;
            while ( !$rsAgencia->eof() ) {
                $inNumAgencia = $rsAgencia->getCampo( "num_agencia" );
                $stNomAgencia = $rsAgencia->getCampo( "nom_agencia" );
                $js .= "f.cmbAgencia.options[$inContador] = new Option('".$stNomAgencia."','".$inNumAgencia."'); \n";
                $inContador++;
                $rsAgencia->proximo();
            }
        }

        if ($_REQUEST["stLimpar"] == "limpar") {
            $js .= "f.inNumAgencia.value='".$_REQUEST["inNumAgencia"]."'; \n";
            $js .= "f.cmbAgencia.options[".$_REQUEST["inNumAgencia"]."].selected = true; \n";
        }
        sistemaLegado::executaFrameOculto($js);
    break;
}
?>
