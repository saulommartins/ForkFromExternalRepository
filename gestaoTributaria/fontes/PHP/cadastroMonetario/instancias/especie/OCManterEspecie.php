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
  * Pagina Oculta para ESPECIE
  * Data de criacao : 02/06/2005

    * @author Analista: Fabio Bertoldi
    * @author Programador: Diego Bueno Coelho

    * $Id: OCManterEspecie.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.05.10
**/

/*
$Log$
Revision 1.2  2006/09/15 14:57:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONEspecieCredito.class.php" );

$stCtrl = $_REQUEST['stCtrl'];

$stJs = "";

//Define o nome dos arquivos PHP
$stPrograma      = "ManterEspecie";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgFormGrupo     = "FM".$stPrograma.".php";
$pgFormCredito   = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

/*
        FIM DAS FUNÇÕES
*/

switch ($_REQUEST ["stCtrl"]) {
  case "preencheGenero":

        $js .= "f.inCodGenero.value=''; \n";
        $js .= "limpaSelect(f.cmbGenero,1); \n";
        $js .= "f.cmbGenero[0] = new Option('Selecione','', 'selected');\n";

        if ($_REQUEST['inCodNatureza']) {

            $obRMONEspecieCredito = new RMONEspecieCredito;
            $obRMONEspecieCredito->setCodNatureza( $_REQUEST["inCodNatureza"] );
            $obRMONEspecieCredito->listarGenero( $rsGeneros );

            $inContador = 1;
            while ( !$rsGeneros->eof() ) {
                $inCodGenero = $rsGeneros->getCampo( "cod_genero" );
                $stNomGenero = $rsGeneros->getCampo( "nom_genero" );
                $js .= "f.cmbGenero.options[$inContador] = new Option('".$stNomGenero."','".$inCodGenero."'); \n";
                $inContador++;
                $rsGeneros->proximo();
            }
        }

        if ($_REQUEST["stLimpar"] == "limpar") {
            $js .= "f.inCodGenero.value='".$_REQUEST["inCodNatureza"]."'; \n";
            $js .= "f.cmbGenero.options[".$_REQUEST["inCodNatureza"]."].selected = true; \n";
        }
    break;
}

SistemaLegado::executaFrameOculto($js);
?>
