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
* Página de Processamento de Almoxarife
* Data de Criação   : 10/02/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Eduardo Antunez

* @ignore

* Casos de uso: uc-03.03.02
*/

/*
$Log$
Revision 1.12  2006/09/15 13:34:21  leandro.zis
Bug #6872#

Revision 1.11  2006/07/06 14:00:21  diego
Retirada tag de log com erro.

Revision 1.10  2006/07/06 12:09:52  diego

*/
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarife.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
//Define o nome dos arquivos PHP
$stPrograma = "ManterAlmoxarife";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRAlmoxarifadoAlmoxarife = new RAlmoxarifadoAlmoxarife;
$obErro  = new Erro;

switch ($stAcao) {
    case "incluir":
            if (!empty($_REQUEST['inCodAlmoxarifado'])) {
               foreach ($_REQUEST['inCodAlmoxarifado'] as $stAlmoxarifado) {
                   $arAlmoxarifado = explode("-", $stAlmoxarifado);
                   $obRAlmoxarifadoAlmoxarife->addAlmoxarifado();
                   $obRAlmoxarifadoAlmoxarife->roUltimoAlmoxarifado->setCodigo( $arAlmoxarifado[0] );
                   if ($arAlmoxarifado[0] == $_REQUEST['inCodPadrao']) {
                        $obRAlmoxarifadoAlmoxarife->obAlmoxarifadoPadrao->setCodigo( $arAlmoxarifado[0] );
                    }
               }
            }
            
            $obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->setNumCGM($_REQUEST['inCodCGMAlmoxarife']);
            $obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->consultarCGM( $rsCGM );
            $obRAlmoxarifadoAlmoxarife->setAtivo( $_REQUEST['boAtivo'] );
            
            $obTAlmoxarifadoAlmoxarife = new TAlmoxarifadoAlmoxarife();
            $obTAlmoxarifadoAlmoxarife->setDado('cgm_almoxarife', $_REQUEST['inCodCGMAlmoxarife']);
            $obTAlmoxarifadoAlmoxarife->recuperaPorChave($rsAlmoxarife);
                        
            //Validar se ja é cadastrado como almoxarife
            if ( $rsAlmoxarife->getNumLinhas() > 0) {
                $obErro->setDescricao("Almoxarife ".$_REQUEST['inCodCGMAlmoxarife']." - ".$_REQUEST['stNomCGMAlmoxarife']." já está cadastrado!");    
            }else{
                $obErro = $obRAlmoxarifadoAlmoxarife->incluir();    
            }

            if ( !$obErro->ocorreu() ) {
                sistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=incluir","Almoxarife: ".$obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->getNumCGM().'-'.$obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->getNomCGM(),"incluir","aviso", Sessao::getId(), "../");
            }else{
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
    break;
    case "alterar":
            if (!empty($_REQUEST['inCodAlmoxarifado'])) {
               foreach ($_REQUEST['inCodAlmoxarifado'] as $stAlmoxarifado) {
                   $arAlmoxarifado = explode("-", $stAlmoxarifado);
                   $obRAlmoxarifadoAlmoxarife->addAlmoxarifado();
                   $obRAlmoxarifadoAlmoxarife->roUltimoAlmoxarifado->setCodigo( $arAlmoxarifado[0] );
                   if ($arAlmoxarifado[0] == $_REQUEST['inCodPadrao']) {
                        $obRAlmoxarifadoAlmoxarife->obAlmoxarifadoPadrao->setCodigo( $arAlmoxarifado[0] );
                    }

                }
            }
            $obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->setNumCGM( $_REQUEST['inCodCGMAlmoxarife']);
            $obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->consultarCGM( $rsCGM );
            $obRAlmoxarifadoAlmoxarife->setAtivo( $_REQUEST['boAtivo'] );

            $obErro = $obRAlmoxarifadoAlmoxarife->alterar();
            if( !$obErro->ocorreu() )
                sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=alterar","Almoxarife: ".$obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->getNumCGM().'-'.$obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->getNomCGM(),"alterar","aviso", Sessao::getId(), "../");
            else
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
    break;
    case "excluir":
       $obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->setNumCGM( $_REQUEST['inCGMAlmoxarife'] );
       $obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->setNomCGM( $_REQUEST['stNomCGMAlmoxarife'] );
       $obErro = $obRAlmoxarifadoAlmoxarife->excluir();

       if ( !$obErro->ocorreu() ) {
           sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir","Almoxarife: ".$obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->getNumCGM().'-'.$obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->getNomCGM(),"excluir","aviso", Sessao::getId(), "../");
       } else {
           $obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->setNumCGM( $_REQUEST['inCGMAlmoxarife'] );
           $obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->setNomCGM( $_REQUEST['stNomCGMAlmoxarife'] );

           $obRAlmoxarifadoAlmoxarife->setAtivo(false);
           $obRAlmoxarifadoAlmoxarife->alterar();
           sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir","Almoxarife inativado com sucesso"." (".$obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->getNumCGM().'-'.$obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->getNomCGM().")","aviso", Sessao::getId(), "../");
       }
    break;
}

?>
