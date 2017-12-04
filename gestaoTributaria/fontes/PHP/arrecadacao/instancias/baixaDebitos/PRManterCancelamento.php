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
    * Página de processamento para calculo
    * Data de criação : 26/07/2007

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Fernando Piccini Cercato

    * $Id: PRManterCancelamento.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.10
**/

/*
$Log$
Revision 1.1  2007/07/27 13:16:25  cercato
Bug#9762#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPermissaoCancelamento.class.php" );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma    = "ManterCancelamento";
$pgForm        = "FM".$stPrograma.".php";

switch ($stAcao) {
    case "permitir":
        $arListaCGM = Sessao::read( 'listaCGM' );
        if ( count( $arListaCGM ) <= 0 ) {
            SistemaLegado::exibeAviso( "A lista de usuários não está preenchido.", "n_incluir", "erro" );
            exit;
        }

        $obTARRPermissaoCancelamento = new TARRPermissaoCancelamento;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTARRPermissaoCancelamento );
            for ( $inX=0; $inX<count( $arListaCGM ); $inX++ ) {
                $obTARRPermissaoCancelamento->setDado( "numcgm", $arListaCGM[$inX]["numcgm"] );
                $obTARRPermissaoCancelamento->inclusao();
            }
        Sessao::encerraExcecao();

        sistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=incluir","Lista de ".count( $arListaCGM )." CGMs","incluir","aviso", Sessao::getId(), "../");
        break;
}
